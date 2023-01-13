<?php

use Illuminate\Support\Facades\Route;
use Modules\Movies\Http\Controllers\MoviesController;

Route::controller(MoviesController::class)->prefix('movies')->group(function() {
    Route::get('', 'index');
    // Route::get('/{id}', 'getOne');
    // Route::get('', 'index');
    // Route::post('', 'create');
    // Route::put('/{id}', 'update');
    // Route::delete('/{id}', 'delete');
    // Route::put('/restore/{id}', 'restore');
    // Route::get('/all', 'getAll');
});
