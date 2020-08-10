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
Route::get('post/email/verify/{id}/{hash}', 'Api\VerificationController@verify')->name('verification.verify');


Route::group(['middleware' => ['auth:api']], function(){

    // Allow users who:
    // HAVE authenticated themselves

    //prefix GET routes with '/get'
    Route::group(['prefix' => 'get'], function () {
        Route::get('logout', 'Api\LoginController@logout');
        Route::get('user', 'Api\UsersController@index');
        // Resend Email Verification route
        Route::get('email/resend', 'Api\VerificationController@resend')->middleware('auth:api')->name('verification.resend');
    });


    //prefix POST routes with '/post'
    Route::group(['prefix' => 'post'], function () {
        Route::post('post/deleteAccount', 'Api\UsersController@delete');
        Route::post('post/updateAccount', 'Api\UsersController@update');
    });

    

        Route::group(['middleware' => ['CustomEmailVerified', 'approved']], function () {
            // Allow users who:
            // HAVE verified their email address,
            // HAVE been approved by admin 
            // NOT banned to reach these routes


            //prefix GET routes with '/get'
            Route::group(['prefix' => 'get'], function () {
                Route::get('verify', 'Api\UsersController@verificationCheck');

                Route::get('allOtherUsers', 'Api\UsersController@getAllOtherUsers');

                Route::get('incomingRequests', 'Api\MatchesController@getIncomingFriendRequests');
                Route::get('outgoingRequests', 'Api\MatchesController@getOutgoingFriendRequests');

                Route::get('acceptedRequests', 'Api\MatchesController@getAcceptedFriendRequests');

                Route::get('deniedRequests', 'Api\MatchesController@getDeniedFriendRequests');

                Route::get('blockedRequests', 'Api\MatchesController@getBlockedFriends');

            });


            //prefix POST routes with '/post'
            Route::group(['prefix' => 'post'], function () {
                Route::post('userWithID', 'Api\UsersController@getUserWithID');

                Route::post('sendFriendRequest', 'Api\MatchesController@sendFriendRequest');
                Route::post('acceptFriendRequest', 'Api\MatchesController@acceptFriendRequest');
                Route::post('denyFriendRequest', 'Api\MatchesController@denyFriendRequest');
                Route::post('unfriend', 'Api\MatchesController@unfriend');
                Route::post('blockFriend', 'Api\MatchesController@blockFriend');
                Route::post('unblockFriend', 'Api\MatchesController@unblockFriend');
            });
            
            // Route::apiResources(['appointments' => 'Api\AppointmentsController']);        
    });
});

