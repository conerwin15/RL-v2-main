<?php

namespace App\Http\Controllers\Creator\TrainingAdmin;

use Config;
use PDF;
use Auth;
use File;
use Exception;
use Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
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
        $page = $request->page ? $request->page : 1;
        $user = Auth::user();
        $regions = $user->region_id;
        $region = explode(',', $regions);
        $regionFiters = Region::where('status', 1)->whereIn('id', $region)->select('id', 'name')->orderBy('name')->get();
        $jobRoles = JobRole::where('status', 1)->orderBy('name')->get();
        $groups = Group::where('status', 1)->orderBy('name')->get();
       
        $limit = 10;
       
        $viewStoragePath =  $this->viewStoragePath;
        $imageFormat =  $this->imageFormat;
        $videoFormat =  $this->videoFormat;

        $userIds = [];

        $superadmins = User::whereHas(
                'roles', function($q){
                        $q->where('name', 'superadmin');
                }
        )->select('id')->get();
        
        foreach($superadmins as  $superadmin){
            $userIds[] = $superadmin->id;
        }
        array_push($userIds, Auth::user()->id);   

        $query = NewsPromotion::buildQuery($request);
      
        $newsPromotions = $query->whereIn('created_by', $userIds)
                              ->where(function ($q) use ($regions) {
                                $q->whereIn('region_id', explode(',', $regions))
                                   ->orWhereNull('region_id');
                              })
                              ->orderby('id', 'desc')->paginate($limit);
       
        return view('creator.admin.news-promotions.index', compact('newsPromotions', 'viewStoragePath', 'imageFormat', 'videoFormat', 'jobRoles', 'groups', 'regionFiters'))
                ->with('index', ($page - 1) * $limit);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();
        $jobRoles = JobRole::where('status', 1)->orderBy('name')->get();
        $groups = Group::where('status', 1)->orderBy('name')->get();
        $regions = $user->adminRegions();
        
        return view('creator.admin.news-promotions.create', compact('jobRoles', 'groups', 'regions', 'user'));
    }

    public function store(Request $request)
    {   
        $user = Auth::user(); 
        $fileName = '';
        request()->validate([
            'title' => 'required|min:2',
            'description' => 'required|min:6',
            'region_id' => 'required'
          ]); 
  
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
            
        }   

        $input = $request->all();
        $input['country_id'] = $user->country_id;
        $input['job_role_id'] = $request->filter_jobrole == -1 ? NULL : $request->filter_jobrole ;
        $input['group_id'] = $request->filter_group == -1 ? NULL : $request->filter_group ;
        $input['created_by'] = $user->id;
        $input['updated_by'] = $user->id;
        $input['media'] = $fileName;

        // for all regions
        if($request->region_id[0] == -1){
            $regions = $user->adminRegions();
            foreach($regions as $region)
            {
                $input['region_id'] = $region->id;
                NewsPromotion::create($input);
            }
        } else {
            foreach($request->region_id as $region)
            {
                $input['region_id'] = $region;
                NewsPromotion::create($input);
            }
        }   
               
        return redirect('/admin/news-promotions')->with('success',\Lang::get('lang.news-promotions').' '.\Lang::get('lang.created-successfully')); 
    }


    public function showPDF($id)
    {
        $user = Auth::user();
        $newsPromotion = NewsPromotion::find($id); 
        $filename = "news-promotion-".time().".pdf";
        $path = $this->storagePath . $newsPromotion->media;
       
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
        $user = Auth::user();
        $jobRoles = JobRole::where('status', 1)->orderBy('name')->get();
        $groups = Group::where('status', 1)->orderBy('name')->get();
        $viewStoragePath =  $this->viewStoragePath;
        $imageFormat =  $this->imageFormat;
        $videoFormat =  $this->videoFormat;
        $regions = $user->adminRegions();
       
       return view('creator.admin.news-promotions.edit', compact('newsPromotion', 'jobRoles', 'groups', 'viewStoragePath', 'imageFormat', 'videoFormat', 'regions'));
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
                'remove_media' => 'required',
                'title'       => 'required|min:2',
                'description' => 'required|min:6',
                'region_id' => 'required|exists:regions,id'
            ]);

            $newsPromotion = NewsPromotion::findOrFail($id);

            $job_role_id = $request->filter_jobrole == -1 ? NULL : $request->filter_jobrole ;
            $group_id = $request->filter_group == -1 ? NULL : $request->filter_group;
           
            if($request->hasFile('media')) {
                //delete file from storage
                if($newsPromotion->media != null) {
                    if(file_exists($this->storagePath . $newsPromotion->media)) {
                        unlink($this->storagePath . $newsPromotion->media);
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
    
                          
                          $update = [ 'title' => $request->title,
                                      'description' =>  $request->description,
                                      'media' =>  $fileName,
                                      'job_role_id' => $job_role_id,
                                      'group_id' => $group_id,
                                      'region_id' => $request->region_id
                          ];
                        
            } else {
               
                if($request->remove_media == 1)
                {
                    if(file_exists($this->storagePath . $newsPromotion->media)) {
                        unlink($this->storagePath . $newsPromotion->media); 
                    } 
                    $update = [ 'title' => $request->title,
                                'description' =>  $request->description,
                                'media' =>  null,
                                'job_role_id' => $job_role_id,
                                'group_id' => $group_id,
                                'region_id' => $request->region_id
                          ];   
                } else {
                    $update = [ 'title' => $request->title,
                                'description' =>  $request->description,
                                'job_role_id' => $job_role_id,
                                'group_id' => $group_id,
                                'region_id' => $request->region_id
                    ];
                }
            }   
            
            $isUpdated = NewsPromotion::where('id', $request->id)->update($update); 
           
            if($isUpdated == 0)
            {
                return redirect()->back()->with('error', 'unable to update');
            }  
               
            return redirect('/admin/news-promotions')->with('success',\Lang::get('lang.news-promotions').' '.\Lang::get('lang.updated-successfully'));         
        } catch(Exception $e)
        {
            Log::error($e);
            return redirect()->back()->with('error', 'unable to update');
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

    public function show($id) {
        $newsRecord = NewsPromotion::findOrFail($id);
        $viewStoragePath =  $this->viewStoragePath;
        $imageFormat =  $this->imageFormat;
        $videoFormat =  $this->videoFormat;

        return view('creator.admin.news-promotions.show', compact('newsRecord', 'viewStoragePath', 'imageFormat', 'videoFormat'));
    }
}
