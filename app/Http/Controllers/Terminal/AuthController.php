<?php

namespace App\Http\Controllers\Terminal;

use App\Utils\AdminAuthUtil;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function loginPage(Request $request)
    {
        return view('terminal.login');
    }

    public function login(Request $request)
    {

        $request->validate([
            "username" => "required|string",
            "password" => "required|string",
        ]);

        AdminAuthUtil::login(
            username: $request->username,
            password: $request->password,
        );

        return redirect()->route('terminal.panel');
    }

    public function logout()
    {
        AdminAuthUtil::logout();
    }
}
