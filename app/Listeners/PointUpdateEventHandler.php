<?php

namespace App\Listeners;

use Auth;
use Config;
use Setting;
use App\Events\PointUpdateEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\{ UserPointHistory };

class PointUpdateEventHandler
{
    /**
     * Create the event listener.
     * // PointUpdateEventHandler new name
     * @return void
     */
    private $addPointEvents = [];
    private $removePointEvents = [];

    public function __construct()
    {
        $this->addPointEvents = array(
            Config::get('constant.NEW_POST'), 
            Config::get('constant.LIKE_POST'), 
            Config::get('constant.DIAMOND_BADGE'),
            Config::get('constant.SILVER_BADGE'),
            Config::get('constant.GOLD_BADGE'),
            Config::get('constant.BRONZE_BADGE'),
            Config::get('constant.ADD_COMMENT'),
            Config::get('constant.QUIZ_SCORE'));
        $this->removePointEvents = array(Config::get('constant.REMOVE_POST'), Config::get('constant.UNLIKE_POST'), Config::get('constant.REMOVE_COMMENT'));
    }

    /**
     * Handle the event.
     *
     * @param  PointUpdateEvent  $event
     * @return void
     */
    public function handle(PointUpdateEvent $event)
    {
        $points = $event->points !=0 ? $event->points : (int)Setting::get('points_per_activity');
        
        if(in_array(@$event->user->roles[0]->name, ['dealer', 'staff'] )) {
            
           
            $userPointHistory = new UserPointHistory;
            $userPointHistory->user_id = $event->user->id;
            $userPointHistory->bonus_point_reason = $event->reason;
            if(in_array($event->event, $this->addPointEvents))
            {
                    $userPointHistory->type = $event->event;
                    if($event->event == Config::get('constant.DIAMOND_BADGE')) {
                        $points = 90;
                    } else if($event->event == Config::get('constant.GOLD_BADGE')) {
                        $points = 60; 
                    } else if($event->event == Config::get('constant.SILVER_BADGE')) {
                        $points = 40; 
                    } else if($event->event == Config::get('constant.BRONZE_BADGE')) {
                        $points = 20; 
                    }
                    $userPointHistory->points = $points;
                    $userPointHistory->save();
            } else if(in_array($event->event, $this->removePointEvents)){
                    $userPointHistory->type = $event->event;
                    $userPointHistory->points = -$points;
                    $userPointHistory->save();
            }
        } else {
            if($event->event == "adjust_point"){
                $userPointHistory = new UserPointHistory;
                $userPointHistory->user_id = $event->user;  
                $userPointHistory->type = $event->event;
                $userPointHistory->bonus_point_reason = $event->reason;
                $userPointHistory->points = $points;
                $userPointHistory->save();
            }    
        }
        return true;
    }
    
}
