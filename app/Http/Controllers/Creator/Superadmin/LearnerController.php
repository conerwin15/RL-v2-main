<?php

namespace App\Http\Controllers\Creator\Superadmin;

use Auth;
use DB;
use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Models\{Course, LearningPath, User, Country, Region, JobRole, Group, Role, UserLearningPath, LearningPathResource, UserLearningProgress };

class LearnerController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createLearner(Request $request, $learningPathId )
    {
        $countries = Country::where('status', 1)->orderBy('name')->get();
        $jobRoles  = JobRole::where('status', 1)->orderBy('name')->get();
        $groups    = Group::where('status', 1)->orderBy('name')->get();

        $roles   = Role::whereNotIn('name', ['superadmin', 'admin'])->get();
        $dealer  = trim($request->query('dealer'));

        $page = $request->page ? $request->page : 1;
        $limit = 10;

        $LearningPath = LearningPath::find($learningPathId);
        $assignedLearners = $LearningPath->users()->pluck('user_id');

        $query = User::buildQuery($request, ['superadmin', 'admin'])
                    ->with(['roles', 'country', 'region', 'createdBy', 'jobRole', 'group'])
                    ->whereNotIn('id', $assignedLearners)
                    ->orderby('created_at', 'asc');

        if(!empty($dealer)){
            $query = $query->whereHas("dealer", function($q) use ($dealer)
                        {
                            $q->where('name', 'like', '%' . $dealer . '%');
                        });
        }

        $learners = $query->paginate($limit);

        $selectedCountry = $request->filter_country;
        $selectedRole = $request->filter_role;
        $selectedRegion = $request->filter_region;
        $selectedJobRole = $request->filter_jobrole;
        $selectedGroup = $request->filter_group;

        $routeSlug = $this->getRouteSlug();
        return view('creator.superadmin.learners.create',
        compact(['countries', 'jobRoles', 'groups', 'roles', 'learners', 'learningPathId', 'selectedRole', 'selectedRegion', 'selectedCountry', 'selectedJobRole', 'selectedGroup', 'routeSlug']))->with('index', ($page- 1) * $limit);
    }

    public function removeAllLearners(Request $request, $id) {


        $query = User::buildQuery($request, ['superadmin', 'admin'])
                    ->select('id')
                    ->orderby('created_at', 'asc');

        $users = $query->get()->toArray();

        $learningPathResources = LearningPathResource::where("learning_path_id", $id)->pluck("id")->toArray();

        UserLearningProgress::whereIn("learning_resource_id", $learningPathResources)->whereIn('user_id', $users)->delete();
        UserLearningPath::where('learning_path_id', $id)->whereIn('user_id', $users)->delete();

        return redirect()->back()->with('success', 'Learner Removed Successfully');
    }

    /**
     * Assign learner resource..
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if((!isset($request->assign_learners)) && $request->assignAll == null)
        {
            return redirect()->back()->with('error', \Lang::get('lang.please-select-user'));
        }

        if($request->assignAll == -1){

            $LearningPath = LearningPath::find($request->learning_path_id);
            $assignedLearners = $LearningPath->users()->pluck('user_id');


            $query = User::buildQuery($request, ['superadmin', 'admin'])
                    ->with(['roles', 'country', 'region', 'createdBy', 'jobRole', 'group'])
                    ->whereNotIn('id', $assignedLearners)
                    ->orderby('created_at', 'asc');

            if(!empty($dealer)){
                $query = $query->whereHas("dealer", function($q) use ($dealer)
                            {
                                $q->where('name', 'like', '%' . $dealer . '%');
                            });
            }
            $query->whereNotIn('id', $assignedLearners);

            $newLearners = $query->get();

            foreach ($newLearners as $key => $assignLearner) {
              $learner = LearningPath::findOrfail($request->learning_path_id);
              $learner->users()->attach($assignLearner->id, [
                    'assign_by' => Auth::user()->id
                ]);
            }

        } else {
            foreach ($request->assign_learners as $key => $value) {
              $learner = LearningPath::findOrfail($request->learning_path_id);
              $learner->users()->attach($value, [
                     'assign_by' => Auth::user()->id
                ]);
            }
        }

        return redirect('superadmin/learners/' . $request->learning_path_id)
                         ->with('success', \Lang::get('lang.learner-assign'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $roles = Role::whereNotIn('name', ['superadmin', 'admin'])->get();
        $groups = Group::where('status', 1)->orderBy('name')->get();
        $jobRoles = JobRole::where('status', 1)->orderBy('name')->get();
        $regions = Region::where('status', 1)->orderBy('name')->get();
        $countries = Country::where('status', 1)->orderBy('name')->get();

        $user = Auth::user();
        $page = $request->page ? $request->page : 1;
        $limit = 10;
        $search = trim($request->query('name'));
        $dealer = trim($request->query('dealer'));
        $dealers = User::select('id')->where('name', 'like', '%' . $dealer . '%')->get();

        $learningPath = LearningPath::findOrFail($id);

        $query = UserLearningPath::where('learning_path_id', $id);

        $filter_role = $request->filter_role;
        $filter_group = $request->filter_group;
        $filter_country = $request->filter_country;
        $filter_jobrole = $request->filter_jobrole;
        $filter_region = $request->filter_region;

        $query = $query->whereHas('user', function($q) use($filter_role, $filter_jobrole, $filter_group, $filter_country, $filter_region)
                {

                    if(!empty($filter_role) && $filter_role != -1) {
                        $q->whereHas('roles', function ($s) use ($filter_role){
                            $s->where("id", $filter_role);
                        });
                    }

                    if(!empty($filter_jobrole) && $filter_jobrole != -1) {
                        $q->where('job_role_id', $filter_jobrole);
                    }

                    if(!empty($filter_group) && $filter_group != -1) {
                        $q->where('group_id', $filter_group);
                    }

                    if(!empty($filter_country) && $filter_country != -1) {
                        $q->where('country_id', $filter_country);
                    }

                    if(!empty($filter_region) && $filter_region != -1) {
                        $q->where('region_id', $filter_region);
                    }

                    $q->orderBy('name', 'asc');
                });

        if (!empty($search)) {
            $query = $query->whereHas('user', function($q) use($search)
                        {
                            $q->where('name', 'like', '%' . $search . '%');
                        });
        }

        if(!empty($dealer)){
            $query = $query->whereHas("user", function($q) use ($dealers)
                        {
                            $q->whereIn('dealer_id', $dealers);
                        });
        }

        $userLearningPaths = $query->get();

        if($request->ajax()){
            return Datatables::of($userLearningPaths, $request)
            ->addIndexColumn()
            ->editColumn('name', function($userLearningPaths)
            {
               return $userLearningPaths->user->name;
            })
            ->editColumn('country', function($userLearningPaths)
            {
               return $userLearningPaths->user->country ? $userLearningPaths->user->country->name : 'N/A';
            })
            ->editColumn('dealer', function($userLearningPaths)
            {
               return ($userLearningPaths->user->dealer_id == null) ? 'N/A' : ucfirst($userLearningPaths->user->getNameById($userLearningPaths->user->dealer_id));
            })
            ->editColumn('jobRole', function($userLearningPaths)
            {
               return $userLearningPaths->user->jobRole ? ucwords($userLearningPaths->user->jobRole->name) : 'N/A';
            })
            ->editColumn('region', function($userLearningPaths)
            {
               return $userLearningPaths->user->region ? $userLearningPaths->user->region->name : 'N/A' ;
            })
            ->editColumn('role', function($userLearningPaths)
            {
               return ucfirst(toRoleLabel($userLearningPaths->user->getRoleNames()->first()));
            })
            ->editColumn('group', function($userLearningPaths)
            {
                return  $userLearningPaths->user->group ? ucwords($userLearningPaths->user->group->name) : 'N/A' ;
            })
            ->editColumn('assigned_by', function($userLearningPaths)
            {
                return ucfirst($userLearningPaths->assignBy->name);
            })

            ->editColumn('assigned_on', function($userLearningPaths)
            {
                return   date('d M Y', strtotime($userLearningPaths->created_at));
            })

            ->editColumn('progress', function($userLearningPaths){
                return ($userLearningPaths->progress_percentage == null) ? \Lang::get('lang.incomplete') : $userLearningPaths->progress_percentage .'% '.\Lang::get('lang.completed');
            })
            ->addColumn('action', function($userLearningPaths){
                $href = $userLearningPaths->user->id.'?learningpath='.$userLearningPaths->learningpath->id;
                $delete = \Lang::get('lang.delete');
                $actionBtn = "<button type='button' class='text-danger delete-user' data-href='$href' data-role='superadmin'><i class='fa fa-trash' aria-hidden='true'></i> $delete </button>";
                return $actionBtn;
            })
            ->rawColumns(['action'])
            ->make(true);
        }
        $routeSlug = $this->getRouteSlug();
        return view('creator.superadmin.learners.show', compact(['learningPath', 'routeSlug', 'roles', 'groups', 'jobRoles', 'countries', 'regions', 'id']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        $user = User::findOrfail($id);

        $learningPathResources = LearningPathResource::where("learning_path_id", $request->learningpath)->pluck("id")->toArray();

        $prgoress = UserLearningProgress::where("user_id", $user->id)->whereIn("learning_resource_id", $learningPathResources)->delete();

        $isDeleted = $user->learningPaths()->wherePivot('user_id', $id)->wherePivot('learning_path_id', $request->learningpath)->detach();
        if($isDeleted == 0)
        {
            throw new \Exception(\Lang::get('lang.unable-to-delete'));
        }

        return back()->with('success', \Lang::get('lang.learner-delete'));
    }

    protected function getRouteSlug()
    {
        $user = Auth::user();
        return $user->getRoleNames()->first();
    }
}
