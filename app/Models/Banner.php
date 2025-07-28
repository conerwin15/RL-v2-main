<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;
    public function createdBy() {
        return $this->belongsTo('App\Models\User', 'created_by');
    }
}
