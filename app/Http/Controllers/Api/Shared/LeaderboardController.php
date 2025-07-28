<?php

namespace App\Http\Controllers\Api\Shared;

use Auth;
use DB;
use Config;
use Log;
use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\{ User, FeaturedTrainee };

class LeaderboardController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
                $page = $request->page ? $request->page : 1;
                $search  = trim($request->searchByName);

                $loggedUser = Auth::user();

                $users = DB::table('user_point_history')
                                ->select('users.name as name', 'countries.name as country', 'users.image as image', DB::raw('user_id, SUM(points) as totalPoints'))
                                ->leftJoin('users', 'user_id', '=', 'users.id')
                                ->leftJoin('countries', 'users.country_id', '=', 'countries.id')
                                ->orderBy('totalPoints', 'DESC')
                ->where('users.name', 'LIKE', '%'.$search.'%')
                                ->groupBy('user_id')->get();

                // get emp of the month
                $month = Carbon::now()->month - 1;
                $EmpOfTheMonth = FeaturedTrainee::select('points')->where('month',  $month)
                                                ->where('year',  Carbon::now()->year)->with('user')->first();

                $response['user_list'] = $users;
                $response['featured_trainee'] = $EmpOfTheMonth;
                $response['image_path'] =  url('/storage/profile-pictures/');

                return $this->sendResponse($response,  \Lang::get('lang.leaderborad-list'));
            } catch (Exception $e) {
                Log::error($e);
                return $this->sendError($e, \Lang::get('lang.invalid-request'));
            }

    }
}
