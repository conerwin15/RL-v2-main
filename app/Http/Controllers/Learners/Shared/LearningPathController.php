<?php

namespace App\Http\Controllers\Learners\Shared;

use DB;
use Log;
use PDF;
use Auth;
use Mail;
use Config;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Events\PointUpdateEvent;
use App\Models\{ User, LearningPath, Group, Course ,JobRole, LearningPathResource, ScormPackage, ScromPackageResourceItem, UserLearningProgress, Certificate, LearnerBadge, UserLearningPath, Thread };
use Spatie\MailTemplates\Models\MailTemplate;
use Spatie\MailTemplates\TemplateMailable;
use App\Mail\CertificateMail;

class LearningPathController extends Controller
{

    public function index(Request $request) {
        $user = Auth::user();
        $id = $user->id;
   
        $page = $request->page ? $request->page : 1;
        $limit = 10;

        $search = trim($request->query('search'));
        $routeSlug = $this->getRouteSlug(); 
        $query = $user->userLearningPaths();

        if (!empty($search)) {
            $userLearningPaths = $query->with('learningPath', function ($q) use ($search){
                $q->where('name', 'like', '%' . $search . '%');
            })->paginate($limit);

            $learningPathCount = UserLearningPath::whereHas('learningPath', function ($s) use ($search){
                $s->where('name', 'like', '%' . $search . '%');   
            })->where('user_id', $id)->count();

        }else{
            $userLearningPaths = $query->with('learningPath')->paginate($limit);
            $learningPathCount = UserLearningPath::where('user_id', $id)->count();
        }

        return view('learners.shared.learning-paths.index', compact('userLearningPaths','routeSlug', 'learningPathCount'))->with('index', ($page - 1) * $limit);
     
    }

