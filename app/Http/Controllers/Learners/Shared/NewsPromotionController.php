<?php

namespace App\Http\Controllers\Learners\Shared;

use Config;
use PDF;
use Auth;
use Response;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{ NewsPromotion, User, JobRole, Group, Country };

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
        $limit = 10;
        $user = Auth::user();
        $region_id = $user->region_id;
        $country_id = $user->country_id;
        $jobRole_id = $user->job_role_id;
        $group_id = $user->group_id;
        
        $routeSlug = $this->getRouteSlug(); 
        $viewStoragePath =  $this->viewStoragePath;
        $imageFormat =  $this->imageFormat;
        $videoFormat =  $this->videoFormat;

        $newsPromotions = NewsPromotion::where('status', 1)
                            ->where(function($query) use ($country_id) {
                                $query->where('country_id', $country_id)->orWhereNull('country_id');
                            })->where(function($query) use ($region_id) {
                                $query->where('region_id', $region_id)->orWhereNull('region_id');
                            })->where(function($query) use ($jobRole_id) {
                                $query->where('job_role_id', $jobRole_id)->orWhereNull('job_role_id'); 
                            })->where(function($query) use ($group_id) {
                                $query->where('group_id', $group_id)->orWhereNull('group_id');   
                            })->orderby('id', 'desc')->paginate($limit); 
                                  
        return view('learners.shared.news-promotions.index', compact('newsPromotions', 'routeSlug', 'viewStoragePath', 'imageFormat', 'videoFormat'))
                ->with('index', ($page - 1) * $limit);
    }

    public function show($id) {
        $newsRecord = NewsPromotion::findOrFail($id);
        $routeSlug = $this->getRouteSlug();  
        $viewStoragePath =  $this->viewStoragePath;
        $imageFormat =  $this->imageFormat;
        $videoFormat =  $this->videoFormat;  
       
        return view('learners.shared.news-promotions.show', compact('newsRecord', 'routeSlug', 'viewStoragePath', 'imageFormat', 'videoFormat'));
    }

    protected function getRouteSlug() 
    {
        $user = Auth::user();
        return $user->getRoleNames()->first();
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
}
