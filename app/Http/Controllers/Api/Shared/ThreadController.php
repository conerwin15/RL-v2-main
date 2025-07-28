<?php

namespace App\Http\Controllers\Api\Shared;

use Log;
use Auth;
use Config;
use Exception;
use App\Events\PointUpdateEvent;
use Illuminate\Http\Request;
use Validator;
use stdClass;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\{ User, Thread, ThreadSubscription, ThreadCategory, ThreadLike, Reply };

class ThreadController extends BaseController
{

    public function index(Request $request) 
    {

        try
        {
                $search = trim($request->searchByName);
            
                $limit = 10;
                $query = Thread::withCount('isLikedBy')
                                ->select('id', 'title', 'body', 'category_id' ,'status', 'user_id', 'created_at', 'is_pinned');
                                
                if (!empty($search)) {
                    $query->where('threads.title', 'like', '%'.$search.'%')
                            ->orWhere('threads.body', 'like', '%'.$search.'%');
                }

                if(!empty($request->category) && $request->category != -1) {
                    $query = $query->where('category_id', $request->category);
                }

                $threadsQuery = $query->whereIn('status', [0, 1])->where('is_hidden', 0)
                                ->with(['creator', 'category'])    
                                ->withCount('replies')
                                ->withCount('isLikedBy')
                                ->with('creator', function ($q){
                                    $q->select('id', 'name', 'image');
                                })->with('category', function ($q){
                                    $q->select('id', 'name');
                                });
                          
                $threads = $threadsQuery->orderBy('is_pinned', 'desc')->orderBy('created_at', 'desc')->paginate($limit);
                $response = new StdClass;
                $response->threads = $threads->toArray();
                $response->image_path = storage_path('app/public' . Config::get('constant.PROFILE_PICTURES'));
                
                return $this->sendResponse($response, \Lang::get('lang.thread-list')); 
              
        } catch(Exception $e) {
            Log::error($e);
            return $this->sendError($e, \Lang::get('lang.invalid-request'));
        } 
        
    }  
    
    public function createThread (Request $request)
    {
        try
        {
            $user = Auth::user();
             
            request()->validate([
                    'title' => 'required|min:6',
                    'body'  => 'required|min:6',
                    'category' => 'required|exists:thread_categories,id'
            ]); 

            $thread           = new Thread;
            $thread->user_id  = $user->id;
            $thread->title    = $request->title;
            $thread->body     = $request->body;
            $thread->category_id = $request->category;
            $thread->save();

            $threadSubscription = new ThreadSubscription;
            $threadSubscription->thread_id = $thread->id;
            $threadSubscription->user_id = $user->id;
            $threadSubscription->save();

            $eventType = Config::get('constant.NEW_POST');
            event(new PointUpdateEvent($eventType,  Auth::user()));

            return $this->sendResponse(true, \Lang::get('lang.thread').' '.\Lang::get('lang.created-successfully')); 
        } catch (Exception $e) {
            Log::error($e);
            return $this->sendError($e, \Lang::get('lang.invalid-request'));
        } 

    }

    public function updateThread (Request $request) {

        try {

                $user = Auth::user();
                request()->validate([
                    'threadId' => 'required|exists:threads,id', 
                    'title' => 'required|min:6',
                    'body'  => 'required|min:6',
                    'category' => 'required|exists:thread_categories,id'
                ]);

                $update = [
                    'title' => $request->title,
                    'body' => $request->body,
                    'category_id' => $request->category
                ];
        
                $isUpdated = Thread::where('id', $request->threadId)
                                    ->update($update);
        
                if($isUpdated == 0)
                {
                    return $this->sendResponse(false, \Lang::get('lang.unable-to-update') ); 
                } else {
                    return $this->sendResponse(true, \Lang::get('lang.thread') .' '. \Lang::get('lang.updated-successfully')); 
                }
        } catch (Exception $e) {
            Log::error($e);
            return $this->sendError($e, \Lang::get('lang.invalid-request'));
        } 
    }

