<?php

namespace App\Http\Controllers\Learners\Dealer;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Http\Controllers\Api\BaseController as BaseController;
use DB;
use Auth;
use Config;
use \stdClass;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LearnerExport;
use App\Models\{ UserLearningPath, Country, User, LearningPath, NewsPromotion, SalesTip };

class HomeController extends BaseController
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index(Request $request)
    {
        $page = $request->page ? $request->page : 1;
        $limit = 10;
        $chartPage = 1;
        $country =  $request->filter_country;
       
        $completeLPs = UserLearningPath::whereHas('user', function($q) {
                                            $q->where('dealer_id',  Auth::user()->id)->where('status', 1);
                                        })->sum('progress_percentage');                                                                     
        // where Not null added somehow query was not working can be removed if we can find a solution without this
        $totalLPs = UserLearningPath::whereNotNull('id')->whereHas('user', function($q) {
                        $q->where('dealer_id',  Auth::user()->id)->where('status', 1);
                    })->count();                         
                                                      
        $completedPercentage = $totalLPs == 0 ? 0 : ($completeLPs / $totalLPs);
       
        $totalLeaners = User::buildQuery($request, ['superadmin', 'admin', 'dealer'])->where('dealer_id', Auth::user()->id)->count();
            
        $learners = User::buildQuery($request, ['superadmin', 'admin'])->with('country', 'region', 'jobRole', 'group')->where('dealer_id', Auth::user()->id)->orderBy('name')->limit(5)->get();
          
        $learningPathCompletion = DB::select("select avg(COALESCE(progress_percentage, 0)) as completed_percentage, ulp.learning_path_id as id, lp.name as name from user_learning_paths ulp join learning_paths lp on 
                                    ulp.learning_path_id = lp.id join users u on ulp.user_id = u.id 
                                    and u.dealer_id = " . Auth::user()->id . " where u.status = 1  and lp.status = 1 group by ulp.learning_path_id limit 7");
        
        $learningPathCompletionCount = DB::select("select count(*) as completed_percentage_count from (select avg(COALESCE(progress_percentage, 0))
                                                    as completed_percentage, ulp.learning_path_id as id, lp.name as name from user_learning_paths ulp join learning_paths lp on 
                                                    ulp.learning_path_id = lp.id join users u on ulp.user_id = u.id 
                                                    and u.dealer_id = " . Auth::user()->id . " where lp.status = 1 group by ulp.learning_path_id) as a" );  
                                                                
        $count =  $learningPathCompletionCount[0]->completed_percentage_count;
                
        return view('learners.dealer.home', compact('learners', 'totalLeaners', 'completedPercentage', 'learningPathCompletion', 'count', 'chartPage'));
    }


    public function exportLearners(Request $request) 
    {
        return Excel::download(new LearnerExport(null, null), 'learners.xls');
    }

    public function prevNextLearningPathCompletion ($page, $offset) {
        try{
            $limit = (int)$page*$offset - (int)$offset;
           
            $learningPathCompletion =DB::select("select avg(COALESCE(progress_percentage, 0)) as completed_percentage, ulp.learning_path_id as id, lp.name as name from user_learning_paths ulp join learning_paths lp on 
                                                ulp.learning_path_id = lp.id join users u on ulp.user_id = u.id 
                                                and u.dealer_id = " . Auth::user()->id . " where u.status = 1  and lp.status = 1 group by ulp.learning_path_id limit " . $limit. "," . $offset );
            
            return $this->sendResponse($learningPathCompletion, "chart data");   
        } catch(Exception $ex) {
            Log::error($ex);
            return $this->sendError($ex, \Lang::get('lang.invalid-request'));
        }
    }
}
