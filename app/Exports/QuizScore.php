<?php

namespace App\Exports;

use Auth;
use DB;
use App\Models\{Quiz, UserQuiz};
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Events\AfterSheet;

class QuizScore implements FromCollection, WithHeadings, WithEvents
{

    function __construct( $country = null, $region = null, $group = null, $jobRole = null, $quizId = null ) 
    {
        $this->countryId  = $country;
        $this->regionId   = $region;
        $this->groupId    = $group;
        $this->jobRoleId  = $jobRole;
        $this->quizId     = $quizId;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $request = new Request();
        $countryId = $this->countryId;
        $regionId = $this->regionId;
        $groupId = $this->groupId;
        $jobRoleId = $this->jobRoleId;
        $quizId = $this->quizId;

        $query = "select q.name as quiz_name, u.name as user, IFNULL(c.name, 'N/A') as country, IFNULL(r.name, 'N/A') as region, IFNULL(j.name, 'N/A') as jobrole, IFNULL(g.name, 'N/A') as quiz_group, CAST(uq.score AS CHAR) as qscore from `quizzes` q JOIN `user_quizzes` uq on q.id = uq.quiz_id JOIN `users` u on uq.user_id = u.id LEFT OUTER JOIN `countries` c on u.country_id = c.id LEFT OUTER JOIN `regions` r on u.region_id = r.id  LEFT OUTER JOIN  `job_roles` j on u.job_role_id = j.id LEFT OUTER JOIN  `groups` g on u.group_id = g.id where q.status = 1 and q.id= $quizId";
        
                if($countryId != 0) {
                    $query .= " and u.country_id in (" . $countryId . ")";
                } 
                    
                if($regionId != 0) {
                    $query .= " and u.region_id in (" . $regionId . ")";
                } 
    
                if($jobRoleId != 0 && $jobRoleId != -1)
                {
                    $query .= " and u.job_role_id in (" . $jobRoleId . ")";
                }
    
                if($groupId != 0 && $groupId != -1)
                {
                    $query .= " and u.group_id in (" . $groupId. ")";
                }  
                
        $scores = DB::select($query);
        return collect($scores);         

    }


    public function headings(): array
    {
        return [
            "Quiz Name",
            "Username",
            "User Organisation",
            "User Entity",
            "User Role",
            "User Group",
            "Score"
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
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(11)->setBold(true);
            },
        ];
    }
}