    public function updateThreadByStatus (Request $request) {

        try
        {
            request()->validate([
                'threadId' => 'required|exists:threads,id'
            ]);

            $isUpdated = Thread::where('id', $request->threadId)
                        ->update(['status'=> $request->status]);  
            if($isUpdated == 0)
            {
                return $this->sendResponse(false, \Lang::get('lang.unable-to-update') ); 
            } else {
                return $this->sendResponse(true, \Lang::get('lang.threads') .' '. \Lang::get('lang.updated-successfully')); 
            } 
        } catch (Exception $e) {
            Log::error($e);
            return $this->sendError($e, \Lang::get('lang.invalid-request'));
        }                 
    }

    public function deleteThread (Request $request)
    {
        try {

             // update parent reply
            $isUpdated = Reply::where('thread_id', $request->threadId)->update(['parent' => null]);
            if($isUpdated == 0)
            {
                // Remove reply
               Reply::where('thread_id', $request->threadId)->delete(); 
            }
          
            // delete thread
            $isdeleted = Thread::findorFail($request->threadId)->delete();
           
            if($isdeleted == '')
            {
                return $this->sendResponse(false, \Lang::get('lang. unable-to-delete')); 
            } 

            // Remove subscription
            ThreadSubscription::where('thread_id', $request->threadId)->delete();
                
            return $this->sendResponse(true, \Lang::get('lang.thread-delete')); 
        } catch (Exception $e) {
            Log::error($e);
            return $this->sendError($e, \Lang::get('lang.invalid-request'));
        }  
    }

    public function categoryList(Request $request)
    {
        try {
            $threadCategories = ThreadCategory::where('status', 1)->select('id', 'name')->get();
            return $this->sendResponse($threadCategories->toArray(), \Lang::get('lang.thread-category-list'));  

        } catch (Exception $e) {
            Log::error($e);
            return $this->sendError($e, \Lang::get('lang.invalid-request'));
        }    
    }

    public function show($id)
    {

        try {
            $user = Auth::user();
            $thread = Thread::findOrFail($id);
            $replies = $thread->replies()->paginate(10);
            $isSubscriber = ThreadSubscription::where('thread_id', $id)->where('user_id', $user->id)->first() ? true : false;
            $isLiked = ThreadLike::where('thread_id', $id)->where('user_id', $user->id)->first() ? true : false;
            $allLikes = ThreadLike::where('thread_id', $id)->count();

            $thread->isLiked = $isLiked;
            $thread->total_likes = $allLikes;
            $thread->subscribed = $isSubscriber;

            return $this->sendResponse($thread, \Lang::get('lang.thread-fetched')); 

        } catch (Exception $e) {
            Log::error($e);
            return $this->sendError($e, \Lang::get('lang.invalid-request'));
        }
       
    }

    public function like($id) {
        try {
            $user = Auth::user();
 
            $where = [
                        'user_id' => $user->id,
                        'thread_id' => $id
                    ];

            $alredyLike = ThreadLike::where($where)->first();
           
            if($alredyLike){
                return $this->sendResponse(false, \Lang::get('lang.thread-liked'));  
            } else {
                $threadLike             = new ThreadLike;
                $threadLike->thread_id  = $id;
                $threadLike->user_id    = $user->id;
                
                if($threadLike->save())
                {
                    $totalLikes = ThreadLike::where('thread_id', $id)->count();

                    // triger event to add points
                    $eventType = Config::get('constant.LIKE_POST');
                    event(new PointUpdateEvent($eventType,  Auth::user()));
                    return $this->sendResponse($totalLikes, \Lang::get('lang.thread-like-successfully')); 
                } 
            }

        } catch (Exception $e) {
            Log::error($e);
            return $this->sendError($e, \Lang::get('lang.invalid-request'));
        }
    }

    public function removeLike($id)
    {
        try
        {
            $user = Auth::user();
            $eventType = Config::get('constant.UNLIKE_POST');
        
            $where = [
                    'user_id' => $user->id,
                    'thread_id' => $id
            ];
            $deleted = ThreadLike::where($where)->delete();
            if($deleted > 0) {
                // triger event to remove points
                event(new PointUpdateEvent($eventType,  Auth::user()));
            }
            $totalLikes = ThreadLike::where('thread_id', $id)->count(); 
            return $this->sendResponse($totalLikes, \Lang::get('lang.thread-like-remove'));  

        } catch (Exception $e){
            Log::error($e);
            return $this->sendError($e, \Lang::get('lang.invalid-request'));
        }
    }

}
