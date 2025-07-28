<?php

namespace App\Http\Controllers\Creator\TrainingAdmin;

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

        $page = $request->page ? $request->page : 1;
        $limit = 10;

        $user = Auth::user();
        $regions = $user->region_id;
        $region = explode(',', $regions);
        $regionFiters = Region::where('status', 1)->whereIn('id', $region)->select('id', 'name')->orderBy('name')->get();
        $jobRoles = JobRole::where('status', 1)->orderBy('name')->get();
        $groups = Group::where('status', 1)->orderBy('name')->get();

        $viewStoragePath =  $this->viewStoragePath;
        $imageFormat =  $this->imageFormat;
        $videoFormat =  $this->videoFormat;

        // get multiple superadmin
        $superadmins = User::whereHas(
            'roles', function($q){
                    $q->where('name', 'superadmin');
            }
        )->select('id')->get();
        
        foreach($superadmins as  $superadmin){
            $userIds[] = $superadmin->id;
        }
        array_push($userIds, Auth::user()->id);
       
        $query = SalesTip::buildQuery($request);
        $salesTips = $query->whereIn('created_by', $userIds)
                              ->where(function ($q) use ($regions) {
                                $q->whereIn('region_id', explode(',', $regions))
                                   ->orWhereNull('region_id');
                              })
                              ->orderby('id', 'desc')->paginate($limit);

        return view('creator.admin.sales-tips.index', compact('salesTips', 'viewStoragePath', 'imageFormat', 'videoFormat', 'jobRoles', 'groups', 'regionFiters'))
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
        
       return view('creator.admin.sales-tips.create', compact('jobRoles', 'groups', 'regions', 'user'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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
            $fileName = md5(microtime()) .'.'. $extension;

            // check directory exist or not 
            $path = $this->storagePath;
             if(!is_dir($path)) {
                 File::makeDirectory($path, $mode = 0775, true, true);
            }

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
                SalesTip::create($input);
            }
        } else {
            foreach($request->region_id as $region)
            {
                $input['region_id'] = $region;
                SalesTip::create($input);
            }
        }

        return redirect('/admin/sales-tips')->with('success',\Lang::get('lang.sales-tips').' '.\Lang::get('lang.created-successfully')); 
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

            return view('creator.admin.sales-tips.show',compact('salesRecord', 'viewStoragePath', 'imageFormat', 'videoFormat'));
       
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(SalesTip $salesTip)
    {
        $user = Auth::user();
        $jobRoles = JobRole::where('status', 1)->orderBy('name')->get();
        $groups = Group::where('status', 1)->orderBy('name')->get();
        $viewStoragePath =  $this->viewStoragePath;
        $imageFormat =  $this->imageFormat;
        $videoFormat =  $this->videoFormat;
        $regions = $user->adminRegions();
       
       return view('creator.admin.sales-tips.edit', compact('salesTip', 'jobRoles', 'groups', 'viewStoragePath', 'imageFormat', 'videoFormat', 'regions'));
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
                'region_id' => 'required|exists:regions,id'
            ]);

            $job_role_id = $request->filter_jobrole == -1 ? NULL : $request->filter_jobrole ;
            $group_id = $request->filter_group == -1 ? NULL : $request->filter_group;

            $salesTip = SalesTip::findOrFail($id);
        
            if($request->hasFile('media')) {
                
                $mediaFormats = implode(',', Config::get("constant.SUPPORTED_MEDIA_FROMATS"));
                request()->validate([
                    'media' => 'mimes:' . $mediaFormats . '|max:1048576' // 1048 mb
                ]);
                
                //delete file from storage
                if($salesTip->media != null && $salesTip->media != '') {
                    if(file_exists($this->storagePath . $salesTip->media)) {
                        unlink($this->storagePath . $salesTip->media);
                    }
                }     
                
                        $file = $request->file('media');
                        $extension = $file->getClientOriginalExtension();
                        $fileName = md5(microtime()) .'.'. $extension;
                        $file->move($this->storagePath, $fileName);
                        $salesTip->media = $fileName;
    
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
                    if(file_exists($this->storagePath . $salesTip->media)) {
                        unlink($this->storagePath . $salesTip->media);
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

            $isUpdated = SalesTip::where('id', $request->id)->update($update); 
           
            if($isUpdated == 0)
            {
                return redirect()->back()->with('error', 'unable to update');
            }  
                
            return redirect('/admin/sales-tips')->with('success',\Lang::get('lang.sales-tips').' '.\Lang::get('lang.updated-successfully'));         
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
        $salesTip = SalesTip::find($id);
        //delete file from storage
        if(file_exists($this->storagePath . $salesTip->media)) {
            unlink($this->storagePath . $salesTip->media);
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
        $path = $this->storagePath. $salesTip->media;
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
