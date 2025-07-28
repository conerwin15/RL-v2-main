<?php

namespace App\Http\Controllers\Shared;

use Auth;
use DB;
use Config;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Models\{ User, Role, FeaturedTrainee, Region };

class LeaderboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $loggedUser = Auth::user();
        $search = trim($request->query('name'));
        $limit = 10;

        $users = DB::table('user_point_history')
                    ->select(
                        'users.name as name',
                        'countries.name as country',
                        'regions.name as region',
                        'users.image as image',
                        DB::raw('user_id, SUM(points) as totalPoints'),
                        'roles.name as role'
                    )
                    ->leftJoin('users', 'user_id', '=', 'users.id')
                    ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                    ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
                    ->leftJoin('countries', 'users.country_id', '=', 'countries.id')
                    ->leftJoin('regions', 'users.region_id', '=', 'regions.id')
                    ->orderBy('totalPoints', 'DESC')
                    ->where('users.name', 'LIKE', '%' . $search . '%')
                    ->where('users.status', 1)
                    ->groupBy('user_id', 'users.name', 'regions.name', 'countries.name', 'users.image', 'role')
                    ->get();

        if ($request->ajax()) {
            return Datatables::of($users, $request)
                ->addIndexColumn()
                ->editColumn('image', function ($users) {
                    $image = $users->image
                        ? asset('storage' . Config::get('constant.PROFILE_PICTURES') . $users->image)
                        : asset('assets/images/avatar_default.png');
                    return $image;
                })
                ->editColumn('points', function ($users) {
                    return ($users->totalPoints > 0)
                        ? $users->totalPoints . ' ' . \Lang::get('lang.points')
                        : '0 ' . \Lang::get('lang.points');
                })
                ->make(true);
        }

        // Get employee of the month
        $month = Carbon::now()->month - 1;
        $EmpOfTheMonth = FeaturedTrainee::where('month', $month)
            ->where('year', Carbon::now()->year)
            ->where('type', 'global')
            ->with('user')
            ->first();

        return view('shared.leaderboards.index', compact('users', 'EmpOfTheMonth'));
    }

    public function regionalUser(Request $request)
    {
        $loggedUser = Auth::user();
        $search = trim($request->query('name'));
        $limit = 10;

        $where = [
            'status' => 1,
            'country_id' => $loggedUser->country_id
        ];
        $regions = Region::where($where)->get();

        $userQuery = DB::table('user_point_history')
                    ->select(
                        'users.name as name',
                        'countries.name as country',
                        'regions.name as region',
                        'users.image as image',
                        DB::raw('user_id, SUM(points) as totalPoints'),
                        'roles.name as role'
                    )
                    ->leftJoin('users', 'user_id', '=', 'users.id')
                    ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                    ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
                    ->leftJoin('countries', 'users.country_id', '=', 'countries.id')
                    ->leftJoin('regions', 'users.region_id', '=', 'regions.id')
                    ->orderBy('totalPoints', 'DESC')
                    ->where('users.name', 'LIKE', '%' . $search . '%')
                    ->where('users.status', 1)
                    ->where('users.country_id', $loggedUser->country_id);

        if (!empty($request->filter_region) && $request->filter_region != -1) {
            $userQuery->where('users.region_id', $request->filter_region);
        }

        $users = $userQuery
            ->groupBy('user_id', 'users.name', 'regions.name', 'countries.name', 'users.image', 'role')
            ->get();

        if ($request->ajax()) {
            return Datatables::of($users, $request)
                ->addIndexColumn()
                ->editColumn('image', function ($users) {
                    $image = $users->image
                        ? asset('storage' . Config::get('constant.PROFILE_PICTURES') . $users->image)
                        : asset('assets/images/avatar_default.png');
                    return $image;
                })
                ->editColumn('points', function ($users) {
                    return ($users->totalPoints > 0)
                        ? $users->totalPoints . ' ' . \Lang::get('lang.points')
                        : '0 ' . \Lang::get('lang.points');
                })
                ->make(true);
        }

        // Get employee of the month (regional)
        $month = Carbon::now()->month - 1;
        $EmpOfTheMonth = FeaturedTrainee::where('month', $month)
            ->where('year', Carbon::now()->year)
            ->where("type", "regional")
            ->whereIn('region_id', explode(',', Auth::user()->region_id))
            ->with('user')
            ->first();

        return view('shared.leaderboards.regional-leaderboard', compact('users', 'EmpOfTheMonth', 'regions'));
    }
}
