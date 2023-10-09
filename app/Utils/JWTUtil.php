<?php

namespace App\Utils;

use App\Models\Admin;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTUtil
{

    private const ALG = 'HS256';

    public static function generate(array $payload, int $lifetime = null)
    {
        $payload["exp"] = now()->getTimestamp() + ($lifetime ?? self::getLifTime());

        return JWT::encode($payload, self::getKey(), self::ALG);
    }

    public static function generateForUser(User $user, $lifetime = null)
    {
        return self::generate(['userUUID' => $user->uuid], $lifetime);
    }

    public static function generateForAdmin(Admin $admin, $lifetime = null)
    {
        return self::generate(['adminUUID' => $admin->uuid], $lifetime);
    }

    public static function decode($token): array
    {
        $decoded = JWT::decode($token, new Key(self::getKey(), self::ALG));

        return json_decode(json_encode($decoded), true);
    }

    public static function decodeForUser($token): User
    {
        $userUUID = self::decode($token)['userUUID'];

        return User::where("uuid", $userUUID)->firstOrFail();
    }

    public static function decodeForAdmin($token): Admin
    {
        $adminUUID = self::decode($token)['adminUUID'];

        return Admin::where("uuid", $adminUUID)->firstOrFail();
    }

    private static function getKey(): string
    {
        return \config('auth.jwt_key');
    }

    private static function getLifTime(): int
    {
        return DateTimeUtil::seconds(\config('auth.jwt_token_lifetime'));
    }

    public static function getRefreshTokenLifetime(): int
    {
        return DateTimeUtil::seconds(\config('auth.jwt_refresh_token_lifetime'));
    }

}
