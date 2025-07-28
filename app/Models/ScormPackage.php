<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScormPackage extends Model
{
    use HasFactory;
    protected $table="scorm_package";
    protected $primaryKey = 'scorm_package_id';


    public function packageResourceItems()
    {
    	return $this->hasMany('App\Models\ScromPackageResourceItem', 'scorm_package_id');
    }
}