    public function show($id) {

        $learningPath = LearningPath::with('resources')->findOrFail($id);
        $resourceIds = [];
        foreach($learningPath->resources as $resource) {
            array_push($resourceIds, $resource->id);
        }


        // BADGE LOGIC, need to be decentralise
        $progressData = UserLearningProgress::where('user_id', Auth::user()->id)->whereIn('learning_resource_id', $resourceIds)->get();
        $totalResources = $learningPath->resources->count();

        $completedCount = 0;
        foreach($progressData as $progressItem) {
            foreach($learningPath->resources as $resource) {
                if($resource->id == $progressItem->learning_resource_id) {
                    $resource->status = $resource->type == 'course_link' ? $progressItem->lesson_status : 'visited' ;
                    if($resource->type == 'course_link') {
                        if($progressItem->lesson_status == 'completed' || $progressItem->lesson_status == 'passed' || $progressItem->lesson_status == 'failed') {
                            $completedCount++;
                        }
                    } else {
                        $completedCount++;
                    }
                }
            }
        } 

        $completedPercentage = round(($completedCount / $totalResources) * 100);
        UserLearningPath::where('user_id', Auth::user()->id)
            ->where('learning_path_id', $learningPath->id)->update([ 'progress_percentage' => $completedPercentage]);

        $badgeToAssign = null;
        $badgeName = null;
        $eventType = null;
        $badge = null;    
        if($completedPercentage >= 25 && $completedPercentage < 50) {
            $badgeName = 'bronze';
            $eventType = Config::get('constant.BRONZE_BADGE');
        } else if($completedPercentage >= 50 && $completedPercentage < 75) {
            $badgeName = 'silver';
            $eventType = Config::get('constant.SILVER_BADGE');
        } else if($completedPercentage >= 75 && $completedPercentage < 100) {
            $badgeName = 'gold';
            $eventType = Config::get('constant.GOLD_BADGE');
        } else if($completedPercentage == 100) {
            $badgeName = 'diamond';
            $eventType = Config::get('constant.DIAMOND_BADGE');
        }

        $badgeUpgraded = false;

        $userLearningPath = UserLearningPath::where('user_id', Auth::user()->id)->where('learning_path_id', $learningPath->id)->with('badge', 'assignBy')->first();
        $currentBadge = $userLearningPath->badge;
     
        if($badgeName != null) {
           
            $badge = LearnerBadge::where('name', $badgeName)->select('id', 'name', 'image')->first();
            $badgeToAssign = $badge->id;
            if($currentBadge == null || $currentBadge->id != $badge->id) {
                $badgeUpgraded = true;
                if($eventType != null) {
                    event(new PointUpdateEvent($eventType,  Auth::user()));
                    // send certificate
                    if($learningPath->certificate_id != null && $userLearningPath->progress_percentage == 100 && $badgeUpgraded == true && $userLearningPath->badge_id !=4) {
                        $this->sendCertificate($id);
                    }
                }
            }
        
            if($userLearningPath->progress_percentage == 100 && $badgeToAssign == 4)
            {
                $update = [
                                'end_date' => date('Y-m-d h:i:s'),
                                'badge_id' => $badgeToAssign
                ];
            } else {
                $update = [
                             'badge_id' => $badgeToAssign
                ];
            }
            UserLearningPath::where('user_id', Auth::user()->id)
                                ->where('learning_path_id', $learningPath->id)->update($update);
            
        }

        $routeSlug = $this->getRouteSlug();
        return view('learners.shared.learning-paths.show', compact('learningPath','routeSlug', 'badge', 'badgeUpgraded', 'currentBadge', 'userLearningPath'));
    }
    public function viewResource(Request $request, $resourceid) {
        $user = Auth::user();
        $resource = LearningPathResource::findOrFail($resourceid);
        $progress = UserLearningProgress::where('user_id', $user->id)->where('learning_resource_id', $resourceid)->first();

        // add start date in user_learning_path table
        $sDate = UserLearningPath::where('learning_path_id', $resource->learning_path_id)->where('user_id', Auth::user()->id)->pluck('start_date')->first();

        if($sDate == null)
        {
            UserLearningPath::where('learning_path_id', $resource->learning_path_id)->where('user_id', Auth::user()->id)->update(['start_date' => date('Y-m-d h:i:s')]);
        }

        if(!$progress) {
            $input = array();
            $input['user_id'] = $user->id;
            $input['learning_resource_id'] = $resourceid;
            $input['start_date'] = date('Y-m-d h:i:s');
            if($resource->type != 'course_link')
            {
                $input['end_date'] = date('Y-m-d h:i:s');
            }

            UserLearningProgress::create($input);
        }

        $routeSlug = $this->getRouteSlug();
        if($resource->type == 'course_link') {
            return redirect($routeSlug .'/learning-paths/course/' . $resource->id . '/' . $resource->scorm_package_id);
        } else {
            $link = $resource->link;
            return view('learners.shared.learning-paths.show_learning_resource', compact('link'));
        }
    }
    
    public function launch($resourceid, $id) {
        $progress = UserLearningProgress::where('user_id', Auth::user()->id)->where('learning_resource_id', $resourceid)->first();
        $scormItems = ScromPackageResourceItem::where('package_id', $id)->get();
        $scormPackage = ScormPackage::where('scorm_package_id', $id)->first();
        $routeSlug = $this->getRouteSlug();
        return view('learners.shared.learning-paths.show_learning_path', compact('scormItems', 'scormPackage', 'progress', 'resourceid', 'routeSlug'));
    }

