<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactCategory extends Model
{
    use HasFactory;
    protected $table = "contact_categories";

    public function roles()
    {
        return $this->belongsTo('App\Models\Role', 'role_id');
    }
}
