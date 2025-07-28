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


class QuizScoreWithAnswer implements FromCollection, WithHeadings, WithEvents
{
    function __construct( $country = null, $region = null, $group = null, $jobRole = null, $quizId ) 
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

        $query = "select q.name as quizname, u.name as user, IFNULL(c.name, 'N/A') as country, IFNULL(r.name, 'N/A') as region, IFNULL(jr.name, 'N/A') as jobrole, IFNULL(g.name, 'N/A') as quizGroup,
                t.question, t.selections, CAST(t.points AS CHAR)  from quizzes q
                JOIN (
                select qq.id as qid, qq.question_text as question, qq.quiz_id as quizid , qs.earned_points as points , qs.user_id,  GROUP_CONCAT(qo.option_text, '') as selections
                from quiz_questions qq
                JOIN quiz_submissions qs on qq.id = qs.question_id
                JOIN user_quizzes uq on uq.user_id = qs.user_id  and uq.quiz_id = qs.quiz_id 
                JOIN question_options qo on FIND_IN_SET(qo.id, qs.selections)
                group by qq.id, qq.question_text, qs.user_id, qq.quiz_id, qs.earned_points)
                t on q.id = t.quizid
                join users u on u.id = t.user_id    
                left outer JOIN countries c on u.country_id  = c.id
                left OUTER JOIN regions r on u.region_id  = r.id 
                left OUTER JOIN  `groups` g on u.group_id = g.id 
                left OUTER JOIN  job_roles jr on u.job_role_id = jr.id 
                where q.status = 1 and q.id= $quizId ";
        
                if($countryId != 0) {
                    $query .= " and u.country_id in ( " . $countryId . ")";
                } 
                    
                if($regionId != 0) {
                    $query .= " and u.region_id in ( " . $regionId . ")";
                } 
    
                if($jobRoleId != 0 && $jobRoleId != -1)
                {
                    $query .= " and u.job_role_id in ( " . $jobRoleId . ")";
                }
    
                if($groupId != 0 && $groupId != -1)
                {
                    $query .= " and u.group_id in ( " . $groupId. ")";
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
            "Quiz Question",
            "Quiz Anwers",
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
                $cellRange = 'A1:I1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(11)->setBold(true);
            },
        ];
    }
}