    public function sendCertificate($id) {
    
        $user = Auth::user();
        $region[] = $user->region_id;
        array_push($region, "-1");
        $country[] = $user->country_id;
        array_push($country, "-1");
        $learningPath = LearningPath::with('certificate')->findOrFail($id);
        $certificate = Certificate::find($learningPath->certificate_id);
       
        $pathname = $learningPath->name;

        if($certificate->is_master == true) {
            $pdf = PDF::loadView('creator.superadmin.certificate.master', ['learnername' => ucwords($user->name), 'pathname' => ucwords($pathname)])->setPaper('letter', 'landscape'); 
        } else {
            $certificate->content = str_replace("{{learnername}}", $user->name, $certificate->content);
            $certificate->content = str_replace("{{pathname}}", $pathname, $certificate->content);
            $pdf = PDF::loadView('creator.superadmin.certificate.pdf', compact('certificate')); 
        }
       
        // add condition to send mail
        $templateConfig = DB::table('user_mail_templates')
                         ->join('mail_template_config', 'user_mail_templates.id', '=', 'mail_template_config.template_id')
                         ->whereIN('mail_template_config.country_id', $country)
                         ->whereIN('mail_template_config.region_id', $region)
                         ->where('user_mail_templates.mailable', 'App\Mail\CertificateMail')->first();

        if($templateConfig != null) {
            $certificateEmail = new \App\Mail\CertificateMail($user->name, $learningPath->name, $templateConfig->template_id);
            $certificateTemplate = $certificateEmail->attachData($pdf->output(), 'certificate.pdf');
    
            Mail::to($user->email)->send($certificateTemplate);
            return true;
        } else {
            Log::warning('Mail template CertificateMail does not exist for country or region' . $user->country_id);
            return back()->with('error', \Lang::get('lang.mail-not-send'));
        }
    }

    public function previewPDF($learningPathId, $id)
    {
        $user = Auth::user();
        $learningPathDetail = UserLearningPath::with('learningPath','badge')->where('learning_path_id', $learningPathId)->where('user_id', $user->id)->firstOrFail();
        $certificate = Certificate::find($id);
        if($certificate->is_master == true)
        {
            $pdf = PDF::loadView('creator.superadmin.certificate.master', ['learnername' => ucwords($user->name), 'pathname' => ucwords($learningPathDetail->learningPath->name)])->setPaper('letter', 'landscape'); 
        } else {
            $certificate->content = str_replace("{{learnername}}", ucfirst($user->name), $certificate->content);
            $certificate->content = str_replace("{{pathname}}", ucfirst($learningPathDetail->learningPath->name), $certificate->content);
            $pdf = PDF::loadView('creator.superadmin.certificate.pdf', compact('certificate'));
        }
        return $pdf->stream('certificate.pdf');    
    }

    public function forumLearningPath()
    {
        $user = Auth::user();
        $region = Auth::user()->region_id;
        $routeSlug = $this->getRouteSlug();
        $viewStoragePath = storage_path('app/public' . Config::get('constant.THREAD_IMAGE_STORAGE_PATH'));
        $userLearningPaths = $user->userLearningPaths()->with('learningPath')->paginate(5);
        $publicThreads = Thread::select('id', 'title', 'body', 'category_id' ,'status', 'user_id', 'created_at', 'is_pinned', 'is_private', 'image', 'embedded_link')->where('is_hidden', false)->where('is_private', 0)->whereIn('status', [0, 1, 3])
                                ->with(['creator', 'category', 'replies'])
                                ->withCount('replies')
                                ->withCount('isLikedBy')->orderBy('id', 'desc')->limit(4)->get();

        $threadQuery = Thread::select('id', 'title', 'body', 'category_id' ,'status', 'user_id', 'created_at', 'is_pinned', 'is_private', 'image', 'embedded_link')->where('is_hidden', false)->where('is_private', 1)->whereIn('status', [0, 1, 3])
                             ->with(['creator', 'category', 'replies', 'threadCountries']);

        $threadQuery = $threadQuery->whereHas("threadCountries", function($query)
        {
            $query->where("country_id", Auth::user()->country_id);
            $query->where("region_id", Auth::user()->region_id);
        });

        $privateThreads =  $threadQuery->withCount('replies')
                                ->withCount('isLikedBy')->orderBy('id', 'desc')->limit(4)->get();
        return  view('learners.shared.forum_learningPath', compact('userLearningPaths', 'routeSlug', 'publicThreads', 'privateThreads', 'viewStoragePath'));
    }

    protected function getRouteSlug() 
    {
        $user = Auth::user();
        return $user->getRoleNames()->first();
    }
}
