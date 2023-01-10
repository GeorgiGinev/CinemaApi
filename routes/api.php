<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->post('/logout', [App\Http\Controllers\Auth\LogoutController::class, 'logout'])->name('logout');
Route::middleware('auth:sanctum')->post('users/update', [App\Http\Controllers\Auth\UserUpdateController::class, 'update'])->name('update');

Route::post('register', [App\Http\Controllers\Auth\RegisterController::class, 'register'])->name('register');
Route::post('login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login');
