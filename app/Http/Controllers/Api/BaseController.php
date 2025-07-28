<?php

namespace App\Http\Controllers\Api;

use Auth;
use Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class BaseController extends Controller
{
	public function __construct() {

	}

	/**
     * return json response.
     *
     * @return \Illuminate\Http\Response
    */

	public function sendResponse($result, $message)
	{
		$response = [
			'success' => true,
			'data' => $result,
			'message' => $message,
		];
		return response()->json($response, 200);
	}

	/**
	* return json error response.
	*
	* @return \Illuminate\Http\Response
	*/

	public function sendError($error, $errorMessages = [], $code = 400)
	{
		$response = [
			'success' => false,
			'message' => $error,
		];
		if(!empty($errorMessages)){
			$response['data'] = $errorMessages;
		}
		Log::error($error);
		return response()->json($response, $code);
	}

}