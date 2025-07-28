<?php

namespace App\Exports;
use Auth;
use DB;
use App\Models\{User, Role, UserLearningProgress};
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Events\AfterSheet;

class LearningPathResponseExport implements FromCollection, WithHeadings, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $countryId;
    protected $regionId;
    protected $headings;

    function __construct( $country = null, $region = null, $role = null, $id, $headings) {
        $this->countryId = $country;
        $this->regionId  = $region;
        $this->roleId  = $role;
        $this->id = $id;
        $this->headings = $headings;
    }

    public function collection()
    {

        $data = array();
        $countryId = $this->countryId;
        $regionId = $this->regionId;
        $roleId = $this->roleId;
        $id = $this->id;

        $query = "select u.name as name, IFNULL(dealer.name, 'N/A') as dealer, IFNULL(g.name, 'N/A') as group_name, JSON_EXTRACT(ulp.cmi_data, '$.core.score.raw') as score ,JSON_EXTRACT(ulp.cmi_data, '$.interactions') as result from  user_learning_progress ulp join users u on ulp.user_id = u.id join model_has_roles mhr on u.id = mhr.model_id join roles role on mhr.role_id = role.id left join users dealer on u.dealer_id = dealer.id left outer join `groups` g on g.id = u.group_id where ulp.learning_resource_id = " .$id;

        if(Auth::user()->roles[0]->name == 'admin') {
            $regionIds = Auth::user()->region_id;
            $query .= " and u.region_id in ( " . $regionIds . ")";
        } else {
            if($countryId != 0 && $countryId != -1)
            {
                $query .= " and u.country_id =" . $countryId;
            }

            if($regionId != 0 && $regionId != -1)
            {
                $query .= " and u.region_id =" . $regionId;
            }
        }

        if($roleId != 0 && $roleId != -1)
        {
            $query .= " and mhr.role_id =" . $roleId;
        }
        $responses =  DB::select($query);

        $i = 0;
        foreach($responses as $response)
        {
            $j = 0 ;
            $data[$i]['name'] = ucfirst($response->name);
            $data[$i]['dealer'] = ucfirst($response->dealer);
            $data[$i]['group_name'] = ucfirst($response->group_name);

            if( is_null($response->score)) {
                $data[$i]['score'] = "N/A";
            } else {
                $data[$i]['score'] = json_decode($response->score);
            }

            if( is_null($response->result)) {
                $data[$i][$j] = "N/A";
                $data[$i][$j] = "N/A";

            } else {
                $interactions = json_decode($response->result);
                foreach ($interactions->childArray as $interaction)
                {
                    $data[$i][$j++] = $interaction->id;
                    $data[$i][$j++] = $interaction->student_response;
                    $data[$i][$j++] = $interaction->result;
                }
            }
            $i++;
        }

        return collect($data);
    }

    public function headings(): array
    {
        return $this->headings;
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
                $cellRange = 'A1:BC1'; // All headers
                $event->sheet->getDelegate()->getStyle('A1:BC1')->getFont()->setBold(true);
            },
        ];
    }
}
