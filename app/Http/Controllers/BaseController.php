<?php

namespace App\Http\Controllers;

use Auth;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BaseController extends Controller
{
	

	public function getViewPath($viewPath) {

		$user = Auth::user();
		$userRole = $user->roles->pluck('name')->first();

		switch ($userRole) { 

			case ("Admin"):

				if (\View::exists('admin.trainer-admin.' . $viewPath))
				{
				 	return 'admin.trainer-admin.' . $viewPath;
				} else {
					return 'admin.common.' . $viewPath;
				}

				break;

			case ("Superadmin"):

				if (\View::exists('admin.superadmin.' . $viewPath))
				{
				 	return 'admin.superadmin.' . $viewPath;
				} else {
					return 'admin.common.' . $viewPath;
				}
				break;
				
			case ("Dealer"):

				if (\View::exists('user.dealer.' . $viewPath))
				{
				 	return 'user.dealer.' . $viewPath;
				} else {
					return 'user.common.' . $viewPath;
				}
				
				break;

			case ("User"):

				if (\View::exists('user.customer.' . $viewPath))
				{
				 	return 'user.customer.' . $viewPath;
				} else {
					return 'user.common.' . $viewPath;
				}
				
				break;	
				
			default:
				throw new \Exception(\Lang::get('lang.view-exception'));  
				break; 			

		}	
	}		
}

