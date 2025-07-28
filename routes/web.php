<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

use App\Http\Controllers\RoleController;
use App\Http\Controllers\Api\Shared\QuizController;
use App\Http\Controllers\Api\Shared\FaqController;
use App\Http\Controllers\ScormController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\JobRoleController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\SalesTipController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\LearningPathController;
use App\Http\Controllers\NewsPromotionController;
//use App\Http\Controllers\FaqController;
use App\Http\Controllers\LearningScormTrackingController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\ThreadController;
use App\Http\Controllers\ReplyController;

use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\UserController;

use App\Http\Controllers\Superadmin\SuperadminHomeeController;
use App\Http\Controllers\Superadmin\SuperadminUserController;
use App\Http\Controllers\CustomerController;

use App\Models\{Banner, LearningPackage};


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



use App\Http\Controllers\GoogleController;

Route::get('login/google', [GoogleController::class, 'redirectToGoogle']);
Route::get('login/google/callback', [GoogleController::class, 'handleGoogleCallback']);



Route::get('/app/privacy-policy', function() {
    return view('app.privacy');
});

Route::get('/game/puzzle', function() {
    return view('learners.game.puzzle');
});


Route::get('/{locale}', function ($locale) {
    App::setLocale($locale);
    Session::put('locale', $locale);
    return redirect('/auth/login');
});

Route::get('/', function () {
    return redirect('/auth/login') ;
})->middleware('guest');

Route::delete('/learning-paths/{id}', [LearningPathController::class, 'destroy'])->middleware('auth');

Route::post('/customer/login', 'App\Http\Controllers\CustomerController@login');
Route::get('/customer/register', 'App\Http\Controllers\CustomerController@register');

// google login
Route::get('auth/google', 'App\Http\Controllers\GoogleController@redirectToGoogle');
Route::get('authorized/google/callback', 'App\Http\Controllers\GoogleController@handleGoogleCallback');

// save customer
Route::post('/customer/register', 'App\Http\Controllers\CustomerController@saveCustomer');

//Route::get('/', 'App\Http\Controllers\PublicViewController@index');
//Route::get('/package/search', 'App\Http\Controllers\PublicViewController@searchPackage');
//Route::get('/package/{id}', 'App\Http\Controllers\PublicViewController@packageDetails');
//Route::get('/add-to-cart/{id}','App\Http\Controllers\CartController@addToCart');
//Route::get('/public/checkout','App\Http\Controllers\CartController@checkOut');
//Route::delete('/public/delete-cart-item/{id}','App\Http\Controllers\CartController@deleteCartPackage');
//Route::delete('/public/delete-all-cart-item','App\Http\Controllers\CartController@deleteAllCartPackage');

Route::middleware('auth')->get('/staff/quiz/{quizId}/question/{questionId}', [QuizController::class, 'showQuestion']);

Route::match(['get', 'post'], '/send-reset', [AuthController::class, 'sendReset']);

// For Blade page
Route::get('{role}/quiz', [QuizController::class, 'index'])->name('quiz.index');

// For DataTables JSON
//Route::get('{role}/quiz/data', [QuizController::class, 'data'])->name('quiz.data');


Route::get('/shared/faqs', [FaqController::class, 'index']);
