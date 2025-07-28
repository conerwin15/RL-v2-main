<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledEmailUser extends Model
{
    use HasFactory;
    protected $table="scheduled_email_users";

    public function jobs()
    {
       return $this->belongsTo('App\Models\ScheduledEmailJob', 'job_id', 'id');
    }

    public function users()
    {
       return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
