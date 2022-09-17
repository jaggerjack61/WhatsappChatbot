<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\WebhookController;
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

Route::get('/', function () {
    return view('pages.dashboard');
});


Route::get('/webhook/',[WebhookController::class,'webhook']);


Route::controller(AuthController::class)->group(function(){
    Route::get('/login','showLogin')->name('show-login');
    Route::post('/login','login')->name('login');
    Route::get('/logout','logout')->name('logout');

});

Route::middleware('auth')->group(function () {

    Route::controller(MainController::class)->group(function(){
        Route::get('/','showDashboard')->name('show-dashboard');
    });

    Route::controller(SettingsController::class)->group(function(){
        Route::prefix('settings')->group(function(){
            Route::get('/users','showUsers')->name('show-users');
            Route::post('/users/save','saveUser')->name('save-user');
            Route::post('/users/update','updateUser')->name('update-user');
            Route::get('/users/demote/{user}','demote')->name('demote');
            Route::get('/users/promote/{user}','promote')->name('promote');
            Route::get('/users/activate/{user}','activate')->name('activate');
            Route::get('/users/deactivate/{user}','deactivate')->name('deactivate');
            Route::get('/profile','showProfile')->name('show-profile');
        });

    });
});
