<?php

namespace App\Http\Controllers\Shared;

use Auth;
use Config;
use Log;
use Exception;
use Illuminate\Http\Request;
use App\Events\PointUpdateEvent;
use App\Http\Controllers\Controller;
use App\Models\{ User, Thread, ThreadSubscription,  ThreadLike};

class ThreadLikeResource extends Controller
{
   
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
           
            request()->validate([
                'threadId' => 'required|exists:threads,id'
            ]);
 
            $where = [
                        'user_id' => $user->id,
                        'thread_id' => $request->threadId
            ];

            $alredyLike = ThreadLike::where($where)->first();
           
            if($alredyLike){
                return response()->json(['success' => true, "messsage" => "Already Liked"], 200);
            } else {
                $threadLike             = new ThreadLike;
                $threadLike->thread_id  = $request->threadId;
                $threadLike->user_id    = $user->id;
                
                if($threadLike->save())
                {
                    $totalLikes = ThreadLike::where('thread_id', $request->threadId)->count();

                    // triger event to add points
                    $eventType = Config::get('constant.LIKE_POST');
                    event(new PointUpdateEvent($eventType,  Auth::user()));
                    return response()->json(['success' => true, "messsage" => "Like added successfully", 'allLikes'=> $totalLikes], 200);
                } 
            }
            
        } catch (Exception $e) {
            Log::error($e); 
            return response()->json(['success' => false, "messsage"=> \Lang::get('lang.generic-error')], 200);
        } 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try
        {
            $user = Auth::user();
            $eventType = Config::get('constant.UNLIKE_POST');
        
            $where = [
                    'user_id' => $user->id,
                    'thread_id' => $id
            ];
            $isDeleted = ThreadLike::where($where)->delete();
            
            if($isDeleted == 0)
            { 
                return response()->json(['success' => false, "messsage"=> "unable to delete"], 400);  
            } 

            // triger event to remove points
            event(new PointUpdateEvent($eventType,  Auth::user()));
            $totalLikes = ThreadLike::where('thread_id', $id)->count(); 
            return response()->json(['success' => true, "messsage" => "Like removed successfully", 'allLikes'=> $totalLikes], 200);

        } catch (Exception $e){
            Log::error($e); 
            return response()->json(['success' => false, "messsage"=> \Lang::get('lang.generic-error')], 200);
        }
        
    }

}
