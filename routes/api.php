<?php

use Illuminate\Http\Request;

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

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'UsersController@login');
    Route::post('signup', 'UsersController@signup');
  
    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::get('logout', 'UsersController@logout');
        Route::get('deleteAccount', 'UsersController@deleteAccount');
        Route::get('currentUser', 'UsersController@currentUser');
        Route::get('allUsers', 'UsersController@allUsers');
        Route::get('matches', 'UsersController@allMatches');
        Route::post('sendMatchRequest', 'UsersController@sendMatchRequest');
        Route::get('getMatchRequests','UsersController@getMatchRequests');
        Route::post('acceptMatchRequest', 'UsersController@acceptMatchRequest');
        Route::post('denyMatchRequest', 'UsersController@denyMatchRequest');
        Route::post('unmatch', 'UsersController@unmatch');
        Route::get('getMatchedUsers', 'UsersController@getMatchedUsers');
    });
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
