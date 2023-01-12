<?php

use Illuminate\Support\Facades\Route;
use Modules\Cinema\Http\Controllers\CinemaController;

Route::controller(CinemaController::class)->middleware('auth:sanctum')->prefix('cinemas')->group(function() {
    Route::get('/{id}', 'getOne');
    Route::get('', 'getMany');
    Route::post('', 'create');
    Route::put('/{id}', 'update');
    Route::delete('/{id}', 'delete');
    Route::put('/restore/{id}', 'restore');
});
