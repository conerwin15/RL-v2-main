<?php

namespace App\Http\Controllers\Api\Shared;

use Log;
use Config;
use Auth;
use Exception;
use Response;
use App\Http\Controllers\Api\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\{ NewsPromotion, User };

class NewsPromotionController extends BaseController
{
    /**
     * Create a new NewsPromotionController instance.
     *
     * @return void
     */

    public function index(Request $request)
    {   
        try { 

            $page = $request->page ? $request->page : 1;
            $limit = 10;
            
            $user = Auth::user();
            $region_id = $user->region_id;
            $country_id = $user->country_id;
            $jobRole_id = $user->job_role_id;
            $group_id = $user->group_id;
           
            $newsPromotions = NewsPromotion::select('id', 'title', 'description', 'media', 'created_at')->where('status', 1)
                                ->where(function($query) use ($country_id) {
                                    $query->where('country_id', $country_id)->orWhereNull('country_id');
                                })->where(function($query) use ($region_id) {
                                    $query->where('region_id', $region_id)->orWhereNull('region_id');
                                })->where(function($query) use ($jobRole_id) {
                                    $query->where('job_role_id', $jobRole_id)->orWhereNull('job_role_id'); 
                                })->where(function($query) use ($group_id) {
                                    $query->where('group_id', $group_id)->orWhereNull('group_id');   
                                })->orderby('id', 'desc')->paginate($limit); 

            $response['news_promotions'] = $newsPromotions; 
            $response['storage_path'] = asset('storage' . Config::get('constant.NEWS_STORAGE_PATH'));         

            return $this->sendResponse($response, \Lang::get('lang.news-promotion-list'));                         
        } catch (Exception $e) {
            Log::error($e);
            return $this->sendError($e, \Lang::get('lang.invalid-request'));
        }                              
    }

}
