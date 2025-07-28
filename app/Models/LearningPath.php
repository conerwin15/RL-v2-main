<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningPath extends BaseModel
{
    use HasFactory; 
    protected $table = "learning_paths";

    public function createdBy()
    {
        return $this->belongsTo('App\Models\User', 'created_by');
    }

    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'user_learning_paths', 'learning_path_id' , 'user_id')
                ->withPivot('assign_by', 'created_at');    
    }

    public function usersCountByRegion()
    {
        return $this->belongsToMany('App\Models\User' , 'user_learning_paths', 'learning_path_id' , 'user_id')->whereIn('region_id', explode(',', Auth::user()->region_id))->count();   
    }

    public function resources() {
        return $this->hasMany('App\Models\LearningPathResource');
    }

    public function packageResource()
    {
        return $this->belongsTo('App\Models\ScormPackageResources', 'package_id', 'scorm_package_id');
    }

    public function categories()
    {
        return $this->belongsTo('App\Models\Category', 'category_id');
    }

    public function subCategories()
    {
        return $this->belongsTo('App\Models\Category', 'sub_category_id');
    }

    public function certificate()
    {
        return $this->belongsTo('App\Models\Certificate', 'certificate_id');
    }

    public function packageLearningPaths()
    {
        return $this->hasMany('App\Models\PackageLearningPath');
    }

    public static function boot() {

        parent::boot();

        static::creating(function($model)
        {
            $user = Auth::user();           
            $model->created_by = $user->id;
            $model->updated_by = $user->id;
        });

        static::updating(function($model)
        {
            $user = Auth::user();
            $model->updated_by = $user->id;
        }); 
    }
}
