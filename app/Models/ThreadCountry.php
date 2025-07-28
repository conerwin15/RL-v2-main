<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThreadCountry extends Model
{
    use HasFactory;

    public function thread()
    {
    	return $this->belongsTo(Thread::class, 'thread_id');
    }

    public function country()
    {
       return $this->belongsTo(Country::class, 'country_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }
}
