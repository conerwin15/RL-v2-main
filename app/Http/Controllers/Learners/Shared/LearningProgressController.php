<?php

namespace App\Http\Controllers\Learners\Shared;

use Auth;
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

    function saveProgress(Request $request, $resourceid) {
        $user = Auth::user();

        if($request->cmi["core"]["lesson_status"] == 'passed')
        {
            $update = [
                'lesson_status' => $request->cmi["core"]["lesson_status"],
                'cmi_data' => $request->cmi,
                'end_date' => date('Y-m-d h:i:s')
            ];

        } else {

            $update = [
                'lesson_status' => $request->cmi["core"]["lesson_status"],
                'cmi_data' => $request->cmi
            ];
        }

        if(($request->cmi['core']['score']['raw'] != null) && ($request->cmi['core']['score']['raw'] != 0))
        {
            $score = [
                'score' => $request->cmi['core']['score']['raw'],
                'max_score' => $request->cmi['core']['score']['max']
            ];

            $update = array_merge($update, $score);
        }

        $progress = UserLearningProgress::where('user_id', $user->id)->where('learning_resource_id', $resourceid)->first();
        if($progress && ($progress->lesson_status == 'passed' || $progress->lesson_status == 'complete')) {
            return response()->json([ "success" => true ]);
        }

        UserLearningProgress::where('user_id', $user->id)
                ->where('learning_resource_id', $resourceid)
                ->update($update);

        return response()->json([ "success" => true ]);
    }

}
