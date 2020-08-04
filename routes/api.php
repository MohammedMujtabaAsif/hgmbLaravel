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
Route::post('register', 'Api\RegisterController@register')->name('user.register');
Route::post('login', 'Api\LoginController@login')->name('user.login');

// Password reset routes
Route::post('password/sendResetEmail', 'Auth\ForgotPasswordController@sendResetLinkEmail');
// Route::post('password/resetPassword', 'Api\ResetPasswordController@reset');

// Email Verification route
Route::get('email/verify/{id}/{hash}', 'Api\VerificationController@verify')->name('verification.verify');

Route::group(['middleware' => ['auth:api']], function(){
    // Allow users who are authenticated to access these routes
    Route::get('logout', 'Api\LoginController@logout');
    Route::get('deleteAccount', 'UsersController@deleteAccount');
    Route::get('user', 'Api\UsersController@currentUser');
    
    // Resend Email Verification route
    Route::get('email/resend', 'Api\VerificationController@resend')->middleware('auth:api')->name('verification.resend');

        Route::group(['middleware' => ['verified', 'approved']], function () {
            // Only allow user's who are admin approved and
            // have verified their email address to reach these routes
            Route::get('/verify', 'Api\UsersController@verificationCheck');
            Route::get('allOtherUsers', 'Api\UsersController@allOtherUsers');
            // Route::get('getMatches', 'Api\UsersController@allMatches');
            Route::get('getMatchedUsers', 'Api\UsersController@getMatchedUsers');
            Route::post('sendMatchRequest', 'Api\UsersController@sendMatchRequest');
            Route::get('getMatchRequests','Api\UsersController@getMatchRequests');
            Route::post('acceptMatchRequest', 'Api\UsersController@acceptMatchRequest');
            Route::post('denyMatchRequest', 'Api\UsersController@denyMatchRequest');
            Route::post('unmatch', 'Api\UsersController@unmatch');
            Route::post('blockUser', 'Api\UsersController@blockUser');
            Route::post('unblockUser', 'Api\UsersController@unblockUser');
            
            // Route::apiResources(['appointments' => 'Api\AppointmentsController']);        
    });
});


//GET AUTHENTICATED USER (skip verified middleware)
// 
// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

