<?php

namespace App\Http\Controllers\Shared;

use DB;
use Auth;
use File;
use Config;
use App\Events\PointUpdateEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Spatie\MailTemplates\Models\MailTemplate;
use Spatie\MailTemplates\TemplateMailable;
use App\Mail\{HideThread, DeleteHideThread, HideThreadSuperadmin, ReportThreadSuperadmin};
use App\Models\{ User, Thread, ThreadSubscription, ThreadLike, Reply, ThreadSpamRequest, ThreadCategory, ReplyLike, Country, Region, ThreadCountry, PinThread};

class ThreadController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public $storagePath;

    public function __construct()
    {
       $this->storagePath = storage_path('app/public' . Config::get('constant.THREAD_IMAGE_STORAGE_PATH'));
    }

    public function creator()
{
    return $this->belongsTo(User::class, 'user_id');
}

    public function index(Request $request)
{
    $limit = 10;
    $user = Auth::user();

    if ($user->getRoleNames()->first() === "superadmin") {
        $regions = Region::orderBy('name', 'asc')->get();
    } else {
        $regions = $user->adminRegions();
    }

    $export_countries = empty($request->query('filter_country')) ? 0 : implode(',', $request->query('filter_country'));
    $export_regions = empty($request->query('filter_region')) ? 0 : implode(',', $request->query('filter_region'));
    $threadCategories = ThreadCategory::where('status', 1)->select('id', 'name')->orderBy('name')->get();
    $countries = Country::where('status', 1)->orderBy('name')->get();

    $routeSlug = $this->getRouteSlug();
    $search = trim($request->query('search'));
    $forumType = $request->forum_type;

    $query = Thread::select('id', 'title', 'body', 'category_id', 'status', 'user_id', 'created_at', 'is_private');

    // Search: title, body, or creator's name
    if (!empty($search)) {
        $query->where(function ($q) use ($search) {
            $q->where('threads.title', 'like', '%' . $search . '%')
              ->orWhere('threads.body', 'like', '%' . $search . '%')
              ->orWhereHas('creator', function ($q2) use ($search) {
                  $q2->where('name', 'like', '%' . $search . '%');
              });
        });
    }

    // Filter by forum visibility
    if (!is_null($forumType) && $forumType == 1) {
        $query = $query->where('is_private', 1);
    } else {
        $query = $query->where('is_private', 0);
    }

    // Filter by category
    if (!empty($request->category) && $request->category != -1) {
        $query = $query->where('category_id', $request->category);
    }

    $threadsQuery = $query->where('is_hidden', false)
        ->whereIn('status', [0, 1, 3])
        ->with(['creator', 'category', 'threadCountries'])
        ->with('threadSubscriptions', function ($q) {
            $q->where('user_id', Auth::user()->id);
        })
        ->with('pinThreads', function ($q) {
            $q->where('pinned_by', Auth::user()->id);
        })
        ->withCount(['replies', 'isLikedBy']);

    if ($user->getRoleNames()->first() === "superadmin") {
        $threadsQuery->withCount('spamRequests');
    }

    // Filter by selected countries
    if (!empty($request->filter_country)) {
        $country = $request->filter_country;
        $threadsQuery->whereHas('threadCountries', function ($query) use ($country) {
            $query->whereIn('country_id', $country);
        });
    }

    // Filter by selected regions
    if (!empty($request->filter_region)) {
        $region = $request->filter_region;
        $threadsQuery->whereHas('threadCountries', function ($query) use ($region) {
            $query->whereIn('region_id', $region);
        });
    }

    // Limit visibility for dealer or staff in private forum
    if ($forumType == 1 && in_array($user->getRoleNames()->first(), ['dealer', 'staff'])) {
        $threadsQuery->whereHas('threadCountries', function ($query) {
            $query->where('region_id', Auth::user()->region_id);
        });
    }

    // Custom order: pinned threads first
    $threadsQuery = $threadsQuery->orderBy(function ($q) {
        return $q->from('pin_threads')
                 ->whereRaw('`pin_threads`.thread_id = `threads`.id')
                 ->where('pinned_by', Auth::user()->id)
                 ->select('thread_id');
    }, 'desc');

    $threads = $threadsQuery->orderBy('created_at', 'desc')->paginate($limit);

    return view('shared.threads.index', compact(
        'threads',
        'user',
        'routeSlug',
        'threadCategories',
        'countries',
        'regions',
        'export_countries',
        'export_regions'
    ))->with('index', (request()->input('page', 1) - 1) * $limit);
}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();
        $routeSlug = $this->getRouteSlug();
        $regions = '';
        if($user->getRoleNames()->first() == "admin"){
            $regions = $user->adminRegions();
        }
        $countries = Country::where('status', 1)->orderBy('name')->get();
        $threadCategories = ThreadCategory::where('status', 1)->select('id', 'name')->orderBy('name')->get();

        return view('shared.threads.create', compact('user', 'routeSlug', 'threadCategories', 'countries', 'regions'));
    }

    /**
     * Store a newly created resource in storage. //
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        request()->validate([
                'forum_type' => 'required',
                'title' => 'required|min:6',
                'category' => 'required|exists:thread_categories,id',
                'description_type' => 'required',
        ]);

        $thread           = new Thread;
        $thread->user_id  = $user->id;
        $thread->is_private = $request->forum_type;
        $thread->title    = $request->title;
        $thread->category_id = $request->category;
        if($request->description_type == 0)
        {
            $thread->body = $request->body;
        } else {
            $thread->embedded_link = $request->embedded_link;
        }

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
            $thread->image = $uploadFilename;

        }
        $thread->save();

        // thread country
        if($request->forum_type == 1)
        {
            if(($this->getRouteSlug() == 'dealer') || ($this->getRouteSlug() == 'staff')){
                $threadCountry = new ThreadCountry;
                $threadCountry->thread_id = $thread->id;
                $threadCountry->country_id = $user->country_id;
                $threadCountry->region_id = $user->region_id;
                $threadCountry->save();
            }
            elseif($this->getRouteSlug() == 'admin')
            {
                foreach($request->region as $region)
                {
                    $threadCountry = new ThreadCountry;
                    $threadCountry->thread_id = $thread->id;
                    $threadCountry->country_id = $user->country_id;
                    $threadCountry->region_id = $region;
                    $threadCountry->save();
                }
            } else {
                foreach($request->country as $country)
                {
                    foreach($request->region as $region)
                    {
                        // check region for country
                        $regionArr = Region::where('country_id', $country)->pluck('id')->toArray();

                        if(in_array($region, $regionArr))
                        {
                            $threadCountry = new ThreadCountry;
                            $threadCountry->thread_id = $thread->id;
                            $threadCountry->country_id = $country;
                            $threadCountry->region_id = $region;
                            $threadCountry->save();
                        }
                    }
                }
            }
        }

        //thread subscription
        $threadSubscription = new ThreadSubscription;
        $threadSubscription->thread_id = $thread->id;
        $threadSubscription->user_id = $user->id;
        $threadSubscription->save();

        $eventType = Config::get('constant.NEW_POST');
        event(new PointUpdateEvent($eventType,  Auth::user()));

        $href = url($this->getRouteSlug() . '/forum/threads');
        return redirect($href)->with('success', \Lang::get('lang.thread').' '.\Lang::get('lang.created-successfully'));
    }

    // spam thread
    public function reportSpam(Request $request, $id) {

        $thread = Thread::findOrFail($id);

        $spamRequest = ThreadSpamRequest::where('thread_id', $id)->where('reported_by', Auth::user()->id)->first();

        if($spamRequest) {
            if($request->ajax()){
                return response()->json(['success' => false, "messsage" => \Lang::get('lang.thread-reported-already')], 200);
            } else {
                return redirect()->back()->with("error",   \Lang::get('lang.thread-reported-already') );
            }
        }

        ThreadSpamRequest::create([
            'thread_id' => $id,
            'reported_by' => Auth::user()->id,
        ]);

         // send mail to superadmins

         $role = 'superadmin';
         $country[] = "-1";
         $threadLink = url( 'superadmin/forum/threads/'. $id);

         $superadmins = User::whereHas("roles", function($query) use($role)
                         {
                             $query->where("name", $role);
                         })->select('email', 'country_id')->get();

        foreach ($superadmins as $superadmin) {

             if(is_null($superadmin->country_id))
             {
               Log::warning('Not sending email for ' . $superadmin->email . ' as country does not exist.');

             } else {

                // send mail
                array_push($country, $superadmin->country_id);
                $templateConfig = DB::table('user_mail_templates')
                                ->join('mail_template_config', 'user_mail_templates.id', '=', 'mail_template_config.template_id')
                                ->whereIn('mail_template_config.country_id', $country)
                                ->where('user_mail_templates.mailable', 'App\Mail\ReportThreadSuperadmin')->first();

                if($templateConfig != null) {
                    Mail::to($superadmin->email)
                         ->send(new \App\Mail\ReportThreadSuperadmin( Auth::user()->name, $thread->title, $threadLink, $templateConfig->template_id));
                } else {
                    Log::warning('Mail template ReportThreadSuperadmin does not exist for country ' .  $superadmin->country_id);
                }
             }
        }

        if($request->ajax()){
            return response()->json(['success' => true, "messsage" => \Lang::get('lang.thread-reported')], 200);
        }
        return redirect()->back()->with("success",  \Lang::get('lang.thread-reported') );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $limit = 5;
        $user = Auth::user();

        $thread = Thread::findOrFail($id);
        $ownerRole = $thread->creator->getRoleNames();

        if($user->getRoleNames()[0] == 'superadmin') {

            $where = [
                'thread_id' => $thread->id,

            ];

        } else {

            $where = [
                'is_hidden' => false,
                'thread_id' => $thread->id,

            ];
        }

        // get reply with like count and replies count
        $replies = Reply::withCount('replyLike')->withCount('replies')->withCount('spamComments')->where($where)->whereNull('parent')->paginate($limit);

        $routeSlug = $this->getRouteSlug();
        $isSubscriber = ThreadSubscription::where('thread_id',$id)->where('user_id', $user->id)->first();
        $isLiked = ThreadLike::where('thread_id',$id)->where('user_id', $user->id)->first();
        $allLikes = ThreadLike::where('thread_id', $id)->count();
        $pinnedThread = PinThread::where('thread_id', $id)->where('pinned_by', Auth::user()->id)->first();

        $isReplyLiked = ReplyLike::select('reply_id')->where('user_id', $user->id)->get()->toArray();

        return view('shared.threads.show', compact('thread', 'isSubscriber','isLiked' ,'user', 'routeSlug', 'replies', 'allLikes', 'isReplyLiked', 'ownerRole', 'pinnedThread'))
                    ->with('index', (request()->input('page', 1) - 1) * $limit);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = Auth::user();
        $thread = Thread::with('threadCountries')->where('id', $id)->first();
        $routeSlug = $this->getRouteSlug();

        $regions = '';
        $countries = Country::where('status', 1)->orderBy('name')->get();
        $regions = Region::where('status', 1)->orderBy('name')->get();
        $threadCategories = ThreadCategory::where('status', 1)->select('id', 'name')->get();
        $displayCountry = [];
        $displayRegion = [] ;
        $countryRecord = '';
        $regionRecord = '';

        if($user->getRoleNames()->first() == "superadmin" && $thread->is_private == 1){
            $displayCountry = ThreadCountry::where('thread_id', $id)->groupBy('country_id')->pluck('country_id')->toArray();
            $displayRegion = ThreadCountry::where('thread_id', $id)->groupBy('region_id')->pluck('region_id')->toArray();

            $countryRecord = (count($displayCountry) > 1) ? (count($displayCountry)) . ' Selected': Country::where('id', $displayCountry[0])->pluck('name')->first();
            $regionRecord = (count($displayRegion) > 1) ? (count($displayRegion)) . ' Selected': Region::where('id', $displayRegion[0])->pluck('name')->first();
        }

        if($user->getRoleNames()->first() == "admin" &&  $thread->is_private == 1){
            $regions = $user->adminRegions();
            $displayRegion = ThreadCountry::where('thread_id', $id)->groupBy('region_id')->pluck('region_id')->toArray();
            $regionRecord = (count($displayRegion) > 1) ? (count($displayRegion)) . ' Selected': Region::where('id', $displayRegion[0])->pluck('name')->first();
        }
        return view('shared.threads.edit',compact('thread', 'user', 'routeSlug', 'threadCategories', 'regions', 'countries', 'countryRecord', 'regionRecord', 'displayCountry', 'displayRegion'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        request()->validate([
            'thread_id' => 'required|exists:threads,id',
            'title' => 'required|min:6',
            'category' => 'required|exists:thread_categories,id'
        ]);

        $thread = Thread::findOrFail($request->thread_id);
        $update = [
                'title' => $request->title,
                'category_id' => $request->category
        ];

        if($request->hasFile('image'))
        {
            $mediaFormats = implode(',', Config::get("constant.SUPPORTED_IMAGE_FORAMTS"));
            request()->validate([
                'image' => 'mimes:' . $mediaFormats . '|max:1048576' // 1048 mb
            ]);

            //delete file from storage
                if($thread->image != null) {
                    $imagePath = $this->storagePath . $thread->image;
                    if(file_exists($imagePath)) {
                        unlink($imagePath);
                  }
            }

            $file = $request->file('image');

            $uploadFilename =  md5(microtime()) .'.'.$file->getClientOriginalExtension();

            // check directory exist or not
            $path = $this->storagePath;

            $file->move($this->storagePath, $uploadFilename);

            $update['image'] = $uploadFilename;
        }

        if(isset($request->embedded_link))
        {
            $update['embedded_link'] =  $request->embedded_link;
        } else {
            $update['body'] = $request->body;
        }

        $isUpdated = Thread::where('id', $request->thread_id)
                            ->update($update);

        if($isUpdated == 0)
        {
             return redirect()->back()->with("error",  \Lang::get('lang.unable-to-update') );
        }

        if($thread->is_private == 1){

        // delete country record
            $delete = ThreadCountry::where('thread_id', $id)->delete();

            if($this->getRouteSlug() == 'superadmin') {

                // enter country & region
                foreach($request->country as $country)
                {
                    foreach($request->region as $region)
                    {
                        // check region for country
                        $regionArr = Region::where('country_id', $country)->pluck('id')->toArray();

                        if(in_array($region, $regionArr))
                        {
                            $threadCountry = new ThreadCountry;
                            $threadCountry->thread_id = $thread->id;
                            $threadCountry->country_id = $country;
                            $threadCountry->region_id = $region;
                            $threadCountry->save();
                        }
                    }
                }
            } elseif($this->getRouteSlug() == 'admin')
            {
                foreach($request->region as $region)
                {
                    $threadCountry = new ThreadCountry;
                    $threadCountry->thread_id = $thread->id;
                    $threadCountry->country_id = Auth::user()->country_id;
                    $threadCountry->region_id = $region;
                    $threadCountry->save();
                }
            } else {
                $threadCountry = new ThreadCountry;
                $threadCountry->thread_id = $thread->id;
                $threadCountry->country_id = $user->country_id;
                $threadCountry->region_id = $user->region_id;
                $threadCountry->save();
            }
        }

        $routeSlug = $this->getRouteSlug();
        return redirect( $routeSlug . '/forum/threads' )->with("success",  \Lang::get('lang.thread') .' '. \Lang::get('lang.updated-successfully'));
    }

    protected function getRouteSlug() {
        $user = Auth::user();
        return $user->getRoleNames()->first();
    }

    public function updateThreadByStatus(Request $request) {
        request()->validate([
            'threadId' => 'required|exists:threads,id'
        ]);
        $isUpdated = Thread::where('id', $request->threadId)
                           ->update(['status'=> $request->status]);
        if($isUpdated == 0)
        {
            return redirect()->back()->with("error",  \Lang::get('lang.unable-to-update') );
        }

        $routeSlug = $this->getRouteSlug();
        return redirect( $routeSlug . '/forum/threads' )->with("success",  \Lang::get('lang.threads') .' '. \Lang::get('lang.updated-successfully'));
    }

    public function destroy ($threadId)
    {
        // remove from thread spam
        ThreadSpamRequest::where('thread_id', $threadId)->delete();

        // update parent reply
        $isUpdated = Reply::where('thread_id', $threadId)->update(['parent' => null]);
        if($isUpdated == 0)
        {
            // Remove reply
            $replies = Reply::where('thread_id', $threadId)->delete();
        }

        // delete from thread
        $isDeleted = Thread::where('id', $threadId)->delete();

        if($isDeleted == 0)
        {
            return back()->with('error', \Lang::get('lang.unable-to-delete'));
        }

        // Remove subscription
        ThreadSubscription::where('thread_id', $threadId)->delete();
        $routeSlug = $this->getRouteSlug();
        return redirect( $routeSlug . '/forum/threads' )->with("success", \Lang::get('lang.thread-delete'));
    }

    public function hide (Request $request)
    {
        $routeSlug = $this->getRouteSlug();
        request()->validate([
            'thread_id' => 'required|exists:threads,id'
        ]);

        // add data in thread spam table
        ThreadSpamRequest::create([
            'thread_id' => $request->thread_id,
            'reported_by' => Auth::user()->id,
        ]);

        $update = [
            'is_hidden' => true,
            'hidden_by' => Auth::user()->id,
        ];

        $isUpdated = Thread::where('id', $request->thread_id)
                            ->update($update);

        if($isUpdated == 0)
        {
                return redirect()->back()->with("error",  \Lang::get('lang.unable-to-update') );
        }

        $threadOwner = Thread::with('creator')->find($request->thread_id);

        // send mail to superadmins

        $role = 'superadmin';
        $country[] = "-1";
        $threadLink = url( 'superadmin/forum/threads/'. $threadOwner->id);

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
                array_push($country, $superadmin->country_id);
                $existMailTemplate = DB::table('user_mail_templates')
                                    ->join('mail_template_config', 'user_mail_templates.id', '=', 'mail_template_config.template_id')
                                    ->whereIn('mail_template_config.country_id', $country)
                                    ->where('user_mail_templates.mailable', 'App\Mail\HideThreadSuperadmin')->first();

                if($existMailTemplate != null) {

                    Mail::to($superadmin->email)
                        ->send(new \App\Mail\HideThreadSuperadmin( Auth::user()->name, $threadOwner->title, $threadLink, $existMailTemplate->template_id));

                } else {
                    Log::warning('Mail template HideReportCommentSuperadmin does not exist for country ' .  $superadmin->country_id);
                }
            }
        }


        // send mail to users

        $userCountry[] = $threadOwner->creator->country_id;
        array_push($userCountry, "-1");
        $userRegion[] = $threadOwner->creator->region_id;
        array_push($userRegion, "-1");

        $existMailTemplate = DB::table('user_mail_templates')
                            ->join('mail_template_config', 'user_mail_templates.id', '=', 'mail_template_config.template_id')
                            ->whereIn('mail_template_config.country_id', $userCountry)
                            ->whereIn('mail_template_config.region_id', $userRegion)
                            ->where('user_mail_templates.mailable', 'App\Mail\HideThread')->first();

        if($existMailTemplate != null) {

            Mail::to($threadOwner->creator->email)
                ->send(new \App\Mail\HideThread($threadOwner->title, $threadOwner->creator->name, $existMailTemplate->template_id));

        } else {
            Log::warning('Mail template HideThread does not exist for country ' .  $threadOwner->creator->country_id);
        }

        return redirect( $routeSlug . '/forum/threads' )->with("success",  \Lang::get('lang.thread') .' '. \Lang::get('lang.hide'));
    }

    /* for superadmin only */
    public function reportedThreads (Request $request )
    {
        $user = Auth::user();
        $threadCategories = ThreadCategory::where('status', 1)->select('id', 'name')->orderBy('name')->get();
        $routeSlug = $this->getRouteSlug();
        $search = trim($request->query('search'));
        $status = $request->query('status');
        $category = $request->category;

        $limit = 10;

        $query = ThreadSpamRequest::with(['thread' => function ($s){
                    $s->with(['creator', 'category']);
                }]);

        if (!empty($search)) {
            $query = $query->whereHas('thread', function ($q) use ($search){

                    $q->where('title', 'like', '%'.$search.'%')
                        ->orWhere('body', 'like', '%'.$search.'%');
                });

        }

        if(!empty($category) && $category != -1) {

            $query = $query->whereHas('thread', function ($q) use ($category){
                      $q->where('category_id', $category);
                    });
       }

        $reportedThreads = $query->orderBy('id', 'desc')->paginate($limit);

        return view('shared.threads.reported-thread', compact('reportedThreads', 'user', 'routeSlug', 'threadCategories'))
               ->with('index', (request()->input('page', 1) - 1) * $limit);
    }

    /* for superadmin only */
    public function updateThread (Request $request)
    {
        request()->validate([
            'thread_id' => 'required|exists:threads,id',
            'type' => 'required'
        ]);

        if($request->type == 'pin')
        {
            $pinThread = new PinThread;
            $pinThread->thread_id = $request->thread_id;
            $pinThread->pinned_by = Auth::user()->id;
            $pinThread->save();
            $message = \Lang::get('lang.pinned');
        } else if($request->type == 'unpin') {

            $where = ['thread_id' => $request->thread_id, 'pinned_by' => Auth::user()->id];
            $pinThread = PinThread::where($where)->first();
            $isDeleted = $pinThread->delete();

            if($isDeleted == 0)
            {
                return redirect()->back()->with('error', \Lang::get('lang. unable-to-delete'));
            }

            $message = \Lang::get('lang.unpinned');
        } else if ($request->type == 'unhide') {
            $update = [
                'is_hidden' => false,
                'hidden_by' => null,
            ];
            $isUpdated = Thread::where('id', $request->thread_id)
                               ->update($update);
            $message = \Lang::get('lang.unhided');

        } else {
            $update = [
                'is_hidden' => true,
                'hidden_by' => Auth::user()->id,
            ];
            $isUpdated = Thread::where('id', $request->thread_id)
                                ->update($update);
            $message = \Lang::get('lang.hide');
        }

        return redirect()->back()->with("success",  \Lang::get('lang.thread') .' '. $message);
    }

    /* for superadmin only */
    public function deleteReportedThread(Request $request) {

        $thread = Thread::with('creator')->find($request->thread_id);
        $email = $thread->creator->email;
        $name = $thread->creator->name;
        $isDeleted = $thread->delete();

        $country[] = $thread->creator->country_id;
        array_push($country, '-1');
        $region[] = $thread->creator->region_id;
        array_push($region, '-1');
        if($isDeleted == 0)
        {
            return redirect()->back()->with('error', \Lang::get('lang. unable-to-delete'));
        }

        // Remove reply
        Reply::where('thread_id', $request->thread_id)->delete();

        // Remove subscription
        ThreadSubscription::where('thread_id', $request->thread_id)->delete();

        // send mail
        $existMailTemplate = DB::table('user_mail_templates')
                            ->join('mail_template_config', 'user_mail_templates.id', '=', 'mail_template_config.template_id')
                            ->whereIN('mail_template_config.country_id', $country)
                            ->whereIN('mail_template_config.region_id', $region)
                            ->where('user_mail_templates.mailable', 'App\Mail\DeleteHideThread')->first();

        if($existMailTemplate != null) {
            Mail::to($email)
                ->send(new \App\Mail\DeleteHideThread($name, $thread->title, $existMailTemplate->template_id));
        } else {
            Log::warning('Mail template DeleteHideThread does not exist for country ' .  $thread->creator->country_id);
        }
        return redirect()->back()->with("success", \Lang::get('lang.thread-delete'));
    }

    public function previewThreadLink($id)
    {
        $thread = Thread::findOrfail($id);
        return view('shared.threads.show_embedded_link', compact('thread'));
    }

}
