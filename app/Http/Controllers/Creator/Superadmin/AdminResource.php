<?php

namespace App\Http\Controllers\Creator\Superadmin;

use Log;
use Auth;
use Hash;
use Config;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{ User, JobRole, Role, Country, Region, Group, UserMapping, MailTemplateConfig };
use Illuminate\Support\Facades\Mail;
use Spatie\MailTemplates\Models\MailTemplate;
use Spatie\MailTemplates\TemplateMailable;
use App\Mail\AccountCreateEmail;


class AdminResource extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $roles  = Role::all();
        $search = trim($request->query('search'));
        $role   = trim($request->query('role'));

        $page = $request->page ? $request->page : 1;
        $selectedRole = $role ? $role : -1; 
        $limit = 5;

        if (!empty($search)) {

            $users = User::with('roles')->where('name', 'like', '%' . $search . '%')
                             ->orWhere('email', 'like', '%' . $search . '%')   
                             ->paginate($page);

        } elseif($selectedRole != -1) {

            $users = User::whereHas("roles", function($query) use($selectedRole )
                          { 
                             $query->where("name", $selectedRole); 
                          })->paginate($limit);

        } else {
            $users = User::with('roles')->latest()->paginate($limit);                   
        }
        
        return view( 'creator.superadmin.users.user.index', compact(['users', 'roles']))
                   ->with('index', ($page- 1) * $limit);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $jobRoles    = JobRole::where('status', 1)->orderBy('name')->get();  
        $countries   = Country::where('status', 1)->orderBy('name')->get();  
        $groups      = Group::where('status', 1)->orderBy('name')->get(); 

        return view('creator.superadmin.users.traningadmin.create', compact(['jobRoles', 'countries', 'groups']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $regions = '';
        $this->validate($request, [
                'name'       => 'required|min:2',
                'email'      => 'required|email|unique:users,email',
                'password'   => 'required|same:confirm-password',
                'country_id' => 'required|exists:countries,id',
                'region_id'  => 'required|exists:regions,id'
        ]);

        $regionArray = [];
        $country[] = $request->country_id;
        array_push($country, "-1");
        $input = $request->all();
        unset($input['region_id']);

        foreach($request->region_id as $region)
        {
            array_push($regionArray, $region);
            $regions .= $region . ',';
        }
        array_push($regionArray, "-1");
        $input['region_id'] = rtrim($regions, ',');
       
        $input['password'] = Hash::make($input['password']);
        $input['created_by'] = Auth::user()->id;

        $input['job_role_id'] = $request->job_role_id == 0 ?  NULL : $request->job_role_id;      
        $input['group_id'] = $request->group_id == 0 ?  NULL : $request->group_id; 

        $role = Role::where('name', 'admin')->first();
      
        $user = User::create($input);
    
        $user->assignRole($role->name);

        $team =  Config::get('constant.team');

        $templateConfig =  MailTemplateConfig::with(['mailTemplates'])
                                    ->whereHas('mailTemplates', function($q) {
                                        $q->where('mailable', 'App\Mail\AccountCreateEmail');
                                    })
                                    ->whereIn('country_id', $country)
                                    ->whereIn('region_id', $regionArray)
                                    ->first();
        if($templateConfig != null) {

            Mail::to($request->email)
            ->send(new \App\Mail\AccountCreateEmail($user->name, $team, $request->password, $templateConfig->template_id));

            return redirect('superadmin/users')
                ->with('success', \Lang::get('lang.admin-create'));
        } else {
            return redirect('superadmin/users')
                ->with('success', \Lang::get('lang.admin-create-without-mail'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::with(['roles', 'country', 'createdBy'])->find($id);
       
        return view('creator.superadmin.users.traningadmin.show', compact('user'));
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $jobRoles    = JobRole::where('status', 1)->orderBy('name')->get();   
        $countries   = Country::where('status', 1)->orderBy('name')->get();  
        $groups      = Group::where('status', 1)->orderBy('name')->get(); 
        $user        = User::with(['roles', 'country', 'region', 'createdBy'])->find($id);
        $where = [
                    'status' => 1,
                    'country_id' => $user->country_id,
                 ];         
        $regions = Region::where($where)->orderBy('name')->get(); 
        $userRegion = explode(',', $user->region_id);
       
        return view('creator.superadmin.users.traningadmin.edit', compact('user', 'jobRoles', 'countries', 'groups', 'regions', 'userRegion'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $regions = '';    
        $this->validate($request, [
                'id'         => 'required|exists:users,id',
                'name'       => 'required|min:2',
                'email'      => 'required|email|unique:users,email,'.$id,
                'country_id' => 'required|exists:countries,id',
                'region_id'  => 'required|exists:regions,id'
        ]);

        $job_role_id = $request->job_role_id == 0 ?  NULL : $request->job_role_id;
            
        $group_id = $request->group_id == 0 ?  NULL : $request->group_id;      
        
        $user = new User();
       
        foreach($request->region_id as $region)
        {
            $regions .= $region . ',';
        }
       
        $update = [

                    'name' => $request->name,
                    'email' => $request->email,
                    'country_id' => $request->country_id,
                    'region_id' => rtrim($regions, ','),
                    'job_role_id' =>  $job_role_id,
                    'group_id' =>  $group_id,
                    'updated_by' => Auth::user()->id,
                    'remarks' => $request->remarks
        ];

        $isUpdated = User::where('id', $request->id)
                         ->update($update);

        if($isUpdated == 0)
        {
            throw new \Exception('Unable to update'); 
        }

        return redirect('superadmin/users')
                            ->with('success', \Lang::get('lang.admin-update')); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try
        {
           $user = User::findOrFail($id); //get the record
           //soft delete
           $isDeleted = User::where('id', $user->id)->update(['status' => 0, 'email' => $user->email.'-'.time()]);
           return response()->json(['success' => true, "messsage" =>  \Lang::get('lang.admin-delete')], 200);
        } catch (Exception $e){
            Log::error($e);
            return response()->json(['success' => false, "messsage"=> \Lang::get('lang.generic-error')], 400);
        }

    }

    public function getAdminByRegion ($regionId) {
        $admin = User::role('admin')->where('region_id', $regionId)->get(); 
        return $admin;
    }


}
