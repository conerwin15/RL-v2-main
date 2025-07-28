<?php

namespace App\Http\Controllers\Api\Shared;

use Log;
use Exception;
use Auth;
use Config;
use Carbon\Carbon;
use App\Events\PointUpdateEvent;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;
use Illuminate\Support\Facades\Mail;
use Spatie\MailTemplates\Models\MailTemplate;
use Spatie\MailTemplates\TemplateMailable;
use App\Mail\{ReportCommentSuperadmin};
use App\Models\{ Reply, User, Thread, ThreadSubscription, ReportedReply };

class ReplyController extends BaseController
{
    public function replyList(Request $request)
    {
        try
        {
            $page = $request->page ? $request->page : 1;
            $limit = 10;
            request()->validate([
                'threadId' => 'required|exists:threads,id'
            ]);
            
            if($request->parentId)
            {
                $where = [
                             'thread_id' => $request->threadId,
                             'parent'    => $request->parentId,
                         ];
                $replies = Reply::where($where)->withCount('replies')->withCount('replyLike')->with('ownerDetails')->withCount(['replyLike as is_liked' => function($q) {
                                     $q->where('user_id', Auth::user()->id);
                            }])->get();

            } else {
        
                $where = [
                            'thread_id' => $request->threadId,
                            'parent'    => null,
                         ];
                $replies = Reply::where($where)->withCount('replies')->withCount('replyLike')->with('ownerDetails')->withCount(['replyLike as is_liked' => function($q) {
                                $q->where('user_id', Auth::user()->id);
                            }])->paginate($limit)->toArray();
            }

            $response["replies"] = $replies;
            $response['image_path'] = asset('storage' . Config::get('constant.PROFILE_PICTURES'));
            $response['owner_id'] = Auth::user()->id;
            
            return $this->sendResponse($response, \Lang::get('lang.thread-reply-list'));  
        }  catch (Exception $e) {
            Log::error($e);
            return $this->sendError($e, \Lang::get('lang.invalid-request'));
        }    
    }

    public function addReply (Request $request) {

        try 
        {
            $user = Auth::user();
            request()->validate([
                    'threadId' => 'required|exists:threads,id',
                    'reply'  => 'required',
            ]); 
             
            $reply             = new Reply;
            $reply->thread_id  = $request->threadId;
            $reply->user_id    = $user->id;
            $reply->body       = $request->reply;

            if($request->replyId)
            {
                request()->validate([
                    'replyId' => 'required|exists:replies,id',
                ]);
                $reply->parent = $request->replyId;
                $reply->save();
            } else {

                    $reply->save();
                    $userExists = ThreadSubscription::where('thread_id', $request->threadId)->where( 'user_id', $user->id)->exists();
                
                    if($userExists != true) {
                        $threadSubscription = new ThreadSubscription;
                        $threadSubscription->thread_id = $request->threadId;
                        $threadSubscription->user_id = $user->id;
                        $threadSubscription->save();
                    }
                
                    // triger event to add point
                    $where = [
                            'thread_id' => $request->threadId,
                            'user_id' =>  $user->id  
                    ];
                    
                    $totalComments = Reply::where($where)->count();
                    if($totalComments <= 1) // Only add points if user has not more than 1 comment
                    {
                        $eventType = Config::get('constant.ADD_COMMENT');
                        event(new PointUpdateEvent($eventType,  Auth::user()));
                    
                    }
            }
            return $this->sendResponse('true', \Lang::get('lang.reply') .' '. \Lang::get('lang.added-successfully')); 
        } catch (Exception $e) {
            Log::error($e);
            return $this->sendError($e, \Lang::get('lang.invalid-request'));
        }  
       
    }


    public function unsubscribe (Request $request)
    {
        try 
        {
            $user = Auth::user();
            $where = [
                        'thread_id' => $request->threadId,
                        'user_id' => $user->id
            ];
           
            $isdeleted = ThreadSubscription::where($where)->delete();
            if($isdeleted == 0)
            {
                return $this->sendResponse('false', \Lang::get('lang.unable-to-unsubscribe'));  
            } 
          
            return $this->sendResponse('true', \Lang::get('lang.unsubscribe-successfully'));  
        } catch (Exception $e) {
            Log::error($e);
            return $this->sendError($e, \Lang::get('lang.invalid-request'));
        }     

    }

    public function subscribe (Request $request)
    {
        try 
        {
            $user = Auth::user();
            $threadSubscription = new ThreadSubscription;
            $threadSubscription->thread_id = $request->threadId;
            $threadSubscription->user_id = $user->id;
            $threadSubscription->save();

            return $this->sendResponse('true', \Lang::get('lang.thread-subscribe-successfully'));  
        } catch (Exception $e) {
            Log::error($e);
            return $this->sendError($e, \Lang::get('lang.invalid-request'));
        }      

    }


    // add comment on each report
    public function reportComment($id) {

        try 
        {
            $reply = Reply::findOrFail($id);
            $spamRequest = ReportedReply::where('reply_id', $id)->where('reported_by', Auth::user()->id)->first();
            if($spamRequest) {
                return $this->sendResponse('true', \Lang::get('lang.comment-reported-already'));  
                
            }

            ReportedReply::create([
                'reply_id' => $id,
                'reported_by' => Auth::user()->id,
                'reported_at' => Carbon::now(),
            ]);

            // send mail to superadmins

            $role = 'superadmin';
            $reply = Reply::with('owner', 'thread')->find($id);
            $threadLink = url( 'superadmin/forum/threads/'. $reply->thread->id); 
            $commentLink = url('superadmin/forum/threads/' . $reply->thread->id . '/' . $id . '/reported');
        
            $superadmins = User::whereHas("roles", function($query) use($role )
                            { 
                                $query->where("name", $role); 
                            })->select('email', 'country_id')->get();
                        
            foreach ($superadmins as $superadmin) {

                if(is_null($superadmin->country_id))
                {
                    Log::warning('Not sending email for ' . $superadmin->email . ' as country does not exist.');

                } else {

                    $country[] = $superadmin->country_id;
                    array_push($country, "-1");
                    $templateConfig = DB::table('user_mail_templates')
                                    ->join('mail_template_config', 'user_mail_templates.id', '=', 'mail_template_config.template_id')
                                    ->whereIn('mail_template_config.country_id', $country)
                                    ->where('user_mail_templates.mailable', 'App\Mail\ReportCommentSuperadmin')->first();
                    if($templateConfig != null) {
                        Mail::to($superadmin->email)
                            ->send(new \App\Mail\ReportCommentSuperadmin( Auth::user()->name, $reply->body, $reply->thread->title, $threadLink, $commentLink, $templateConfig->template_id));

                    } else {
                        Log::warning('Mail template HideReportCommentSuperadmin does not exist for country ' .  $superadmin->country_id);
                    }
                }    
            
            }

            return $this->sendResponse('true', \Lang::get('lang.comment-reported'));  
            
        } catch (Exception $e) {
            Log::error($e);
            return $this->sendError($e, \Lang::get('lang.invalid-request'));
        }         
    }
}
