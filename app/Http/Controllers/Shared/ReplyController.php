<?php

namespace App\Http\Controllers\Shared;

use DB;
use Auth;
use File;
use Config;
use Log;
use Exception;
use Carbon\Carbon;
use App\Events\PointUpdateEvent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\FollowerMail;
use Illuminate\Support\Facades\Mail;
use Spatie\MailTemplates\Models\MailTemplate;
use Spatie\MailTemplates\TemplateMailable;
use App\Mail\{HideComment, HideReportCommentSuperadmin, ReportCommentSuperadmin};
use App\Models\{ Reply, User, Thread, ThreadSubscription, ReportedReply, ReplyLike, Country, ThreadLike };

class ReplyController extends Controller
{
   
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public $storagePath;

    public function __construct()
    {
       $this->storagePath = storage_path('app/public' . Config::get('constant.THREAD_REPLY_IMAGE_STORAGE_PATH'));
    }

    public function store(Request $request)
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

        // file upload
        $mediaFormats = implode(',', Config::get("constant.SUPPORTED_IMAGE_FORAMTS"));

        if($request->hasFile('image')) {
            request()->validate([
                'image' => 'mimes:' . $mediaFormats . '|max:1048576' // 1048 mb
            ]);

            $file = $request->file('image');

            $uploadFilename =  md5(microtime()) .'.'.$file->getClientOriginalExtension();

            // check directory exist or not
            $path = $this->storagePath;

            if(!is_dir($path)) {
                File::makeDirectory($path, $mode = 0775, true, true);
            }
            $file->move($this->storagePath, $uploadFilename);
            $reply->image = $uploadFilename;

        }
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
        
        // send mail to follower
        $followers = ThreadSubscription::with('user', 'thread')->where('thread_id', $request->threadId)->where('user_id', '!=', Auth::user()->id)->get();

        foreach($followers as $follower)
        {
            Mail::to($follower->user->email)->send(new FollowerMail($follower, $request->reply));
        }

