<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsPromotion extends Model
{
    use HasFactory;
    protected $table="news_promotion";

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'media',
        'country_id',
        'region_id',
        'job_role_id',
        'group_id',
        'created_by',
        'updated_by'
    ];
    
    public function createdBy() {
        return $this->belongsTo('App\Models\User', 'created_by');
    }

    public function country() {
        return $this->belongsTo('App\Models\Country', 'country_id');
    }

    public function region() {
        return $this->belongsTo('App\Models\Region', 'region_id');
    }

    public function jobRole() {

        return $this->belongsTo('App\Models\JobRole');
    }

    public function group() {

        return $this->belongsTo('App\Models\Group');
    }

    public static function buildQuery($request) {
        $newsQuery = NewsPromotion::where('status', 1);
        if(!empty($request->filter_region) && $request->filter_region != -1) {
            $newsQuery = $newsQuery->where('region_id', $request->filter_region);
        } 
        if(!empty($request->filter_country) && $request->filter_country != -1) {
            $newsQuery = $newsQuery->where('country_id', $request->filter_country); 
        }

        if(!empty($request->filter_jobrole) && $request->filter_jobrole != -1) {
            $newsQuery = $newsQuery->where('job_role_id', $request->filter_jobrole); 
        }

        if(!empty($request->filter_group) && $request->filter_group != -1) {
            $newsQuery = $newsQuery->where('group_id', $request->filter_group);
        }

        return $newsQuery;
    }
}
