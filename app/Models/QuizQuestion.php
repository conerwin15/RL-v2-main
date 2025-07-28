<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    use HasFactory;
    protected $table = "quiz_questions";

    public function options()
    {
       return $this->hasMany('App\Models\QuestionOption', 'question_id', 'id' );
       
    }

    public function quizSubmissions() {

        return $this->hasMany('App\Models\QuizSubmission', 'question_id', 'id');
    }
}
