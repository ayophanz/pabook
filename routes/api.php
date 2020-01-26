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

Route::get('/hotel-receptionist/{id}', 'API\UserController@hotelReceptionist');

Route::get('/hotel-receptionist', 'API\UserController@hotelReceptionist');

Route::get('/profile', 'API\UserController@profile');

Route::put('/update-profile', 'API\UserController@UpdateProfile');

Route::post('/recep-capability/{action}', 'API\UserController@recapCap');

Route::get('/check-two-factor-if-expired', 'API\UserController@checkTwoFactExpired');


/**
* Hotel management
*/
Route::get('/hotels', 'API\HotelController@index');

Route::get('/hotels/{id}/{recep}/{capa}', 'API\HotelController@index');

Route::post('/create-hotel', 'API\HotelController@create');

Route::get('/edit-hotel/{id}', 'API\HotelController@show');

Route::put('/update-hotel/{id}', 'API\HotelController@update');

Route::delete('/delete-hotel/{id}', 'API\HotelController@destroy');

Route::put('/approve-hotel', 'API\HotelController@approveHotel');


/**
* Room Type
*/
Route::get('/room-types', 'API\RoomTypeController@index');

Route::get('/room-with-room-types/{id}/{roomId}', 'API\RoomTypeController@roomRoomTypes');

Route::get('/hotel-with-room-types/{hotelId}', 'API\RoomTypeController@bookingRoomTypes');

Route::post('/create-room-type', 'API\RoomTypeController@create');

Route::get('/edit-room-type/{id}', 'API\RoomTypeController@show');

Route::put('/update-room-type/{id}', 'API\RoomTypeController@update');

Route::delete('/delete-type/{id}', 'API\RoomTypeController@destroy');


/**
* Room
*/
Route::get('/rooms', 'API\RoomController@index');

Route::get('/specific-rooms/{ids}', 'API\RoomController@specificRooms');

Route::post('/create-room', 'API\RoomController@create');

Route::get('/edit-room/{id}', 'API\RoomController@show');

Route::post('/update-room/{id}', 'API\RoomController@update');

Route::delete('/delete-room/{id}', 'API\RoomController@destroy');

Route::get('/load-rooms/{start}/{end}', 'API\RoomController@availableRooms');


/**
* Booking
*/
Route::get('/bookings', 'API\BookController@index');

Route::post('/create-book', 'API\BookController@create');

Route::put('/book-cancelled', 'API\BookController@autoCancel');

Route::put('/room-check-out/{id}', 'API\BookController@checkOut');

Route::put('/room-check-in/{id}', 'API\BookController@checkIn');

Route::put('/room-book-cancel/{id}', 'API\BookController@bookCancel');

Route::put('/mark-as-read/{id}', 'API\BookController@markAsRead');

Route::get('/mark-as-read', 'API\BookController@markAsRead');

Route::get('/warning-incomplete-booking', 'API\BookController@incompleteBooking');

Route::delete('/cancel-booking/{id}', 'API\BookController@cancelBooking');

Route::get('/continue-booking/{id}', 'API\BookController@continueBooking');


/**
* Option
*/
Route::get('/config/{id}', 'API\OptionController@index');

Route::get('/config', 'API\OptionController@index');

Route::post('/create-config', 'API\OptionController@create');

// Client ID: 1
// Client secret: qrGGChraskfl3MXtcrSXK5afaFehdffW519S5pHX
// Password grant client created successfully.
// Client ID: 2
// Client secret: xtSnFJZUgIj4on6TJMGpME58kxfUZLmmH8rE0DyR

