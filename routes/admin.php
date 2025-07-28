<?php

use App\Http\Controllers\Creator\TrainingAdmin\HomeController;
use App\Http\Controllers\Creator\TrainingAdmin\UserController;
use App\Http\Controllers\Creator\TrainingAdmin\DealerResource;
use App\Http\Controllers\Creator\TrainingAdmin\CustomerResource;
use App\Http\Controllers\Creator\TrainingAdmin\CertificateController;
use App\Http\Controllers\Creator\Shared\LearningPathController;
use App\Http\Controllers\Creator\TrainingAdmin\LearnerController;
use App\Http\Controllers\Creator\TrainingAdmin\NewsPromotionController;
use App\Http\Controllers\Creator\TrainingAdmin\SalesTipController;
use App\Http\Controllers\Creator\TrainingAdmin\AccountController;
use App\Http\Controllers\Creator\TrainingAdmin\ManualEmailController;
use App\Http\Controllers\Creator\TrainingAdmin\LeaderboardController;

use App\Http\Controllers\Shared\ThreadController;
use App\Http\Controllers\Shared\ReplyController;
use App\Http\Controllers\Shared\SupportController;
use App\Http\Controllers\Shared\ThreadLikeResource;
use App\Http\Controllers\Shared\ChangePaswordController;
use App\Http\Controllers\Shared\ReplyLikeResource;

use App\Http\Controllers\Learners\Shared\FAQController;




/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['namespace' => 'Creator\TrainingAdmin', 'as'=>'admin.'], function(){
    Route::get('dashboard', 'HomeController@index');
    Route::get('chart/learning-path/{id}/data', 'HomeController@getChartData');
    Route::get('chart/learning-path/{id}/{dealer}/data', 'HomeController@getChartDataForStaff');
    Route::get('leraners/export', 'HomeController@exportLearners');
    Route::get('learning-progress/export', 'UserController@exportLearningPathProgress');
    Route::resource('users', 'UserController');
    Route::resource('dealers', 'DealerResource');
    Route::resource('customers', 'CustomerResource');
    Route::resource('certificates', 'CertificateController');
    Route::get('/certificate/{id}/preview', 'CertificateController@previewPDF');
    Route::resource('news-promotions', 'NewsPromotionController');
    Route::resource('sales-tips', 'SalesTipController');
    Route::get('/news-promotions/{id}/attachment', 'NewsPromotionController@showPDF');
    Route::get('/sales-tips/{id}/attachment', 'SalesTipController@showPDF');
    Route::resource('my-profile', 'AccountController');

    /********** Leaderboard *********/
    Route::get('global/leaderboard', 'LeaderboardController@index');
    Route::get('global/featured-records', 'LeaderboardController@globalFeaturedRecords');
    Route::get('regional/leaderboard', 'LeaderboardController@regionalLeadeboard');
    Route::delete('mark-featured', 'LeaderboardController@removeFeaturedTrainee');
    Route::get('regional/mark-featured/{userid}', 'LeaderboardController@markAsFeatured');
    Route::post('mark-featured', 'LeaderboardController@updateMarkAsFeatured');
    Route::get('regional/view-point-history/{userid}', 'LeaderboardController@viewPointHistory');
    Route::post('regional/leaderboard/user/{userid}/points', 'LeaderboardController@adjustPoint');
    Route::post('regional/bonus-reason/{id}/edit', 'LeaderboardController@updateBonusReason');
    Route::get('regional/mark-featured/{userid}/edit', 'LeaderboardController@show');
    Route::post('regional/mark-featured/content', 'LeaderboardController@updateFeaturedTraineeText');
    Route::get('regional/featured-records', 'LeaderboardController@regionalFeaturedRecords');
    Route::get('regional/manage-points', 'LeaderboardController@managePoints');
    Route::post('leaderboard/user/manage-points', 'LeaderboardController@bulkManagePoint');
    Route::delete('package/{id}/remove/learning-paths', 'LearningPackageResource@removeLearningPaths');
     /******** Learner  **********/
     Route::delete('learners/{id}/{learningPathId}', 'LearnerController@removeLearner');
     Route::resource('learners', 'LearnerController');
     Route::get('learning-paths/{id}/create-learner', 'LearnerController@createLearner')->name('create-learner');

    /* Email Template Management */
    Route::resource('email-templates','EmailTemplateController');
    Route::get('event/{eventId}/variables', 'EmailTemplateController@getVaraibles');
    /************* Send Mail  *************/
    Route::get('mail/send', 'ManualEmailController@sendMail');
    Route::get('mail/list', 'ManualEmailController@mailList');
    Route::post('mail/send', 'ManualEmailController@sendMailUsers');
    Route::post('schedule/mail', 'ManualEmailController@scheduledMailUsers');
    Route::get('scheduled/mails', 'ManualEmailController@scheduledMails');
    Route::get('scheduled/mail/{id}/edit', 'ManualEmailController@editScheduledMail');
    Route::post('schedule/mail/update', 'ManualEmailController@updateScheduledMail');
    Route::delete('scheduled/mail/{id}/delete', 'ManualEmailController@deleteScheduledMail');
    Route::get('scheduled/mail/{id}', 'ManualEmailController@viewScheduledMail');

     /************* Ajax  ***************/
     Route::get('/sales-tips/delete-attachment/{id}', 'SalesTipController@deleteAttachment');
     Route::get('prev-next-learning-paths/{page}/{offset}', 'HomeController@prevNextLearningPathCompletion');
});

