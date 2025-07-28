<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionOption extends Model
{
    use HasFactory;
    protected $table = "question_options";

    public function question() {

        return $this->belongsTo('App\Models\QuizQuestion');
    }

   
}
