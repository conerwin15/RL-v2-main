<?php

namespace App\Http\Controllers\Api\Shared;

use Exception;
use Log;
use Auth;
use Illuminate\Http\Request;
use App\Models\{ User, UserLanguage };
use App\Http\Controllers\Api\BaseController as BaseController;

class UpdatedeviceIdController extends BaseController
{
    public function updateDeviceId (Request $request)
    {
        try {

            $user = Auth::user();
            request()->validate([
                'device_id' => 'required', 
                'lang_code' => 'required'
            ]);

            $userLanguage = UserLanguage::where('user_id', $user->id)->orWhere('device_id', $request->device_id)->first();
           
            if(is_null($userLanguage)) {
                // insert
               $userLanguage = new UserLanguage;
               $userLanguage->user_id = $user->id;
               $userLanguage->device_id = $request->device_id;
               $userLanguage->lang_code = $request->lang_code;
               $userLanguage->save();

               return $this->sendResponse('true', \Lang::get('lang.device-lang') .' '. \Lang::get('lang.added-successfully')); 

            } else {
                //update
                $update = [
                    'user_id' => $user->id,
                    'device_id' => $request->device_id,
                    'lang_code' => $request->lang_code,
                ];
        
                $isUpdated = UserLanguage::where('id', $userLanguage->id)->update($update);
        
                if($isUpdated == 0)
                {
                    return $this->sendResponse(false, \Lang::get('lang.unable-to-update') ); 
                } else {
                    return $this->sendResponse(true, \Lang::get('lang.device-lang') .' '. \Lang::get('lang.updated-successfully')); 
                }
            }

        } catch (Exception $e) {
            Log::error($e);
            return $this->sendError($e, \Lang::get('lang.invalid-request'));
        } 
    }
}