Route::group(['namespace' => 'Shared', 'as'=>'admin.'], function(){
    Route::resource('forum/threads', 'ThreadController');
    Route::resource('forum/threads/{id}/replies', 'ReplyController');
    Route::post('forum/threads/status', 'ThreadController@updateThreadByStatus');
    Route::post('thread/update', 'ThreadController@updateThread');
    Route::post('forum/threads/{id}/report', 'ThreadController@reportSpam');
    Route::post('upload-profile-picture', 'AccountController@uploadPicture');
    Route::post('forum/thread/unsubscribe', 'ReplyController@unsubscribe');
    Route::post('forum/thread/subscribe', 'ReplyController@subscribe');
    Route::post('forum/thread/hide', 'ThreadController@hide');
    Route::resource('support/mail', 'SupportController');
    Route::resource('change-password', 'ChangePaswordController');
    Route::post('reply/{id}/report/comment', 'ReplyController@reportComment');
    Route::post('reply/{id}/comment/hide', 'ReplyController@commentHide');
    Route::get('reply/{id}/edit', 'ReplyController@editReply');
    Route::post('reply/{id}/update', 'ReplyController@updateReply');
    Route::get('quiz', 'QuizResource@adminQuiz');
    Route::get('quiz/{quizId}', 'QuizResource@showAdminQuiz');
    Route::get('forum/thread/{id}/preview', 'ThreadController@previewThreadLink');
    /***************** Ajax  ******************/
    Route::post('thread/{id}/like', 'ThreadLikeResource@store');
    Route::delete('thread/{id}/like', 'ThreadLikeResource@destroy');
    Route::post('reply/{id}/like', 'ReplyLikeResource@store');
    Route::delete('reply/{id}/like', 'ReplyLikeResource@destroy');
    Route::get('reply/{id}/child', 'ReplyLikeResource@childReply');
    Route::post('reply/{id}/replies', 'ReplyController@storeChildReply');
    Route::resource('terms-conditions', 'TermsController')->only(['index', 'store']);
});


Route::group(['namespace' => 'Creator\Shared', 'as'=>'admin.'], function(){
    Route::resource('learning-paths', 'LearningPathController');
    Route::get('learning-paths/preview/{id}', 'LearningPathController@previewCourse');
    Route::get('learning-paths/{id}/responses', 'LearningPathController@showAdminResponses');
    Route::get('learning-paths/resource/{id}/status', 'LearningPathController@resourceStatus');
    Route::get('learner-response/{id}/export', 'LearningPathController@exportLearningPathResponse');

    /************* Ajax  ***************/
    Route::post('uploadFile', 'LearningPathController@fileUploader');
    Route::get('getScromRef/{packageId}', 'LearningPathController@getScromRef');
    Route::get('learning-paths/resource/{userId}/{learningPathId}', 'LearningPathController@contentwiseProgress');
    Route::get('learning-paths/resource/{userId}/{learningPathId}/progress', 'LearningPathController@contentwiseProgressRecord');

});

Route::group(['namespace' => 'Learners\Shared'], function() {

    /* FAQ */
    Route::get('faqs', 'FAQController@index');
});

?>
