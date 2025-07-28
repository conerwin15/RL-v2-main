<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    public function country()
    {
       return $this->belongsTo('App\Models\Country', 'country_id', 'id');
    }

    public function region()
    {
       return $this->belongsTo('App\Models\Region', 'region_id', 'id');
    }

    public function jobRole() {

        return $this->belongsTo('App\Models\JobRole', 'job_role_id', 'id');
    }

    public function group() {

        return $this->belongsTo('App\Models\Group', 'group_id', 'id');
    }

    public function userQuizzes ()
    {
        return $this->hasMany('App\Models\UserQuiz', 'quiz_id', 'id' );
    }

    public static function quizQuery($request) {
       
        $quizQuery = Quiz::where('status', 1)->where(function ($quizQuery) use ($request){

     
            
            if(!empty($request->filter_country)  && $request->filter_country != null) {
                
                foreach ($request->filter_country as $country) {
                    $quizQuery = $quizQuery->orWhereRaw("find_in_set( $country, country_id)");
                    
                }
            }

            if(!empty($request->filter_region)  && $request->filter_region != null) {
                
                foreach ($request->filter_region as $region) {
                    $quizQuery = $quizQuery->orWhereRaw("find_in_set( $region, region_id)");
                    
                }
            }

            if(!empty($request->filter_jobRole)  && $request->filter_jobRole != null) {
                
                foreach ($request->filter_jobRole as $jobRole) {
                    $quizQuery = $quizQuery->orWhereRaw("find_in_set( $jobRole, job_role_id)");
                    
                }
            }

            if(!empty($request->filter_group)  && $request->filter_group != null) {
                
                foreach ($request->filter_group as $group) {
                    $quizQuery = $quizQuery->orWhereRaw("find_in_set( $group, group_id)");
                    
                }
            }

            if (!empty($request->name)) {
                $quizQuery = $quizQuery->where('name', 'like', '%' . trim($request->name) . '%'); 
            } 
        });    
       
        return $quizQuery;
    }

    public function quizCountries() { 
        $regions = Country::whereIn('id', explode(',', $this->country_id))->orderBy('name')->get();
        return $regions;
    }

    public function quizRegions() { 
        $regions = Region::whereIn('id', explode(',', $this->region_id))->orderBy('name')->get();
        return $regions;
    }

    public function quizJobRoles() { 
        $regions = JobRole::whereIn('id', explode(',', $this->job_role_id))->orderBy('name')->get();
        return $regions;
    }

    public function quizGroups() { 
        $regions = Group::whereIn('id', explode(',', $this->group_id))->orderBy('name')->get();
        return $regions;
    }

}
