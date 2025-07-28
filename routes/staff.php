<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\Learners\Shared\FAQController;
use App\Http\Controllers\Shared\LearningPathController;

use App\Http\Controllers\Shared\SupportController;
use App\Http\Controllers\Learners\Shared\NewsPromotionController;
use App\Http\Controllers\Learners\Shared\SalesTipController;
use App\Http\Controllers\Learners\Shared\LearningProgressController;
use App\Http\Controllers\Learners\Shared\TermsController;


use App\Http\Controllers\Shared\ThreadController;
use App\Http\Controllers\Shared\ReplyController;
use App\Http\Controllers\Shared\ThreadLikeResource;
use App\Http\Controllers\Shared\LeaderboardController;
use App\Http\Controllers\Shared\AccountController;
use App\Http\Controllers\Shared\ChangePaswordController;
use App\Http\Controllers\Shared\QuizResource;


/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['namespace' => 'Learners\Shared', 'as'=>'staff.'], function() {

    Route::get('progress/{resourceid}', 'LearningProgressController@getProgress');
    Route::post('progress/track/{resourceid}', 'LearningProgressController@saveProgress');

    /* News and Promotions */
     Route::resource('news-promotions', 'NewsPromotionController');
     Route::get('news-promotions/unread/count', 'NewsPromotionController@unreadCount');
     Route::get('news-promotions/mark-read/{id}', 'NewsPromotionController@markRead');
     Route::get('/news-promotions/{id}/attachment', 'NewsPromotionController@showPDF');

    /* Sales and Tips */
    Route::resource('sales-tips', 'SalesTipController');
    Route::get('/sales-tips/{id}/attachment', 'SalesTipController@showPDF');

     /* Learning Path */
     Route::resource('learning-paths', 'LearningPathController');
     Route::get('resource/view/{resourceid}', 'LearningPathController@viewResource');
     Route::get('learning-paths/course/{resourceid}/{id}', 'LearningPathController@launch');

      /* learning path certificate */
    Route::get('learning-paths/{id}/send-certificate', 'LearningPathController@sendCertificate');
    Route::get('/learning-path/{lid}/certificate/{id}/preview', 'LearningPathController@previewPDF');

    /* FAQ */
    Route::get('faqs', 'FAQController@index');

    /* Fourm & Learning Path */
    Route::get('home', 'LearningPathController@forumLearningPath');
});


Route::group(['namespace' => 'Shared', 'as'=>'staff.'], function(){
    Route::resource('forum/threads', 'ThreadController');
    Route::resource('forum/threads/{id}/replies', 'ReplyController');
    Route::post('forum/threads/status', 'ThreadController@updateThreadByStatus');
    Route::post('forum/threads/{id}/report', 'ThreadController@reportSpam');
    Route::post('forum/thread/unsubscribe', 'ReplyController@unsubscribe');
    Route::post('forum/thread/subscribe', 'ReplyController@subscribe');
    Route::resource('support/mail', 'SupportController');
    Route::resource('leaderboard', 'LeaderboardController');
    Route::resource('my-profile', 'AccountController');
    Route::post('upload-profile-picture', 'AccountController@uploadPicture');
    Route::resource('change-password', 'ChangePaswordController');
    Route::resource('quiz', 'QuizResource');
    Route::get('quiz/{quizId}{questionId}', 'QuizResource@show');
    Route::post('quiz/question', 'QuizResource@quizQuestion');
    Route::get('quiz/{id}/response', 'QuizResource@quizResponse');
    Route::post('reply/{id}/report/comment', 'ReplyController@reportComment');
    Route::get('forum/threads/{threadId}/{commentId}/{type}', 'ReplyController@commentLink');
    Route::get('regional/leaderboard', 'LeaderboardController@regionalUser');
    Route::get('forum/thread/{id}/preview', 'ThreadController@previewThreadLink');
    Route::get('reply/{id}/edit', 'ReplyController@editReply');
    Route::post('reply/{id}/update', 'ReplyController@updateReply');
    Route::post('thread/update', 'ThreadController@updateThread');

    /******************** Ajax Controller *********************/
    Route::post('thread/{id}/like', 'ThreadLikeResource@store');
    Route::delete('thread/{id}/like', 'ThreadLikeResource@destroy');
    Route::post('reply/{id}/like', 'ReplyLikeResource@store');
    Route::delete('reply/{id}/like', 'ReplyLikeResource@destroy');
    Route::get('reply/{id}/child', 'ReplyLikeResource@childReply');
    Route::post('reply/{id}/replies', 'ReplyController@storeChildReply');
    Route::resource('terms-conditions', 'TermsController')->only(['index', 'store']);

});

