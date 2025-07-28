<?php

namespace App\Http\Controllers\Api\Shared;

use Auth;
use Config;
use Hash;
use Exception;
use Log;
use Validator;
use Illuminate\Http\Request;
use App\Models\{ User, UserLearningPath };
use App\Http\Controllers\Api\BaseController as BaseController;

class AccountController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            $loggedUser = Auth::user();
            $user = User::with('roles', 'country', 'region', 'jobRole', 'group', 'totalPoints')->where('id', $loggedUser->id)->first();
            $response['name'] = $user->name;
            $response['email'] = $user->email;
            $response['image'] = $user->image;
            $response['country'] = $user->country->name;
            $response['region'] = $user->region->name;
            $response['job_role'] = $user->jobRole ? $user->jobRole->name : 'N/A';
            $response['total_points'] = count($user->totalPoints) > 0 ? $user->totalPoints[0]->totalPoints : 0;
            $response['dealer']  = $user->dealer ? $user->dealer->name : null;
         
            $response['image_path'] = asset('storage' . Config::get('constant.PROFILE_PICTURES'));
        
            return $this->sendResponse($response, \Lang::get('lang.user-profile'));  
        } catch (Exception $e) {
            Log::error($e);
            return $this->sendError($e, \Lang::get('lang.invalid-request'));
        }     
        
    }

    public function badges(Request $request) {
        try {

            $loggedUser = Auth::user();
            $response['learning_paths'] = UserLearningPath::where('user_id', Auth::user()->id)->whereNotNull('badge_id')->with('badge', 'learningPath')->get();; 
            $response['image_path'] = asset('storage' . Config::get('constant.LEARNING_PATH_STORAGE'));
            $response['badge_image_path'] = asset('assets/images/');
        
            return $this->sendResponse($response, \Lang::get('lang.learning-path-detail'));  
        } catch (Exception $e) {
            Log::error($e);
            return $this->sendError($e, \Lang::get('lang.invalid-request'));
        }     
    }

    /**
     * Upload Picture.
     *
     * @return \Illuminate\Http\Response
     */
    public function uploadPicture(Request $request)
    {
        try 
        {       
                $user = Auth::user() ;     
                $file = $request->file('profile-picture');
                $extension = $file->getClientOriginalExtension();
               
                // check directory exist or not 
                $path = storage_path('app/public' . Config::get('constant.PROFILE_PICTURES'));
              
                if(!is_dir($path)) {
                    File::makeDirectory($path, $mode = 0775, true, true);
                }

                //delete file from storage
                if($user->image != null) {
                    if(file_exists($path . $user->image)){
                        unlink($path . $user->image);
                    }
                }
            
                $fileName = md5(microtime()) . '.' . $extension;
                $file->move($path, $fileName);
                
                $update = [
                        'image' => $fileName
                ];  
              
                $isUpdated = User::where('id', $user->id)->update($update);
        
                if($isUpdated == 0)
                {
                    return $this->sendResponse(false, \Lang::get('lang.profile-image-not-uploaded'));  
                } else {
                    return $this->sendResponse(true, \Lang::get('lang.profile-image-uploaded'));
                }              
        } catch (Exception $e) {
            Log::error($e);
            return $this->sendError($e, \Lang::get('lang.invalid-request'));
        }  
    }

     /**
     * Chnage Password.
     *
     * @return \Illuminate\Http\Response
     */
    public function changePassword(Request $request)
    {
        try 
        {   
            $validator = Validator::make($request->all(), [
                'old_password'     => 'required',
                'new_password'     => 'required|different:old_password',
                'confirm_password' => 'required|same:new_password',
                ],
                [
                'old_password.required'     => \Lang::get('lang.old-password-required'), // custom message
                'new_password.required'     => \Lang::get('lang.new-password-required'), // custom message
                'confirm_password.required' => \Lang::get('lang.confirm-password-required'), // custom message
                'confirm_password.same'     => \Lang::get('lang.confirm-password-new-password-match'), // custom message
                'new_password.different'    => \Lang::get('lang.new-password-different') // custom message
                ]
            );

            if (!$validator->passes()) {
                return response()->json(['success' => false, "messsage" => $validator->errors()], 200);
            } 
           
            $user = User::find(auth()->user()->id);
            
            if (!Hash::check($request->old_password, $user->password)) {
                return $this->sendError(\Lang::get('lang.password-match-error')); 
            } else{
                $isUpdated = User::find($user->id)->update(['password'=> Hash::make($request->new_password)]);
            }    
            return $this->sendResponse(true, \Lang::get('lang.password-changed'));
               
        } catch (Exception $e) {
            Log::error($e);
            return $this->sendError($e, \Lang::get('lang.invalid-request'));
        }   
    }

    public function rewards(Request $request)
    {
        try{
            $page = $request->page ? $request->page : 1;
            $limit = 10;
            $response['rewards'] = UserLearningPath::where('user_id', Auth::user()->id)->whereNotNull('badge_id')->with('learningPath','badge')->paginate($limit);
            $response['badge_image'] = asset('assets/images/');
            return $this->sendResponse($response, \Lang::get('lang.user-reward'));    
        } catch(Exception $e) {
            Log::error($e);
            return $this->sendError($e, \Lang::get('lang.invalid-request'));
        }
        
    }
}
