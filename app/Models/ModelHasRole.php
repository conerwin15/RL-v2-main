<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelHasRole extends Model
{
    use HasFactory;
    protected $table="model_has_roles";

    public function role() {
        return $this->hasOne('App\Models\Role','model_id', 'id');
    }
}