        return redirect()->back()->with("success",  \Lang::get('lang.reply') .' '. \Lang::get('lang.added-successfully')); 
    }

    public function unsubscribe (Request $request)
    {
        $routeSlug = $this->getRouteSlug();
        $user = Auth::user();
        $where = [
                    'thread_id' => $request->thread_id,
                    'user_id' => $user->id
        ];
       
        $isdeleted = ThreadSubscription::where($where)->delete();
        if($isdeleted == 0)
        {
            return back()->with('error', \Lang::get('lang.unable-to-unsubscribe'));
        } 

        return redirect()->back()->with("success",  \Lang::get('lang.thread') .' '. \Lang::get('lang.unsubscribed')); 
    }

    public function subscribe (Request $request)
    {
        $routeSlug = $this->getRouteSlug();
        $user = Auth::user();
        
        $threadSubscription = new ThreadSubscription;
        $threadSubscription->thread_id = $request->thread_id;
        $threadSubscription->user_id = $user->id;
        $threadSubscription->save();

        return redirect()->back()->with("success",  \Lang::get('lang.thread-followed')); 
    }

    protected function getRouteSlug() {
        $user = Auth::user();
        return $user->getRoleNames()->first();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try
        {
            request()->validate([
                'id' => 'required|exists:replies,id',
                'reply'  => 'required|min:6'
            ]);

            $isUpdated = Reply::where('id', $request->id)
                ->update(['body' => $request->reply]);

            if($isUpdated == 0)
            {
                return response()->json(['success' => true, "messsage" => "Comment not updated."], 500);
            }
            
            return response()->json(['success' => true, "messsage" => "Comment updated sucessfully."], 200);
        }  catch (Exception $e) {
            Log::error($e); 
            return response()->json(['success' => false, "messsage"=> \Lang::get('lang.generic-error')], 200);
        }      
 
    }    

    public function destroy($threadId, $replyId)
    {
        $isDeleted = Reply::find($replyId)->delete();
       
        if($isDeleted == 0)
        {
            return back()->with('error', \Lang::get('lang. unable-to-delete'));  
        } 

        // Remove points of user 
        $eventType = Config::get('constant.REMOVE_COMMENT');
        event(new PointUpdateEvent($eventType,  Auth::user()));
        return back()->with('success', \Lang::get('lang.reply-delete')); 
    }  
    
    
    public function storeChildReply (Request $request)
    {
        try {
               
                request()->validate([
                    'threadId' => 'required|exists:threads,id',
                    'replyId' => 'required|exists:replies,id',
                    'childReply' => 'required'
                ]);   
                
                $reply             = new Reply;
                $reply->thread_id  = $request->threadId;
                $reply->user_id    = Auth::user()->id;
                $reply->parent     = $request->replyId;
                $reply->body       = $request->childReply;
               
                 // file upload
                $mediaFormats = implode(',', Config::get("constant.SUPPORTED_IMAGE_FORAMTS"));

                if($request->hasFile('image')) {
                    request()->validate([
                        'image' => 'mimes:' . $mediaFormats . '|max:1048576' // 1048 mb
                    ]);

                    $file = $request->file('image');

                    $uploadFilename =  md5(microtime()) .'.'.$file->getClientOriginalExtension();

                    // check directory exist or not
                    $path = $this->storagePath;

                    if(!is_dir($path)) {
                        File::makeDirectory($path, $mode = 0775, true, true);
                    }
                    $file->move($this->storagePath, $uploadFilename);
                    $reply->image = $uploadFilename;

                }

                if($reply->save())
                {

                    $replyCount = Reply::where('parent', $request->replyId)->count();

                    // send mail to follower
                    $followers = ThreadSubscription::with('user', 'thread')->where('thread_id', $request->threadId)->where('user_id', '!=', Auth::user()->id)->get();

                    foreach($followers as $follower)
                    {
                        Mail::to($follower->user->email)->send(new FollowerMail($follower, $request->childReply));
                    }
                    return response()->json(['success' => true, "messsage" => "Reply added successfully", 'replyCount' => $replyCount, 'replyId' => $request->replyId], 200);
                }  

        } catch (Exception $e) {
            Log::error($e); 
            return response()->json(['success' => false, "messsage"=> \Lang::get('lang.generic-error')], 200);
        }
    }


    // reported comment by all

    public function reportComment($id) {

        $reply = Reply::findOrFail($id);
        $spamRequest = ReportedReply::where('reply_id', $id)->where('reported_by', Auth::user()->id)->first();
        if($spamRequest) {
            return redirect()->back()->with("error",   \Lang::get('lang.comment-reported-already') ); 
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
                
                 // send mail
                 $country[] = $superadmin->country_id;
                 array_push($country, '-1');
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

        return redirect()->back()->with("success",  \Lang::get('lang.comment-reported') ); 
    }

    // reported comments by admin

    public function commentHide (Request $request) {

        $routeSlug = $this->getRouteSlug();
        
        request()->validate([
            'replyId' => 'required|exists:replies,id'
        ]);

        // add data in comment spam table
        $where = [
                    'reply_id' => $request->replyId,
                    'reported_by' => Auth::user()->id
                ];

        $reportExist = ReportedReply::where($where)->count();
               
        if($reportExist == 0) {

            ReportedReply::create([
                'reply_id' => $request->replyId,
                'reported_by' => Auth::user()->id,
                'reported_at' => Carbon::now(),
            ]);
        }    

        $update = [
            'is_hidden' => true,
            'hidden_by' => Auth::user()->id,  
        ];

        $isUpdated = Reply::where('id', $request->replyId)
                            ->update($update);

        if($isUpdated == 0)
        {
                return redirect()->back()->with("error",  \Lang::get('lang.unable-to-update') ); 
        }

        $replyOwner = Reply::with('owner', 'thread')->find($request->replyId);

        $threadLink = url($replyOwner->owner->getRoleNames()->first() . '/forum/threads/'. $replyOwner->thread->id);

        $superadminThreadLink = url( 'superadmin/forum/threads/'. $replyOwner->thread->id); 

        $superadminCommentLink = url('superadmin/forum/threads/' . $replyOwner->thread->id . '/' . $replyOwner->id . '/hidden');
       
        $commentLink  = url($replyOwner->owner->getRoleNames()->first() . '/forum/threads/'. $replyOwner->thread->id . '/' . $replyOwner->id . '/hidden');

        // send mail to superadmin
        $role = 'superadmin';
    
        $superadmins = User::whereHas("roles", function($query) use($role )
                        { 
                            $query->where("name", $role); 
                        })->select('email', 'country_id')->get();
            

        foreach ($superadmins as $superadmin) {

            if(is_null($superadmin->country_id))
            {
                Log::warning('Not sending email for ' . $superadmin->email . ' as country does not exist.');

            } else {

                 // send mail
                 $country[] = $superadmin->country_id;
                 array_push($country, '-1');
                $templateConfig = DB::table('user_mail_templates')
                                    ->join('mail_template_config', 'user_mail_templates.id', '=', 'mail_template_config.template_id')
                                    ->whereIn('mail_template_config.country_id', $country)
                                    ->where('user_mail_templates.mailable', 'App\Mail\HideReportCommentSuperadmin')->first();

                if($templateConfig != null) {
                        Mail::to($superadmin->email)
                            ->send(new \App\Mail\HideReportCommentSuperadmin( Auth::user()->name, $replyOwner->body, $replyOwner->thread->title, $superadminThreadLink, $superadminCommentLink, $templateConfig->template_id));

                } else {
                    Log::warning('Mail template HideReportCommentSuperadmin does not exist for country ' .  $superadmin->country_id);
                }
            }    

        }

        // send mail to user
        $userCountry[] = $replyOwner->owner->country_id;
        array_push($userCountry, '-1');
        $userRegion[] = $replyOwner->owner->region_id;
        array_push($userRegion, '-1');  
        $existMailTemplate = DB::table('user_mail_templates')
                            ->join('mail_template_config', 'user_mail_templates.id', '=', 'mail_template_config.template_id')
                            ->whereIn('mail_template_config.country_id', $userCountry)
                            ->whereIn('mail_template_config.region_id', $userRegion)
                            ->where('user_mail_templates.mailable', 'App\Mail\HideComment')->first();

        if($existMailTemplate != null) { 
            Mail::to($replyOwner->owner->email)
                ->send(new \App\Mail\HideComment($replyOwner->owner->name, Auth::user()->name, $replyOwner->body, $replyOwner->thread->title, $threadLink, $commentLink, $existMailTemplate->template_id));
        } else {
            Log::warning('Mail template HideComment does not exist for country ' .  $replyOwner->owner->country_id);
        }

        return redirect()->back()->with("success",  \Lang::get('lang.comment-reported') ); 
    }

    /****** for superadmin only ******/
    public function reportedComments (Request $request) {
        
        $user = Auth::user();
        $routeSlug = $this->getRouteSlug();
        
        $limit = 10;

        $query = ReportedReply::with('reply');

        $reported_replies = $query->orderBy('id', 'desc')->paginate($limit);
       
        return view('shared.threads.reported-comment', compact('reported_replies', 'routeSlug'))
               ->with('index', (request()->input('page', 1) - 1) * $limit);
    }


    // delete reported comment
    public function destroyComment($commentId)
    {
        $routeSlug = $this->getRouteSlug();
        $reply = Reply::findOrFail($commentId);

        Reply::where('id', $commentId)->delete();

        return redirect( $routeSlug . '/forum/threads/' . $reply->thread_id )->with("success", \Lang::get('lang.comment-deleted'));
      
    }

    // update comment
    public function updateComment (Request $request)
    {
        request()->validate([
            'replyId' => 'required|exists:replies,id',
            'type' => 'required'
        ]);

        if($request->type == 'hide') {

            $update = [
                'is_hidden' => true,
                'hidden_by' => Auth::user()->id,  
            ];
            $message = \Lang::get('lang.hide');

        } else {

            $update = [
                'is_hidden' => false,
                'hidden_by' => null,  
            ];
            $message = \Lang::get('lang.unhided');

            ReportedReply::where('reply_id', $request->replyId)->delete(); 
        }
            
        $isUpdated = Reply::where('id', $request->replyId)
                            ->update($update);
        if($isUpdated == 0)
        {
                return redirect()->back()->with("error",  \Lang::get('lang.unable-to-update') ); 
        }

        return redirect()->back()->with("success",  \Lang::get('lang.comment') .' '. $message); 
    }

    public function commentLink ($threadId, $commentId, $type) {
        
        $routeSlug = $this->getRouteSlug();
        $user = Auth::user();
        $comment = Reply::where('id', $commentId)->with('replies', 'parentDetails', 'thread')->first();
        $ownerRole = $comment->thread->creator->getRoleNames();
        $allLikes = ThreadLike::where('thread_id', $threadId)->count();
       
        return view('shared.threads.comment-mail-view', compact('threadId', 'commentId', 'comment', 'routeSlug', 'user', 'ownerRole', 'allLikes', 'type'));
        
    }

    public function editReply($id)
    {
        $routeSlug = $this->getRouteSlug();
        $reply = Reply::findOrfail($id);
        return view('shared.threads.edit_reply',compact('reply', 'routeSlug'));
    }

    public function updateReply(Request $request)
    {
        request()->validate([
            'reply_id' => 'required|exists:replies,id',
            'body' => 'required'
        ]);

        $reply = Reply::findOrFail($request->reply_id);
        $update = [
                'body' => $request->body
        ];

        // file upload
        $mediaFormats = implode(',', Config::get("constant.SUPPORTED_IMAGE_FORAMTS"));

        if($request->hasFile('image')) {
            request()->validate([
                'image' => 'mimes:' . $mediaFormats . '|max:1048576' // 1048 mb
            ]);

            //delete file from storage
            if($reply->image != null) {
                $imagePath = $this->storagePath .'/'. $reply->image;
                if(file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            $file = $request->file('image');

            $uploadFilename =  md5(microtime()) .'.'.$file->getClientOriginalExtension();

            // check directory exist or not
            $path = $this->storagePath;

            if(!is_dir($path)) {
                File::makeDirectory($path, $mode = 0775, true, true);
            }
            $file->move($this->storagePath, $uploadFilename);
            $update['image'] = $uploadFilename;
        }

        $isUpdated = Reply::where('id', $request->reply_id)
                            ->update($update);

        if($isUpdated == 0)
        {
            return redirect()->back()->with("error",  \Lang::get('lang.unable-to-update') );
        }
        $routeSlug = $this->getRouteSlug();
        return redirect( $routeSlug . '/forum/threads/'. $reply->thread_id )->with("success",  \Lang::get('lang.reply') .' '. \Lang::get('lang.updated-successfully'));
    }
}
