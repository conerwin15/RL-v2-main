<?php

namespace App\Http\Controllers\Creator\Superadmin;


use Hash;
use App\Models\Country;
use App\Models\Region;
use App\Models\JobRole;
use App\Models\Group;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ImportUserResource extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('creator.superadmin.import-users.create');
    }

    public function importUser(Request $request, $type) {

        $roles = Role::pluck('name')->all();
        $roleExist = in_array($type, $roles);
        $file;

        if($roleExist) {

            if($type == 'dealer') {
                request()->validate([
                    'dealer_file' => 'required',
                ]);
                $file = $request->file('dealer_file');
            } else {
                request()->validate([
                    'staff_file' => 'required'
                ]);
                $file = $request->file('staff_file');
            }

            $dataAll = fopen($file, "r");
            $column = fgetcsv($dataAll);
            $importRecordCount = 0;

            $failedRecordCount = 0;
            $failedFor = [];

            while(!feof($dataAll)) {
                $usersData[] = fgetcsv($dataAll);
            }

            foreach ($usersData as $key => $value) {

                if(!empty($value)) {

                    //check valid name
                    $name = trim($value[0]);
                    if(strlen($name) == 0) {
                        $failedRecordCount++;
                        array_push($failedFor, $email);
                        continue;
                    }

                    //check valid email
                    $email = trim($value[1]);
                    if(strlen($email) == 0) {
                        $failedRecordCount++;
                        array_push($failedFor, $email);
                        continue;
                    }

                    // check User already exist
                    $existUser = User::where('email', $email)->first();
                    if($existUser != null){
                        $failedRecordCount++;
                        array_push($failedFor, $email);
                        continue;
                    }

                    //check valid country
                    $country = trim($value[2]);
                    if(strlen($country) == 0) {
                        $failedRecordCount++;
                        array_push($failedFor, $email);
                        continue;
                    }

                    // check country exist
                    $countryFromDB = Country::where('name', $country)->first();
                    if($countryFromDB == null) {
                        $failedRecordCount++;
                        array_push($failedFor, $email);
                        continue;
                    }

                    //check valid region
                     $region = trim($value[3]);
                     if(strlen($region) == 0) {
                         $failedRecordCount++;
                         array_push($failedFor, $email);
                         continue;
                     }

                    // check region exist
                    $regionFromDB = Region::where('name', $region)->where('country_id', $countryFromDB->id)->first();
                    if($regionFromDB == null) {
                        $failedRecordCount++;
                        array_push($failedFor, $email);
                        continue;
                    }

                    // check group exist
                    $group = trim($value[4]);
                    $groupFromDB = null;
                    if($group != null) {
                        $groupFromDB = Group::where('name', $group)->first();
                        if($groupFromDB == null) {
                            $failedRecordCount++;
                            array_push($failedFor, $email);
                            continue;
                        }
                    }

                    // check job role exist
                    $jobRole = trim($value[5]);
                    $jobRoleFromDB = null;
                    if($jobRole != null) {
                        $jobRoleFromDB = JobRole::where('name', $jobRole)->first();
                        if($jobRoleFromDB == null) {
                            $failedRecordCount++;
                            array_push($failedFor, $email);
                            continue;
                        }
                    }


                    $input['name'] = $name;
                    $input['email'] = $email;
                    $input['password'] = Hash::make('123456');
                    $input['country_id'] = $countryFromDB->id;
                    $input['region_id'] = $regionFromDB->id;
                    $input['group_id'] = $groupFromDB == null ? null : $groupFromDB->id;
                    $input['job_role_id'] = $jobRoleFromDB == null ? null : $jobRoleFromDB->id;

                    $role = Role::where('name', $type)->first();

                    if($type == 'staff') {

                        $dealer = $value[6];
                        if(strlen($dealer) == 0) {
                            $failedRecordCount++;
                            array_push($failedFor, $email);
                            continue;
                        }

                        $delearFromDB = User::where('email', $dealer)->first();
                        if($delearFromDB == null) {
                            $failedRecordCount++;
                            array_push($failedFor, $email);
                            continue;
                        }

                        $input['dealer_id'] = $delearFromDB->id;
                    }

                    $user = User::create($input);
                    $user->assignRole($role->name);
                    $importRecordCount++;
                }
            }

            return view('creator.superadmin.import-users.create' , compact('failedRecordCount', 'importRecordCount', 'failedFor'));

        } else {

            return redirect()->back()->with('error', \Lang::get('lang.invalid-role'));
        }

    }

}

