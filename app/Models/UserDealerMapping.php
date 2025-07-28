<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDealerMapping extends Model
{
    use HasFactory;
    protected $table="user_dealer_mapping";

    public function users(){
        return $this->belongsToMany(User::class, 'dealer_id','user_id');
    }
}
