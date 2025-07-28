<?php

namespace App\Http\Controllers\Creator\Superadmin;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{ User, Role };


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
        $user = User::with('roles')->where('id', $loggedUser->id)->first();
      
        return view('creator.superadmin.account.index', compact('user'));
    }

}
