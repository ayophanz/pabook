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

// Route::get('/', function () {
//     return view('welcome');
// });

//Auth::routes();

//Route::get('/', 'HomeController@index')->name('name');

Auth::routes(['verify' => true, 'reset' => true]);

Route::get('/resend-email-verification', 'HomeController@ResendVerification');

Route::get('/{vue?}', 'HomeController@index')->where('vue', '[\/\w\.-]*');
