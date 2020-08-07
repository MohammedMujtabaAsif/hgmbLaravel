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

    // Allow users who:
    // HAVE authenticated themselves

    //prefix GET routes with '/get'
    Route::group(['prefix' => 'get'], function () {
        Route::get('logout', 'Api\LoginController@logout');
        Route::get('user', 'Api\UsersController@index');
    });


    //prefix POST routes with '/post'
    Route::group(['prefix' => 'post'], function () {
        Route::post('post/deleteAccount', 'Api\UsersController@delete');
        Route::post('post/updateAccount', 'Api\UsersController@update');
    });

    
    // Resend Email Verification route
    Route::get('email/resend', 'Api\VerificationController@resend')->middleware('auth:api')->name('verification.resend');

        Route::group(['middleware' => ['CustomEmailVerified', 'approved']], function () {
            // Allow users who:
            // HAVE verified their email address,
            // HAVE been approved by admin 
            // NOT banned to reach these routes


            //prefix GET routes with '/get'
            Route::group(['prefix' => 'get'], function () {
                Route::get('verify', 'Api\UsersController@verificationCheck');

                Route::get('allOtherUsers', 'Api\UsersController@getAllOtherUsers');

                Route::get('pendingMatches', 'Api\MatchesController@getPendingMatches');
                Route::get('deniedMatches', 'Api\MatchesController@getDeniedMatches');

                Route::get('acceptedMatches', 'Api\MatchesController@getAcceptedMatches');

                Route::get('blockedMatches', 'Api\MatchesController@getBlockedMatches');

                Route::get('sentRequests', 'Api\MatchesController@getsentRequests');
            });


            //prefix POST routes with '/post'
            Route::group(['prefix' => 'post'], function () {
                Route::post('userWithID', 'Api\UsersController@getUserWithID');

                Route::post('sendMatchRequest', 'Api\MatchesController@sendMatchRequest');
                Route::post('acceptMatchRequest', 'Api\MatchesController@acceptMatchRequest');
                Route::post('denyMatchRequest', 'Api\MatchesController@denyMatchRequest');
                Route::post('unmatch', 'Api\MatchesController@unmatch');
                Route::post('blockMatch', 'Api\MatchesController@blockMatch');
                Route::post('unblockMatch', 'Api\MatchesController@unblockMatch');
            });
            
            // Route::apiResources(['appointments' => 'Api\AppointmentsController']);        
    });
});

