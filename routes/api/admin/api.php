<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::post('auth/login', [AuthController::class, 'login']);

Route::middleware('jwt:admin')->post('user/register', [UserController::class, 'register']);

Route::middleware('jwt:admin')->get('users', [UserController::class, 'getAll']);
Route::middleware('jwt:admin')->get('user/{id}', [UserController::class, 'getUser']);