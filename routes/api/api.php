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


Route::middleware('jwt:user,refresh')->get('refresh-token', [AuthController::class, 'refreshRToken']);

Route::post('login', [AuthController::class, 'login']);

Route::middleware('jwt:user')->get('config/{id}', [V2rayConfigController::class, 'get']);
