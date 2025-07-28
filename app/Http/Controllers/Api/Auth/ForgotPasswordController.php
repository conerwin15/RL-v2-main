<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\BaseController as BaseController;
use DB;
use Response;
use Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\{ User,JobRole, Group,GroupMapping,Course, LearningPath, CourseTracking,LearningPathMapping };
use Spatie\Permission\Models\Role;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;
use App\Rules\MatchOldPassword;
use App;

class ForgotPasswordController extends BaseController
{
    /** 
    ** forget password
    **/

    public function forgot(Request $request) {

        $lang = $request->lang_code ? $request->lang_code : 'en';
        App::setLocale($lang);

        $credentials = request()->validate([
            'email' => 'required'
        ]);


        $user = User::where('email', $request->email)->first();
        if(!$user) {
            return response()->json(['success' => false, 'error' => \Lang::get('lang.invalid-email-token')], 400);
        }

        Password::sendResetLink($credentials);
        return $this->sendResponse($request->email, \Lang::get('lang.reset-link')); 
    }

    public function reset() {
        $credentials = request()->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|confirmed'
        ]);
        $reset_password_status = Password::reset($credentials, function ($user, $password) {
            $user->password = $password;
            $user->save();
        });

        if ($reset_password_status == Password::INVALID_TOKEN) {
            return response()->json(["msg" =>  \Lang::get('lang.invalid-token')], 400);
        }
        return response()->json(["msg" =>  \Lang::get('lang.password-changed')]);
    }
}
