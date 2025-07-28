<?php

namespace App\Http\Controllers\Creator\TrainingAdmin;

use DB;
use Auth;
use Hash;
use Config;
use Log;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{ User, JobRole, Role, UserMapping, Group, UserMailTemplate, UserLearningPath, UserPointHistory, FeaturedTrainee, UserLearningProgress };
use Illuminate\Support\Facades\Mail;
use Spatie\MailTemplates\Models\MailTemplate;
use Spatie\MailTemplates\TemplateMailable;
use App\Mail\AccountCreateEmail;

class CustomerResource extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $admin     = Auth::user();
        $jobRoles  = JobRole::where('status', 1)->orderBy('name')->get();  
        $groups    = Group::where('status', 1)->orderBy('name')->get(); 

        $request->filter_country = $admin->country_id; 
        $regions = $admin->adminRegions();
      
        $dealers   = User::buildQuery($request, ['superadmin', 'admin', 'staff'])->whereIn('region_id', explode(',', $admin->region_id))->orderBy('name')->get();
        
        return view('creator.admin.users.customers.create', compact(['jobRoles', 'groups', 'dealers', 'regions']));
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
                'name'          => 'required',
                'email'         => 'required|email|unique:users,email',
                'password'      => 'required|same:confirm-password',
                'dealer_id'     => 'required|exists:users,id'
        ]);

        $input = $request->all();
       
        $input['password'] = Hash::make($input['password']);
        $input['job_role_id'] = $request->job_role_id == 0 ?  NULL : $request->job_role_id; 
        $input['group_id'] = $request->group_id == 0 ?  NULL : $request->group_id; 
       
        $admin = Auth::user();
        $input['country_id'] = $admin->country_id;
        $input['region_id']  = $request->region_id;
     
        $role = Role::where('name', 'staff')->first();
      
        $user = User::create($input);
       
        $user->assignRole($role->name);  

        $team =  Config::get('constant.team'); 
        $country[] = $admin->country_id;
        array_push($country, "-1");
        $region[] = $request->region_id;
        array_push($region, "-1");

        $templateConfig = DB::table('user_mail_templates')
                            ->join('mail_template_config', 'user_mail_templates.id', '=', 'mail_template_config.template_id')
                            ->whereIN('mail_template_config.country_id', $country)
                            ->whereIN('mail_template_config.region_id', $region)
                            ->where('user_mail_templates.mailable', 'App\Mail\AccountCreateEmail')->first();

        if($templateConfig != null) {
            Mail::to($request->email)
            ->send(new \App\Mail\AccountCreateEmail($user->name, $team, $request->password, $templateConfig->template_id));

            return redirect('admin/users')
                    ->with('success', \Lang::get('lang.staff-create'));
        } else {
            return redirect('admin/users')
                        ->with('success', \Lang::get('lang.staff-create-without-mail'));
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
        $user = User::with(['roles', 'createdBy', 'userLearningPaths', 'userLearningPaths.badge'])->find($id);
      
        return view('creator.admin.users.customers.show', compact('user'));
        
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $admin       = Auth::user();
        $jobRoles    = JobRole::where('status', 1)->orderBy('name')->get();  
        $groups      = Group::where('status', 1)->orderBy('name')->get(); 
        $user        = User::with(['roles', 'country', 'region', 'createdBy'])->find($id);
        $where       =  [
                         'country_id' => $admin->country_id              
        ];
        $dealers = User::whereHas (
                                'roles', function($q){
                                    $q->where('name', 'dealer');
                                }
                    )->where($where)->whereIn('region_id', explode(',', $admin->region_id))->orderBy('name')->get();   
        $regions = $admin->adminRegions();                      
        return view('creator.admin.users.customers.edit', compact('user', 'jobRoles', 'groups', 'dealers', 'regions'));
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
        $this->validate($request, [
                'id'         => 'required|exists:users,id',
                'name'       => 'required',
                'email'      => 'required|email|unique:users,email,'.$id,
                'dealer_id'  => 'required|exists:users,id'
        ]);
       
        $job_role_id = $request->job_role_id == 0 ?  NULL : $request->job_role_id;      
        $group_id = $request->group_id == 0 ?  NULL : $request->group_id;

        $admin = Auth::user();
        $user = new User();
        $update = [
                    'name' => $request->name,
                    'email' => $request->email,
                    'dealer_id' => $request->dealer_id,
                    'country_id' => $admin->country_id,
                    'region_id' => $request->region_id,
                    'job_role_id' => $job_role_id,
                    'group_id' => $group_id,
                    'remarks' => $request->remarks
        ];

        $isUpdated = User::where('id', $request->id)
                         ->update($update);

        if($isUpdated == 0)
        {
            throw new \Exception(\Lang::get('lang.unable-to-update')); 
        }

        return redirect('/admin/users')
                         ->with('success', \Lang::get('lang.user') .' '. \Lang::get('lang.updated-successfully')); 
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

            // delete user from user_learning_paths
            $userLearningPaths = UserLearningPath::where('user_id', $id)->get();
           
            if(count($userLearningPaths)) {
                UserLearningPath::where('user_id', $id)->delete();
            }
            
            // delete user from user_point_history
            $userPointHistory = UserPointHistory::where('user_id', $id)->get();
            if(count($userPointHistory)) {
                UserPointHistory::where('user_id', $id)->delete();
            }

            // delete user from featured_trainees
            $featuredTrainee = FeaturedTrainee::where('user_id', $id)->get();
            if(count($featuredTrainee)) {
                FeaturedTrainee::where('user_id', $id)->delete();
            }

            // delete user from user_learning_progress
            $userLearningProgress = UserLearningProgress::where('user_id', $id)->get();
            if(count($userLearningProgress)) {
                UserLearningProgress::where('user_id', $id)->delete();
            }

            //soft delete
            $isDeleted = User::where('id', $user->id)->update(['status' => 0, 'email' => $user->email.'-'.time()]);
            if($isDeleted == 0){
                return response()->json(['success' => false, "messsage" =>  \Lang::get('lang.generic-error')], 200);
            }

            return response()->json(['success' => true, "messsage" =>  \Lang::get('lang.staff-delete')], 200);
        } catch (Exception $e){
            Log::error($e);
            return response()->json(['success' => false, "messsage"=> \Lang::get('lang.generic-error')], 200);
        }  
    }
}
