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

Route::prefix('bookings')->group(function() {
    Route::middleware('auth:sanctum')->post('/{cinemaId}/{slotId}', 'BookingsController@store');
    Route::middleware('auth:sanctum')->get('/', 'BookingsController@get');
    Route::middleware('auth:sanctum')->delete('/{id}', 'BookingsController@destroy');

    Route::middleware('auth:sanctum')->get('/all', 'BookingsController@index');

    Route::get('/{cinemaId}/{slotId}', 'BookingsController@show');
});