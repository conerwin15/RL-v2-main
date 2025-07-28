<?php

namespace App\Http\Controllers\Creator\TrainingAdmin;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Auth;
use DB;
use Config;
use Log;
use Exception;
use \stdClass;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LearnerExport;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\{ UserLearningPath, Country, Region, User, LearningPath, NewsPromotion, SalesTip, Role, JobRole, Group };


class HomeController extends BaseController
{

    public function __construct()
    {
       $this->newsViewStoragePath =  Config::get('constant.NEWS_STORAGE_PATH');
       $this->salesViewStoragePath =  Config::get('constant.SALESTIPS_STORAGE_PATH');
       $this->imageFormat = Config::get('constant.SUPPORTED_IMAGE_FORAMTS'); 
       $this->videoFormat = Config::get('constant.SUPPORTED_VIDEO_FORAMTS'); 
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index(Request $request)
    {   
        $imageFormat =  $this->imageFormat;
        $videoFormat =  $this->videoFormat;
        $newsViewStoragePath =  $this->newsViewStoragePath;
        $salesViewStoragePath =  $this->salesViewStoragePath;

        $page = $request->page ? $request->page : 1;
        $limit = 10;
        $chartPage = 1;
        $learningPathLimit = 7;
        $regionIds = explode(',', Auth::user()->region_id);

        $export_countries     =  Auth::user()->country_id;
        $export_regions       =  empty($request->query('filter_region')) ? 0 : implode(',', $request->query('filter_region'));
        $export_groups        =  empty($request->query('filter_group')) ? 0 : implode(',', $request->query('filter_group'));
        $export_jobRoles      =  empty($request->query('filter_jobRole')) ? 0 : implode(',', $request->query('filter_jobRole'));
        $export_dealers       =  empty($request->query('filter_dealer')) ? 0 : implode(',', $request->query('filter_dealer'));
        $export_learningPaths =  empty($request->query('filter_learningPath')) ? 0 : implode(',', $request->query('filter_learningPath'));

        $admin = Auth::user();
        $adminRegions = $admin->adminRegions();
    
        $jobRoles = JobRole::where('status', 1)->orderBy('name','asc')->get();
        $groups   = Group::where('status', 1)->orderBy('name','asc')->get();
        $dealers = User::whereHas (
                    'roles', function($q){
                        $q->where('name', 'dealer');
                    })->whereIn('region_id', $regionIds)->where('status', 1)->get();

        $groupWithNa = [];

        $object = new stdClass();
        $object->id = -1;
        $object->name = 'N/A';
        array_push($groupWithNa, $object);

        foreach ($groups as $group) {
            $object = new stdClass();
            $object->id = $group->id;
            $object->name = $group->name;
            array_push($groupWithNa, $object);
        }


        $jobRoleWithNa = [];

        $object = new stdClass();
        $object->id = -1;
        $object->name = 'N/A';
        array_push($jobRoleWithNa, $object);

        foreach ($jobRoles as $jobRole) {
            $object = new stdClass();
            $object->id = $jobRole->id;
            $object->name = $jobRole->name;
            array_push($jobRoleWithNa, $object);
        }
        
    
        $completeLPs = UserLearningPath::whereHas('user', function($q) use ($regionIds) {
                            $q->whereIn('region_id', $regionIds)->where('status', 1);
                        })->sum('progress_percentage');
       
        $totalLPs = UserLearningPath::whereHas('user', function($q) use ($regionIds) {
                        $q->whereIn('region_id', $regionIds)->where('status', 1);
                    })->count();                                                           
       
        $completedPercentage = $totalLPs > 0 ? (($completeLPs / ($totalLPs * 100)) * 100) : 0;
       
        if(count($request->all()) == 0)
        {
            $totalLeaners = User::buildQuery($request, ['superadmin', 'admin'])->whereIn('region_id', $regionIds)->with('country','region', 'jobRole', 'group')->count();
            $learners = User::buildQuery($request, ['superadmin', 'admin'])->whereIn('region_id', $regionIds)->with('country', 'region', 'jobRole', 'group')->orderBy('name')->limit(5)->get();
        } else {
            $totalLeaners = User::buildQueryDashboard($request, ['superadmin', 'admin'])->with('region', 'jobRole', 'group')->count();
            $learners = User::buildQueryDashboard($request, ['superadmin', 'admin'])->with('region', 'jobRole', 'group')->limit(5)->get();
        }    
        
        $eligibleRoles = Role::whereIn('name', ['staff', 'dealer'])->pluck('id')->toArray();

        $superadminIds = User::whereHas (
            'roles', function($q){
                $q->where('name', 'superadmin');
            })->pluck('id')->toArray();

        // Get all learning paths created by superadmin or by self
        $learningPathCompletion = DB::select("select avg(COALESCE(progress_percentage, 0)) as completed_percentage, ulp.learning_path_id as id, lp.name as name from user_learning_paths ulp 
                                                join learning_paths lp on ulp.learning_path_id = lp.id 
                                                join users u on ulp.user_id = u.id where u.region_id in  (" . Auth::user()->region_id . ") and lp.status = 1 group by ulp.learning_path_id limit 7");
           

        $learningPathCompletionCount = DB::select("select count(*) as completed_percentage_count from (select avg(COALESCE(progress_percentage, 0)) as completed_percentage, ulp.learning_path_id as id, lp.name as name from user_learning_paths ulp 
        join learning_paths lp on ulp.learning_path_id = lp.id 
        join users u on ulp.user_id = u.id where u.region_id in  (" . Auth::user()->region_id . ") and lp.status = 1 group by ulp.learning_path_id) as a" );
            
        $count = $learningPathCompletionCount[0]->completed_percentage_count;  

        $learningPaths = DB::select("select lp.id, lp.name, count(ulp.id) from user_learning_paths ulp join 
                        users u on ulp.user_id = u.id  join learning_paths lp on ulp.learning_path_id = lp.id where u.region_id in (" . Auth::user()->region_id . ") group by lp.id order by lp.name asc");
                        
        $selecetedLearningPath = count($learningPaths) > 0 ? $learningPaths[0] : null;

        $dealerData = [];
        if($selecetedLearningPath) {
            $dealerData = $this->getFormattedChartData($selecetedLearningPath->id);
        }

        $regions = Auth::user()->region_id;
        $newsPromotions = NewsPromotion::where(function ($q) use ($regions) {
                                $q->whereIn('region_id', explode(',', $regions))
                                   ->orWhereNull('region_id');
                              })
                              ->orderby('id', 'desc')->latest()->limit(2)->get();


        $salesTips = SalesTip::where(function ($q) use ($regions) {
                        $q->whereIn('region_id', explode(',', $regions))
                        ->orWhereNull('region_id');
                    })
                    ->orderby('id', 'desc')->latest()->limit(2)->get();

        $dealers = User::whereIn("region_id", explode(',', $regions))->orderBy('name','asc')->whereHas("roles", function($query)
        { 
           $query->where("name", 'dealer'); 
        })->get();

        $staffData = [];
        if($selecetedLearningPath) {
            $staffData = $this->getFormattedChartDataByDealer($selecetedLearningPath->id, $dealers[0]->id);
        }
        
        return view('creator.admin.home', compact('dealers', 'learners', 'totalLeaners', 'completedPercentage', 'learningPathCompletion', 'learningPaths', 'dealerData', 'staffData', 'newsPromotions', 'salesTips', 'imageFormat', 'videoFormat', 'newsViewStoragePath', 'salesViewStoragePath', 'chartPage', 'count', 'learningPathLimit', 'adminRegions', 'jobRoles', 'groups', 'jobRoleWithNa', 'groupWithNa', 'dealers', 'export_countries', 'export_regions', 'export_groups', 'export_jobRoles', 'export_dealers', 'export_learningPaths'));
    }

    public function exportLearners(Request $request) 
    {
        return Excel::download(new LearnerExport(Auth::user()->country_id, $request->region, $request->dealer, $request->group, $request->jobRole, $request->learningPath), 'learners.xls');
    }

    public function getChartDataForStaff($id, $dealer) {
        try {
            $data = $this->getFormattedChartDataByDealer($id, $dealer);
            return $this->sendResponse($data, "Fetched Successfully");  
        } catch(Exception $e) {
            Log::error($e);
            return $this->sendError($e, 'Invalid Request');
        }
    }

    public function getChartData($id) {        
        try {
            $data = $this->getFormattedChartData($id);
            return $this->sendResponse($data, "Fetched Successfully");  
        } catch(Exception $ex) {
            Log::error($ex);
            return $this->sendError($ex, 'Invalid Request');
        }
    }

    private function getFormattedChartDataByDealer($learningPathId, $dealerId) {

        $eligibleRoles = Role::where('name', ['staff'])->pluck('id')->toArray();

        $coursesCompletionByLP = DB::select("select ulp.progress_percentage as completed_percentage, u.id, u.name from user_learning_paths ulp join users u on ulp.user_id = u.id  
                                            join model_has_roles mhr on u.id = mhr.model_id join roles role on mhr.role_id = role.id
                                            where role.id in ( " . implode(",", $eligibleRoles ) . " ) and u.dealer_id = " . $dealerId . " and learning_path_id = " . $learningPathId);

        return $coursesCompletionByLP;
    }


    private function getFormattedChartData($learningPathId) {

        $eligibleRoles = Role::where('name', ['dealer'])->pluck('id')->toArray();

        $coursesCompletionByLP = DB::select("select ulp.progress_percentage as completed_percentage, u.id, u.name from user_learning_paths ulp join users u on ulp.user_id = u.id  
                                            join model_has_roles mhr on u.id = mhr.model_id join roles role on mhr.role_id = role.id
                                            where role.id in ( " . implode(",", $eligibleRoles ) . " ) and learning_path_id = " . $learningPathId);
        return $coursesCompletionByLP;
    }

    public function prevNextLearningPathCompletion ($page) {
        try {
            $limit = 7;
            $offset = ( $page - 1 ) * $limit;
            $eligibleRoles = Role::whereIn('name', ['staff', 'dealer'])->pluck('id')->toArray();    
            
            $learningPathCompletion = DB::select("select avg(COALESCE(progress_percentage, 0)) as completed_percentage, ulp.learning_path_id as id, lp.name as name from user_learning_paths ulp 
                                                    join learning_paths lp on ulp.learning_path_id = lp.id 
                                                    join users u on ulp.user_id = u.id where u.region_id in  (" . Auth::user()->region_id . ") and lp.status = 1 group by ulp.learning_path_id limit " . $offset . ", " . $limit);
            
            return $this->sendResponse($learningPathCompletion, "chart data");   
        } catch(Exception $ex) {
            Log::error($ex);
            return $this->sendError($ex, \Lang::get('lang.invalid-request'));
        }
    }
}
