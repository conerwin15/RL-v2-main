<?php

namespace App\Http\Controllers\Creator\Superadmin;

use DB;
use Auth;
use Config;
use Log;
use Exception;
use \stdClass;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LearnerExport;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\{ UserLearningPath, Country, User, LearningPath, NewsPromotion, SalesTip, JobRole, Group, Region };

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
        $country =  $request->filter_country;
        $chartPage = 1;
        $learningPathLimit = 7;

        $export_countries     =  empty($request->query('filter_country')) ? 0 : implode(',', $request->query('filter_country'));
        $export_regions       =  empty($request->query('filter_region')) ? 0 : implode(',', $request->query('filter_region'));
        $export_groups        =  empty($request->query('filter_group')) ? 0 : implode(',', $request->query('filter_group'));
        $export_jobRoles      =  empty($request->query('filter_jobRole')) ? 0 : implode(',', $request->query('filter_jobRole'));
        $export_dealers       =  empty($request->query('filter_dealer')) ? 0 : implode(',', $request->query('filter_dealer'));
        $export_learningPaths =  empty($request->query('filter_learningPath')) ? 0 : implode(',', $request->query('filter_learningPath'));

        $completeLPs = UserLearningPath::whereHas('user', function($q) {
            $q->where('status', 1);
        })->sum('progress_percentage');
        
        $totalLPs = UserLearningPath::whereHas('user', function($q) {
            $q->where('status', 1);
        })->count();                                                      
                                        
        $countries = Country::select('id', 'name')->orderBy('name','asc')->where('status', 1)->get();
        $regions = [];

        if($request->query('filter_country') && count($request->query('filter_country')) > 0) {
            $regions = Region::whereIn('country_id',$request->query('filter_country'))->orderBy('name','asc')->get();
        } else {
            $regions = Region::orderBy('name','asc')->get();
        }

        $completedPercentage = $totalLPs == 0 ? $totalLPs : ($completeLPs / ($totalLPs* 100)) * 100;

       
        $totalLeaners = User::buildQueryDashboard($request, ['superadmin', 'admin'])->with('country', 'region', 'jobRole', 'group')->count();
            
        $learners = User::buildQueryDashboard($request, ['superadmin', 'admin'])->with('country', 'region', 'jobRole', 'group')->orderBy('name')->limit(5)->get();
          
        $learningPathCompletion = DB::select("select avg(COALESCE(progress_percentage, 0)) as completed_percentage, ulp.learning_path_id as id, lp.name as name from user_learning_paths ulp join learning_paths lp on 
                                                ulp.learning_path_id = lp.id where lp.status = 1 group by ulp.learning_path_id limit 7" );
        
        $learningPathCompletionCount = DB::select("select count(*) as completed_percentage_count from (select avg(COALESCE(progress_percentage, 0)) as completed_percentage, ulp.learning_path_id as id, lp.name as name from user_learning_paths ulp join learning_paths lp on 
        ulp.learning_path_id = lp.id where lp.status = 1 group by ulp.learning_path_id) as a" );
            
        $count = $learningPathCompletionCount[0]->completed_percentage_count;        
        $learningPaths = LearningPath::select('id', 'name')->orderBy('name','asc')->where('status', 1)->get();
        $selecetedLearningPath = count($learningPaths) > 0 ? $learningPaths[0] : null;

        $countriesCompletionByLP = $selecetedLearningPath ? $this->getFormattedChartData($selecetedLearningPath->id) : [];
          
        $newsPromotions = NewsPromotion::latest()->limit(2)->get();
        $salesTips = SalesTip::latest()->limit(2)->get();
        $jobRoles = JobRole::where('status', 1)->orderBy('name','asc')->get();
        $groups   = Group::where('status', 1)->orderBy('name','asc')->get(); 
        $dealers = User::role('dealer')->orderBy('name','asc')->where('status', 1)->get();

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
          
        return view('creator.superadmin.home', compact('countries', 'regions', 'learners', 'totalLeaners', 
        'completedPercentage', 'learningPathCompletion', 'learningPaths', 'countriesCompletionByLP',
         'newsPromotions', 'salesTips', 'imageFormat', 'videoFormat', 'newsViewStoragePath', 'salesViewStoragePath', 
         'chartPage', 'count', 'learningPathLimit', 'jobRoleWithNa', 'groupWithNa', 'dealers', 'export_countries', 'export_regions', 'export_groups', 'export_jobRoles', 'export_dealers', 'export_learningPaths'));
    }


    public function exportLearners(Request $request) 
    {
        return Excel::download(new LearnerExport($request->country, $request->region, $request->dealer, $request->group, $request->jobRole, $request->learningPath), 'learners.xls');
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


    private function getFormattedChartData($learningPathId) {
        $coursesCompletionByLP = DB::select("select avg(COALESCE(progress_percentage, 0)) as completed_percentage, u.country_id as country_id from user_learning_paths ulp join users u on ulp.user_id = u.id where u.status = 1 and ulp.learning_path_id = " . $learningPathId  . " group by u.country_id");

        $countriesCompletionByLP = [];
        $countries = Country::select('id', 'name')->where('status', 1)->get();
        // itreate over country
        foreach($countries as $country) {
            $countryLPData = new StdClass();
            $countryLPData->name = $country->name;
            $countryLPData->id = $country->id;
            $countryLPData->data = [];

            // itreate over course completion
            foreach($coursesCompletionByLP as $courseCompletionByLP) {
                
                if($courseCompletionByLP->country_id == $country->id) {
                    array_push($countryLPData->data, ["completed_precentage" => $courseCompletionByLP->completed_percentage]);
                    break;
                }
            }

            array_push($countriesCompletionByLP, $countryLPData); 
           
        }
        
        $countriesCompletionByLPArr = [];
        $i = 0;
        foreach ($countriesCompletionByLP as $courseData){
            $countriesCompletionByLPArr[$i]['country'] = $courseData->name;
            if(empty($courseData->data)) {
                $countriesCompletionByLPArr[$i]['completed_precentage'] = 0;
            } else {
                $countriesCompletionByLPArr[$i]['completed_precentage'] = $courseData->data[0]['completed_precentage'];
            }
            $i++;
        }
       
        return $countriesCompletionByLPArr;
    }


    public function prevNextLearningPathCompletion ($page, $offset) {
        try{
            $limit = (int)$page*$offset - (int)$offset;
            $learningPathCompletion = DB::select("select avg(COALESCE(progress_percentage, 0)) as completed_percentage, ulp.learning_path_id as id, lp.name as name from user_learning_paths ulp join learning_paths lp on 
                                                    ulp.learning_path_id = lp.id where lp.status = 1 group by ulp.learning_path_id limit " . $limit. "," . $offset );
 
            return $this->sendResponse($learningPathCompletion, "chart data");   
        } catch(Exception $ex) {
            Log::error($ex);
            return $this->sendError($ex, \Lang::get('lang.invalid-request'));
        }
    }

}
 
