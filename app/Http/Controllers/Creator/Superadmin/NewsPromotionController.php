<?php

namespace App\Http\Controllers\Creator\Superadmin;

use Config;
use PDF;
use Auth;
use File;
use Exception;
use Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\{ NewsPromotion, User, JobRole, Group, Country, Region };

class NewsPromotionController extends Controller
{
    
    /**
     * Create a new NewsPromotionController instance.
     *
     * @return void
     */
    public $storagePath;
    public $viewStoragePath;
    public $imageFormat;
    public $videoFormat;

    public function __construct()
    {
       $this->storagePath = storage_path('app/public' . Config::get('constant.NEWS_STORAGE_PATH'));
       $this->viewStoragePath =  Config::get('constant.NEWS_STORAGE_PATH');
       $this->imageFormat = Config::get('constant.SUPPORTED_IMAGE_FORAMTS'); 
       $this->videoFormat = Config::get('constant.SUPPORTED_VIDEO_FORAMTS'); 
    }

    public function index(Request $request)
    {
        $regions = Region::orderBy('name')->get(); 
        $countries = Country::where('status', 1)->orderBy('name')->get(); 
        $jobRoles = JobRole::where('status', 1)->orderBy('name')->get(); 
        $groups = Group::where('status', 1)->orderBy('name')->get();

        $page = $request->page ? $request->page : 1;
        $limit = 10;
        
        $viewStoragePath =  $this->viewStoragePath;
        $imageFormat =  $this->imageFormat;
        $videoFormat =  $this->videoFormat;

        $query = NewsPromotion::buildQuery($request);
   
        $newsPromotions = $query->orderBy('created_at', 'DESC')->paginate($limit);
       
        return view('creator.superadmin.news-promotions.index', compact('newsPromotions', 'viewStoragePath', 'imageFormat', 'videoFormat', 'countries', 'regions', 'jobRoles', 'groups'))
                ->with('index', ($page - 1) * $limit);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $jobRoles = JobRole::where('status', 1)->orderBy('name')->get();
        $groups = Group::where('status', 1)->orderBy('name')->get();
        $countries = Country::where('status', 1)->orderBy('name')->get(); 

        return view('creator.superadmin.news-promotions.create', compact('jobRoles', 'groups', 'countries'));
    }

    public function store(Request $request)
    {   
        request()->validate([
            'title' => 'required|min:2',
            'description' => 'required|min:6'
          ]); 

        $user = Auth::user();
        $newsPromotion = new NewsPromotion;
        $newsPromotion->title = $request->title;
        $newsPromotion->description = $request->description;
        $newsPromotion->country_id = $request->filter_country == -1 ? NULL : $request->filter_country ;
        $newsPromotion->region_id = $request->region_id == -1 ? NULL : $request->region_id;
        $newsPromotion->job_role_id = $request->filter_jobrole == -1 ? NULL : $request->filter_jobrole ;
        $newsPromotion->group_id = $request->filter_group == -1 ? NULL : $request->filter_group;
        $newsPromotion->created_by = $user->id;
        $newsPromotion->updated_by = $user->id;
      
        // file upload
        $mediaFormats = implode(',', Config::get("constant.SUPPORTED_MEDIA_FROMATS"));
        if($request->hasFile('media')) {
            request()->validate([
                'media' => 'mimes:' . $mediaFormats . '|max:1048576' // 1048 mb
                ]);

            $file = $request->file('media');
            $extension = $file->getClientOriginalExtension();

            // check directory exist or not 
            $path = $this->storagePath;
            if(!is_dir($path)) {
                File::makeDirectory($path, $mode = 0775, true, true);
            }
           
            $fileName = md5(microtime()) .'.'. $extension;
            $file->move($this->storagePath, $fileName);
            $newsPromotion->media = $fileName;
            
        }     
        
        $newsPromotion->save();

        return redirect('/superadmin/news-promotions')->with('success',\Lang::get('lang.news-promotions').' '.\Lang::get('lang.created-successfully')); 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $newsRecord = NewsPromotion::findOrFail($id);
        $viewStoragePath =  $this->viewStoragePath;
        $imageFormat =  $this->imageFormat;
        $videoFormat =  $this->videoFormat;

        return view('creator.superadmin.news-promotions.show', compact('newsRecord', 'viewStoragePath', 'imageFormat', 'videoFormat'));
    }

