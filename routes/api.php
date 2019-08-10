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


/**
* User management
*/
Route::get('/users', 'API\UserController@index');

Route::post('/create-user', 'API\UserController@create');

Route::get('/edit-user/{id}', 'API\UserController@show');

Route::put('/update-user/{id}', 'API\UserController@update');

Route::delete('/delete-user/{id}', 'API\UserController@destroy');

Route::get('/hotel-owners', 'API\UserController@hotelOwner');

Route::get('/profile', 'API\UserController@profile');

Route::put('/update-profile', 'API\UserController@UpdateProfile');


/**
* Hotel management
*/
Route::get('/hotels', 'API\HotelController@index');

Route::post('/create-hotel', 'API\HotelController@create');

Route::get('/edit-hotel/{id}', 'API\HotelController@show');

Route::put('/update-hotel/{id}', 'API\HotelController@update');

Route::delete('/delete-hotel/{id}', 'API\HotelController@destroy');


/**
* Room Type
*/
Route::get('/room-types', 'API\RoomTypeController@index');

Route::get('/room-types/{id}', 'API\RoomTypeController@index');

Route::post('/create-room-type', 'API\RoomTypeController@create');

Route::get('/edit-room-type/{id}', 'API\RoomTypeController@show');

Route::put('/update-room-type/{id}', 'API\RoomTypeController@update');

Route::delete('/delete-type/{id}', 'API\RoomTypeController@destroy');


/**
* Room
*/
Route::get('/rooms', 'API\RoomController@index');

Route::post('/create-room', 'API\RoomController@create');

Route::get('/edit-room/{id}', 'API\RoomController@show');

Route::post('/update-room/{id}', 'API\RoomController@update');

Route::delete('/delete-room/{id}', 'API\RoomController@destroy');

// Client ID: 1
// Client secret: qrGGChraskfl3MXtcrSXK5afaFehdffW519S5pHX
// Password grant client created successfully.
// Client ID: 2
// Client secret: xtSnFJZUgIj4on6TJMGpME58kxfUZLmmH8rE0DyR