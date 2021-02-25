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

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => config('admin.path') ], function() {
    Route::get('login','AdminAuth\LoginAdminController@loginForm')->name('admin.login');
    Route::post('login','AdminAuth\LoginAdminController@login');
    Route::group(['middleware' => 'auth:admin'], function() {
        Route::get('/','HomeAdminController@index')->name('admin.home');
        Route::post('logout','AdminAuth\LoginAdminController@logout')->name('admin.logout');
        Route::get('add','HomeAdminController@add');
        Route::get('profile','HomeAdminController@profile');
    });
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
