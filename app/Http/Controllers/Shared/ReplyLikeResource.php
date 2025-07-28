<?php

namespace App\Http\Controllers\Shared;

use Auth;
use Config;
use Log;
use Exception;
use Illuminate\Http\Request;
use App\Events\PointUpdateEvent;
use App\Http\Controllers\Controller;
use App\Models\{ User, Reply,  ReplyLike};

class ReplyLikeResource extends Controller
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
                'replyId' => 'required|exists:replies,id'
            ]);
 
            $where = [
                        'user_id' => $user->id,
                        'reply_id' => $request->replyId
            ];

            $alredyLike = ReplyLike::where($where)->first();
           
            if($alredyLike){

                    if($request->ajax()){
                        return response()->json(['success' => false,  'message' => \Lang::get('lang.already-liked')]);    
                    } 
                return response()->json(['success' => true, "messsage" => \Lang::get('lang.already-liked')], 200);
            } else {
                $replyLike             = new ReplyLike;
                $replyLike->reply_id   = $request->replyId;
                $replyLike->user_id    = $user->id;
                
                if($replyLike->save())
                {
                    $totalLikes = ReplyLike::where('reply_id', $request->replyId)->count();
                    if($request->ajax()){

                       return response()->json(['success' => true, "messsage" => \Lang::get('lang.reply-liked'), 'allLikes'=> $totalLikes], 200);   
                    } else {

                        return response()->json(['success' => true, "messsage" => \Lang::get('lang.reply-liked'), 'allLikes'=> $totalLikes], 200);
                    }
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
    public function destroy(Request $request, $id)
    {
        try
        {
            $user = Auth::user();
            $eventType = Config::get('constant.UNLIKE_POST');
        
            $where = [
                    'user_id' => $user->id,
                    'reply_id' => $id
            ];
            $isDeleted = ReplyLike::where($where)->delete();
        
            if($isDeleted == 0)
            { 
                return response()->json(['success' => false, "messsage"=> "unable to delete"], 400);  
            } 

            $totalLikes = ReplyLike::where('reply_id', $id)->count();

            if($request->ajax()){

                return response()->json(['success' => true, "messsage" => \Lang::get('lang.like-removed'), 'allLikes'=> $totalLikes], 200);
            } else {

                return response()->json(['success' => true, "messsage" => \Lang::get('lang.like-removed'), 'allLikes'=> $totalLikes], 200);
            }     

        } catch (Exception $e){
            Log::error($e); 
            return response()->json(['success' => false, "messsage"=> \Lang::get('lang.generic-error')], 200);
        }
    }


    /****
     * 
     * get child reply for reply
     */

    public function childReply($replyId)
    {
        try {
                $reply = Reply::findOrFail($replyId);

                if( Auth::user()->getRoleNames()[0] == 'superadmin') {

                    $where = [
                        'parent' => $reply->id,
                        
                    ];
        
                } else {
        
                    $where = [
                        'is_hidden' => false,
                        'parent' => $reply->id,
                        
                    ];
                }
               
                $childReply = Reply::withCount('replies')->withCount('replyLike')->withCount('spamComments')->where($where)
                                ->with('ownerDetails')
                                ->get();
                              
                $isReplyLiked = ReplyLike::select('reply_id')->where('user_id', Auth::user()->id)->get()->toArray();                      
                return response()->json(['success' => true,  'data' => $childReply, 'isReplyLiked' => $isReplyLiked], 200);

        } catch (Exception $e) {
            Log::error($e); 
            return response()->json(['success' => false, "messsage"=> \Lang::get('lang.generic-errorz')], 200);
        }
        
    } 

   
}
