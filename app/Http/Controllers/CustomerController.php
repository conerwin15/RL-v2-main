<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Auth;
use Http;
use Hash;
use Config;
use Illuminate\Support\Facades\Session;
use Validator;
use App\Models\{User, Role, LearningPackage};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function login(Request $request)
    { 
        $packageIds = $request->packageIds;
        return view('learners.banner.package_login_form');
    }

    public function register()
    { 
        return view('learners.banner.package_register_form');
    }

    public function saveCustomer(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|same:confirm_password'
            ]);

            if (!$validator->passes()) {
                return response()->json(['success' => false, "message" => $validator->errors()], 200);
            }
            $input = $request->all();
            $input['password'] = Hash::make($input['password']);
            $user = User::create($input);
            $role = Role::where('name', 'staff')->first();
            $user->assignRole($role->name);
            Auth::login($user); //login

            Http::post("https://v2.reallybot.com/api/users/add-user", [
                "name" => $input['name'],
                "email" => $input['email'],
                "password" => "12345678"
            ]);

            return response()->json(['success' => true, "message" => "Customer has been created successfully."], 200);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['success' => false, "message" => \Lang::get('lang.generic-error')], 200);
        }
    }

    private function cartPackages()
    {
        $userId = Auth::id();
        if ($userId) {
            $cartPackageId = Cart::where('user_id', $userId)->get('package_id');
            $cartPackages = LearningPackage::whereIn('id', $cartPackageId)->get();


        } else {
            $cartPackageIds = Session::get('cart', []);
            $cartPackages = LearningPackage::whereIn('id', $cartPackageIds)->get();
        }
        return $cartPackages;
    }
}
