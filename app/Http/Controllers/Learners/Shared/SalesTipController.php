<?php

namespace App\Http\Controllers\Learners\Shared;

use Config;
use PDF;
use Auth;
use Response;
use App\Models\{ SalesTip, User, JobRole, Group, Country };
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
        $routeSlug = $this->getRouteSlug();  

        $user = Auth::user();
        $region_id = $user->region_id;
        $country_id = $user->country_id;
        $jobRole_id = $user->job_role_id;
        $group_id = $user->group_id;

        $viewStoragePath =  $this->viewStoragePath;
        $imageFormat =  $this->imageFormat;
        $videoFormat =  $this->videoFormat;

        $salesTips = SalesTip::where('status', 1)
                            ->where(function($query) use ($country_id) {
                                $query->where('country_id', $country_id)->orWhereNull('country_id');
                            })->where(function($query) use ($region_id) {
                                $query->where('region_id', $region_id)->orWhereNull('region_id');
                            })->where(function($query) use ($jobRole_id) {
                                $query->where('job_role_id', $jobRole_id)->orWhereNull('job_role_id'); 
                            })->where(function($query) use ($group_id) {
                                $query->where('group_id', $group_id)->orWhereNull('group_id');   
                            })->orderby('id', 'desc')->paginate($limit); 
       
        return view('learners.shared.sales-tips.index', compact('salesTips', 'routeSlug', 'viewStoragePath', 'imageFormat', 'videoFormat'))
                ->with('index', ($page - 1) * $limit);
    }

    public function show($id) {
        $salesRecord = SalesTip::findOrFail($id);
        $routeSlug = $this->getRouteSlug();   
        $viewStoragePath =  $this->viewStoragePath;
        $imageFormat =  $this->imageFormat;
        $videoFormat =  $this->videoFormat;  

        return view('learners.shared.sales-tips.show', compact('salesRecord', 'routeSlug', 'viewStoragePath', 'imageFormat', 'videoFormat'));
    }

    public function showPDF($id)
    {
        $user = Auth::user();
        $salesTip = SalesTip::find($id); 
        $filename = "sales-tip-".time().".pdf";
        $path =  $this->storagePath. $salesTip->media;
      
        if(file_exists($path)){
            return Response::make(file_get_contents($path), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="'.$filename.'"'
            ]); 
        } else {
            return redirect()->back()->with('error', \Lang::get('lang.file-not-exist'));
        }  
    }

    protected function getRouteSlug() 
    {
        $user = Auth::user();
        return $user->getRoleNames()->first();
    }
}
