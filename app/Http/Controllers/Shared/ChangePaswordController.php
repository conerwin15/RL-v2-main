<?php

namespace App\Http\Controllers\Shared;

use Auth;
use Hash;
use Validator;
use Log;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use App\Rules\MatchOldPassword;
use App\Models\{ User };

class ChangePaswordController extends Controller
{
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $routeSlug = $this->getRouteSlug();
        return view('shared.change-password.index', compact('user','routeSlug'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
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
        
            $user_password = \Auth::User()->password;
            
            if(!(\Hash::check($request->input('old_password'), $user_password)))
            {
                return response()->json(['success' => false, "messsage" => \Lang::get('lang.incorrect-old-password')], 200);
            }

            $isUpdated = User::find(auth()->user()->id)->update(['password'=> Hash::make($request->new_password)]);

            if($isUpdated)
                return response()->json(['success' => true, "messsage" => \Lang::get('lang.password-changed')], 200);
            else
                return response()->json(['success' => false, "messsage" => \Lang::get('lang.unable-to-update-password')], 200);

               
        } catch (Exception $e) {
            Log::error($e); 
            return response()->json(['success' => false, "messsage"=> \Lang::get('lang.generic-error')], 200);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password',
            ],

            [
            'new_password.required'  => \Lang::get('lang.new-password-required'), // custom message
            'confirm_password.required' => \Lang::get('lang.confirm-password-required'), // custom message
            'confirm_password.same'     => \Lang::get('lang.confirm-password-new-password-match'), // custom message
            ]
            );
            if (!$validator->passes()) {
                return response()->json(['success' => false, "messsage" => $validator->errors()], 200);
            }
        $user = User::findOrFail($id);
        $isUpdated = $user->update(['password'=> Hash::make($request->new_password)]);

        if($isUpdated)
            return response()->json(['success' => true, "messsage" => \Lang::get('lang.password-updated')], 200);
        else
            return response()->json(['success' => false, "messsage" => "Unable to update password"], 200);
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