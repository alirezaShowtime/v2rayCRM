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
        return self::generate(['userId' => $user->id], $lifetime);
    }

    public static function generateForAdmin(Admin $admin, $lifetime = null)
    {
        return self::generate(['adminId' => $admin->id], $lifetime);
    }

    public static function decode($token): array
    {
        $decoded = JWT::decode($token, new Key(self::getKey(), self::ALG));

        return json_decode(json_encode($decoded));
    }

    public static function decodeForUser($token): User
    {
        $userId = self::decode($token)['userId'];

        return User::findOrFail($userId);
    }

    public static function decodeForAdmin($token): Admin
    {
        $adminId = self::decode($token)['userId'];

        return Admin::findOrFail($adminId);
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
