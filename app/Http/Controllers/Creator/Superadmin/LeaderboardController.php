<?php

namespace App\Http\Controllers\Creator\Superadmin;

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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $countries = Country::all();
        $search  = trim($request->query('name'));

        $region_id = ($request->filter_country == -1) ? null : $request->filter_country ;
        $page = $request->page ? $request->page : 1;
        $limit = 10;

        $query = DB::table('user_point_history')
                        ->select('users.id as id', 'users.name as name', 'users.created_by as created_by',
                        'countries.name as country', 'regions.name as region', 'users.image as image', DB::raw('user_id, SUM(points) as totalPoints'))
                        ->leftJoin('users', 'user_id', '=', 'users.id')
                        ->leftJoin('countries', 'users.country_id', '=', 'countries.id')
                        ->leftJoin('regions', 'users.region_id', '=', 'regions.id')
                        ->orderBy('totalPoints', 'DESC')
                 ->where('users.name', 'LIKE', '%'.$search.'%')
                        ->where('users.status', '=', 1);

        if($request->filter_country && $request->filter_country != -1) {
            $query->where('users.country_id', '=', $request->filter_country);
        }

        $users = $query->groupBy('user_id', 'id' , 'created_by' , 'regions.name' , 'name', 'image', 'countries.name')->get();
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
                $href = url('/superadmin/mark-featured', $users->id);
                $pointHref = url('/superadmin/view-point-history', $users->id);
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
                    'year' => Carbon::now()->year,
                    'type' => 'global',
        ];
        $EmpOfTheMonth = FeaturedTrainee::where('month',  $month)
                                        ->where($where)->with('user')->first();
        return view('creator.superadmin.leaderboards.index', compact(['users', 'countries', 'EmpOfTheMonth']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $month = Carbon::now()->month - 1;
        $trainee = FeaturedTrainee::where("month", $month)->where("year", Carbon::now()->year)->where('type', 'global')->select('featured_text')->first();
        return view('creator.superadmin.leaderboards.featured-trainee.edit', compact('id', 'trainee'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        $region [] = $user->region_id;
        array_push($region, "-1");
        $country[] = $user->country_id;
        array_push($country, "-1");
        UserPointHistory::where('user_id', $id)->delete();

        // send mail
        $existMailTemplate = DB::table('user_mail_templates')
                         ->Join('mail_template_config', 'user_mail_templates.id', '=', 'mail_template_config.template_id')
                         ->whereIN('mail_template_config.country_id', $country)
                         ->whereIN('mail_template_config.region_id', $region)
                         ->where('user_mail_templates.mailable', 'App\Mail\ResetPoint')->first();
        if($existMailTemplate != null) {

            Mail::to($user->email)
                ->send(new \App\Mail\ResetPoint($user->name, $existMailTemplate->template_id));

        } else {
            Log::warning('Mail template ResetPoint does not exist for country ' .  $user->country_id);
        }

        return back()->with('success', \Lang::get('lang.user-point-reset'));

    }

    public function removeFeaturedTrainee() {
        $month = Carbon::now()->month - 1;
        FeaturedTrainee::where('month',  $month)
                        ->where('year',  Carbon::now()->year)
                        ->where('type', 'global')
                        ->delete();
        return back()->with('success', \Lang::get('lang.remove-featured-success'));
    }

    public function markAsFeatured($userid) {

        return view('creator.superadmin.leaderboards.featured-trainee.index', compact('userid'));
    }

    public function updateFeaturedTraineeText(Request $request) {

        $month = Carbon::now()->month - 1;
        $currentFeaturedTrainee = FeaturedTrainee::where("month", $month)->where("year", Carbon::now()->year)->where('type', 'global')->first();
        $currentFeaturedTrainee->update([
            "featured_text" => $request->featured_text
        ]);

        return redirect('/superadmin/leaderboard')->with('success', \Lang::get('lang.update-featured-content-sucess'));
    }

    public function updateMarkAsFeatured (Request $request) {

        $user = User::findOrFail($request->userId);
        $month = Carbon::now()->month - 1;
        $lastMonthPoints = UserPointHistory::where('user_id', $request->userId)
                                        ->whereMonth('created_at', '=', $month)
                                        ->whereYear('created_at', '=', Carbon::now()->year)->sum('points');

        $where = [
                    'year' => Carbon::now()->year,
                    'type' => 'global',
        ];
        $currentFeaturedTrainee = FeaturedTrainee::where("month", $month)->where($where)->first();
        if($currentFeaturedTrainee) {
            $currentFeaturedTrainee->update([
                "user_id" => $request->userId,
                "region_id" => $user->region_id,
                "type" => "global",
                "created_by" => Auth::user()->id,
                "points" => $lastMonthPoints,
                "featured_text" => $request->featured_text
            ]);
        } else {
            FeaturedTrainee::create([
                "user_id" => $request->userId,
                "region_id" => $user->region_id,
                "type" => "global",
                "created_by" => Auth::user()->id,
                "points" => $lastMonthPoints,
                "month" => $month,
                "featured_text" => $request->featured_text,
                "year" => Carbon::now()->year
            ]);
        }

        return redirect('/superadmin/leaderboard')->with('success', \Lang::get('lang.trainee-of-the-month-marked'));
    }

    public function viewPointHistory (Request $request, $userid) {
        $pointHistories = UserPointHistory::with('user')
        ->where('user_id', $userid)->orderBy('created_at', 'DESC')->get();

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
                    $edithref = url('/superadmin/bonus-point-reason/'. $pointHistories->id);
                    $edit = \Lang::get('lang.edit');
                    $actionBtn = $pointHistories->bonus_point_reason . "&nbsp; &nbsp; <button class='editPointReason' type='submit' data-backdrop='static' data-keyboard='false'
                    data-id='$pointHistories->id' data-reason='$pointHistories->bonus_point_reason' data-href='$edithref'>
                    <i class='fa fa-pencil' aria-hidden='true'></i>  $edit </button>";
                }
               return $actionBtn;
            })
            ->make(true);
        }
        $pointStats = DB::select("select ulp.user_id, sum(ulp.points) as accumulated, (select sum(points) from user_point_history where user_id = ulp.user_id and month(created_at) = MONTH(CURRENT_TIMESTAMP) ) as current_month_points from user_point_history ulp where ulp.user_id = " . $userid . " group by ulp.user_id");
        return view('creator.superadmin.leaderboards.point-history', compact('pointHistories', 'pointStats', 'userid'));
    }

    public function managePoints (Request $request)
    {
        $countries = Country::where('status', 1)->orderBy('name')->get();
        $page = $request->page ? $request->page : 1;
        $limit = 10;
        $country = $request->filter_country;
        $region = $request->filter_region;
        $managePoints =  DB::table('user_point_history')
                            ->select(DB::raw('SUM(points) as totalPoints'), 'u.id as id', 'u.name as username', 'c.name as country', 'r.name as region', 'u.dealer_id as dealer')
                            ->leftJoin('users as u', 'user_id', '=', 'u.id')
                            ->leftJoin('countries as c', 'u.country_id', '=', 'c.id')
                            ->leftJoin('regions as r', 'u.region_id', '=', 'r.id')
                            ->where('u.status', 1)
                            ->when(($country != -1 && $country != null), function ($q) use ($country) {
                                $q->where('u.country_id', $country);
                            })
                            ->when(($region != -1 && $region != null), function ($q) use ($region) {
                                $q->where('u.region_id', $region);
                            })
                            ->groupBy('user_id', 'u.id' , 'u.name', 'c.name' , 'r.name', 'u.dealer_id')
                            ->orderBy('totalPoints', 'DESC')->paginate($limit);

        return view('creator.superadmin.leaderboards.manage-points', compact('managePoints', 'countries'));
    }

    public function featuredRecords (Request $request) {
        $page = $request->page ? $request->page : 1;
        $limit = 10;
        $country = $request->country;
        $admin = $request->filter_admin;
        $countries = Country::where('status', 1)->orderBy('name')->get();
        $admins = User::role('admin')->get();
        $adminRole = Role::where('name', 'admin')->pluck('id')->first();
        $featuredUsers = FeaturedTrainee::pluck('id')->toArray();
        $featuredRecords = FeaturedTrainee::whereHas('user' , function ($query) use ($country, $admin){
                                if(!empty($country)  && $country[0] != -1) {
                                    $query->whereIn('country_id', $country);
                                }
                                if(!empty($admin) && ($admin != -1))
                                {
                                    $query->where('created_by', $admin);
                                }
                            })->orderBy('created_at')->paginate($limit);

        return view('creator.superadmin.leaderboards.featured-records', compact('featuredRecords', 'countries', 'admins', 'adminRole'));
    }

    public function adjustPoint(Request $request, $userid) {
        $eventType = Config::get('constant.ADJUST_POINT');
        event(new PointUpdateEvent($eventType, $userid, $request->adjust_point, $request->bonus_point_reason));

        return redirect()->back()->with("success",  'Adjust Point added successfully' );
    }

    public function bulkManagePoint(Request $request)
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

        return redirect('/superadmin/leaderboard')->with('success', \Lang::get('lang.points-adjust-bulk'));

    }

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
}
