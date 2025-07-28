<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMailTemplate extends Model
{
    use HasFactory;

    public function templates()
    {
       return $this->hasMany('App\Models\MailTemplateConfig', 'template_id', 'id');
    }
}
