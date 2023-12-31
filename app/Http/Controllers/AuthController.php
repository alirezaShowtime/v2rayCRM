<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Utils\JWTUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
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

        if ($user->blocked_at != null) {
            return errorRes(403, "شما مسدود شده اید.");
        }

        return successRes([
            "token" => [
                "access" => JWTUtil::generateForUser($user),
                "refresh" => JWTUtil::generateRefreshTokenForUser($user),
            ],
        ]);

    }

    public function refreshRToken(Request $request)
    {
        return successRes([
            "token" => [
                "access" => JWTUtil::generateForUser($request->user),
                "refresh" => $request->bearerToken(),
            ],
        ]);
    }

}
