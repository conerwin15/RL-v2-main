<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupMapping extends Model
{
    use HasFactory;
    protected $table="group_mapping";


    public function jobRoleName(){
    	return $this->belongsTo(JobRole::class,'job_role_id');
    }
}
