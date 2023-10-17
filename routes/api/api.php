<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

use App\Http\Controllers\AuthController;
use App\Http\Controllers\V2rayConfigController;
use Illuminate\Support\Facades\Route;


Route::middleware(['jwt:user,refresh', 'userNotBlock'])->get('auth/refresh-token', [AuthController::class, 'refreshRToken']);

Route::post('auth/login', [AuthController::class, 'login']);

Route::middleware(['jwt:user', 'userNotBlock'])->get('configs', [V2rayConfigController::class, 'getAll']);
Route::middleware(['jwt:user', 'userNotBlock'])->get('config/{id}', [V2rayConfigController::class, 'get']);
Route::middleware(['jwt:user', 'userNotBlock'])->post('config/{id}/enable', [V2rayConfigController::class, 'enable']);
