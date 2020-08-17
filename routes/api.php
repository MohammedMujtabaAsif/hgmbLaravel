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

// Throttle users who attempt to access these routes more than 10 times in a minute
Route::middleware('throttle:10,1')->group(function () {
    Route::post('post/register', 'Api\RegisterController@register')->name('user.register');

    // Disallow users who are banned to login
    Route::post('post/login', 'Api\LoginController@login')->name('user.login')->middleware('banned');

    // Password reset routes
    Route::post('post/password/sendResetEmail', 'Auth\ForgotPasswordController@sendResetLinkEmail')->middleware('banned');
    // Route::post('password/resetPassword', 'Api\ResetPasswordController@reset');

});


// Email Verification route
Route::get('get/email/verify/{id}/{hash}', 'Api\VerificationController@verify')->name('verification.verify');


Route::group(['middleware' => ['auth:api']], function(){

    // Allow users who:
    // HAVE authenticated themselves
    Route::get('get/logout', 'Api\LoginController@logout');

    // Prefix GET routes with '/get/'
    Route::group(['prefix' => 'get', 'middleware' => 'banned'], function () {
        // Get authed user's full profile
        Route::get('user', 'Api\UsersController@index');
        // Resend Email Verification route
        Route::get('email/resend', 'Api\VerificationController@resend')->name('verification.resend');
    });


    // Prefix POST routes with '/post/'
    // Disallow banned users to delete or update their accounts
    Route::group(['prefix' => 'post', 'middleware' => 'banned'], function () {
        Route::post('deleteAccount', 'Api\UsersController@delete');
        Route::post('updateAccount', 'Api\UsersController@update');
    });

    
        Route::group(['middleware' => ['emailVerified', 'banned', 'approved']], function () {
            // Allow users who:
            // HAVE verified their email address,
            // HAVE been approved by admin 
            // HAVE NOT been banned
            // to reach these routes


            // Prefix GET routes with '/get/'
            Route::group(['prefix' => 'get'], function () {
                Route::get('verify', 'Api\UsersController@verificationCheck');


                Route::get('incomingRequests', 'Api\MatchesController@getIncomingFriendRequests');
                Route::get('outgoingRequests', 'Api\MatchesController@getOutgoingFriendRequests');

                Route::get('acceptedRequests', 'Api\MatchesController@getAcceptedFriendRequests');

                Route::get('deniedRequests', 'Api\MatchesController@getDeniedFriendRequests');

                Route::get('blockedRequests', 'Api\MatchesController@getBlockedFriends');

            });


            // Prefix POST routes with '/post/'
            Route::group(['prefix' => 'post'], function () {
                Route::post('allOtherUsers', 'Api\UsersController@getAllOtherUsers');
                Route::post('userWithID', 'Api\UsersController@getUserWithID');

                Route::post('sendFriendRequest', 'Api\MatchesController@sendFriendRequest');
                Route::post('acceptFriendRequest', 'Api\MatchesController@acceptFriendRequest');
                Route::post('denyFriendRequest', 'Api\MatchesController@denyFriendRequest');
                Route::post('deleteFriendRequest', 'Api\MatchesController@deleteFriendRequest');
                Route::post('unfriend', 'Api\MatchesController@unfriend');
                Route::post('blockFriend', 'Api\MatchesController@blockFriend');
                Route::post('unblockFriend', 'Api\MatchesController@unblockFriend');
            });
            
            // Route::apiResources(['appointments' => 'Api\AppointmentsController']);        
    });
});

