<?php

namespace App\Http\Controllers\Creator\TrainingAdmin;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{ User, Role, Country, Region };

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
        $user = User::with('roles', 'country')->where('id', $loggedUser->id)->first();
       
        return view('creator.admin.account.index', compact('user'));
    }

}
