<?php

namespace App\Http\Controllers\Creator\TrainingAdmin;

use Auth;
use Mail;
use Validator;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Models\{User, JobRole, Role, Country, Region, Group, ScheduledEmailJob, ScheduledEmailUser};

class ManualEmailController extends Controller
{
    // send mail manually
    public function sendMail (Request $request)
    {
        $role      = array('dealer', 'staff');
        $jobRoles  = JobRole::where('status', 1)->orderBy('name')->get(); 
        $groups    = Group::where('status', 1)->orderBy('name')->get();
        $roles     = Role::orderBy('name')->whereIn('name', $role)->get();
        $user      = Auth::user();
        $regions   = $user->adminRegions();
        $currentDate = date('Y-m-d H:i:s');
        $endDate = date('Y-m-d', strtotime("+1 day", strtotime($currentDate)));
        return view('creator.admin.manual-emails.index', compact('regions', 'jobRoles', 'groups', 'roles', 'currentDate', 'endDate'));
    }


    public function mailList (Request $request)
    {
        $admin = Auth::user();
        if ($request->ajax())
        {
            $roleNamesToExclude = ['superadmin', 'admin'];
            $userQuery= User::where('status',1)->whereIn('region_id', explode(',', $admin->region_id));
            
            if(count($roleNamesToExclude) > 0) {
                $userQuery->whereHas("roles", function($query) use($roleNamesToExclude) { 
                    $query->whereNotIn('name', $roleNamesToExclude);
                   
                });
            }
              
            if(!empty($request->filter_region) && $request->filter_region != -1) {
                $userQuery = $userQuery->whereRaw("FIND_IN_SET($request->filter_region, region_id)");
            } 

            if(!empty($request->filter_jobrole) && $request->filter_jobrole != -1) {
                $userQuery->where(function($query) use($request)
                {
                    $query->where('job_role_id', $request->filter_jobrole)
                    ->orWhereNull('job_role_id');
                });
            }

            if(!empty($request->filter_group) && $request->filter_group != -1) {
                $userQuery = $userQuery->where(function($query) use($request)
                {
                    $query->where('group_id', $request->filter_group)
                    ->orWhereNull('group_id');

                });
            }

            if(!empty($request->filter_role) && $request->filter_role != -1) {
                $role = $request->filter_role;
                $userQuery = $userQuery->whereHas("roles", function($query) use($role)
                            { 
                                $query->where("id", $role); 
                                
                            });
            } 

           $user =  $userQuery->orderBy('name')->get();
       
            return Datatables::of($user, $request)
                    ->addIndexColumn()
                    ->addColumn('checkbox', function ($user) {
                        $checkbox = "  <input type='checkbox' name='learners[]' class='check_learner select-checkbox' value='$user->email' >  ";
                        return $checkbox;
                    })

                    ->editColumn('dealer', function($user)
                    {
                       return ($user->dealer_id == null) ? 'N/A' : ucfirst($user->getNameById($user->dealer_id));
                    })
                    ->editColumn('jobRole', function($user)
                    {
                       return $user->jobRole ? ucwords($user->jobRole->name) : "N/A";
                    })
                    ->editColumn('region', function($user)
                    {
                       return $user->region_id ? $user->region->name : "N/A";
                    })
                    ->editColumn('role', function($user)
                    {
                       return ucfirst(toRoleLabel($user->getRoleNames()->first()));
                    })
                    ->editColumn('group', function($user)
                    {
                        return  $user->group_id ? ucwords($user->group->name) : "N/A";
                    })
                   
                    ->rawColumns(['checkbox'])
                    ->make(true);
        }
    }

    public function sendMailUsers (Request $request) 
    {
        $learners = explode(',', $request->learners_mail);
          
        request()->validate([
            'subject'     => 'required|min:2',
            'description' => 'required|min:6',
            'learners_mail' => 'required'
        ]);

        $subject = $request->subject;
        foreach ($learners as $learner) {

            Mail::raw($request->description, function ($m)  use ($learner, $subject) {
                $m->to($learner);  
                $m->subject($subject);
            });
        }

        return back()->with('success', \Lang::get('lang.mail-send'));        
    }


    /**
     * Schedule mail list
     */

