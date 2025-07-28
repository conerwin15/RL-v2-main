<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizSubmission extends Model
{
    use HasFactory;
    protected $table="quiz_submissions";

    public function user()
    {
       return $this->belongsTo('App\Models\User');
    }

    public function quiz() {

        return $this->belongsTo('App\Models\Quiz');
    }

    public function question() {

        return $this->belongsTo('App\Models\QuizQuestion');
    }
}
