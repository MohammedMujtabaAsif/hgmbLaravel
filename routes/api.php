<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use Route;

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

// Allow any user to reach these routes
Route::post('post/register', 'Api\RegisterController@register')->name('user.register');
Route::post('post/login', 'Api\LoginController@login')->name('user.login');

// Password reset routes
Route::post('post/password/sendResetEmail', 'Auth\ForgotPasswordController@sendResetLinkEmail');
// Route::post('password/resetPassword', 'Api\ResetPasswordController@reset');

// Email Verification route
Route::get('email/verify/{id}/{hash}', 'Api\VerificationController@verify')->name('verification.verify');

Route::group(['middleware' => ['auth:api']], function(){
    // Allow users who are authenticated to access these routes
    Route::get('get/logout', 'Api\LoginController@logout');
    Route::get('get/user', 'Api\UsersController@index');
    Route::post('post/deleteAccount', 'Api\UsersController@delete');
    Route::post('post/updateAccount', 'Api\UsersController@update');
    
    // Resend Email Verification route
    Route::get('email/resend', 'Api\VerificationController@resend')->middleware('auth:api')->name('verification.resend');

        Route::group(['middleware' => ['verified', 'approved']], function () {
            // Only allow user's who are admin approved and
            // have verified their email address to reach these routes
            Route::group(['prefix' => 'get'], function () {
                Route::get('verify', 'Api\UsersController@verificationCheck');

                Route::get('allOtherUsers', 'Api\UsersController@getAllOtherUsers');
                // Route::get('matchRequests','Api\UsersController@getMatchRequests');
                Route::get('pendingMatches', 'Api\UsersController@getPendingMatches');
                Route::get('deniedMatches', 'Api\UsersController@getDeniedMatches');

                Route::get('acceptedMatches', 'Api\UsersController@getAcceptedMatches');

                Route::get('blockedMatches', 'Api\UsersController@getBlockedMatches');
            });

            Route::group(['prefix' => 'post'], function () {
                Route::post('userWithID', 'Api\UsersController@getUserWithID');

                Route::post('sendMatchRequest', 'Api\UsersController@sendMatchRequest');
                Route::post('acceptMatchRequest', 'Api\UsersController@acceptMatchRequest');
                Route::post('denyMatchRequest', 'Api\UsersController@denyMatchRequest');
                Route::post('unmatch', 'Api\UsersController@unmatch');
                Route::post('blockMatch', 'Api\UsersController@blockMatch');
                Route::post('unblockMatch', 'Api\UsersController@unblockMatch');
            });
            
            // Route::apiResources(['appointments' => 'Api\AppointmentsController']);        
    });
});


//GET AUTHENTICATED USER (skip verified middleware)
// 
// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

