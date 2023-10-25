<?php

use App\Http\Controllers\Terminal\AuthController;
use App\Http\Controllers\Terminal\TerminalController;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::prefix("auth")->name("auth.")->group(function () {


    Route::middleware("terminal_auth:false")->name("login")->get('login', [AuthController::class, "loginPage"]);

    Route::middleware("terminal_auth:false")->name("login.store")->post('login', [AuthController::class, "login"]);

    Route::middleware("terminal_auth")->name("logout")->post('logout', [AuthController::class, "logout"]);
});


Route::middleware("terminal_auth")->withoutMiddleware(VerifyCsrfToken::class)->name("run-command")->post("run-command", [TerminalController::class, "runCommend"]);
Route::middleware("terminal_auth")->name("panel")->get("/", [TerminalController::class, "panel"]);
