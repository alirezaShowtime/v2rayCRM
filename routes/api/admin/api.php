<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\V2rayConfigController;
use Illuminate\Support\Facades\Route;

Route::middleware('jwt:admin,refresh')->get('auth/refresh-token', [AuthController::class, 'refreshToken']);

Route::post('auth/login', [AuthController::class, 'login']);

Route::middleware('jwt:admin')->post('user/register', [UserController::class, 'register']);

Route::middleware('jwt:admin')->get('users', [UserController::class, 'getAll']);
Route::middleware('jwt:admin')->get('user/{id}', [UserController::class, 'getUser']);
Route::middleware('jwt:admin')->post('user/{id}/block', [UserController::class, 'block']);

Route::middleware('jwt:admin')->get('inbounds', [V2rayConfigController::class, "getInbounds"]);
Route::middleware('jwt:admin')->post('user/{id}/config/', [V2rayConfigController::class, 'create']);
Route::middleware('jwt:admin')->get('user/{id}/configs', [V2rayConfigController::class, 'getAllOfUser']);
Route::middleware('jwt:admin')->put('config/{id}/enable', [V2rayConfigController::class, 'enable']);
Route::middleware('jwt:admin')->get('configs', [V2rayConfigController::class, 'getAll']);
Route::middleware('jwt:admin')->get('configs/statistics', [V2rayConfigController::class, 'getConfigStatistics']);
