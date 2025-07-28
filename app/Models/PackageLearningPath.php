<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageLearningPath extends Model
{
    use HasFactory;

    public function learningPaths()
    {
        return $this->belongsTo('App\Models\LearningPath', 'learning_path_id');
    }
    public function learningPath()
    {
        return $this->belongsTo('App\Models\LearningPath', 'learning_path_id');
    }
    public function learningPackage()
    {
        return $this->belongsTo('App\Models\LearningPackage', 'learning_package_id');
    }
}
