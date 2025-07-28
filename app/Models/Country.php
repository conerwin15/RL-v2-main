<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends BaseModel
{
    use HasFactory;
    protected $table = "countries";

    public function regions()
    {
       return $this->hasMany('App\Models\Region')->where('status','=', 1);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function createdBy() {
        return $this->belongsTo('App\Models\User', 'created_by');
    }

}
