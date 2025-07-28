<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    use HasFactory;

    public function creator()
    {
    	return $this->belongsTo(User::class, 'user_id');
    }

    public function replies()
    {
    	return $this->hasMany(Reply::class);
    }

    public function threadSubscriptions() 
    {
        return $this->hasMany(ThreadSubscription::class);
    }

    public function isLikedBy()
    {
        return $this->hasMany(ThreadLike::class);
    }

    public function spamRequests()
    {
        return $this->hasMany(ThreadSpamRequest::class, 'thread_id');
    }

    public function category()
    {
    	return $this->belongsTo(ThreadCategory::class, 'category_id');
    }

    public function threadCountries()
    {
        return $this->hasMany(ThreadCountry::class, 'thread_id');
    }

    public function pinThreads()
    {
        return $this->hasMany(PinThread::class)->orderBy('thread_id', 'desc');
    }

    public function getNameById($id) {
        $userType = User::where('id', $id)->first();
        return $userType->name ?? null;
    }
    
}
