<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;


    public function jobRole()
    {
    	return $this->hasMany('App\Models\GroupMapping', 'group_id');
    }
}
