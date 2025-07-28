<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BaseController; 
use App\Http\Controllers\Api\Auth\AuthController; 
use App\Http\Controllers\Api\Auth\ForgotPasswordController;  
use App\Http\Controllers\Api\Auth\UpdatePasswordController; 

use App\Http\Controllers\Api\Shared\NewsPromotionController;
use App\Http\Controllers\Api\Shared\SalesTipController;   
use App\Http\Controllers\Api\Shared\LeaderboardController;   
use App\Http\Controllers\Api\Shared\SupportController; 
use App\Http\Controllers\Api\Shared\AccountController; 
use App\Http\Controllers\Api\Shared\FAQController; 
use App\Http\Controllers\Api\Shared\LearningPathController;
use App\Http\Controllers\Api\Shared\ThreadController;
use App\Http\Controllers\Api\Shared\ReplyController;
use App\Http\Controllers\Shared\ThreadLikeResource;
use App\Http\Controllers\Api\Shared\LearningProgressController;
use App\Http\Controllers\Api\Dealer\StaffController; 
use App\Http\Controllers\Api\Shared\UpdatedeviceIdController;
use App\Http\Controllers\Api\Shared\QuizController;

use App\Http\Controllers\Shared\ReplyLikeResource;
use App\Http\Controllers\Shared\QuizResource;



/* 
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/resource/view/{resourceid}', [LearningPathController::class, 'viewResource']);

Route::group([
    'prefix' => 'scorm'
], function () {
    Route::get('learning-paths/course/{resourceid}/{id}', [LearningPathController::class, 'launch']);
    Route::post('{role}/progress/track/{resourceid}', [LearningProgressController::class, 'saveProgress']);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'

], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/forgot-password', [ForgotPasswordController::class, 'forgot']); 
    Route::post('/password-reset', [ForgotPasswordController::class, 'reset']);
});


Route::group ([
    'middleware' => ['auth:api', 'apiLocale'],    
], function () {

    /************************* Shared ********************************/

    /****** news-promotions **********/
    Route::get('news-promotions', [NewsPromotionController::class, 'index']);

     /****** sales-tips **********/
    Route::get('sales-tips', [SalesTipController::class, 'index']);

    /****** leaderboard **********/
    Route::get('leaderboard', [LeaderboardController::class, 'index']);

    /****** supportMail **********/
    Route::get('support-categories', [SupportController::class, 'getSupportCategories']);
    Route::post('support/mail', [SupportController::class, 'supportMail']);

    /****** My Profile **********/
    Route::get('my-profile', [AccountController::class, 'index']);
    Route::get('learning-paths/badges', [AccountController::class, 'badges']);
    Route::post('upload-profile-picture', [AccountController::class, 'uploadPicture']);
    Route::post('change-password', [AccountController::class, 'changePassword']);
    Route::get('rewards', [AccountController::class, 'rewards']);

    /****** FAQ **********/
    Route::get('faqs', [FAQController::class, 'index']);
    Route::get('faq/categories', [FAQController::class, 'faqCategoryList']);

     /****** LearningPath **********/ 
    Route::get('learning-paths', [LearningPathController::class, 'index']);
    Route::get('learning-paths/{id}', [LearningPathController::class, 'show']);

       /****** Discussion Forum **********/   
    Route::get('forum/thread/categories', [ThreadController::class, 'categoryList']);   
    Route::get('forum/threads', [ThreadController::class, 'index']); 
    Route::get('forum/threads/{id}', [ThreadController::class, 'show']); 
    Route::post('forum/threads/{id}/like', [ThreadController::class, 'like']);
    Route::delete('forum/threads/{id}/like', [ThreadController::class, 'removeLike']);
    Route::post('forum/threads/create', [ThreadController::class, 'createThread']);
    Route::post('forum/threads/update-thread', [ThreadController::class, 'updateThread']);
    Route::post('forum/threads/status', [ThreadController::class, 'updateThreadByStatus']);
    Route::post('forum/threads/delete-thread', [ThreadController::class, 'deleteThread']);
    
    Route::post('forum/threads/reply-list', [ReplyController::class, 'replyList']);
    Route::post('forum/threads/add-reply', [ReplyController::class, 'addReply']);
    Route::post('forum/thread/unsubscribe', [ReplyController::class, 'unsubscribe']);
    Route::post('forum/thread/subscribe', [ReplyController::class, 'subscribe']);
    Route::get('reply/{id}/report/comment', [ReplyController::class, 'reportComment']);
    
    /****** update device Id and lang code **********/
    Route::post('user-device', [UpdatedeviceIdController::class, 'updateDeviceId']);

});

/********** Quiz and Thread ************/
Route::group ([
    'middleware' => ['auth:api', 'apiLocale'],
    'namespace' => 'Shared',    
], function () {

    Route::resource('quiz', 'QuizResource');
    Route::post('quiz/question', 'QuizResource@quizQuestion');
    Route::get('quiz/{id}/response', 'QuizResource@quizResponse');
    Route::post('reply/like', 'ReplyLikeResource@store');
    Route::delete('reply/{id}/like', 'ReplyLikeResource@destroy');
    Route::post('forum/threads/{id}/report', 'ThreadController@reportSpam');
});   


Route::group ([
    'middleware' => ['auth:api', 'apiLocale'],
    'prefix' => 'dealer'    
], function () {

     /****** Staff **********/ 
    Route::get('staff-filters', [StaffController::class, 'filtersData']);
    Route::get('stats', [StaffController::class, 'stats']);
    Route::get('staff', [StaffController::class, 'staffList']);
    Route::post('staff/learning-path', [StaffController::class, 'viewLearningPath']);

});




