<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Auth::routes();


Route::group(['middleware' => ['auth']], function () {
  Route::get('logout', 'Auth\LoginController@userLogout')->name('user.logout');
  Route::get('home', 'HomeController@index')->name('user.home');
  Route::get('/user/profile', 'UsersController@show')->name('user.profile');
  Route::post('/user/delete', 'UsersController@destroy')->name('user.deleteAccount');
  Route::post('/user/deactivate', 'UsersController@deactivate')->name('user.deactivateAccount'); //TODO
});

Route::group(['prefix' => 'admin'], function () {
  Route::get('/home', 'AdminsController@index')->name('admin.home');
  Route::get('/login', 'Auth\AdminsLoginController@getLoginForm')->name('admin.login');
  Route::post('/login', 'Auth\AdminsLoginController@login')->name('admin.login.submit');
  Route::get('/logout', 'Auth\AdminsLoginController@logout')->name('admin.logout');

  Route::group(['prefix' => '/users'], function () {

    Route::get('/approved/{filter?}', 'AdminsController@getApprovedUsers')->name('admin.approvedUsers');
    Route::get('/unapproved/{filter?}', 'AdminsController@getUnapprovedUsers')->name('admin.unapprovedUsers');
    Route::get('/banned/{filter?}', 'AdminsController@getBannedUsers')->name('admin.bannedUsers');
  });

  Route::group(['prefix' => '/user/{id}'], function () {
    Route::get('/', 'AdminsController@getUser')->name('admin.user');

    Route::post('/approve', 'AdminsController@approveUser')->name('admin.approveUser'); 
    Route::post('/unapprove', 'AdminsController@unapproveUser')->name('admin.unapproveUser'); 
    Route::post('/ban', 'AdminsController@banUser')->name('admin.banUser'); 
    Route::post('/unban', 'AdminsController@unbanUser')->name('admin.unbanUser');   
    Route::post('/delete', 'AdminsController@deleteUser')->name('admin.deleteUser');
  });
 

  //TODO: setup appointment scheduling
  Route::get('/schedule', 'AdminsController@getSchedule')->name('admin.schedule'); 


  //TODO: setup superadmin controls
  Route::group(['middleware' => ['superadmin']], function () {      
    Route::get('/admins', 'AdminsController@getAdmins')->name('admin.admins');
    Route::post('/admins/delete', 'AdminsController@deleteAdmin')->name('admin.deleteAdmin');
  });
});
