<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningScormTracking extends Model
{
    use HasFactory;
    protected $table="learning_scorm_tracking";
    protected $primaryKey = 'scorm_tracking_id';

     public function user()
     {
    	return $this->belongsTo(User::class);
     }

     public function group()
     {
    	return $this->belongsTo(Group::class);
     }

     public function learningPath()
     {
    	return $this->belongsTo(LearningPath::class);
     }

     public function course()
     {
        return $this->belongsTo(Course::class);
     }

     public function scorm_resource()
     {
    	return $this->belongsTo(ScormResource::class,'reference_id','scorm_resource_id');
     }

     public function scorm_item()
     {
    	return $this->belongsTo(ScormItem::class,'scorm_item_id','scorm_item_id');
     }
}
