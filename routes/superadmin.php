<?php
  

use App\Http\Controllers\Creator\Superadmin\HomeController;
use App\Http\Controllers\Creator\Superadmin\UserController;
use App\Http\Controllers\Creator\Superadmin\CountryController;
use App\Http\Controllers\Creator\Superadmin\RegionController;
use App\Http\Controllers\Creator\Superadmin\AdminResource;
use App\Http\Controllers\Creator\Superadmin\DealerResource;
use App\Http\Controllers\Creator\Superadmin\CustomerResource;
use App\Http\Controllers\Creator\Superadmin\EmailTemplateController;
use App\Http\Controllers\Creator\Shared\LearningPathController; 
use App\Http\Controllers\Creator\Superadmin\LearnerController; 
use App\Http\Controllers\Creator\Superadmin\GroupController; 
use App\Http\Controllers\Creator\Superadmin\JobRoleController; 
use App\Http\Controllers\Creator\Superadmin\FAQController; 
use App\Http\Controllers\Creator\Superadmin\CertificateController; 
use App\Http\Controllers\Creator\Superadmin\ContactCategoryController; 
use App\Http\Controllers\Creator\Superadmin\AccountController; 
use App\Http\Controllers\Creator\Superadmin\LeaderboardController; 
use App\Http\Controllers\Creator\Superadmin\SalesTipController; 
use App\Http\Controllers\Creator\Superadmin\SettingResource; 
use App\Http\Controllers\Creator\Superadmin\ThreadCategoryController; 
use App\Http\Controllers\Creator\Superadmin\ImportUserResource; 
use App\Http\Controllers\Creator\Superadmin\QuizQuestionResource; 
use App\Http\Controllers\Creator\Superadmin\ThreadController;
use App\Http\Controllers\Creator\Superadmin\CategoryResource;

use App\Http\Controllers\Shared\ThreadLikeResource; 
use App\Http\Controllers\Shared\ReplyLikeResource; 
use App\Http\Controllers\Shared\ChangePaswordController;
 

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

