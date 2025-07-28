<?php

namespace App\Http\Controllers\Creator\superadmin;

use Config;
use PDF;
use Auth;
use File;
use Exception;
use Response;
use Storage;
use App\Models\{ SalesTip, User, JobRole, Group, Country, Region };
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;


class SalesTipController extends Controller
{

    /**
     * Create a new SalesTipController instance.
     *
     * @return void
     */
    public $storagePath;
    public $viewStoragePath;
    public $imageFormat;
    public $videoFormat;

    public function __construct()
    {
       $this->storagePath = storage_path('app/public' . Config::get('constant.SALESTIPS_STORAGE_PATH'));
       $this->viewStoragePath =  Config::get('constant.SALESTIPS_STORAGE_PATH');
       $this->imageFormat = Config::get('constant.SUPPORTED_IMAGE_FORAMTS'); 
       $this->videoFormat = Config::get('constant.SUPPORTED_VIDEO_FORAMTS'); 
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        $regions = Region::where('status', 1)->orderBy('name')->get();
        $countries = Country::where('status', 1)->orderBy('name')->get(); 
        $jobRoles = JobRole::where('status', 1)->orderBy('name')->get();
        $groups = Group::where('status', 1)->orderBy('name')->get();

        $page = $request->page ? $request->page : 1;
        $limit = 10;
  
        $viewStoragePath =  $this->viewStoragePath;
        $imageFormat =  $this->imageFormat;
        $videoFormat =  $this->videoFormat;

        $query = SalesTip::buildQuery($request);
   
        $salesTips = $query->orderBy('created_at', 'DESC')->paginate($limit);

        return view('creator.superadmin.sales-tips.index', compact('salesTips', 'viewStoragePath', 'imageFormat', 'videoFormat', 'countries', 'regions', 'jobRoles', 'groups'))
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

       return view('creator.superadmin.sales-tips.create', compact('jobRoles', 'groups', 'countries'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate([
            'title' => 'required|min:2',
            'description' => 'required|min:6',
          ]); 

        $user = Auth::user();  
        $salesTip = new SalesTip;
        $salesTip->title = $request->title;
        $salesTip->description = $request->description;
        $salesTip->country_id = $request->filter_country == -1 ? NULL : $request->filter_country ;
        $salesTip->region_id = $request->region_id == -1 ? NULL : $request->region_id;
        $salesTip->job_role_id = $request->filter_jobrole == -1 ? NULL : $request->filter_jobrole ;
        $salesTip->group_id = $request->filter_group == -1 ? NULL : $request->filter_group;
        $salesTip->created_by = $user->id;
        $salesTip->updated_by = $user->id;  

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
            $salesTip->media = $fileName;
        }  

        $salesTip->save();

        return redirect('/superadmin/sales-tips')->with('success',\Lang::get('lang.sales-tips').' '.\Lang::get('lang.created-successfully')); 
    }

      /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $salesRecord = SalesTip::find($id);
        $viewStoragePath =  $this->viewStoragePath;
        $imageFormat =  $this->imageFormat;
        $videoFormat =  $this->videoFormat;

        return view('creator.superadmin.sales-tips.show',compact('salesRecord', 'viewStoragePath', 'imageFormat', 'videoFormat'));
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(SalesTip $salesTip)
    {
        $jobRoles = JobRole::where('status', 1)->orderBy('name')->get();
        $groups = Group::where('status', 1)->orderBy('name')->get();
        $countries = Country::where('status', 1)->orderBy('name')->get(); 
        $viewStoragePath =  $this->viewStoragePath;
        $imageFormat =  $this->imageFormat;
        $videoFormat =  $this->videoFormat;

       return view('creator.superadmin.sales-tips.edit', compact('salesTip', 'jobRoles', 'groups', 'countries', 'viewStoragePath', 'imageFormat', 'videoFormat'));
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
                'id'          => 'required|exists:sales_tips,id',
                'remove_media' => 'required',
                'title'       => 'required|min:2',
                'description' => 'required|min:6',
            ]);

            $salesTip = SalesTip::findOrFail($id);
            
            $country_id = $request->filter_country == -1 ? NULL : $request->filter_country ;
            $region_id = $request->region_id == -1 ? NULL : $request->region_id;
            $job_role_id = $request->filter_jobrole == -1 ? NULL : $request->filter_jobrole ;
            $group_id = $request->filter_group == -1 ? NULL : $request->filter_group;

            if($request->hasFile('media')) {
                //delete file from storage
                if($salesTip->media != null && $salesTip->media != '') {
                    if(file_exists($this->storagePath . $salesTip->media)) {
                        unlink($this->storagePath . $salesTip->media);
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
                $salesTip->media = $fileName;

                    
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

                    if($salesTip->media != null && $salesTip->media != '') {
                        if(file_exists($this->storagePath . $salesTip->media)) {
                            unlink($this->storagePath . $salesTip->media);
                        }
                       
                    }   

                    $update = [ 
                        'title' => $request->title,
                        'description' =>  $request->description,
                        'media' =>  null,
                        'country_id' => $country_id,
                        'region_id' => $region_id,
                        'job_role_id' => $job_role_id,
                        'group_id' => $group_id
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

            $isUpdated = SalesTip::where('id', $request->id)->update($update); 
           
            if($isUpdated == 0)
            {
                return redirect()->back()->with('error', \Lang::get('lang.unable-to-update'));
            }  
               
            return redirect('/superadmin/sales-tips')->with('success',\Lang::get('lang.sales-tips').' '.\Lang::get('lang.updated-successfully'));         
        } catch(Exception $e)
        {
            Log::error($e);
            return redirect()->back()->with('error', \Lang::get('lang.unable-to-update'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $salesTip = SalesTip::findOrFail($id);
        //delete file from storage

        if($salesTip->media != null && $salesTip->media != '') {
            $mediaPath = $this->storagePath . $salesTip->media;
            if(file_exists($mediaPath)) {
                unlink($this->storagePath . $salesTip->media);
            }
        }
        
        $isDeleted = $salesTip->delete();
        
        if($isDeleted == 0)
        {
            return back()->with('error', \Lang::get('lang. unable-to-delete'));  
        } 

        return back()->with('success', \Lang::get('lang.sales-delete'));
    }

    public function showPDF($id)
    {
        $user = Auth::user();
        $salesTip = SalesTip::find($id); 
        $filename = "sales-tip-".time().".pdf";
        $path = $this->storagePath . $salesTip->media;
       
        if(file_exists($path)){
            return Response::make(file_get_contents($path), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="'.$filename.'"'
            ]); 
        } else {
            return redirect()->back()->with('error', \Lang::get('lang.file-not-exist'));
        } 
    }

}
