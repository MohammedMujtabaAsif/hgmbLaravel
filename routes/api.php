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
        Route::get('user', 'UsersController@user');
        Route::get('allUsers', 'UsersController@allUsers');
    });
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
