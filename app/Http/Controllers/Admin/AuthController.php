<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Utils\JWTUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        $request->validate([
            "username" => "required",
            "password" => "required",
        ], [
            "username" => "نام کاربری",
            "password" => "رمز عبور",
        ]);

        $admin = Admin::where("username", $request->username)->first();

        if ($admin == null || Hash::check($admin->password, $request->password)) {
            return errorRes(404, "نام کاربری یا رمز عبور اشتباه است.");
        }

        return successRes([
            "token" => [
                "access" => JWTUtil::generateForAdmin($admin),
                "refresh" => JWTUtil::generateForAdmin($admin, JWTUtil::getRefreshTokenLifetime()),
            ],
        ]);

    }

    public function refreshRToken(Request $request)
    {
        return successRes([
            "token" => [
                "access" => JWTUtil::generateForUser($request->admin),
                "refresh" => $request->bearerToken(),
            ],
        ]);
    }

}
