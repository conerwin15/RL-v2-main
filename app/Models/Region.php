<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends BaseModel
{
    use HasFactory;
    protected $table = "regions";

    public function country()
    {
       return $this->belongsTo('App\Models\Country');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function createdBy() {
        return $this->belongsTo('App\Models\User', 'created_by');
    }
}
