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
Route::post('register', 'Api\Auth\RegisterController@post');

Route::post('authenticate', 'Api\Auth\AuthenticateController@authenticate');
Route::post('logout', 'Api\Auth\AuthenticateController@logout');

Route::group(['middleware' => ['acl.api']], function () {
    // User
    Route::get('user', 'Api\UserController@getMyself');
    Route::get('user/{userId}', 'Api\UserController@getUser');
    Route::post('user', 'Api\UserController@store');
    Route::patch('user', 'Api\UserController@updateMyInfo');
    Route::patch('user/{userId}', 'Api\UserController@updateUserInfo');
    Route::delete('user/{userId}', 'Api\UserController@delete');

    // Client
    Route::get('client', 'Api\ClientController@get');
    Route::patch('client', 'Api\ClientController@update');

    // UserPhoneNumber
    Route::get('phone-number', 'Api\UserPhoneNumberController@getAll');
    Route::post('phone-number', 'Api\UserPhoneNumberController@store');
    Route::patch('phone-number/{phoneNumberId}', 'Api\UserPhoneNumberController@update');
    Route::delete('phone-number/{phoneNumberId}', 'Api\UserPhoneNumberController@delete');
});
