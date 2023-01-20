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

Route::middleware('auth:sanctum')->prefix('movies')->group(function() {
    Route::get('/', 'MoviesController@index');
    Route::post('/', 'MoviesController@store');
    Route::get('/{id}', 'MoviesController@show');
    Route::put('/{id}', 'MoviesController@update');
    Route::delete('/{id}', 'MoviesController@destroy');
    Route::put('/restore/{id}', 'MoviesController@restore');
});

Route::prefix('slots')->group(function () { 
    Route::get('/{id}', 'SlotsController@show');
});

Route::get('/allMovies', 'MoviesController@allMovies');
