<?php

namespace App\Http\Controllers\Learners\Dealer;

use Auth;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Models\{ User, JobRole, Role, UserMapping, Group, UserLearningPath };

class StaffResource extends Controller
{    
    public function index(Request $request)
    {
        $jobRoles = JobRole::where('status', 1)->orderBy('name')->get();
        $groups = Group::where('status', 1)->orderBy('name')->get();
        $query = User::buildQuery($request, ['superadmin', 'admin', 'dealer'])->with('jobRole');
        
        $users = $query->where('dealer_id', Auth::user()->id)->orderBy('name')->get();
        if($request->ajax()){

            return Datatables::of($users)
                    ->addIndexColumn()
                    ->editColumn('jobRole', function($users)
                    {
                       return $users->jobRole ? ucwords($users->jobRole->name) : "N/A";
                    })
                    ->editColumn('group', function($users)
                    {
                        return  $users->group_id ? ucwords($users->group->name) : "N/A";
                    })
                    ->editColumn('created_by', function($users)
                    {
                        return  $users->createdBy ? ucfirst($users->createdBy->name) : '';
                    })

                    ->editColumn('created_on', function($users)
                    {
                        return  date('d M Y', strtotime($users->created_at));
                    })
                    ->addColumn('action', function($users){
                        $href = 'staff/'. $users->id;
                        $learningPath = 'staff/learning-path/' . $users->id;
                        $view = \Lang::get('lang.view');
                        $viewLearningPath = \Lang::get('lang.view-learning-path');
                        $actionBtn = "<a href='$href'><i class='fa fa-users' aria-hidden='true'></i> $view </a>";
                        $actionBtn = $actionBtn."<a href='$learningPath'><i class='fa fa-pencil' aria-hidden='true'></i> $viewLearningPath </a>";
                        return $actionBtn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
       
        return view( 'learners.dealer.staff.index', compact(['groups', 'jobRoles']));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::with(['roles', 'createdBy', 'userLearningPaths', 'userLearningPaths.badge'])->find($id);
      
        return view('learners.dealer.staff.show', compact('user'));
        
    }

    public function viewLearningPath(Request $request, $id) 
    {
        $page = $request->page ? $request->page : 1;
        $limit = 10;
        $userLearningPaths = UserLearningPath::where('user_id', $id)->with('learningPath', 'assignBy')->get();
        return view('learners.dealer.staff.show-learning-path', compact('userLearningPaths'))->with('index', ($page - 1) * $limit);
    }
    
   
}
