<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledEmailJob extends Model
{
    use HasFactory;
    protected $table="scheduled_email_jobs";

    public function scheduleUsers ()
    {
        return $this->hasMany('App\Models\ScheduledEmailUser', 'job_id', 'id' );
    }
}
