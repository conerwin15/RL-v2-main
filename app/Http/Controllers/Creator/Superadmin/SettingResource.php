<?php

namespace App\Http\Controllers\Creator\superadmin;

use Config; 
use Auth;
use Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettingResource extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $settings = Setting::all();
        return view('creator.superadmin.settings.index', compact('settings'));
    }

    public function store(Request $request)
    {
        request()->validate([
            'points_per_activity' => 'required|numeric', 
            'correct_answer_points' => 'required|numeric'
        ]);

        $input = $request->all();
        unset($input['_token']);
      
        foreach($input as $key => $value) 
        {
            Setting::set($key, $value);
        }
        Setting::save();

        return redirect()->back()->with('success', \Lang::get('lang.setting') .' '. \Lang::get('lang.updated-successfully')); 
    }    

} 
