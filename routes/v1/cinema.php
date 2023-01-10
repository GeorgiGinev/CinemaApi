<?php

use Illuminate\Support\Facades\Route;
use Modules\Cinema\Http\Controllers\CinemaController;

Route::controller(CinemaController::class)->middleware('auth:sanctum')->prefix('cinema')->group(function() {
    Route::get('/{id}', 'getOne');
    Route::post('', 'create');
    Route::put('/{id}', 'update');
    Route::delete('/{id}', 'delete');
});

Route::controller(CinemaController::class)->middleware('auth:sanctum')->prefix('cinemas')->group(function() {
    Route::get('', 'getMany');
});
