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

Route::middleware('auth:sanctum')->get('/me', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->post('/me/logout', [App\Http\Controllers\Auth\LogoutController::class, 'logout'])->name('logout');
Route::middleware('auth:sanctum')->post('/me/update', [App\Http\Controllers\Auth\UserUpdateController::class, 'update'])->name('update');
Route::middleware('auth:sanctum')->patch('/me/change-password', [App\Http\Controllers\Auth\ChangePasswordController::class, 'reset'])->name('reset');

Route::post('register', [App\Http\Controllers\Auth\RegisterController::class, 'register'])->name('register');
Route::post('login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login');
