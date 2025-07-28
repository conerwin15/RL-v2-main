<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use HasFactory;

    public function owner()
    {
    	return $this->belongsTo(User::class, 'user_id');
    }

    public function ownerDetails() {
        return $this->belongsTo(User::class, 'user_id')->select(['id', 'image', 'name']);;
    }

    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    public function replies() {

        if(Auth::user()->getRoleNames()[0] == 'superadmin') {

            return $this->hasMany('App\Models\Reply', 'parent', 'id');
        } else {
            return $this->hasMany('App\Models\Reply', 'parent', 'id')->where('is_hidden', false);
        }
    }

    public function replyLike()
    {
        return $this->hasMany('App\Models\ReplyLike', 'reply_id', 'id');
    }

    public function spamComments()
    {
        return $this->hasMany(ReportedReply::class, 'reply_id');
    }

    public function parentDetails() {

        return $this->belongsTo('App\Models\Reply', 'parent', 'id');
    }
    
}
