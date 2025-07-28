<?php

namespace App\Http\Controllers\Shared;

use Auth;
use Config;
use File;
use Log;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{ User, JobRole, Role, Country, Region, Group, UserLearningPath };

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $loggedUser = Auth::user();
        $user = User::with('roles', 'country', 'region', 'jobRole', 'group', 'totalPoints')->where('id', $loggedUser->id)->first();

        $userLearningPaths = UserLearningPath::where('user_id', Auth::user()->id)->whereNotNull('badge_id')->with('badge', 'learningPath')->get();
        $routeSlug = $this->getRouteSlug();
        return view('shared.account.index', compact('user', 'userLearningPaths', 'routeSlug'));
    }

    public function uploadPicture(Request $request){
       
        try 
        {               
                $file = $request->file('profile-picture');
                $extension = $file->getClientOriginalExtension();

                // check directory exist or not 
                $path = storage_path('app/public' . Config::get('constant.PROFILE_PICTURES'));
                if(!is_dir($path)) {
                    File::makeDirectory($path, $mode = 0775, true, true);
                }

                //delete file from storage
                $oldImage = User::find(Auth::user()->id);
                
                if($oldImage->image != null) {
                    if(file_exists($path . $oldImage->image)){
                        unlink($path . $oldImage->image);
                    }
                } 

                $fileName = md5(microtime()) . '.' . $extension;
                $file->move($path, $fileName);
                
                $user = new User();
                $update = [
                        'image' => $fileName
                ];  
                
                $isUpdated = User::where('id', Auth::user()->id)->update($update);
                if($isUpdated == 0)
                {
                    return response()->json(['success' => true, "messsage" => "Profile image not uploaded. "], 200);
                } else {
                    return response()->json(['success' => true, "messsage" => "Profile image uploaded sucessfully. "], 200);
                }              
        } catch (Exception $e) {
            Log::error($e); 
            return response()->json(['success' => false, "messsage"=> \Lang::get('lang.generic-error')], 200);
        }                                         
    }

    protected function getRouteSlug() {
        $user = Auth::user();
        return $user->getRoleNames()->first();
    }

}
