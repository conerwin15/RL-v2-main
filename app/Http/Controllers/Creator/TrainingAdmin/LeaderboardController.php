<?php

namespace App\Http\Controllers\Creator\TrainingAdmin;

use Auth;
use DB;
use Config;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{ User, Role, Region, UserPointHistory, FeaturedTrainee, Country };
use Illuminate\Support\Facades\Mail;
use Spatie\MailTemplates\Models\MailTemplate;
use Spatie\MailTemplates\TemplateMailable;
use App\Mail\ResetPoint;
use App\Events\PointUpdateEvent;
use Yajra\DataTables\DataTables;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $search  = trim($request->query('name'));
        $page = $request->page ? $request->page : 1;
        $limit = 10;

        $users = DB::table('user_point_history')
                        ->select('users.id as id', 'users.name as name', 'users.created_by as created_by',
                        'countries.name as country', 'regions.name as region', 'users.image as image', DB::raw('user_id, SUM(points) as totalPoints'))
                        ->leftJoin('users', 'user_id', '=', 'users.id')
                        ->leftJoin('countries', 'users.country_id', '=', 'countries.id')
                        ->leftJoin('regions', 'users.region_id', '=', 'regions.id')
                        ->orderBy('totalPoints', 'DESC')
                 ->where('users.name', 'LIKE', '%'.$search.'%')
                        ->where('users.status', '=', 1)
                        ->groupBy('user_id', 'id' , 'created_by' , 'regions.name' , 'name', 'image', 'countries.name')->get();
        if($request->ajax()){
            return Datatables::of($users, $request)
            ->addIndexColumn()
            ->editColumn('image', function($users)
            {
                $image = $users->image ? asset('storage' . Config::get('constant.PROFILE_PICTURES') . $users->image) : asset('assets/images/avatar_default.png');
                return $image;
            })
            ->editColumn('points', function($users)
            {
                return ($users->totalPoints > 0) ? $users->totalPoints.' '.\Lang::get('lang.points') : 0 .' '.\Lang::get('lang.points');
            })
            ->make(true);
        }
        // get emp of the month
        $month = Carbon::now()->month - 1;
        $where = [
            'year' => Carbon::now()->year,
            'type' => 'global',
        ];
        $EmpOfTheMonth = FeaturedTrainee::where('month',  $month)
                                        ->where($where)->with('user')->first();
        return view('creator.admin.leaderboard.index', compact(['users', 'EmpOfTheMonth']));
    }

    public function globalFeaturedRecords (Request $request)
    {
        $page = $request->page ? $request->page : 1;
        $limit = 10;
        $featuredRecords = FeaturedTrainee::with('user')->orderBy('created_at')->paginate($limit);
        return view('creator.admin.leaderboard.featured-records', compact('featuredRecords'));
    }

    public function regionalLeadeboard(Request $request)
    {
        $search  = trim($request->query('name'));
        $page = $request->page ? $request->page : 1;
        $limit = 10;

        $users = DB::table('user_point_history')
                        ->select('users.id as id', 'users.name as name', 'users.created_by as created_by',
                        'countries.name as country', 'regions.name as region', 'users.image as image', DB::raw('user_id, SUM(points) as totalPoints'))
                        ->leftJoin('users', 'user_id', '=', 'users.id')
                        ->leftJoin('countries', 'users.country_id', '=', 'countries.id')
                        ->leftJoin('regions', 'users.region_id', '=', 'regions.id')
                        ->orderBy('totalPoints', 'DESC')
               ->where('users.name', 'LIKE', '%'.$search.'%')
                        ->where('users.status', '=', 1)
                        ->where('users.country_id', Auth::user()->country_id)
                        ->groupBy('user_id', 'id' , 'created_by' , 'regions.name' , 'name', 'image', 'countries.name')->get();
        if($request->ajax()){
            return Datatables::of($users, $request)
            ->addIndexColumn()
            ->editColumn('image', function($users)
            {
                $image = $users->image ? asset('storage' . Config::get('constant.PROFILE_PICTURES') . $users->image) : asset('assets/images/avatar_default.png');
                return $image;
            })
            ->editColumn('points', function($users)
            {
                return ($users->totalPoints > 0) ? $users->totalPoints.' '.\Lang::get('lang.points') : 0 .' '.\Lang::get('lang.points');
            })
            ->addColumn('action', function($users){
                $href = url('/admin/regional/mark-featured', $users->id);
                $pointHref = url('/admin/regional/view-point-history', $users->id);
                $mark = \Lang::get('lang.mark-as-featured');
                $viewPoint = \Lang::get('lang.view-points');
                $actionBtn = "<a href='$href'>$mark</a><span class='color mr-2'> | </span> <a href='$pointHref'>$viewPoint</a>";
                return $actionBtn;
            })
            ->rawColumns(['action'])
            ->make(true);
        }
        // get emp of the month
        $month = Carbon::now()->month - 1;
        $where = [
                    'month' => $month,
                    'year' => Carbon::now()->year,
                    'type' => 'regional',
        ];
        $adminRegion = Auth::user()->region_id;
        $EmpOfTheMonth = FeaturedTrainee::where($where)->whereIn('region_id', explode(',', $adminRegion))->with('user')->first();
        return view('creator.admin.leaderboard.regional.leaderboard', compact(['users', 'EmpOfTheMonth']));
    }

    /***** Delete featured trainee *******/
    public function removeFeaturedTrainee() {
        $month = Carbon::now()->month - 1;
        FeaturedTrainee::where('month',  $month)
                        ->where('year',  Carbon::now()->year)
                        ->where('type', 'regional')
                        ->whereIN('region_id', explode(',', Auth::user()->region_id))
                        ->delete();
        return back()->with('success', \Lang::get('lang.remove-featured-success'));
    }

    /***** mark featured trainee *******/
    public function markAsFeatured($userid) {
        return view('creator.admin.leaderboard.featured-trainee.index', compact('userid'));
    }

    public function updateMarkAsFeatured (Request $request) {

        $user = User::findOrFail($request->userId);
        $adminRegion = Auth::user()->region_id;
        $month = Carbon::now()->month - 1;
        $lastMonthPoints = UserPointHistory::where('user_id', $request->userId)
                                        ->whereMonth('created_at', '=', $month)
                                        ->whereYear('created_at', '=', Carbon::now()->year)->sum('points');

        $where = [
                    'year' => Carbon::now()->year,
                    'type' => 'regional',
        ];
        $currentFeaturedTrainee = FeaturedTrainee::where("month", $month)->where($where)->whereIN('region_id', explode(',', $adminRegion))->first();
        if($currentFeaturedTrainee) {
            $currentFeaturedTrainee->update([
                "user_id" => $request->userId,
                "region_id" => $user->region_id,
                "type" => "regional",
                "created_by" => Auth::user()->id,
                "points" => $lastMonthPoints,
                "featured_text" => $request->featured_text
            ]);
        } else {
            FeaturedTrainee::create([
                "user_id" => $request->userId,
                "region_id" => $user->region_id,
                "type" => "regional",
                "created_by" => Auth::user()->id,
                "points" => $lastMonthPoints,
                "month" => $month,
                "featured_text" => $request->featured_text,
                "year" => Carbon::now()->year
            ]);
        }

        return redirect('/admin/regional/leaderboard')->with('success', \Lang::get('lang.trainee-of-the-month-marked'));
    }
    /***** mark featured trainee *******/


    /******** View Point History **********/
    public function viewPointHistory (Request $request, $userid) {
        $pointHistories = UserPointHistory::with('user')->where('user_id', $userid)->orderBy('created_at', 'DESC')->get();
        $pointStats = DB::select("select ulp.user_id, sum(ulp.points) as accumulated, (select sum(points) from user_point_history where user_id = ulp.user_id and month(created_at) = MONTH(CURRENT_TIMESTAMP) ) as current_month_points from user_point_history ulp where ulp.user_id = " . $userid . " group by ulp.user_id");
        if($request->ajax()){
            return Datatables::of($pointHistories, $request)
            ->addIndexColumn()
            ->editColumn('created_at', function($pointHistories) {
                 return [
                    'display' => date("d/m/Y",strtotime($pointHistories->created_at)),
                    'timestamp' => strtotime($pointHistories->created_at)
                ];
            })
            ->editColumn('activity', function($pointHistories)
            {
                if ($pointHistories->type == "new_post")
                {
                    return Config::get('constant.new_post_comment');
                } elseif ($pointHistories->type == "remove_post")
                {
                     return Config::get('constant.remove_post_comment');
                } elseif ($pointHistories->type == "add_comment")
                {
                    return Config::get('constant.add_comment');
                } elseif ($pointHistories->type == "remove_comment")
                {
                    return Config::get('constant.remove_comment');
                } elseif ($pointHistories->type == "like_post")
                {
                    return Config::get('constant.like_post_comment');
                } elseif ($pointHistories->type == "unlike_post")
                {
                    return Config::get('constant.unlike_post_comment');
                } elseif ($pointHistories->type == "diamond_badge")
                {
                    return Config::get('constant.diamond_badge_comment');
                } elseif ($pointHistories->type == "silver_badge_comment")
                {
                    return Config::get('constant.silver_badge');
                } elseif ($pointHistories->type == "gold_badge")
                {
                    return Config::get('constant.gold_badge_comment');
                } elseif ($pointHistories->type == "bronze_badge")
                {
                    return Config::get('constant.bronze_badge_comment');
                } elseif ($pointHistories->type == "quiz_score")
                {
                    return Config::get('constant.quiz_score_point');
                } else {
                    return Config::get('constant.adjust_point_comment');
                }
            })
            ->editColumn('points', function($pointHistories)
            {
                return ($pointHistories->points > 0) ? $pointHistories->points.' '.\Lang::get('lang.points') : 0 .' '.\Lang::get('lang.points');
            })
            ->addColumn('action', function($pointHistories)
            {
                if($pointHistories->bonus_point_reason == '')
                {
                    $actionBtn = '';
                } else {
                    $edithref = url('/admin/regional/bonus-point-reason/'. $pointHistories->id);
                    $edit = \Lang::get('lang.edit');
                    $actionBtn = $pointHistories->bonus_point_reason . "&nbsp; &nbsp; <button class='editPointReason' type='submit' data-backdrop='static' data-keyboard='false'
                    data-id='$pointHistories->id' data-reason='$pointHistories->bonus_point_reason' data-href='$edithref'>
                    <i class='fa fa-pencil' aria-hidden='true'></i>  $edit </button>";
                }
               return $actionBtn;
            })
            ->make(true);
        }
        return view('creator.admin.leaderboard.point-history', compact('pointHistories', 'pointStats', 'userid'));
    }

    /*********** Adjust Point ************/
    public function adjustPoint(Request $request, $userid) {
        $eventType = Config::get('constant.ADJUST_POINT');
        event(new PointUpdateEvent($eventType, $userid, $request->adjust_point, $request->bonus_point_reason));

        return redirect()->back()->with("success",  \Lang::get('lang.adjust-point') );
    }

    /*********** Update Bonus Region ************/
    public function updateBonusReason (Request $request)
    {
        try
        {
            $history = UserPointHistory::findOrFail($request->id);
            $update = UserPointHistory::where('id', $request->id)->update(['bonus_point_reason' => $request->bonus_reason]);
            return response()->json(['success' => true, "messsage" =>  \Lang::get('lang.bonus-reason-updated')], 200);
        } catch (Exception $e){
            Log::error($e);
            return response()->json(['success' => false, "messsage"=> \Lang::get('lang.generic-error')], 400);
        }
    }

    /********** show feature text ***********/
    public function show($id)
    {
        $month = Carbon::now()->month - 1;
        $trainee = FeaturedTrainee::where("month", $month)->where("year", Carbon::now()->year)->where('type', 'regional')->whereIn('region_id', explode(',', Auth::user()->region_id))->select('featured_text')->first();
        return view('creator.admin.leaderboard.featured-trainee.edit', compact('id', 'trainee'));
    }

    /******* update fetured text ******/
    public function updateFeaturedTraineeText(Request $request) {
        $month = Carbon::now()->month - 1;
        $currentFeaturedTrainee = FeaturedTrainee::where("month", $month)->where("year", Carbon::now()->year)->where('type', 'regional')->whereIn('region_id', explode(',', Auth::user()->region_id))->first();
        $currentFeaturedTrainee->update([
            "featured_text" => $request->featured_text
        ]);

        return redirect('/admin/regional/leaderboard')->with('success', \Lang::get('lang.update-featured-content-sucess'));
    }

    /*************** Regional Feature Record ***************/
    public function regionalFeaturedRecords(Request $request)
    {
        $page = $request->page ? $request->page : 1;
        $limit = 10;

        $featuredRecords = FeaturedTrainee::with('user')->whereIn('region_id', explode(',', Auth::user()->region_id))->where("type", "regional")->orderBy('created_at')->paginate($limit);
        return view('creator.admin.leaderboard.regional.featured-records', compact('featuredRecords'));
    }

    /******* Bulk mangae points *******/
    public function managePoints (Request $request)
    {
        $regions = Auth::user()->adminRegions();
        $page = $request->page ? $request->page : 1;
        $limit = 10;
        $region = $request->filter_region;
        $managePoints =  DB::table('user_point_history')
                            ->select(DB::raw('SUM(points) as totalPoints'), 'u.id as id', 'u.name as username', 'c.name as country', 'r.name as region', 'u.dealer_id as dealer')
                            ->leftJoin('users as u', 'user_id', '=', 'u.id')
                            ->leftJoin('countries as c', 'u.country_id', '=', 'c.id')
                            ->leftJoin('regions as r', 'u.region_id', '=', 'r.id')
                            ->whereIn('u.region_id', explode(',', Auth::user()->region_id))
                            ->where('u.status', 1)
                            ->when(($region != -1 && $region != null), function ($q) use ($region) {
                                $q->where('u.region_id', $region);
                            })
                            ->groupBy('user_id', 'u.id' , 'u.name', 'r.name', 'u.dealer_id')
                            ->orderBy('totalPoints', 'DESC')->paginate($limit);

        return view('creator.admin.leaderboard.regional.manage-points', compact('managePoints', 'regions'));
    }

    public function bulkManagePoint (Request $request)
    {
        if($request->selectAll == null && (!$request->bulk_point)) {
            return redirect()->back()->with("error",   \Lang::get('lang.select-user-error') );
        }

        $eventType = Config::get('constant.ADJUST_POINT');
        if($request->selectAll == -1) {
            $users = DB::table('user_point_history')->select('user_id')->distinct()->get();
            foreach($users as $user) {
                event(new PointUpdateEvent($eventType, $user->user_id, $request->adjust_point, $request->bonus_point_reason));
            }

        } else {
            foreach($request->bulk_point as $key => $value) {
                event(new PointUpdateEvent($eventType, $value, $request->adjust_point, $request->bonus_point_reason));
            }
        }

        return redirect('/admin/regional/leaderboard')->with('success', \Lang::get('lang.points-adjust-bulk'));
    }

    /******* Bulk mangae points *******/
}
