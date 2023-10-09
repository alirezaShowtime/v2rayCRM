<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Utils\JWTUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('username', $request->username)->first();

        if ($user == null || !Hash::check($request->password, $user->password)) {
            return errorRes(400, "نام کاربری یا رمز عبور اشتباه است.");
        }

        return successRes([
            "token" => [
                "access" => JWTUtil::generateForUser($user),
                "refresh" => JWTUtil::generateForUser($user, JWTUtil::getRefreshTokenLifetime()),
            ],
        ]);

    }
}
