<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThreadSpamRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'thread_id',
        'reported_by'
    ];

    public function reportedBy()
    {
    	return $this->belongsTo(User::class, 'reported_by');
    }

    public function thread()
    {
    	return $this->belongsTo(Thread::class, 'thread_id');
    }

}
