<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesTip extends Model
{
    use HasFactory;
    protected $table="sales_tips";

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
    
    public function jobRole() {

        return $this->belongsTo('App\Models\JobRole');
    }

    public function group() {

        return $this->belongsTo('App\Models\Group');
    }

    public function country() {
        return $this->belongsTo('App\Models\Country', 'country_id');
    }

    public function region() {
        return $this->belongsTo('App\Models\Region', 'region_id');
    }

    public function createdBy() {
        return $this->belongsTo('App\Models\User', 'created_by');
    }

    public static function buildQuery($request) {
        $salesQuery = SalesTip::where('status', 1);
        if(!empty($request->filter_region) && $request->filter_region != -1) {
            $salesQuery = $salesQuery->where('region_id', $request->filter_region);
        } 
        if(!empty($request->filter_country) && $request->filter_country != -1) {
            $salesQuery = $salesQuery->where('country_id', $request->filter_country); 
        }

        if(!empty($request->filter_jobrole) && $request->filter_jobrole != -1) {
            $salesQuery = $salesQuery->where('job_role_id', $request->filter_jobrole); 
        }

        if(!empty($request->filter_group) && $request->filter_group != -1) {
            $salesQuery = $salesQuery->where('group_id', $request->filter_group);
        }

        return $salesQuery;
    }
}
