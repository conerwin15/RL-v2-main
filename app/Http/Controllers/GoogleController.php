<?php

namespace App\Http\Controllers;

use Http;
use Hash;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use App\Models\{User, Role};
use Illuminate\Support\Facades\Auth;

class GoogleController extends Controller
{
    public function redirectToGoogle(Request $request)
    {
        \Log::debug("helo");
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {

            $user = Socialite::driver('google')->user();
            $finduser = User::where('google_id', $user['id'])->first();

            if($finduser){

                Auth::login($finduser);
                return redirect('staff/home');

            }else{

             /*   // chatbot account creation
                Http::post("https://v2.reallybot.com/api/users/add-user", [
                    "name" => $user['name'],
                    "email" => $user['email'],
                    "password" => "12345678"
                ]);

                $newUser = User::create([
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'password' => Hash::make('12345678'),
                    'google_id' => $user['id']
                ]);*/

    $newUser = User::create([
    'name' => $googleUser->getName(),
    'email' => $googleUser->getEmail(),
    'password' => Hash::make('12345678'),
    'google_id' => $googleUser->getId(),
    'region' => 'Singapore',
    'organisation' => 'Tech Tree',
    'group' => 'learning',
    'role' => 'designer',
]);
                $role = Role::where('name', 'staff')->first();
                $newUser->assignRole($role->name);
                Auth::login($newUser); //login

                return redirect('staff/home');
            }

        } catch (Exception $e) {

            return redirect('auth/google');

        }

    }
}
