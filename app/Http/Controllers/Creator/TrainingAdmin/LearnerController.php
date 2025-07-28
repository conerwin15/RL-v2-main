<?php

namespace App\Http\Controllers\Creator\TrainingAdmin;

use Auth;
use Exception; 
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Models\{Course, LearningPath, User, Region,  Role, Group, JobRole, UserLearningPath, LearningPathResource, UserLearningProgress };

class LearnerController extends Controller
{
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createLearner(Request $request, $learningPathId )
    {
        $admin = Auth::user();
        $regions = $admin->region_id;
        $region = explode(',', $regions);
        $regionNames = Region::whereIn('id', $region)->select('id', 'name')->orderBy('name')->get();
        $request->filter_country = $admin->country_id;

        $jobRoles  = JobRole::where('status', 1)->orderBy('name')->get();
        $groups    = Group::where('status', 1)->orderBy('name')->get();
        $roles     = Role::whereNotIn('name', ['superadmin', 'admin'])->get();
        $dealer    = trim($request->query('dealer')); 
        $page      = $request->page ? $request->page : 1;
        $limit     = 10;

        $LearningPath = LearningPath::find($learningPathId);
        
        $assignedLearners = $LearningPath->users()->pluck('user_id');

        $query = User::buildQuery($request, ['superadmin','admin'])
                ->whereIn('region_id', explode(',', $admin->region_id))
                ->with(['country', 'region', 'roles', 'createdBy', 'jobRole', 'group'])
                ->orderby('created_at', 'asc')
                ->whereNotIn('id', $assignedLearners);

        if(!empty($dealer)){
            $query = $query->whereHas("dealer", function($q) use ($dealer)
                        {
                            $q->where('name', 'like', '%' . $dealer . '%');
                        });
        }         
           
        $learners = $query->paginate(10);
        $selectedRole = $request->filter_role;
        $selectedJobRole = $request->filter_jobrole;
        $selectedGroup = $request->filter_group;
        
        return view('creator.admin.learners.create', 
        compact(['jobRoles', 'groups', 'roles', 'learners', 'learningPathId', 'selectedRole', 'selectedJobRole', 'selectedGroup', 'regionNames']))->with('index', ($page- 1) * $limit);
    }

    /**
     * Assign learner resource..
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $admin = Auth::user();
        if((!isset($request->assign_learners)) && $request->assignAll == null)
        {
            return redirect()->back()->with('error', \Lang::get('lang.please-select-user'));
        }

        if($request->assignAll == -1){
            
            $LearningPath = LearningPath::find($request->learning_path_id);
            $assignedLearners = $LearningPath->users()->pluck('user_id');

            $query = User::buildQuery($request, ['superadmin', 'admin'])->whereNotIn('id', $assignedLearners);
            if($request->filter_region == null || $request->filter_region == -1) {
                $query->whereIn('region_id', explode(',', $admin->region_id));
            }

            $newLearners = $query->get();
            foreach ($newLearners as $key => $assignLearner) {
              $learningPath = LearningPath::findOrfail($request->learning_path_id);
              $learningPath->users()->attach($assignLearner->id, [
                    'assign_by' => Auth::user()->id
                ]);
            }

        } else {
            foreach ($request->assign_learners as $key => $value) {
              $learningPath = LearningPath::findOrfail($request->learning_path_id);
              $learningPath->users()->attach($value, [
                     'assign_by' => Auth::user()->id
                ]);
            }
        }

        return redirect('/admin/learners/'. $request->learning_path_id)
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
        $user = Auth::user();
        $roles = Role::whereNotIn('name', ['superadmin', 'admin'])->get();
        $groups = Group::where('status', 1)->orderBy('name')->get(); 
        $jobRoles = JobRole::where('status', 1)->orderBy('name')->get(); 
        $adminRegions = $user->adminRegions();

        $regions = $user->region_id;
        $search = trim($request->query('name'));
        $dealer = trim($request->query('dealer'));
        $dealers = User::select('id')->where('name', 'like', '%' . $dealer . '%')->get();
    
        $page = $request->page ? $request->page : 1;
        $limit = 10;

        $learningPath = LearningPath::findOrFail($id);
        $query = UserLearningPath::where('learning_path_id', $id);

        $filter_role = $request->filter_role;
        $filter_group = $request->filter_group;
        $filter_jobrole = $request->filter_jobrole;
        $filter_region = $request->filter_region;

        if((!$request->filter_region || $request->filter_region == -1) && $user->roles[0]->name == "admin") {
            $filter_region = explode(',', Auth::user()->region_id);
        }

        $query = $query->whereHas('user', function($q) use($filter_role, $filter_jobrole, $filter_group, $filter_region)
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

                    if(is_array($filter_region)) {
                        $q->whereIn('region_id', $filter_region);
                    } else {
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
        
        $learners = $query->get();
        if($request->ajax()){
            return Datatables::of($learners, $request)
            ->addIndexColumn()
            ->editColumn('name', function($learners)
            {
               return $learners->user->name;
            })
            ->editColumn('dealer', function($learners)
            {
               return ($learners->user->dealer_id == null) ? 'N/A' : ucfirst($learners->user->getNameById($learners->user->dealer_id));
            })
            ->editColumn('jobRole', function($learners)
            {
               return $learners->user->jobRole ? ucwords($learners->user->jobRole->name) : 'N/A';
            })
            ->editColumn('region', function($learners)
            {
               return $learners->user->region ? $learners->user->region->name : 'N/A' ;
            })
            ->editColumn('role', function($learners)
            {
               return ucfirst(toRoleLabel($learners->user->getRoleNames()->first()));
            })
            ->editColumn('group', function($learners)
            {
                return  $learners->user->group ? ucwords($learners->user->group->name) : 'N/A' ;
            })
            ->editColumn('progress', function($learners){
                return ($learners->progress_percentage == null) ? \Lang::get('lang.incomplete') : $learners->progress_percentage .'% '.\Lang::get('lang.completed');
            })
            ->editColumn('assigned_by', function($learners)
            {
                return ucfirst($learners->assignBy->name);
            })

            ->editColumn('assigned_on', function($learners)
            {
                return   date('d M Y', strtotime($learners->created_at));
            })
            ->addColumn('action', function($learners){
                $href = url('/admin/learners/' . $learners->user->id . '/' . $learners->learning_path_id);
                $delete = \Lang::get('lang.remove-from-path');
                $actionBtn = "<button type='button' class='text-danger delete-user' data-href='$href' data-role='superadmin'><i class='fa fa-trash' aria-hidden='true'></i> $delete </button>";
                return $actionBtn;
            })
            ->rawColumns(['action'])
            ->make(true);
        }

        return view('creator.admin.learners.show', compact(['learners', 'learningPath', 'roles', 'groups', 'jobRoles', 'adminRegions','id']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function removeLearner(Request $request, $id, $learningPathId)
    {
        $user = User::findOrfail($id);

        $learningPathResources = LearningPathResource::where("learning_path_id", $learningPathId)->pluck("id")->toArray();
       
        $prgoress = UserLearningProgress::where("user_id", $user->id)->whereIn("learning_resource_id", $learningPathResources)->delete();


        $isDeleted = $user->learningPaths()->wherePivot('user_id', $id)->wherePivot('learning_path_id', $learningPathId)->detach();
        if($isDeleted == 0)
        {
            throw new \Exception(\Lang::get('lang.unable-to-delete'));  
        } 

        return back()->with('success', \Lang::get('lang.learner-delete')); 
    }
}