    public function showPDF($id)
    {
        $user = Auth::user();
        $newsPromotion = NewsPromotion::find($id); 
        $filename = "news-promotion-".time().".pdf";
        $path =  $this->storagePath . $newsPromotion->media;
       
        if(file_exists($path)){
            return Response::make(file_get_contents($path), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="'.$filename.'"'
            ]); 
        } else {
            return redirect()->back()->with('error', \Lang::get('lang.file-not-exist'));
        }
         
    }

     /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(NewsPromotion $newsPromotion)
    {
        $jobRoles = JobRole::where('status', 1)->orderBy('name')->get();
        $groups = Group::where('status', 1)->orderBy('name')->get();
        $countries = Country::where('status', 1)->orderBy('name')->get(); 
        $viewStoragePath =  $this->viewStoragePath;
        $imageFormat =  $this->imageFormat;
        $videoFormat =  $this->videoFormat;

       return view('creator.superadmin.news-promotions.edit', compact('newsPromotion', 'jobRoles', 'groups', 'countries', 'viewStoragePath', 'imageFormat', 'videoFormat'));
    }

     /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    
    public function update(Request $request, $id = null)
    {
        try
        {
            request()->validate([
                'id'          => 'required|exists:news_promotion,id',
                'title'       => 'required|min:2',
                'description' => 'required|min:6',
            ]);

            $newsPromotion = NewsPromotion::findOrFail($id);
           
            $country_id = $request->filter_country == -1 ? NULL : $request->filter_country;
            $region_id = $request->region_id == -1 ? NULL : $request->region_id;
            $job_role_id = $request->filter_jobrole == -1 ? NULL : $request->filter_jobrole;
            $group_id = $request->filter_group == -1 ? NULL : $request->filter_group;

            if($request->hasFile('media')) {
                //delete file from storage
                if($newsPromotion->media != null && $newsPromotion->media != '') {
                    $mediaPath = $this->storagePath . $newsPromotion->media;
                    if(file_exists($mediaPath)) {
                        unlink($mediaPath);
                    }
                   
                }    
                $mediaFormats = implode(',', Config::get("constant.SUPPORTED_MEDIA_FROMATS"));
                request()->validate([
                    'media' => 'mimes:' . $mediaFormats . '|max:1048576' // 1048 mb
                ]);
    
                $file = $request->file('media');
                $extension = $file->getClientOriginalExtension();
                $fileName = md5(microtime()) .'.'. $extension;
                $file->move($this->storagePath, $fileName);
                $newsPromotion->media = $fileName;

                    
                $update = [   
                    'title' => $request->title,
                    'description' =>  $request->description,
                    'media' =>  $fileName,
                    'country_id' => $country_id,
                    'region_id' => $region_id,
                    'job_role_id' => $job_role_id,
                    'group_id' => $group_id
                ];
                        
            } else {
               
                if($request->remove_media == 1)
                {
                    if($newsPromotion->media != null && $newsPromotion->media != '') {
                        $mediaPath = $this->storagePath . $newsPromotion->media;
                        if(file_exists($mediaPath)) {
                            unlink($mediaPath);
                        }
                       
                    }
                    $update = [ 
                        'title' => $request->title,
                        'description' =>  $request->description,
                        'country_id' => $country_id,
                        'region_id' => $region_id,
                        'job_role_id' => $job_role_id,
                        'group_id' => $group_id,
                        'media' =>  null
                    ];   
                } else {
                    $update = [ 
                        'title' => $request->title,
                        'description' =>  $request->description,
                        'country_id' => $country_id,
                        'region_id' => $region_id,
                        'job_role_id' => $job_role_id,
                        'group_id' => $group_id
                    ];
                }
            }   
            
            $isUpdated = NewsPromotion::where('id', $request->id)->update($update); 
           
            if($isUpdated == 0)
            {
                return redirect()->back()->with('error', \Lang::get('lang.unable-to-update'));
            }  
    
            return redirect('/superadmin/news-promotions')->with('success',\Lang::get('lang.news-promotions').' '.\Lang::get('lang.updated-successfully'));         
        } catch(Exception $e)
        {
            Log::error($e);
            return redirect()->back()->with('error', \Lang::get('lang.unable-to-update'));
        }
    }

    public function destroy($id)
    {
        $newsPromotion = NewsPromotion::find($id);
        //delete file from storage
        if($newsPromotion->media != null && $newsPromotion->media != '') {
            $mediaPath = $this->storagePath . $newsPromotion->media;
            if(file_exists($mediaPath)) {
                unlink($this->storagePath . $newsPromotion->media);
            }
        }
        $isDeleted = $newsPromotion->delete();

        if($isDeleted == 0)
        {
            return back()->with('error', \Lang::get('lang. unable-to-delete'));  
        } 

        return back()->with('success', \Lang::get('lang.news-delete')); 
    }

}
