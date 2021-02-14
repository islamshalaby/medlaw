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

Route::get('/setlocale/{locale}',function($lang){
    Session::put('locale',$lang);
    return redirect()->back();   
});



// Main Dashboard Routes
Route::group([
    'middleware'=>'language',
    'prefix' => "admin-panel",
    'namespace' => "Admin"  
] , function($router){

    Route::get('' ,'HomeController@show');
    Route::get('login' ,  [ 'as' => 'adminlogin', 'uses' => 'AdminController@getlogin']);
    Route::post('login' , 'AdminController@postlogin');
    Route::get('logout' , 'AdminController@logout');
    Route::get('profile' , 'AdminController@profile');
    Route::post('profile' , 'AdminController@updateprofile');    
    Route::get('databasebackup' , 'AdminController@backup');

    // Users routes for dashboard
    Route::group([
        'prefix' => 'users',
    ] , function($router){
            Route::get('add' , 'UserController@AddGet');
            Route::post('add' , 'UserController@AddPost');
            Route::get('show' , 'UserController@show');
            Route::get('edit/{id}' , 'UserController@edit');
            Route::post('edit/{id}' , 'UserController@EditPost');
            Route::get('details/{id}' , 'UserController@details');
            Route::post('send_notifications/{id}' , 'UserController@SendNotifications');
            Route::get('block/{id}' , 'UserController@block');
            Route::get('active/{id}' , 'UserController@active');
        }
    );

    // admins routes for dashboard
    Route::group([
        'prefix' => "managers",
    ], function($router){
        Route::get('add' , 'ManagerController@AddGet');
        Route::post('add' , 'ManagerController@AddPost');
        Route::get('show' , 'ManagerController@show');
        Route::get('edit/{id}' , 'ManagerController@edit');
        Route::post('edit/{id}' , 'ManagerController@EditPost');
        Route::get('delete/{id}' , 'ManagerController@delete');
    });

    // App Pages For Dashboard
    Route::group([
        'prefix' => 'app_pages'
    ] , function($router){
        Route::get('aboutapp' , 'AppPagesController@GetAboutApp');
        Route::post('aboutapp' , 'AppPagesController@PostAboutApp');
        Route::get('termsandconditions' , 'AppPagesController@GetTermsAndConditions');
        Route::post('termsandconditions' , 'AppPagesController@PostTermsAndConditions');
    });

    // Setting Route
    Route::get('settings' , 'SettingController@GetSetting');
    Route::post('settings' , 'SettingController@PostSetting');

    // Rates
    Route::get('rates' , 'RateController@Getrates');
   Route::get('rates/active/{id}' , 'RateController@activeRate');

    // meta tags Route
    Route::get('meta_tags' , 'MetaTagController@getMetaTags');
    Route::post('meta_tags' , 'MetaTagController@postMetaTags');

    // Ads Route
    Route::group([
        "prefix" => "ads"
    ],function($router){
        Route::get('add' , 'AdController@AddGet');
        Route::post('add' , 'AdController@AddPost');
        Route::get('show' , 'AdController@show');
        Route::get('edit/{id}' , 'AdController@EditGet');
        Route::post('edit/{id}' , 'AdController@EditPost');
        Route::get('details/{id}' , 'AdController@details');
        Route::get('delete/{id}' , 'AdController@delete');
    });

    // Categories Route
    Route::group([
        "prefix" => "categories"
    ], function($router){
         Route::get('add' , 'CategoryController@AddGet');
         Route::post('add' , 'CategoryController@AddPost');
         Route::get('show' , 'CategoryController@show');
         Route::get('edit/{id}' , 'CategoryController@EditGet');
         Route::post('edit/{id}' , 'CategoryController@EditPost');
         Route::get('delete/{id}' , 'CategoryController@delete');        
    });

    // Services Route
    Route::group([
        "prefix" => "services"
    ], function($router){
         Route::get('add' , 'ServiceController@AddGet')->name('services.add');
         Route::post('add' , 'ServiceController@AddPost');
         Route::get('show' , 'ServiceController@show')->name('services.index');
         Route::get('edit/{service}' , 'ServiceController@EditGet')->name('services.edit');
         Route::post('edit/{service}' , 'ServiceController@EditPost');
         Route::get('delete/{service}' , 'ServiceController@delete')->name('services.delete');
         Route::get('fetchcategoriesbytype/{type}' , 'ServiceController@fetchCategoriesByType');  
    });

     // doctors&lawyers Route
     Route::group([
        "prefix" => "doctorslawyers"
    ], function($router){
         Route::get('add' , 'DoctorLawyerController@AddGet')->name('doctors&lawyers.add');
         Route::post('add' , 'DoctorLawyerController@AddPost');
         Route::get('show' , 'DoctorLawyerController@show')->name('doctors&lawyers.index');
         Route::get('edit/{drlaw}' , 'DoctorLawyerController@EditGet')->name('doctors&lawyers.edit');
         Route::post('edit/{drlaw}' , 'DoctorLawyerController@EditPost');
         Route::get('approve/{drlaw}/{status}' , 'DoctorLawyerController@adminApprove')->name('doctors&lawyers.adminApprove');
         Route::get('fetchservicesbycategory/{category}' , 'DoctorLawyerController@fetchServicesByCategoryId');
         Route::get('checkphoneexist/{phone}/{type}' , 'DoctorLawyerController@checkPhoneExist');
         Route::get('deleteplaceimage/{image}' , 'DoctorLawyerController@deletePlaceImage')->name('doctors&lawyers.deletePlaceImage');
    });

    // Reservations Route
    Route::group([
        "prefix" => "reservations"
    ], function($router){
         Route::get('show' , 'ReservationController@show')->name('resrvations.index');
         Route::get('details/{reservation}' , 'ReservationController@details')->name('services.details');
    });


    // Contact Us Messages Route
    Route::group([
        "prefix" => "contact_us"
    ] , function($router){
        Route::get('' , 'ContactUsController@show');
        Route::get('details/{id}' , 'ContactUsController@details');
        Route::get('delete/{id}' , 'ContactUsController@delete');
    });

    // Notifications Route
    Route::group([
        "prefix" => "notifications"
    ], function($router){
        Route::get('show' , 'NotificationController@show');
        Route::get('details/{id}' , 'NotificationController@details');
        Route::get('delete/{id}' , 'NotificationController@delete');
        Route::get('send' , 'NotificationController@getsend');
        Route::post('send' , 'NotificationController@send');
        Route::get('resend/{id}' , 'NotificationController@resend');        
    });

    // Holidays Route
    Route::group([
        "prefix" => "holidays"
    ], function($router){
         Route::get('show' , 'HolidayController@show')->name('holidays.index');
         Route::post('show' , 'HolidayController@postSetHoliday');
    });

});


