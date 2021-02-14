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

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {
    Route::post('login/{lang}/{v}', [ 'as' => 'login', 'uses' => 'AuthController@login'])->middleware('checkguest');
    Route::post('verifyphone/{lang}/{v}', [ 'as' => 'verifyphone', 'uses' => 'AuthController@verifyphone'])->middleware('checkguest');
    Route::post('logout/{lang}/{v}', 'AuthController@logout');
    Route::post('refresh/{lang}/{v}', 'AuthController@refresh');
    Route::post('me/{lang}/{v}', 'AuthController@me');
    Route::post('register/{lang}/{v}' , [ 'as' => 'register', 'uses' => 'AuthController@register'])->middleware('checkguest');

});

Route::get('/invalid/{lang}/{v}', [ 'as' => 'invalid', 'uses' => 'AuthController@invalid']);


// users apis group
Route::group([
    'middleware' => 'api',
    'prefix' => 'user'
], function($router) {
    Route::get('profile/{lang}/{v}' , 'UserController@getprofile');
    Route::put('updateprofile/{lang}/{v}' , 'UserController@updateprofile');
    Route::put('resetpassword/{lang}/{v}' , 'UserController@resetpassword');
    Route::put('resetforgettenpassword/{lang}/{v}' , 'UserController@resetforgettenpassword')->middleware('checkguest');
    Route::post('checkphoneexistance/{lang}/{v}' , 'UserController@checkphoneexistance')->middleware('checkguest');
    Route::get('walletbalance/{lang}/{v}' , 'UserController@getwalletbalance');
    Route::put('walletbalance/{lang}/{v}' , 'UserController@updatewalletbalance');
});

// 
Route::group([
    'middleware' => 'api',
    'prefix' => '{type}'
] , function($router){
    Route::get('nearby/{lang}/{v}' , 'DoctorLawyerController@nearby')->middleware('checkguest');
    Route::get('profile/{id}/{lang}/{v}' , 'DoctorLawyerController@getprofile')->middleware('checkguest');
    Route::get('timesofwork/{id}/{lang}/{v}' , 'DoctorLawyerController@gettimesofwork')->middleware('checkguest');
});

// favorites
Route::group([
    'middleware' => 'api',
    'prefix' => '{type}/favorites'
] , function($router){
    Route::get('/{lang}/{v}' , 'FavoriteController@getfavorites');
    Route::post('/{lang}/{v}' , 'FavoriteController@addtofavorites');
    Route::delete('/{lang}/{v}' , 'FavoriteController@removefromfavorites');
});

// orders
Route::group([
    'middleware' => 'api',
    'prefix' => '{type}/reservations'
] , function($router){
    Route::post('/create/{lang}/{v}' , 'ReservationController@create');
    Route::get('/details/{id}/{lang}/{v}' , 'ReservationController@getreservationdetails');
    Route::put('/confirm-attendance/{id}/{lang}/{v}' , 'ReservationController@confirmattendance' );
    Route::get('/history/{lang}/{v}' , 'ReservationController@gethistory' );
    Route::put('/cancel/{id}/{lang}/{v}' , 'ReservationController@cancelreservation' );
});

// rates
// get rates 
Route::get('/{type}/rate/{doctor_lawyer_id}/{lang}/{v}' , 'RateController@getrates')->middleware('checkguest');
// add rate
Route::post('/{type}/rate/{lang}/{v}' , 'RateController@addrate');

// search by name
Route::get('/{type}/searchbyname/{lang}/{v}' , 'SearchByNameController@search' )->middleware('checkguest');

// main ads
Route::get('/mainads/{lang}/{v}' , 'Ads@GetMainAds')->middleware('checkguest');

// ads in filter screen
Route::get('/{type}/filterscreenadds/{lang}/{v}' , 'Ads@GetFilterScreenAds')->middleware('checkguest'); // type maybe doctor or lawyer

// get categories
Route::get('/{categorytype}/categories/{lang}/{v}' , 'CategoryController@GetAllCategories')->middleware('checkguest'); // category type maybe doctor or lawyer

// get app number
Route::get('/appnumber/{lang}/{v}' , 'SettingController@GetSettings')->middleware('checkguest');



// send contact us message
Route::post('/contactus/{lang}/{v}' , 'ContactUsController@SendMessage')->middleware('checkguest');