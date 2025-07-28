<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLearningProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'learning_resource_id',
        'user_id',
        'lesson_status',
        'cmi_data',
        'start_date',
        'end_date'
    ];

    protected $casts = [
        'cmi_data' => 'json',
    ];

    public function learningResource() {
        return $this->belongsTo('App\Models\LearningPathResource', 'learning_resource_id');
    }
    
    public function learner()
    {
    	return $this->belongsTo(User::class, 'user_id');
    }
}
