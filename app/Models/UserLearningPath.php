<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLearningPath extends Model
{
    use HasFactory;

    protected $table = "user_learning_paths";

    protected $fillable = [
        'user_id',
        'learning_path_id',
        'assign_by',
        'badge_id',
        'progress_percentage'
    ];

    public function learningPath()
    {
        return $this->belongsTo('App\Models\LearningPath', 'learning_path_id', 'id');
    } 

    public function badge()
    {
        return $this->belongsTo('App\Models\LearnerBadge', 'badge_id');
    } 

    public function assignBy()
    {
        return $this->belongsTo('App\Models\User', 'assign_by');
    }

    public function user() 
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
