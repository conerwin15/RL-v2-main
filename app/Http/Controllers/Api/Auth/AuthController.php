<?php

namespace App\Http\Controllers\Api\Auth;
use App\Http\Controllers\Api\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Validator;
use Config;
use App;
use Log;

class AuthController extends BaseController
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api, apiLocale', ['except' => ['login']]);
    }

    /**
     * Get a JWT token via given credentials.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $lang = $request->lang_code ? $request->lang_code : 'en';
        App::setLocale($lang);
        
        try {

            $credentials = $request->only('email', 'password');
            $token = $this->guard()->attempt($credentials);
       
            if($token && $this->guard()->user()->hasRole(['staff','dealer'])) {
                return $this->respondWithToken($token);
            }
            
            return response()->json(['error' =>  \Lang::get('lang.invalid-credentials')], 401);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['error' => \Lang::get('lang.invalid-credentials')], 401);
        }   

    }

    /**
     * Get the authenticated User
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return $this->sendResponse($this->guard()->user(), \Lang::get('lang.user-logout'));
    }

    /**
     * Log the user out (Invalidate the token)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $this->guard()->logout();
        return response()->json(['message' => \Lang::get('lang.successfully-logout')]); 
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => $this->guard()->factory()->getTTL() * 7200,
            'user'         => $this->guard()->user(),
            'image_path'   => asset('storage' . Config::get('constant.PROFILE_PICTURES'))
        ]);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard('api');
    }

    
}