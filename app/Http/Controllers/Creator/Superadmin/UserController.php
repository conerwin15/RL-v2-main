<?php

namespace App\Http\Controllers\Creator\Superadmin;

use Hash;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{ User, JobRole, Role, Country, Region, UserMapping, Group };
use Illuminate\Support\Facades\Mail;
use Spatie\MailTemplates\Models\MailTemplate;
use Spatie\MailTemplates\TemplateMailable;
use App\Mail\AccountCreateEmail;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LearningPathProgressExport;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
      
    public function index(Request $request)
    {

        $roles   = Role::orderBy('name')->get();
        $groups = Group::where('status', 1)->orderBy('name')->get(); 
        $jobRoles = JobRole::where('status', 1)->orderBy('name')->get(); 
        $regions = Region::where('status', 1)->orderBy('name')->get();
        $countries = Country::where('status', 1)->orderBy('name')->get();
        
        // check condition for user filter or dashboard filter
        $export_countries = empty($request->query('filter_country')) ? 0 : ((gettype($request->query('filter_country')) == 'string') ? $request->query('filter_country') :  (implode(',', $request->query('filter_country'))));
        $export_regions = empty($request->query('filter_region')) ? 0 : ((gettype($request->query('filter_region')) == 'string') ? $request->query('filter_region') :  (implode(',', $request->query('filter_region'))));
        $export_groups =  empty($request->query('filter_group')) ? 0 : ((gettype($request->query('filter_group')) == 'string') ? $request->query('filter_group') :  (implode(',', $request->query('filter_group'))));
        $export_jobRoles = empty($request->query('filter_jobRole')) ? 0 : ((gettype($request->query('filter_jobRole')) == 'string') ? $request->query('filter_jobRole') :  (implode(',', $request->query('filter_jobRole'))));
        $export_roles = empty($request->query('filter_role')) ? 0 : ((gettype($request->query('filter_role')) == 'string') ? $request->query('filter_role') :  (implode(',', $request->query('filter_role'))));

        if($request->ajax()){
            $query = User::buildQueryDashboard($request);

                $toSearchDealer  = trim($request->query('dealer'));
                if(!empty($toSearchDealer)){

                    $query = $query->whereHas("dealer", function($q) use ($toSearchDealer)
                                    {
                                        $q->where('name', 'like', '%' . $toSearchDealer . '%');
                                    });
                }

            $users =  $query->where('id', '!=', Auth::user()->id)->orderBy('name', 'asc')->get();

            return Datatables::of($users, $request)
                    ->addIndexColumn()
                
                    ->editColumn('country', function($users)
                    {
                       return $users->country_id ? $users->country->name : "N/A";
                    })
                    ->editColumn('dealer', function($users)
                    {
                       return ($users->dealer_id == null) ? 'N/A' : ucfirst($users->getNameById($users->dealer_id));
                    })
                    ->editColumn('jobRole', function($users)
                    {
                       return $users->jobRole ? ucwords($users->jobRole->name) : "N/A";
                    })
                    ->editColumn('region', function($users)
                    {
                       return $users->region_id ? $users->region->name : "N/A";
                    })
                    ->editColumn('role', function($users)
                    {
                       return ucfirst(toRoleLabel($users->getRoleNames()->first()));
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
                        $routeSlug = $users->getRoleNames()->first() == "admin" ? 'trainingadmins' : ($users->getRoleNames()->first() == "dealer" ? 'dealers' : 'customers');
                        $href = $routeSlug . '/'. $users->id;
                        $editHref = $href . '/edit';
                        $change = url('/superadmin/change-password'.'/'. $users->id );
                        $view = \Lang::get('lang.view');
                        $edit = \Lang::get('lang.edit');
                        $delete = \Lang::get('lang.delete');
                        $changePassword =  \Lang::get('lang.change-password');
                        $actionBtn = "<a href='$href'><i class='fa fa-users' aria-hidden='true'></i> $view </a>";
                        if( $users->getRoleNames()->first() != 'superadmin') {
                            $actionBtn = $actionBtn."<a href='$editHref'><i class='fa fa-pencil' aria-hidden='true'></i> $edit </a>";
                            $actionBtn = $actionBtn . "<button type='button' class='changePasswordModal' data-toggle='modal' data-backdrop='static' data-keyboard='false'
                                                        data-target='#changePassword' data-id='$users->id' data-href='$change' style='border:0px; color:#3490dc;'>$changePassword</button>";
                            $actionBtn = $actionBtn. "<button type='button' class='text-danger delete-user' data-href='$href' data-role='$routeSlug'><i class='fa fa-trash' aria-hidden='true'></i> $delete </button>";
                        }
                            return $actionBtn;
                    }) 
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view( 'creator.superadmin.users.user.index', compact(['roles', 'regions', 'countries', 'groups', 'jobRoles', 'export_countries', 'export_regions', 'export_groups', 'export_jobRoles', 'export_roles']));
    }

    public function exportLearningPathProgress(Request $request)
    {
        return Excel::download(new LearningPathProgressExport($request->country, $request->region, $request->role, $request->group, $request->jobRole), 'learning-progress.xls');
    }
}