    public function scheduledMails (Request $request) {

        $scheduleMails = ScheduledEmailJob::where('created_by', Auth::user()->id)->get();

        if ($request->ajax())
        {
            return Datatables::of($scheduleMails, $request)
                    ->addIndexColumn()
                    ->editColumn('campaign_name', function($scheduleMails)
                    {
                       return $scheduleMails->name;
                    })
                    ->editColumn('status', function($scheduleMails)
                    {
                       return ($scheduleMails->is_processed == false) ? \Lang::get('lang.scheduled') : \Lang::get('lang.completed');
                    })
                    ->editColumn('scheduled_at', function($scheduleMails)
                    {
                       return  date('F d Y', strtotime($scheduleMails->created_at));
                    })
                    ->editColumn('recurrence', function($scheduleMails)
                    {
                       return ($scheduleMails->frequency == 'once') ? \Lang::get('lang.once') : \Lang::get('lang.repeat');
                    })
                    ->addColumn('action', function($scheduleMails){
                        $href = 'mail/'. $scheduleMails->id;
                        $deleteHref = $href . '/delete';
                        $view = \Lang::get('lang.view');
                        $delete = \Lang::get('lang.delete');
                        if($scheduleMails->is_processed == 1)
                        {
                            $completed = \Lang::get('lang.completed');
                            $actionBtn = "<a href='$href'><i class='fa fa-eye' aria-hidden='true'></i> $view </a><span class='text-success'> $completed </span> &nbsp;<button type='button' class='text-danger delete-user' data-href='$deleteHref' data-role='superadmin'><i class='fa fa-trash' aria-hidden='true'></i> $delete </button>";
                            return $actionBtn;
                        } else {
                            $editHref = 'mail/'. $scheduleMails->id . '/edit';
                            $edit = \Lang::get('lang.edit');
                            $actionBtn = "<a href='$href'><i class='fa fa-eye' aria-hidden='true'></i> $view </a><a href='$editHref'><i class='fa fa-pencil' aria-hidden='true'></i> &nbsp;$edit </a><button type='button' class='text-danger delete-user' data-href='$deleteHref' data-role='superadmin'><i class='fa fa-trash' aria-hidden='true'></i> $delete </button>";
                            return $actionBtn;
                        }

                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('creator.admin.manual-emails.scheduled-mail');
    }

    /**
     * Schedule mail for users
     */
    public function scheduledMailUsers (Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(), [
                'subject'     => 'required|min:2',
                'description' => 'required|min:6',
                'learners'    => 'required',
                'campaign'    => 'required|min:4',
                'start_date'  => 'required'
            ]);

            if (!$validator->passes()) {
                return response()->json(['success' => false, "message" => $validator->errors()], 200);
            } else {
                $startTime = Date('H:i:s', strtotime($request->start_date));
                $endDate = Date('Y-m-d H:i:s', strtotime("$request->end_date $startTime")); // combine end date with start date time
                $startDateTimeUTC = $this->convertTimeZoneToUTC($request->start_date);
                $learnerIds = explode(',', $request->learners_id);
                $schedulemail = new ScheduledEmailJob;
                $schedulemail->name = $request->campaign;
                $schedulemail->subject = $request->subject;
                $schedulemail->description = $request->description;
                $schedulemail->start_date = $startDateTimeUTC;
                $schedulemail->next_run_at = $startDateTimeUTC;
                $schedulemail->frequency = $request->recurrence;
                $schedulemail->created_by = Auth::user()->id;
                if($request->recurrence == 'every')
                {
                    $nextRunDate = Date('Y-m-d H:i:s', strtotime($request->start_date. ' + '.$request->frequency_amt.' ' .$request->unit));
                    if($nextRunDate > Date('Y-m-d H:i:s', strtotime($endDate)))
                    {
                        return response()->json(['success' => false, "nextRunDateError" => \Lang::get('lang.end-date-error')], 200);
                    }
                    $endDateTimeUTC = $this->convertTimeZoneToUTC($endDate);
                    $schedulemail->frequency_unit = $request->unit;
                    $schedulemail->frequency_amount = $request->frequency_amt;
                    $schedulemail->audience_type = 'users';
                    $schedulemail->end_date = $endDateTimeUTC;
                }

                $schedulemail->save();
                $jobId = $schedulemail->id;
                foreach ($learnerIds as $learner)
                {
                    $scheduleUser = new ScheduledEmailUser;
                    $scheduleUser->job_id = $jobId;
                    $scheduleUser->user_id = $learner;
                    $scheduleUser->save();
                }
                return response()->json(['success' => true, 'message' => \Lang::get('lang.mail-scheduled-successfully')], 200);
            }
        } catch (Exception $ex)
        {
            Log::error($ex);
        }
    }

    /**
     * Edit schedule Mail Record
     */
    public function editScheduledMail(Request $request, $id)
    {
        $scheduleMailJobs = ScheduledEmailJob::findOrFail($id);
        $userArray = ScheduledEmailUser::where('job_id', $id)->pluck('user_id')->toArray();
        $scheduleMailJobs = ScheduledEmailJob::findOrFail($id);
        $currentDate = date('Y-m-d H:i:s');
        $endDate = date('Y-m-d', strtotime("+1 day", strtotime($currentDate)));
        $startDate = $this->convertTimeZoneToIST($scheduleMailJobs->start_date);
        $endRunAt =  Date('Y-m-d', strtotime($this->convertTimeZoneToIST($scheduleMailJobs->end_date)));
        $role      = array('dealer', 'staff');
        $admin      = Auth::user();
        $regions   = $admin->adminRegions();
        $jobRoles  = JobRole::where('status', 1)->orderBy('name')->get();
        $groups    = Group::where('status', 1)->orderBy('name')->get();
        $roles     = Role::orderBy('name')->whereIn('name', $role)->get();

        $userIds = implode(",", $userArray);

        if ($request->ajax())
        {
            $roleNamesToExclude = ['superadmin', 'admin'];
            $userQuery= User::where('status', 1)->whereIn('region_id', explode(',', $admin->region_id));;

            if(count($roleNamesToExclude) > 0) {
                $userQuery->whereHas("roles", function($query) use($roleNamesToExclude) {
                    $query->whereNotIn('name', $roleNamesToExclude);

                });
            }

            if(!empty($request->filter_country) && $request->filter_country != -1) {
                $userQuery = $userQuery->where('country_id', $request->filter_country);
            }

            if(!empty($request->filter_region) && $request->filter_region != -1) {
                $userQuery = $userQuery->whereRaw("FIND_IN_SET($request->filter_region, region_id)");
            }

            if(!empty($request->filter_jobrole) && $request->filter_jobrole != -1) {
                $userQuery->where(function($query) use($request)
                {
                    $query->where('job_role_id', $request->filter_jobrole)
                    ->orWhereNull('job_role_id');
                });
            }

            if(!empty($request->filter_group) && $request->filter_group != -1) {
                $userQuery = $userQuery->where(function($query) use($request)
                {
                    $query->where('group_id', $request->filter_group)
                    ->orWhereNull('group_id');

                });
            }

            if(!empty($request->filter_role) && $request->filter_role != -1) {
                $role = $request->filter_role;
                $userQuery = $userQuery->whereHas("roles", function($query) use($role)
                            {
                                $query->where("id", $role);

                            });
            }


           $user =  $userQuery->orderBy('name')->get();

            return Datatables::of($user, $userArray, $request)
                    ->addIndexColumn()
                    ->addColumn('checkbox', function ($user) use ($userArray){
                        if(in_array($user->id, $userArray))
                        {
                            $checkbox = " <input type='checkbox' name='learners[]' class='check_learner select-checkbox' value='$user->id' checked>  ";
                        } else {

                            $checkbox = " <input type='checkbox' name='learners[]' class='check_learner select-checkbox' value='$user->id' >  ";
                        }
                        return $checkbox;
                    })

                    ->editColumn('dealer', function($user)
                    {
                       return ($user->dealer_id == null) ? 'N/A' : ucfirst($user->getNameById($user->dealer_id));
                    })
                    ->editColumn('jobRole', function($user)
                    {
                       return $user->jobRole ? ucwords($user->jobRole->name) : "N/A";
                    })
                    ->editColumn('region', function($user)
                    {
                       return $user->region_id ? $user->region->name : "N/A";
                    })
                    ->editColumn('role', function($user)
                    {
                       return ucfirst(toRoleLabel($user->getRoleNames()->first()));
                    })
                    ->editColumn('group', function($user)
                    {

                        return  $user->group_id ? ucwords($user->group->name) : "N/A";

                    })

                    ->rawColumns(['checkbox'])
                    ->make(true);
        }
        return view('creator.admin.manual-emails.edit', compact('id', 'scheduleMailJobs', 'currentDate', 'endDate', 'startDate', 'endRunAt', 'regions', 'jobRoles', 'groups', 'roles', 'role', 'userIds'));
    }

    /**
     * Edit schedule Mail Record
     */
    public function updateScheduledMail (Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(), [
                'schedule_id' => 'required|exists:scheduled_email_jobs,id',
                'subject'     => 'required|min:2',
                'description' => 'required|min:6',
                'campaign'    => 'required|min:4',
                'start_date'  => 'required'
            ]);

            if (!$validator->passes()) {
                return response()->json(['success' => false, "message" => $validator->errors()], 200);
            } else {

                $scheduleMail = ScheduledEmailJob::findOrFail($request->schedule_id);
                $startDateTimeUTC = $this->convertTimeZoneToUTC($request->start_date);
                $update = [
                            'subject'     => $request->subject,
                            'description' => $request->description,
                            'name'    => $request->campaign,
                            'next_run_at' => $startDateTimeUTC,
                            'start_date' => $startDateTimeUTC
                        ];
                if($scheduleMail->frequency == 'every')
                {
                    $startTime = Date('H:i:s', strtotime($request->start_date));
                    $endDate = Date('Y-m-d H:i:s', strtotime("$request->end_date $startTime")); // combine end date with start date time
                    $nextRunDate = Date('Y-m-d H:i:s', strtotime($request->start_date. ' + '.$request->frequency_amt.' ' .$request->unit));
                    if($nextRunDate > Date('Y-m-d H:i:s', strtotime($endDate)))
                    {
                        return response()->json(['success' => false, "nextRunDateError" => \Lang::get('lang.end-date-error')], 200);
                    }
                    $endtDateTimeUTC = $this->convertTimeZoneToUTC($endDate);
                    $update = [
                        'subject'    => $request->subject,
                        'description' => $request->description,
                        'name'    => $request->campaign,
                        'next_run_at' => $startDateTimeUTC,
                        'start_date' => $startDateTimeUTC,
                        'end_date'    => $endtDateTimeUTC,
                        'frequency_amount' => $request->frequency_amt,
                        'frequency_unit' => $request->unit

                    ];
                }
               $isUpdated = ScheduledEmailJob::where('id', $request->schedule_id)->update($update);
               if($isUpdated == 0)
               {
                  return response()->json(['success' => true, "message" => \Lang::get('lang.country-not-updated')], 500);
               }

               /** update users **/
               $isDeleted = ScheduledEmailUser::where('job_id', $request->schedule_id)->delete(); // delete users
               $existingLearners = explode(',', $request->existing_learners);
               $newLearners = explode(',', $request->new_learners);
               $removeLearners = explode(',', $request->removed_learners);
               $finalLearners = array_diff(array_merge($existingLearners, $newLearners), $removeLearners);

               //insert users
               foreach ($finalLearners as $learner)
                {
                    $scheduleUser = new ScheduledEmailUser;
                    $scheduleUser->job_id = $request->schedule_id;
                    $scheduleUser->user_id = $learner;
                    $scheduleUser->save();
                }

               return response()->json(['success' => true, "message" => \Lang::get('lang.schedule-mail-updated-successfully')], 200);
            }
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['success' => false, "message"=> \Lang::get('lang.generic-error')], 200);
        }
    }

    /**
     * Delete schedule mail
     */

    public function deleteScheduledMail ($id)
    {
        try
        {
            $mail = ScheduledEmailJob::findOrFail($id);
            $isDeleted = $mail->delete();
            if($isDeleted == 0)
                {
                    return response()->json(['success' => false, 'messsage' => \Lang::get('lang.unable-to-delete')], 200);
                }

                return response()->json(['success' => true, 'messsage' => \Lang::get('lang.scheduled-mail-deleted-successfully')], 200);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['success' => false, "messsage"=> \Lang::get('lang.generic-error')], 200);
        }
    }

     /****
     * view Scheduled Mail
     */
    public function viewScheduledMail (Request $request, $id)
    {
        $scheduleJob = ScheduledEmailJob::findOrFail($id);
        $startDate = $this->convertTimeZoneToIST($scheduleJob->start_date);
        $endRunAt =  $this->convertTimeZoneToIST($scheduleJob->end_date);
        $user = User::whereHas('scheduledUsers', function($q) use ($id){
                    $q->where('job_id', $id);
                })->get();

        if ($request->ajax())
        {
            return Datatables::of($user, $request)
            ->addIndexColumn()
            ->editColumn('region', function($user)
            {
               return $user->region_id ? $user->region->name : "N/A";
            })
            ->editColumn('role', function($user)
            {
               return ucfirst(toRoleLabel($user->getRoleNames()->first()));
            })
            ->editColumn('dealer', function($user)
            {
               return ($user->dealer_id == null) ? 'N/A' : ucfirst($user->getNameById($user->dealer_id));
            })
            ->editColumn('jobRole', function($user)
            {
               return $user->jobRole ? ucwords($user->jobRole->name) : "N/A";
            })
            ->editColumn('group', function($user)
            {

                return  $user->group_id ? ucwords($user->group->name) : "N/A";

            })

            ->make(true);
        }
        return view('creator.admin.manual-emails.view', compact('id', 'scheduleJob', 'startDate', 'endRunAt'));
    }

    public function convertTimeZoneToUTC ($dateTime)
    {
        $timezone = 'Asia/Kolkata';
        $newDateTime = new DateTime($dateTime, new DateTimeZone($timezone));
        $newDateTime->setTimezone(new DateTimeZone("UTC"));
        $dateTimeUTC = $newDateTime->format("Y-m-d H:i:s");
        return $dateTimeUTC;
    }

    public function convertTimeZoneToIST ($dateTime)
    {
        $timezone = 'UTC';
        $newDateTime = new DateTime($dateTime, new DateTimeZone($timezone));
        $newDateTime->setTimezone(new DateTimeZone("Asia/Kolkata"));
        $dateTimeUTC = $newDateTime->format("Y-m-d H:i:s");
        return $dateTimeUTC;
    }
}
