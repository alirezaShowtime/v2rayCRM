<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\V2rayConfigController;
use Illuminate\Support\Facades\Route;

Route::middleware('jwt:admin,refresh')->get('refresh-token', [AuthController::class, 'refreshToken']);

Route::post('auth/login', [AuthController::class, 'login']);

Route::middleware('jwt:admin')->post('user/register', [UserController::class, 'register']);

Route::middleware('jwt:admin')->get('users', [UserController::class, 'getAll']);
Route::middleware('jwt:admin')->get('user/{id}', [UserController::class, 'getUser']);

Route::middleware('jwt:admin')->post('user/{id}/config/', [V2rayConfigController::class, 'create']);
Route::middleware('jwt:admin')->get('user/{id}/configs', [V2rayConfigController::class, 'getAll']);
