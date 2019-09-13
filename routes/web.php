<?php
use Illuminate\Support\Facades\Auth;
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
    if(Auth::guard('admin')->check()){
        return redirect('dashboard');
    }
    return view('admin.login');
});

Route::get('/login',function(){
    if(Auth::guard('admin')->check()){
        return redirect('dashboard');
    }
    return view('admin.login');
});
Route::get('/user_login',function(){
    if(Auth::user()){

        return redirect('userdashboard');
    }
    return view('users.login');
});
Route::get('/notificationcron', 'NotificationsController@cron');

Route::get('/send_reminders', 'NotificationsController@sendReminders');
Route::get('/reset', 'Controller@reset');
Route::get('/success', function () {
    return view('reset_success');
});

Route::post('postlogin','AdminController@postLogin');
Route::post('userpostlogin','UserController@postLogin');
Route::group(['middleware'=>['checkUser']],function(){
      //user
      Route::get('userdashboard','UserController@userDashboard');
      Route::get('usersubscription','UserController@getSubscription');
      Route::get('cancel_subscription','UserController@cancelSubscription');

      Route::get('add_subscription_view','UserController@addSubscriptionView');
      Route::get('create_subscription/{plan}','UserController@createSubscription');
      Route::post('new_subscription','UserController@newSubscription');
      Route::get('upgrade_subscription/{plan}','UserController@upgradeSubscription');
   

      Route::get('logout_user','UserController@logout');
      Route::get('edit_user_profile','UserController@editUserProfile')->name('user.edit_profile');
      Route::post('update_user',  'UserController@updateUserProfile')->name('user.update_profile');
      Route::post('change_user_password',  'UserController@changeUserPassword')->name('user.change_password');

      //services
    Route::get('get_services', 'UserController@getServices')->name('user.show_services');

}
);
Route::group(['middleware' => ['admin']], function() {

    Route::get('dashboard','AdminController@dashboard');
    Route::post('create_subscription','AdminController@createSubscription');
    Route::get('get_subscription_plan','AdminController@getSubscriptionPlan');
    Route::get('all_subscriptions','AdminController@allSubscription');
    Route::get('cancel_subscription/{subscription}','AdminController@cancelSubscription');

    Route::get('test_card_token','AdminController@testCreateCardToken');
    Route::get('users','AdminController@getusers');
//    Get User Details
    Route::get('user_detail/{id}','API\UserController@userDetail');
//    Get User Services Detail
    Route::get('used_services/{id}','API\UserController@usedServices');
    Route::get('subscriptions','AdminController@getSubscriptions');


    Route::get('logout','AdminController@logout');
//   Cancel Subscription Plan from admin panel
    Route::post('cancel_sub', 'API\UserController@adminCancelSub');

    //    Admin Edit Profile View
    Route::get('edit_admin_profile_view', 'AdminController@editProfileView');
//    Save Admin Edit Profile Data
    Route::post('save_edit_profile_data', 'AdminController@editProfileData');
//    Change Password
    Route::post('change_password', 'AdminController@changePassword');

//  Test date compare subscription
    Route::get('test_date_compare','AdminController@testDateCompare');
});
//Cron Job Route
Route::get('status_cron','AuthController@statusCron');

Route::get('register_profile', 'AdminController@registerProfile');
Route::post('/register_membership', 'AdminController@register');

Route::group(['namespace' => 'Auth', 'middleware' => 'web', 'prefix' => 'password'], function () {
    Route::get('find/{token}', 'PasswordResetController@find');
});
