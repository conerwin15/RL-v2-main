<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningPackage extends Model
{
    use HasFactory;

    public function category()
    {
        return $this->belongsTo('App\Models\Category');
    }

    public function subCategory()
    {
        return $this->belongsTo('App\Models\Category');
    }

    public function learningPaths()
    {
        return $this->hasMany('App\Models\PackageLearningPath');
    }

    public function priceHistories()
    {
        return $this->hasMany('App\Models\PackagePriceHistory', 'package_id')->orderBy('created_at', 'desc');
    }
    
    public function learningPath()
    {
        return $this->belongsToMany('App\Models\LearningPath', 'package_learning_paths', 'learning_package_id', 'learning_path_id', 'id', 'id');
    }
}
