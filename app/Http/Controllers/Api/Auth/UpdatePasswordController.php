<?php

namespace App\Http\Controllers\Api\Auth;
use Illuminate\Http\Request;

use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\RequestHelper;
use Symfony\Component\HttpFoundation\Response;


class UpdatePasswordController extends BaseController
{
    public function updatePassword(RequestHelper $request){
        return $this->validateToken($request)->count() > 0 ? $this->changePassword($request) : $this->noToken();
    }

    private function validateToken($request){
        return DB::table('password_resets')->where([
            'email' => $request->email,
            'token' => $request->passwordToken
        ]);
    }

    private function noToken() {
        return response()->json([
          'error' => \Lang::get('lang.invalid-email-token')
        ],Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    private function changePassword($request) {
        $user = User::whereEmail($request->email)->first();
        $user->update([
          'password'=>bcrypt($request->password)
        ]);
        $this->validateToken($request)->delete();
        return response()->json([
          'data' => \Lang::get('lang.password-changed')
        ],Response::HTTP_CREATED);
    }  
}