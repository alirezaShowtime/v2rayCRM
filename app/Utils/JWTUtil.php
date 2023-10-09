<?php

namespace App\Utils;

use App\Models\Admin;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use stdClass;

class JWTUtil
{

    private const ALG = 'HS256';

    //-----------------------------access token-----------------------------

    public static function generate(array $payload, int $lifetime = null)
    {
        $head = [
            "exp" => now()->getTimestamp() + ($lifetime ?? self::getLifTime()),
            "tokenType" => "accessToken"
        ];

        return JWT::encode($payload, self::getKey(), self::ALG, null, $head);
    }

    public static function decode($token): array
    {
        $headers = new stdClass();
        $decoded = JWT::decode($token, new Key(self::getKey(), self::ALG), $headers);

        if ($headers->tokenType != "accessToken") {
            throw new \Exception("token type is not 'accessToken'");
        }

        return json_decode(json_encode($decoded), true);
    }

    private static function getLifTime(): int
    {
        return DateTimeUtil::seconds(\config('auth.jwt_token_lifetime'));
    }

    //--------------------------access token: user--------------------------

    public static function generateForUser(User $user, $lifetime = null)
    {
        return self::generate(['userUUID' => $user->uuid], $lifetime);
    }

    public static function decodeForUser($token): User
    {
        $userUUID = self::decode($token)['userUUID'];

        return User::where("uuid", $userUUID)->firstOrFail();
    }

    //--------------------------access token: admin-------------------------

    public static function generateForAdmin(Admin $admin, $lifetime = null)
    {
        return self::generate(['adminUUID' => $admin->uuid], $lifetime);
    }

    public static function decodeForAdmin($token): Admin
    {
        $adminUUID = self::decode($token)['adminUUID'];

        return Admin::where("uuid", $adminUUID)->firstOrFail();
    }


    //-----------------------------refresh token----------------------------

    public static function generateRefresh(array $payload, $lifetime)
    {
        $head = [
            "exp" => now()->getTimestamp() + ($lifetime ?? self::getRefreshTokenLifetime()),
            "tokenType" => "refreshToken"
        ];

        return JWT::encode($payload, self::getKey(), self::ALG, null, $head);
    }

    public static function decodeRefreshToken($token): array
    {
        $headers = new stdClass();

        $decoded = JWT::decode($token, new Key(self::getKey(), self::ALG), $headers);

        if ($headers->tokenType != "refreshToken") {
            throw new \Exception("token type is not 'refreshToken'");
        }

        return json_decode(json_encode($decoded), true);
    }

    //--------------------------refresh token: user--------------------------

    public static function generateRefreshTokenForUser(User $user, $lifetime = null)
    {
        return self::generateRefresh(['userUUID' => $user->uuid], $lifetime);
    }

    public static function decodeRefreshTokenForUser($token): User
    {
        $userUUID = self::decodeRefreshToken($token)['userUUID'];

        return User::where("uuid", $userUUID)->firstOrFail();
    }

    //--------------------------refresh token: admin--------------------------

    public static function generateRefreshTokenForAdmin(Admin $admin, $lifetime = null)
    {
        return self::generateRefresh(['adminUUID' => $admin->uuid], $lifetime);
    }

    public static function decodeRefreshTokenForAdmin($token): Admin
    {
        $adminUUID = self::decodeRefreshToken($token)['adminUUID'];

        return Admin::where("uuid", $adminUUID)->firstOrFail();
    }

    public static function getRefreshTokenLifetime(): int
    {
        return DateTimeUtil::seconds(\config('auth.jwt_refresh_token_lifetime'));
    }

    private static function getKey(): string
    {
        return \config('auth.jwt_key');
    }
}
