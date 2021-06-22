<?php

use Illuminate\Support\Facades\Route;

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

Route::group([

    'prefix' => '/',
    'as' => 'admin.',
    'namespace' => 'App\Http\Controllers\Admin'
    
], function() {
    
    Route::get('login/{provider}', 'Auth\LoginController@redirectToProvider');
    Route::get('login/{provider}/callback', 'Auth\LoginController@handleProviderCallback'); 
    
    Route::get('pass', function() {
        /*
        \DB::statement(\DB::raw("SET lc_time_names = 'es_PY';"));
        $q = \App\Models\User::selectRaw("*, MONTHNAME(created_at)")->first(); // only the "select.." part here
        dd($q);
        
        setlocale(LC_ALL, "es_ES", 'Spanish_Spain', 'Spanish');
        dd(date_parse('Abril'));
        dd(\Hash::make('12345678'));
        */    
        
        
        /*
        $assistances = \App\Models\Assistance::select('student_user_id')
            ->whereHas('studentUser.deviceTokens', null, '>', 1)
            ->whereDate('created_at', '<', now()->subDays(15))
            ->groupBy('student_user_id')->get();
            
        
        foreach ($assistances as $assistance) {
            $assistance->studentUser->notify(new \App\Notifications\Motivation);            
        }
        */
        
        
        $deviceId = 'cYTZ9GlFTnekkggV9b6UO9:APA91bG-70FVgo89HwFnyOsrFj1S2vgDpteZLZSW6fqO3l7YqTUEP3O3VjQ0C-MOmWW4q3-19vRepSwhjHl-W0kbAgl4_X4HWRX5iIyd-SND1vhFhx5SoudpmAk3_gMz0Rv_rbnWaP74';
        //$deviceId = 'eKc4vKIsSmu2oOVl2bQRKW:APA91bG0kVd1A3hsTBu4YOoYlKyPnnflhOESJXkfKT6qoCbGNjotNSJSlYmpq_qyx4k-fdE1X-zwYPHKDxf_HefM6XFttdMZ2kJVTQ-U1TJRDrIFVPOh3MNz-2VUQplJhZhRarGb1k1I';
        //
        $client = new \Fcm\FcmClient(env("FB_CLOUD_MESSAGING_KEY"), env("FB_SENDER_ID"));
        //
        //
        $info = $client->deviceInfo($deviceId, true);
        $response = $client->send($info);  
        
        
        //$subscribe = $client->topicSubscribe('paraguay', $deviceId);
        //$response = $client->send($subscribe);
        
        //$unsubscribe = $client->topicUnsubscribe('paraguay', $deviceId);
        //$response = $client->send($unsubscribe);
        
        
        
        //$notification = $client->pushNotification('The title', 'The body', '/topics/paraguay');
        //$response = $client->send($notification);        
        
        
        dd($response);
        /*    
        //dd($assistances->studentUser->deviceTokens);
        */
        
        
        /*
        $users = \App\Models\User::whereNotNull('city_id')->get();
        
        $tokenIds = [];
        
        foreach ($users as $user) {
            $country = Str::kebab($user->city->country->name);
            
            if (! isset($tokenIds[$country])) {
                $tokenIds[$country] = [];
            }
            
            if ($user->deviceTokens) {
                foreach ($user->deviceTokens as $deviceToken) {
                    $tokenIds[$country][] = $deviceToken->device_token;
                }
            }
        }
        

        foreach ($tokenIds as $country => $tokens) {
            $subscribe = new \Fcm\Topic\Subscribe($country);
            foreach ($tokens as $token) {
                $subscribe->addDevice($token);
            }
            $client->send($subscribe);
        }

        //dd($subscribe);
        */
    });

    Route::get('/terms', 'TermsController@index');
    
    Route::group(['middleware' => 'auth'], function() {
        
        Route::get('/', 'HomeController@index')->name('home');        
        
        //Route::get('/students', 'StudentController@index')->name('students.index');
        Route::get('/students/{student}/assistances', 'StudentController@assistances')->name('students.assistances');
        Route::get('/students/{student}/promotions', 'StudentController@promotions')->name('students.promotions');
        Route::get('/students/{student}/promotions/create', 'StudentController@createPromotion')->name('students.promotions.create');
        Route::post('/students/{student}/promotions', 'StudentController@storePromotion')->name('students.promotions.store');
        Route::delete('/students/{student}/promotions/{promotion}/delete', 'StudentController@destroyPromotion')->name('students.promotions.destroy');
        
        Route::get('/academies/filter', 'AcademyController@filter')->name('academies.filter');
        Route::get('/countries/filter', 'CountryController@filter')->name('countries.filter');
        Route::get('/cities/filter', 'CityController@filter')->name('cities.filter');
        Route::get('/grades/filter', 'GradeController@filter')->name('grades.filter');
        Route::get('/roles/filter', 'RoleController@filter')->name('roles.filter');
        
        Route::get('/assistances/report/{report?}', 'AssistanceController@report')->name('assistances.report');
        
        Route::delete('/users/profile-photo', 'UserController@deleteProfilePhoto')->name('users.profile-photo.delete');
        Route::get('/users/filter', 'UserController@filter')->name('users.filter');        
        
        Route::get('/profile', 'ProfileController@index')->name('profile.index');        
        Route::put('/profile/update-profile', 'ProfileController@updatePersonalInformation')->name('profile.personal-information');
        Route::put('/profile/social-networks', 'ProfileController@updateSocialNetworks')->name('profile.social-networks');
        Route::put('/profile/change-password', 'ProfileController@changePassword')->name('profile.change-password');
        
        Route::any('/students/get', 'StudentController@get')->name('students.get');            
        Route::any('/promotions/get', 'PromotionController@get')->name('promotions.get');            
        Route::any('/instructors/get', 'InstructorController@get')->name('instructors.get');            
        Route::any('/motivations/get', 'MotivationController@get')->name('motivations.get');            
        Route::any('/academies/get', 'AcademyController@get')->name('academies.get');            
        Route::any('/countries/get', 'CountryController@get')->name('countries.get');            
        Route::any('/cities/get', 'CityController@get')->name('cities.get');            
        Route::any('/users/get', 'UserController@get')->name('users.get');            
        Route::any('/assistances/get', 'AssistanceController@get')->name('assistances.get');            
        
        Route::resource('/notifications', 'NotificationController')->only(['create', 'store']);
        Route::resource('/motivations', 'MotivationController');
        Route::resource('/academies', 'AcademyController');
        Route::resource('/countries', 'CountryController');
        Route::resource('/cities', 'CityController');
        Route::resource('/users', 'UserController');
        Route::resource('/students', 'StudentController');
        Route::resource('/instructors', 'InstructorController');
        Route::resource('/promotions', 'PromotionController');
        Route::resource('/assistances', 'AssistanceController')->only(['index']);
    
    });

});