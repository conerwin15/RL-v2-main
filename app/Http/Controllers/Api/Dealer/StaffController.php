<?php

namespace App\Http\Controllers\Api\Dealer;

use Auth;
use Config;
use DB;
use Log;
use Exception;
use App\Models\{ Group, JobRole, User, UserLearningPath };
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;

class StaffController extends BaseController
{
    /**
     * Display a listing of the staff filters.
     *
     * @return \Illuminate\Http\Response
     */
    public function filtersData()
    {   
        try {

            $response['job_roles'] = JobRole::select('id', 'name')->where('status', 1)->orderBy('name')->get();   
            $response['groups']    = Group::select('id', 'name')->where('status', 1)->orderBy('name')->get();
            return $this->sendResponse($response, \Lang::get('lang.staff-filter-list')); 

        } catch (Exception $e) {
            Log::error($e);
            return $this->sendError($e, \Lang::get('lang.invalid-request'));
        }    
    } 

    /**
     * Display a listing of the staff.
     *
     * @return \Illuminate\Http\Response
     */

    public function staffList(Request $request)
    {
        try{

            $page = $request->page ? $request->page : 1;
            $limit = 10;
          
            $query = User::buildQuery($request, ['superadmin', 'admin', 'dealer'])
                            ->with('createdBy', function($q) {
                                $q->select('id', 'name');
                            })->with('country', function($q) {
                                $q->select('id', 'name');
                            })->with('region', function($q){
                                $q->select('id', 'name');
                            })->with('group', function($q){
                                $q->select('id', 'name');
                            })->with('jobRole', function($q){
                                $q->select('id', 'name');
                            });        
            $users = $query->where('dealer_id', Auth::user()->id)->paginate($limit);
            
            $response['staff'] = $users->toArray();
            $response['user_image_path'] = asset('storage' . Config::get('constant.PROFILE_PICTURES'));

            return $this->sendResponse($response, \Lang::get('lang.staff-list')); 

        } catch (Exception $e) {
            Log::error($e);
            return $this->sendError($e, \Lang::get('lang.invalid-request'));
        }       
    }       

    public function stats(Request $request) {
        try {

            $totalLeaners = User::buildQuery($request, ['superadmin', 'admin', 'dealer'])->where('dealer_id', Auth::user()->id)->count();
            $response['totalLearners'] = $totalLeaners;

            

            $data = DB::select("select CAST(sum(completed) / sum(total) * 100 AS DECIMAL(8,0)) as percentage from 
                (select count(*) as total, (select count(*) from user_learning_paths where user_id = u.id and progress_percentage = 100) 
                as completed from user_learning_paths ulp join users u on ulp.user_id = u.id where u.dealer_id = " . Auth::user()->id . " group by user_id) as p");

            $response['completion_percentage'] = $data != null ? $data[0]->percentage : 0;
            return $this->sendResponse($response, "Home Data"); 

        } catch (Exception $e) {
            Log::error($e);
            return $this->sendError($e, \Lang::get('lang.invalid-request'));
        }  
        
    }

    /**
     * Display learning path.
     *
     * @return \Illuminate\Http\Response
     */
    public function viewLearningPath(Request $request)
    {
        try{
             
            $limit = 10;
            $query = UserLearningPath::where('user_id', $request->staffId)->with('badge')
                                 ->with('learningPath', function ($q){
                                    $q->select('id', 'name', 'status', 'featured_image');
                                 })->with('assignBy', function ($q){
                                    $q->select('id', 'name');
                                 });
            $userLearningPaths = $query->paginate($limit);  
            
            $response['learning_paths'] = $userLearningPaths->toArray();
            $response['image_path'] = asset('storage' . Config::get('constant.LEARNING_PATH_STORAGE'));
            $response['badge_image_path'] = asset('assets/images/');
            
            return $this->sendResponse($response, \Lang::get('lang.staff-learning-path'));
        } catch (Exception $e) {
            Log::error($e);
            return $this->sendError($e, \Lang::get('lang.invalid-request'));
        }  
    }
}
