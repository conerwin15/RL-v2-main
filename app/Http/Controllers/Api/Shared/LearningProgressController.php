<?php

namespace App\Http\Controllers\Api\Shared;

use Auth;
use JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{ UserLearningProgress };

class LearningProgressController extends Controller
{
    
    function getProgress($resourceid) {
        $user = Auth::user();
        $progress = UserLearningProgress::where('user_id', $user->id)->where('learning_resource_id', $resourceid)->first();
        return response()->json(["success" => true, "data" => $progress]);
    }

    function saveProgress(Request $request, $role, $resourceid) {

        $decodedToken = base64_decode($request->header('llt'));
        JWTAuth::setToken($decodedToken);
        $user = JWTAuth::toUser();

        $user = Auth::user();
        $update = [
            'lesson_status' => $request->cmi["core"]["lesson_status"],
            'cmi_data' => $request->cmi
        ];

        if(($request->cmi['core']['score']['raw'] != null) && ($request->cmi['core']['score']['raw'] != 0))
        {
            $score = [
                'score' => $request->cmi['core']['score']['raw'],
                'max_score' => $request->cmi['core']['score']['max']
            ];

            $update = array_merge($update, $score);
        }

        UserLearningProgress::where('user_id', $user->id)
                ->where('learning_resource_id', $resourceid)
                ->update($update);

        return response()->json([ "success" => true ]);
    }

}
