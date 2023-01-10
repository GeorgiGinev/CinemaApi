<?php

use Illuminate\Support\Facades\Route;
use Modules\Cinema\Http\Controllers\CinemaController;

Route::controller(CinemaController::class)->prefix('cinema')->group(function() {
    Route::get('/{id}', 'getOne');
    Route::get('s', 'getMany');
    Route::post('', 'create');
    Route::put('/{id}', 'update');
    Route::delete('/{id}', 'delete');
});

Route::controller(CinemaController::class)->prefix('cinemas')->group(function() {
    Route::get('', 'getMany');
});
