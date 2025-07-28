<?php

namespace App\Exports;

use Auth;
use DB;
use App\Models\{User, Role};
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Events\AfterSheet;

class LearnerExport implements FromCollection, WithHeadings, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $countryId;
    protected $regionId;

    
    function __construct( $country = null, $region = null, $dealer = null, $group = null, $jobRole = null, $learningPathId = null ) {
        $this->countryId = $country;
        $this->regionId  = $region;
        $this->dealerId  = $dealer;
        $this->groupId  = $group;
        $this->jobRoleId  = $jobRole;
        $this->learningPathId  = $learningPathId;
}

    public function collection()
    {
       
        $request = new Request();
        $countryId = $this->countryId;
        $regionId = $this->regionId;
        $groupId = $this->groupId;
        $jobRoleId = $this->jobRoleId;
        $dealerId = $this->dealerId;
        $learningPathId = $this->learningPathId;


        $superadminRole = Role::where('name', 'superadmin')->first();

        $query = "select u.name as uname, u.email, role.name, lp.name as lpname, CONCAT(IFNULL(ulp.progress_percentage, 0), ' %'), DATE_FORMAT(ulp.start_date, '%d/%m/%Y') as sdate, DATE_FORMAT(ulp.end_date,'%d/%m/%Y') as edate, CONCAT(IFNULL(DATEDIFF(ulp.end_date, ulp.start_date), null), ' days' )as duration, c.name as cname, r.name as rname, IFNULL(dealer.name, 'N/A') as dname, IFNULL(gr.name, 'N/A') as grname,  IFNULL(jr.name, 'N/A') as jrname, createdBy.name as createdBy from users u left outer join user_learning_paths ulp on 
        u.id = ulp.user_id left outer join learning_paths lp on lp.id = ulp.learning_path_id join countries c on u.country_id = c.id join regions r on u.region_id = r.id  left join `groups` gr on u.group_id = gr.id left join job_roles jr on u.job_role_id = jr.id  left join users dealer on u.dealer_id = dealer.id join users createdBy on u.created_by = createdBy.id 
        join model_has_roles mhr on u.id = mhr.model_id join roles role on mhr.role_id = role.id where u.status = 1 and role.id not in (" . $superadminRole->id . ")";

        if($jobRoleId != 0)
        {
            $query .= " and u.job_role_id in ( " . $jobRoleId . ")";
        }

        if($groupId != 0)
        {
            $query .= " and u.group_id in ( " . $groupId . ")";
        }

        if($learningPathId != 0)
        {
            $query .= " and lp.id in ( " . $learningPathId . ")";
        }

        if(Auth::user()->roles[0]->name == 'admin') {
            $regionIds = Auth::user()->region_id;
            $query .= " and u.region_id in ( " . $regionIds . ")";
        } else if(Auth::user()->roles[0]->name == 'dealer') {
            $userId = Auth::user()->id;
            $query .= " and u.dealer_id in ( " . $userId . ")";
        } else {
            if($countryId != 0) {
                $query .= " and u.country_id in ( " . $countryId . ")";
            } 
              
            if($regionId != 0) {
                $query .= " and u.region_id in ( " . $regionId . ")";
            } 

            if($dealerId != 0)
            {
                $query .= " and u.dealer_id in ( " . $dealerId . ")";
            }

            if($groupId != 0)
            {
                $query .= " and u.group_id in ( " . $groupId. ")";
            }
        }
    
        $learners = DB::select($query);
        return collect($learners);
    }

    public function headings(): array
    {
        return [
            "Name",
            "Email",
            "Profile",
            "Learning Path Name",
            "Completion Rate",
            "Start Date",
            "End Date",
            "Duration",
            "Organisation",
            "Entity",
            "Head",
            "Group",
            "Role",
            "CreatedBy"
        ];
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:J1'; // All headers
                $event->sheet->getDelegate()->getStyle('A1:N1')->getFont()->setBold(true);
            },
        ];
    }

}
