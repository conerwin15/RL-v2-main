<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMapping extends Model
{
    use HasFactory;
    protected $table = "user_mappings";

    public function createUserDetails() {
         return $this->belongsTo('App\Models\User','created_by');
    }
}
