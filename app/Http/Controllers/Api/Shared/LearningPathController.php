<?php

namespace App\Http\Controllers\Api\Shared;

use Config;
use Auth;
use Log;
use Exception;
use JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{ User, LearningPath, UserLearningProgress, UserLearningPath, LearnerBadge, ScromPackageResourceItem, ScormPackage, LearningPathResource };
use App\Events\PointUpdateEvent;
use App\Http\Controllers\Api\BaseController as BaseController;

class LearningPathController extends BaseController
{
    public function index(Request $request) {

        try{

            $page = $request->page ? $request->page : 1;
            $limit = 10;

            $learningPaths = array();
            $resources = array();
            $user = Auth::user();
            $query = $user->userLearningPaths()->with('badge', function ($q){
                            $q->select('id', 'name');
                        })->with('learningPath', function ($q){
                                $q->with('resources', function ($s){
                            });
                        });

            $userLearningPaths = $query->paginate($limit);

            $response['image_path'] = asset('storage' . Config::get('constant.LEARNING_PATH_STORAGE'));
            $response['learning_paths'] = $userLearningPaths->toArray();

            return $this->sendResponse($response, \Lang::get('lang.learning-path-list'));

        } catch(Exception $e) {
            Log::error($e);
            return $this->sendError($e, \Lang::get('lang.invalid-request'));
        }

    }

    public function launch(Request $request, $resourceid, $id) {
        try {
            $decodedToken = base64_decode($request->llt); // we are using this custom request param for the mobile app so that token can be sent
            JWTAuth::setToken($decodedToken);
            $user = JWTAuth::toUser();
            $progress = UserLearningProgress::where('user_id', Auth::user()->id)->where('learning_resource_id', $resourceid)->first();
            $scormItems = ScromPackageResourceItem::where('package_id', $id)->get();
            $scormPackage = ScormPackage::where('scorm_package_id', $id)->first();
            $routeSlug = Auth::user()->roles[0]->name;
            $isMobile = true;
            return view('learners.shared.learning-paths.show_learning_path', compact('scormItems', 'scormPackage', 'progress', 'resourceid', 'routeSlug', 'isMobile'));
        } catch(Exception $ex) {
            Log::error($ex);
        }

    }

    public function viewResource(Request $request, $resourceid) {
        $decodedToken = base64_decode($request->llt); // we are using this custom request param for the mobile app so that token can be sent
        JWTAuth::setToken($decodedToken);
        $user = JWTAuth::toUser();

        $resource = LearningPathResource::findOrFail($resourceid);
        $progress = UserLearningProgress::where('user_id', $user->id)->where('learning_resource_id', $resourceid)->first();
        if(!$progress) {
            UserLearningProgress::create([
                'user_id' => $user->id,
                'learning_resource_id' => $resourceid
            ]);
        }

        if($resource->type == 'course_link') {
            return redirect('/api/scorm/learning-paths/course/' . $resource->id . '/' . $resource->scorm_package_id . '?llt=' . $request->llt);
        } else {
            $link = $resource->link;
            return view('learners.shared.learning-paths.show_learning_resource', compact('link'));
        }

    }

    public function show($id) {
        try {
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
                        $resource->status = $resource->type == 'course_link' ? $progressItem->lesson_status : 'visited';
                        if($resource->type != 'course_link') {
                            $completedCount++;
                        } else if($progressItem->lesson_status == 'completed' || $progressItem->lesson_status == 'passed' || $progressItem->lesson_status == 'failed') {
                            $completedCount++;
                        }
                    }
                }
            }

            $completedPercentage = round(($completedCount / $totalResources) * 100);
            UserLearningPath::where('user_id', Auth::user()->id)
                ->where('learning_path_id', $learningPath->id)->update([ 'progress_percentage' => $completedPercentage ]);

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
                    }
                }
                UserLearningPath::where('user_id', Auth::user()->id)
                                    ->where('learning_path_id', $learningPath->id)->update([ 'badge_id' => $badgeToAssign ]);

            }

            $response['badge'] = $badge;
            $response['badgeUpgraded'] = $badgeUpgraded;
            $response['currentBadge'] = $currentBadge;
            $response['userLearningPath'] = $userLearningPath;
            $response['learningPath'] = $learningPath;
            $response['badge_image_path'] = asset('assets/images/');
            $response['image_path'] = asset('storage' . Config::get('constant.LEARNING_PATH_STORAGE'));

            return $this->sendResponse($response,  \Lang::get('lang.learning-path-list'));
        } catch(Exception $e) {
            Log::error($e);
            return $this->sendError($e, \Lang::get('lang.invalid-request'));
        }
    }


public function destroy($id)
{
    $role = Auth::user()->getRoleNames()->first();

    // Allow only admin or superadmin
    if (!in_array($role, ['admin'])) {
        return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
    }
     if (!in_array($role, ['superadmin'])) {
        return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
    }

    $learningPath = LearningPath::findOrFail($id);

    // Optional: delete the image file if exists
  if ($learningPath->featured_image && \Storage::disk('public')->exists('learning-paths/' . $learningPath->featured_image)) {
       \Storage::disk('public')->delete('learning-paths/' . $learningPath->featured_image);
  }

    // ❗ This deletes the record from the DB
    $learningPath->delete();

    return response()->json([
        'success' => true,
        'message' => 'Learning Path deleted successfully.'
    ]);

  }

}