Route::group([
    'middleware'=>'language',
    'prefix' => "dashboard",
    'namespace' => "Dashboard" 
] , function($router){
    Route::get('' ,'HomeController@show');
    Route::get('login' ,  [ 'as' => 'dashboardlogin', 'uses' => 'DashboardController@getlogin']);
    Route::post('login' , 'DashboardController@postlogin');
    Route::get('logout' , 'DashboardController@logout');
    Route::get('profile' , 'DashboardController@getProfile')->name('dashboard.profile');
    Route::post('profile' , 'DashboardController@postProfile');
    Route::get('location' , 'DashboardController@getLocationData')->name('dashboard.location');   
    Route::post('location' , 'DashboardController@postLocationData');  
    Route::get('times_of_work' , 'DashboardController@getTimesOfWork')->name('dashboard.times_of_work');   
    Route::post('times_of_work' , 'DashboardController@postTimesOfWork'); 
    Route::get('rates' , 'ReservationController@Getrates')->name('dashboard.rates'); 

    // Reservations Route
    Route::group([
        "prefix" => "reservations"
    ], function($router){
         Route::get('show' , 'ReservationController@show')->name('dashboard.resrvations.index');
         Route::get('details/{reservation}' , 'ReservationController@details')->name('dashboard.services.details');
    });

    // Holidays Route
    Route::group([
        "prefix" => "holidays"
    ], function($router){
         Route::get('show' , 'HolidayController@show')->name('dashboard.holidays.index');
         Route::post('show' , 'HolidayController@postSetHoliday');
    });
});


// Web View Routes 
Route::group([
    'prefix' => "webview"
] , function($router){
    Route::get('aboutapp/{lang}' , 'WebViewController@getabout');
    Route::get('termsandconditions/{lang}' , 'WebViewController@gettermsandconditions' );
});