Route::group(['namespace' => 'Creator\Superadmin', 'as'=>'superadmin.'], function(){
    
    Route::get('dashboard', 'HomeController@index');
    Route::get('chart/learning-path/{id}/data', 'HomeController@getChartData');

    Route::get('leraners/export', 'HomeController@exportLearners');
    Route::get('learning-progress/export', 'UserController@exportLearningPathProgress');
    /* Super Admin Resources */
    Route::resource('countries', 'CountryController');
    Route::resource('regions', 'RegionController');
    Route::resource('users', 'UserController');
    Route::resource('trainingadmins', 'AdminResource');
    Route::resource('dealers', 'DealerResource');
    Route::resource('customers', 'CustomerResource');
    Route::resource('groups', 'GroupController');
    Route::resource('job-roles', 'JobRoleController');
    Route::resource('faqs', 'FAQController');
    Route::resource('news-promotions', 'NewsPromotionController');
    Route::resource('sales-tips', 'SalesTipController');
    Route::resource('certificates', 'CertificateController');
    Route::resource('quizzes', 'QuizController');
    Route::resource('quiz-questions', 'QuizQuestionResource');
    Route::resource('contact-categories', 'ContactCategoryController');
    Route::resource('my-profile', 'AccountController');
    Route::resource('import-usersss/info', 'ImportUserResource');
    Route::post('import-usersss/info/{user}', 'ImportUserResource@importUser');
    Route::resource('categories', 'CategoryResource');
    Route::resource('packages', 'LearningPackageResource');
    Route::resource('banners', 'BannerResource');
    Route::get('/category/{id}/sub-category', 'LearningPackageResource@getSubCategory');
    Route::get('/package/manage/{id}', 'LearningPackageResource@managePackage');
    Route::put('/publish/package/{id}', 'LearningPackageResource@publishPackage');
    Route::put('/un-publish/package/{id}', 'LearningPackageResource@unPublishPackage');
    Route::get('package/{id}/assign/learing-paths', 'LearningPackageResource@assignLearningPath');
    Route::post('package/add/learning-path', 'LearningPackageResource@addLearningPath');
    Route::delete('package/{id}/remove/learning-paths', 'LearningPackageResource@removeLearningPaths');

     /********** Leaderboard *********/
    Route::delete('mark-featured', 'LeaderboardController@removeFeaturedTrainee');
    Route::post('mark-featured', 'LeaderboardController@updateMarkAsFeatured');
    Route::get('mark-featured/{userid}', 'LeaderboardController@markAsFeatured');
    Route::get('mark-featured/{userid}/edit', 'LeaderboardController@show');
    Route::post('mark-featured/content', 'LeaderboardController@updateFeaturedTraineeText');
    Route::get('view-point-history/{userid}', 'LeaderboardController@viewPointHistory');
    Route::get('manage-points', 'LeaderboardController@managePoints');
    Route::get('featured-records', 'LeaderboardController@featuredRecords');
    Route::post('leaderboard/user/{userid}/points', 'LeaderboardController@adjustPoint');
    Route::post('leaderboard/user/manage-points', 'LeaderboardController@bulkManagePoint');

     /********** FAQ *********/
    Route::get('faq/categories', 'FAQController@FAQCategories');
    Route::post('faq/categories/create', 'FAQController@addFAQCategory');
    Route::post('faq/categories/update/{id}', 'FAQController@updateFAQCategory');
    Route::delete('faq/categories/delete/{id}', 'FAQController@deleteFAQCategory');

    Route::resource('leaderboard', 'LeaderboardController');
    Route::resource('settings', 'SettingResource'); 
    Route::resource('/threads/categories', 'ThreadCategoryController'); 

    /* Email Template Management */
    Route::resource('email-templates','EmailTemplateController');
    Route::get('event/{eventId}/variables', 'EmailTemplateController@getVaraibles');
    
    Route::get('/certificate/{id}/preview', 'CertificateController@previewPDF');
    Route::get('/sales-tips/{id}/attachment', 'SalesTipController@showPDF');
    Route::get('/news-promotions/{id}/attachment', 'NewsPromotionController@showPDF');

    /******** Learner  **********/
    Route::delete('learners/:id/:learningPathId', 'LearnerController@destroy');
    Route::resource('learners', 'LearnerController');
    Route::get('learning-paths/{id}/create-learner', 'LearnerController@createLearner')->name('create-learner');
    Route::get('learning-paths/{id}/learners/remove', 'LearnerController@removeAllLearners')->name('remove-all-learner');

    /******** Quiz  **********/
    Route::get('/quiz-questions/create/{id}', 'QuizQuestionResource@createQuestions');
    Route::get('quiz-question/option/add/{id}', 'QuizQuestionResource@addOption');
    Route::post('quiz-question/option/add', 'QuizQuestionResource@storeOption');
    Route::get('quiz-question/option/status/{id}/{status}', 'QuizQuestionResource@changeOptionStatus');
    Route::delete('quiz-question/option/delete', 'QuizQuestionResource@deleteOption');
    Route::post('quiz/status', 'QuizController@changeQuizStatus');
    Route::get('quiz-question/{question}/option/{id}/edit', 'QuizQuestionResource@editOption');
    Route::post('quiz-question/option/update', 'QuizQuestionResource@updateOption');
    Route::get('quiz/responses/{quizId}' , 'QuizController@viewResponse');
    Route::get('scores/{quizId}/export', 'QuizController@exportScores');
    Route::get('answer-scores/{quizId}/export', 'QuizController@exportScoresWithAnswer');

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

    /******************** Ajax Controller *********************/
    Route::get('country/{id}/region', 'RegionController@getRegionsByCountry');
    Route::get('region/{id}/admin', 'AdminResource@getAdminByRegion');
    Route::get('region/{id}/dealer', 'DealerResource@getDealerByRegion');
    Route::get('/sales-tips/delete-attachment/{id}', 'SalesTipController@deleteAttachment');
    Route::get('prev-next-learning-paths/{page}/{offset}', 'HomeController@prevNextLearningPathCompletion');
    Route::post('bonus-reason/{id}/edit', 'LeaderboardController@updateBonusReason');

    /************* Thread *******************/
});

