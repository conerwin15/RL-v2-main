<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailTemplateConfig extends Model
{
    use HasFactory;
    protected $table="mail_template_config";

    public function mailTemplates()
    {
       return $this->belongsTo('App\Models\UserMailTemplate', 'template_id', 'id');
    }
}
