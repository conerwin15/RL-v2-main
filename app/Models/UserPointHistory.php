<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPointHistory extends Model
{
    use HasFactory;
    protected $table = "user_point_history";

    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id');
   }
}
