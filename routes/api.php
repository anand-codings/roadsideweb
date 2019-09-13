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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('login', 'API\UserController@login');
//Get Location
Route::get('get_location/{job_id}', 'NotificationsController@getLocation');
Route::get('get_status/{job_id}', 'NotificationsController@getStatus');



Route::post('register', 'API\UserController@register');
    Route::group(['middleware' => ['auth:api', 'checkSession']], function() {
    Route::post('details', 'API\UserController@details');
    Route::post('update_user_profile', 'API\UserController@updateUserProfile');
    Route::post('update_avatar', 'API\UserController@updateAvatar');
    Route::get('logout', 'API\UserController@logout');
    Route::post('get_make', 'API\CarsController@getMake');
    Route::post('get_model', 'API\CarsController@getModel');
    Route::post('change_password', 'API\UserController@changePassword');
    Route::post('createSubscription', 'API\UserController@create_subscription');
    Route::post('createGuestService', 'API\UserController@create_guest_service');
    Route::post('retrieveJobId', 'API\UserController@retrieve_job_id');
    Route::post('memberPayPerUse', 'API\UserController@pay_per_use_member');
    Route::post('retrieveMilesForService', 'API\UserController@retrieve_miles_for_service');
    Route::post('retrieveMilesForMembers', 'API\UserController@retrieve_miles_for_members');
    Route::get('getSubscriptionPlan', 'API\UserController@get_subscription_plan');
    Route::get('cancelSubscription', 'API\UserController@cancel_subscription');
    Route::post('charge', 'API\UserController@create_charge');
    
    Route::post('subscriptions','AdminController@createSubscription');
    Route::post('test','AdminController@test');
   
   
});

Route::group(['namespace' => 'Auth', 'middleware' => 'api', 'prefix' => 'password'], function () {
    Route::post('create', 'PasswordResetController@create');
//    Route::get('find/{token}', 'PasswordResetController@find');
//    Route::post('reset', 'PasswordResetController@reset');
});

Route::group(['middleware' => ['auth:api', 'checkSession'], 'prefix' => 'car'], function () {
    Route::get('all_cars', 'API\CarsController@all_cars');
    Route::post('create', 'API\CarsController@create');
    Route::get('edit/{id}', 'API\CarsController@edit');
    Route::post('update', 'API\CarsController@update');
    Route::get('show/{id}', 'API\CarsController@show');
    Route::get('delete/{id}', 'API\CarsController@destroy');
});

Route::group(['middleware' => ['auth:api', 'checkSession'], 'prefix' => 'job'], function () {
    Route::get('all_jobs', 'API\JobsController@all_jobs');
    Route::post('create', 'API\JobsController@create');
    Route::post('cancel_job', 'API\JobsController@cancel_job');
    Route::get('edit/{id}', 'API\JobsController@edit');
    Route::post('update', 'API\JobsController@update');
    Route::get('show/{id}', 'API\JobsController@show');
    Route::get('delete/{id}', 'API\JobsController@destroy');
});

Route::group(['middleware' => ['auth:api', 'checkSession'], 'prefix' => 'reminder'], function () {
    Route::get('all_reminders', 'API\RemindersController@index');
    Route::post('add_reminder', 'API\RemindersController@addReminder');
    Route::post('show_reminder', 'API\RemindersController@showReminder');
    Route::post('update_reminder', 'API\RemindersController@updateReminder');
    Route::get('delete_reminder/{id}', 'API\RemindersController@deleteReminder');
});