Route::group(['namespace' => 'Shared', 'as'=>'superadmin.'], function(){
   
    Route::resource('forum/threads', 'ThreadController');
    Route::post('forum/threads/status', 'ThreadController@updateThreadByStatus');
    Route::delete('forum/threads/destroy/{id}', 'ThreadController@destroy');
    Route::get('threads/reported', 'ThreadController@reportedThreads');
    Route::post('thread/update', 'ThreadController@updateThread');
    Route::post('thread/delete', 'ThreadController@deleteReportedThread');
    Route::get('forum/thread/{id}/preview', 'ThreadController@previewThreadLink');

    Route::resource('forum/threads/{id}/replies', 'ReplyController');
    Route::post('forum/thread/unsubscribe', 'ReplyController@unsubscribe');
    Route::post('forum/thread/subscribe', 'ReplyController@subscribe');
    Route::resource('change-password', 'ChangePaswordController');
   
    Route::post('upload-profile-picture', 'AccountController@uploadPicture');
    Route::get('comment/reported', 'ReplyController@reportedComments');
    Route::post('reply/{id}/delete', 'ReplyController@destroyComment');
    Route::post('comment/update', 'ReplyController@updateComment');
    Route::get('forum/threads/{threadId}/{commentId}/{type}', 'ReplyController@commentLink');
    Route::get('reply/{id}/edit', 'ReplyController@editReply');
    Route::post('reply/{id}/update', 'ReplyController@updateReply');
    /******************** Ajax Controller *********************/
    Route::post('thread/{id}/like', 'ThreadLikeResource@store');
    Route::delete('thread/{id}/like', 'ThreadLikeResource@destroy');
    Route::post('reply/{id}/like', 'ReplyLikeResource@store');
    Route::delete('reply/{id}/like', 'ReplyLikeResource@destroy');
    Route::get('reply/{id}/child', 'ReplyLikeResource@childReply');
    Route::post('reply/{id}/replies', 'ReplyController@storeChildReply');
    Route::resource('terms-conditions', 'TermsController')->only(['index', 'store']);

});

Route::group(['namespace' => 'Creator\Shared', 'as'=>'superadmin.'], function() {
    Route::resource('learning-paths', 'LearningPathController');
    Route::get('learning-paths/{id}/responses', 'LearningPathController@showSuperadminResponses');
    Route::get('learning-paths/preview/{id}', 'LearningPathController@previewCourse');
    Route::get('learner-response/{id}/{header}/export', 'LearningPathController@exportLearningPathResponse');
    Route::get('learner-response/{id}/export', 'LearningPathController@exportLearningPathResponse');
    Route::get('package/manage/learning-paths/{id}', 'LearningPathController@show');
      /******************** Ajax Controller *********************/
    Route::get('getScromRef/{packageId}', 'LearningPathController@getScromRef');
    Route::get('learning-paths/preview/{id}', 'LearningPathController@previewCourse');
    Route::post('uploadFile', 'LearningPathController@fileUploader');
    Route::get('learning-paths/resource/{userId}/{learningPathId}', 'LearningPathController@contentwiseProgress');
    Route::get('learning-paths/resource/{userId}/{learningPathId}/progress', 'LearningPathController@contentwiseProgressRecord');
});    

Route::group(['namespace' => 'Learners\Shared'], function() {
    Route::get('sales-tips/{id}', 'SalesTipController@show');
});    





