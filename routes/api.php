<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

    'middleware' => 'api',
    'namespace' => 'App\Http\Controllers\Api',

], function ($router) {

    Route::post('subnotifyandroid', 'SubscriptionController@subNotifyAndroid');
    Route::post('subnotifyios', 'SubscriptionController@subNotifyiOS');

    //Route::get('xxx', 'SubscriptionController@xxx');
    //Route::get('verifypurchase', 'SubscriptionController@verifyPurchase');

    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('register', 'AuthController@register');

    Route::post('login/social-network', 'SocialNetworkController@login');

    Route::prefix('password')->group(function () {
        Route::post('forgot', 'PasswordResetController@forgot');
        Route::get('find/{token}', 'PasswordResetController@find');
        Route::post('reset', 'PasswordResetController@reset');
    });

    Route::get('countries', 'DataController@countries');
    Route::get('cities', 'DataController@cities');
    Route::get('roles', 'DataController@roles');
    Route::get('grades', 'DataController@grades');
    Route::get('academies', 'DataController@academies');

    Route::middleware('auth:api')->group(function () {
        Route::post('assistances/code', 'AssistanceController@code');
        Route::post('assistances/register', 'AssistanceController@register');
        Route::get('assistances/records', 'AssistanceController@records');

        Route::get('promotions/records', 'PromotionController@records');

        Route::get('profile', 'ProfileController@getProfile');
        Route::match(['post', 'put'], 'profile/information', 'ProfileController@updateInformation');
        Route::match(['post', 'put'], 'profile/social-networks', 'ProfileController@updateSocialNetworks');
        Route::match(['post', 'put'], 'profile/photo', 'ProfileController@updatePhoto');
        Route::delete('profile/photo', 'ProfileController@deletePhoto');

        Route::get('subscription', 'SubscriptionController@get');
        Route::match(['post', 'put'], 'subscription', 'SubscriptionController@set');
        Route::post('subscription/cancel', 'SubscriptionController@cancel');
        Route::get('subscription/starttrial', 'SubscriptionController@starttrial');

        Route::get('subscription-data', 'SubscriptionController@subscriptionData');

        Route::match(['post', 'put'], 'device-token', 'DeviceTokenController@register');
    });

});
