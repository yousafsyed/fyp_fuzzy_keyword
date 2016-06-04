<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
 */
Route::get('/', function () {
    return view('welcome');
});

Route::auth();

Route::get('/home', 'HomeController@index');
Route::get('/dashboard', 'HomeController@index');
Route::get('/dashboard/addfile', 'HomeController@addFile');
Route::post('/dashboard/savefile', 'HomeController@saveFile');
Route::get('/dashboard/deletefile', 'HomeController@deteteFile');

Route::get('download', 'HomeController@DownloadFile');
Route::get('register/confirm/{token}', 'UserController@confirmEmail');

Route::group(['middleware' => 'web'], function () {
    Route::get('contact', 'ContactController@getContact');
    Route::post('contact', 'ContactController@postContact');
});
