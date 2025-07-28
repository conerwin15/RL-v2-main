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

class LearningPathProgressExport implements FromCollection, WithHeadings, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $countryId;
    protected $regionId;

    function __construct( $country = null, $region = null, $role = null, $group = null, $jobRole = null ) {
        $this->countryId = $country;
        $this->regionId  = $region;
        $this->roleId  = $role;
        $this->groupId  = $group;
        $this->jobRoleId  = $jobRole;
    }

    public function collection()
    {

        $request = new Request();
        $countryId = $this->countryId;
        $regionId = $this->regionId;
        $groupId = $this->groupId;
        $jobRoleId = $this->jobRoleId;
        $roleId = $this->roleId;

        $superadminRole = Role::where('name', 'superadmin')->first();

        $query = "select u.name, u.email, role.name as role, lp.name as learning_path_name, DATE_FORMAT(ulp2.start_date, '%d/%m/%Y') as sdate, DATE_FORMAT(ulp2.end_date,'%d/%m/%Y') as edate, CONCAT(IFNULL(DATEDIFF(ulp2.end_date, ulp2.start_date), null), ' days' )as duration, lpr.title as resource,
                    CASE
                        WHEN  lpr.type = 'media_link' THEN 'Media'
                        WHEN  lpr.type = 'course_link' THEN 'Course'
                        ELSE 'Chatbot'
                    END as resorec_type,
                    CASE
                    WHEN ulp.id IS NOT NULL
                    THEN
                    case
                    when lpr.type = 'chatbot_link' or lpr.`type` = 'media_link' then 'completed'
                    else ulp.lesson_status
                    end
                    ELSE
                    case when ulp.id is null then 'incomplete' end
                    END
                    AS progress,
                    DATE_FORMAT(ulp.start_date, '%d/%m/%Y') as resource_start_date,
                    DATE_FORMAT(ulp.end_date, '%d/%m/%Y') as resource_end_date,

                    CASE
                    WHEN  lpr.type = 'media_link' THEN  '00:00:00'
                    WHEN  lpr.type = 'chatbot_link' THEN '00:00:00'
                    ELSE  time_format(JSON_EXTRACT(ulp.cmi_data, '$.core.session_time'), '%H:%i:%s')
                    END as scrom_duration,
                    ulp.max_score as max_score, ulp.score as score,
                    CONCAT(IFNULL(ulp2.progress_percentage, 0), ' %') as learning_path_status,
                    c.name as country, r.name as region, IFNULL(dealer.name, 'N/A') as dealer_name,
                    IFNULL(g.name, 'N/A') as group_name, IFNULL(jr.name, 'N/A') as job_role,
                    createdBy.name as createdBy
                    from users u
                    left outer join job_roles jr on u.job_role_id = jr.id
                    left outer join countries c on u.country_id = c.id
                    left outer join regions r on u.region_id = r.id
                    left join users dealer on u.dealer_id = dealer.id
                    left outer join `groups` g on g.id = u.group_id
                    join users createdBy on u.created_by = createdBy.id
                    join model_has_roles mhr on u.id = mhr.model_id join roles role on mhr.role_id = role.id
                    join user_learning_paths ulp2 on u.id = ulp2.user_id
                    join learning_paths lp on lp.id = ulp2.learning_path_id
                    join learning_path_resources lpr on ulp2.learning_path_id = lpr.learning_path_id
                    left outer join user_learning_progress ulp on ulp.learning_resource_id = lpr.id and ulp.user_id= u.id where 1";

        if($jobRoleId != 0)
        {
            $query .= " and u.job_role_id in ( " . $jobRoleId . ")";
        }

        if($groupId != 0)
        {
            $query .= " and u.group_id in ( " . $groupId . ")";
        }

        if($countryId != 0 && $countryId != -1) {
            $query .= " and u.country_id in ( " . $countryId . ")";
        }

        if($regionId != 0 && $regionId != -1) {
            $query .= " and u.region_id in ( " . $regionId . ")";
        }

        if($roleId != 0 && $roleId != -1)
        {
            $query .= " and mhr.role_id =" . $roleId;
        }


        $progress = DB::select($query);
        $data = array();
        $groupedData = [];

        for($i=0; $i< count($progress); $i++)
        {
            $email = $progress[$i]->email;
            $lp = $progress[$i]->learning_path_name;
            for($j=0; $j < count($progress); $j++)
            {
                if(($email == $progress[$j]->email) && ($lp == $progress[$j]->learning_path_name))
                {
                    $groupedData[$j]['name'] = $progress[$j]->name;
                    $groupedData[$j]['email'] = $progress[$j]->email;
                    $groupedData[$j]['role'] = $progress[$j]->role;
                    $groupedData[$j]['learning_path_name'] = $progress[$j]->learning_path_name;
                    $groupedData[$j]['sdate'] = $progress[$j]->sdate;
                    $groupedData[$j]['edate'] = $progress[$j]->edate;
                    $groupedData[$j]['duration'] = $progress[$j]->duration;
                    $groupedData[$j]['resource'] = $progress[$j]->resource;
                    $groupedData[$j]['resorec_type'] = $progress[$j]->resorec_type;
                    $groupedData[$j]['progress'] = $progress[$j]->progress;
                    $groupedData[$j]['resource_start_date'] = $progress[$j]->resource_start_date;
                    $groupedData[$j]['resource_end_date'] = $progress[$j]->resource_end_date;
                    $groupedData[$j]['scrom_duration'] = $progress[$j]->scrom_duration;
                    $groupedData[$j]['max_score'] = $progress[$j]->max_score;
                    $groupedData[$j]['score'] = $progress[$j]->score;
                    $groupedData[$j]['learning_path_status'] = $progress[$j]->learning_path_status;
                    $groupedData[$j]['country'] = $progress[$j]->country;
                    $groupedData[$j]['region'] = $progress[$j]->region;
                    $groupedData[$j]['dealer_name'] = $progress[$j]->dealer_name;
                    $groupedData[$j]['group_name'] = $progress[$j]->group_name;
                    $groupedData[$j]['job_role'] = $progress[$j]->job_role;
                    $groupedData[$j]['createdBy'] = $progress[$j]->createdBy;
                }
            }
        }

        for ($i=0; $i < count($groupedData); $i++) {

            $duration = '';
            $initial_email = $groupedData[$i]['email'];
            $initial_lp = $groupedData[$i]['learning_path_name'];

            for ($j=0 ; $j < count($groupedData) ; $j++) {
                if($initial_email == $groupedData[$j]['email'] && $initial_lp == $groupedData[$j]['learning_path_name'])
                {
                    $duration = date("H:i:s",strtotime($duration)+strtotime($groupedData[$j]['scrom_duration']));
                }
            }

            $data[$i]['name'] = $groupedData[$i]['name'];
            $data[$i]['email'] = $groupedData[$i]['email'];
            $data[$i]['role'] = $groupedData[$i]['role'];
            $data[$i]['learning_path_name'] = $groupedData[$i]['learning_path_name'];
            $data[$i]['sdate'] = $groupedData[$i]['sdate'];
            $data[$i]['edate'] = $groupedData[$i]['edate'];
            $data[$i]['duration'] = $duration;
            $data[$i]['resource'] = $groupedData[$i]['resource'];
            $data[$i]['resorec_type'] = $groupedData[$i]['resorec_type'];
            $data[$i]['progress'] = $groupedData[$i]['progress'];
            $data[$i]['resource_start_date'] = $groupedData[$i]['resource_start_date'];
            $data[$i]['resource_end_date'] = $groupedData[$i]['resource_end_date'];
            $data[$i]['scrom_duration'] = $groupedData[$i]['scrom_duration'];
            $data[$i]['max_score'] = $groupedData[$i]['max_score'];
            $data[$i]['score'] = $groupedData[$i]['score'];
            $data[$i]['learning_path_status'] = $groupedData[$i]['learning_path_status'];
            $data[$i]['country'] = $groupedData[$i]['country'];
            $data[$i]['region'] = $groupedData[$i]['region'];
            $data[$i]['dealer_name'] = $groupedData[$i]['dealer_name'];
            $data[$i]['group_name'] = $groupedData[$i]['group_name'];
            $data[$i]['job_role'] = $groupedData[$i]['job_role'];
            $data[$i]['createdBy'] = $groupedData[$i]['createdBy'];
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [
            "Name",
            "Email",
            "Role",
            "Learning Path Name",
            "Start Date of Learning Path",
            "End Date of Learning Path",
            "Duration of Learning Path",
            "Resources",
            "Resource Type",
            "Resource Status",
            "Start Date",
            "End Date",
            "Engagement Time",
            "Maximum Marks",
            "Quiz Score",
            "Learning Path Status",
            "Organisation",
            "Entity",
            "Head",
            "Group",
            "Role",
            "Created By"
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
                $event->sheet->getDelegate()->getStyle('A1:V1')->getFont()->setBold(true);
            },
        ];
    }
}
