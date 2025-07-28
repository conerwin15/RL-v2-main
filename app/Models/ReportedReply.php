<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportedReply extends Model
{
    use HasFactory;
    protected $table = "reported_replies";

    protected $fillable = [
        'reply_id',
        'reported_by',
        'reported_at',
    ];

    public function reportedBy()
    {
    	return $this->belongsTo(User::class, 'reported_by');
    }

    public function reply()
    {
    	return $this->belongsTo(Reply::class, 'reply_id');
    }
}
