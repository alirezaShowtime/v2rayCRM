<?php

namespace App\Utils;

use App\Utils\JWTUtil;
use Illuminate\Support\Facades\Session;

class AdminAuthUtil
{

    protected static $SESSION_KEY = "adminAuthenticate";

    public static function getUsername(): string
    {
        return env('TERMINAL_USERNAME');
    }

    public static function getPassword(): string
    {
        return env('TERMINAL_PASSWORD');
    }

    public static function login(string $username, string $password): bool
    {

        if ($username !== self::getUsername() || $password !== self::getPassword()) return false;

        $token =  JWTUtil::generate([
            "username" => self::getUsername(),
            "password" => self::getPassword(),
        ], seconds("1h"));

        Session::remove(self::$SESSION_KEY);
        Session::put(self::$SESSION_KEY, $token);

        return true;
    }


    public static function isLogged(): bool
    {
        $token = Session::get(self::$SESSION_KEY);

        if ($token === null) return false;

        try {

            $payload = JWTUtil::decode($token);

            if (
                !is_array($payload)
                || !array_key_exists('username', $payload)
                || !array_key_exists('password', $payload)
            ) throw new \Exception();


            return true;
        } catch (\Exception $e) {
            return false;
        }
    }


    public static function logout(): void
    {
        Session::remove(self::$SESSION_KEY);
    }
}
