<?php

namespace App\Http\Controllers\Shared;

use Auth;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;


class TermsController extends Controller
{
    //

    public function index(Request $request)
    {
        $user = Auth::user();   
        $role = $user->roles[0]->name;    
        return view('shared.terms-condition.index', compact('role'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();   
        User::where('id', $user->id)->update([ 'terms_accepted' => true ]);    
        return Redirect::to("/");
    }

}
