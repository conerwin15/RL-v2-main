<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeaturedTrainee extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "region_id",
        "type",
        "created_by",
        "month",
        "year",
        "points",
        "featured_text"
    ];

    public function user() {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
}
