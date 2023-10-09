<?php

namespace App\Utils;

use App\Exceptions\MarzbanException;
use App\Models\Settings;
use App\Models\User;
use App\Models\V2rayConfig;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class MarzbanUtil
{

    public const BASE_API_URL = "http://moboland.shop:8000/api";

    private static function generateConfigUsername(V2rayConfig $v2rayConfig)
    {

        $name = $v2rayConfig->user->name;
        $id = $v2rayConfig->id;

        return $id . "ID_$name";
    }

    /**
     * @throws \Exception
     */
    public static function getAccessToken(): string
    {
        return Settings::getMarzbanAccessToken();
    }

    protected static function defaultHeader(): array
    {
        try {

            return [
                "Authorization" => "Bearer " . self::getAccessToken(),
                "content-type" => "application/json",
            ];

        } catch (\Exception $e) {
            self::login();
            return self::defaultHeader();
        }
    }

    private static function getUrl(string $url): string
    {
        $url = str_starts_with('/', $url) ? $url : "/" . $url;

        return self::BASE_API_URL . $url;
    }

    public static function login()
    {

        $username = env('MARZBAN_ADMIN_USERNAME', "");
        $password = env("MARZBAN_ADMIN_PASSWORD", "");

        if (empty($username)) {
            throw new \Exception("MARZBAN_ADMIN_USERNAME environment variable undefined");
        }

        if (empty($password)) {
            throw new \Exception("MARZBAN_ADMIN_PASSWORD environment variable undefined");
        }

        $res = Http::asForm()->post(self::getUrl('admin/token'), [
            "username" => $username,
            "password" => $password,
        ]);

        if ($res->failed()) {
            throw new MarzbanException("login is not successful",MarzbanException::LOGIN_FAILED);
        }

        if (!Settings::setMarzbanAccessToken($res->json("access_token"))) {
            throw new MarzbanException("We can not save access token in database",MarzbanException::SAVE_TOKEN_FAILED);
        }

    }

    public static function addConfig(V2rayConfig $v2rayConfig): V2rayConfig
    {
        $v2rayConfig->marzban_config_username = self::generateConfigUsername($v2rayConfig);
        $v2rayConfig->enabled_at = now();

        $body = [
            "status" => "active",
            "username" => $v2rayConfig->marzban_config_username,
            "data_limit" => $v2rayConfig->sizeBytes,
            "data_limit_reset_strategy" => "no_reset",
            "expire" => $v2rayConfig->enabled_at->timestamp + $v2rayConfig->daysTimestamp,
            "inbounds" => [
                "vless" => [
                    "VLESS GRPC REALITY",
                ],
            ],
            "proxies" => [
                "vless" => [
                    "flow" => "",
                ],
            ],
        ];

        $res = Http::withHeaders(self::defaultHeader())->post(self::getUrl('user'), $body);

        switch ($res->status()) {
            case 409:
                throw new MarzbanException("the config was created with id = $v2rayConfig->id",MarzbanException::CONFIG_ALREADY_ADDED);
            case 403:
                self::login();
                self::addConfig($v2rayConfig);
        }

        if (!$res->ok()) throw new MarzbanException("we could not enable this config",MarzbanException::CREATE_CONFIG_FAILED);

        $exp = $res->json("expire");

        if ($exp != null) {
            $v2rayConfig->expired_at = new Carbon($exp);
        }

        $v2rayConfig->setConfig($res->json());

        return $v2rayConfig;
    }

    public static function getConfig(V2rayConfig|int $v2rayConfig): V2rayConfig
    {

        try {

            $v2rayConfig = is_int($v2rayConfig) ? V2rayConfig::findOrFail($v2rayConfig) : $v2rayConfig;

        } catch (\Exception $e) {
            throw new MarzbanException("v2rayConfig not found",MarzbanException::CONFIG_NOT_FOUND);
        }

        if ($v2rayConfig->marzban_config_username == null) {
            return $v2rayConfig;
        }

        $res = Http::withHeaders(self::defaultHeader())->get(self::getUrl("user/$v2rayConfig->marzban_config_username"));

        switch ($res->status()) {
            case 404:
                throw new MarzbanException("config not found",MarzbanException::CONFIG_NOT_FOUND);
            case 403:
                self::login();
                self::getConfig($v2rayConfig);
                break;
        }

        $v2rayConfig->setConfig($res->json());

        return $v2rayConfig;
    }

    public static function getConfigs(User|int $user): array
    {
        $user = is_int($user) ? User::findOrFail($user) : $user;

        $res = Http::withHeaders(self::defaultHeader())->get(self::getUrl("users?username=$user->uuid"));

        if ($res->status() == 403) {
            self::login();
            self::getConfigs($user);
        }

        $marzbarnUsers = [];

        foreach ($res->json("users") as $config) {

            if (preg_match("/\[id:(\d+)\]/", $config["username"], $matched) === false) {
                continue;
            }
            $marzbarnUsers[$matched->group(1)] = $config;
        }

        $v2rayConfigs = V2rayConfig::where('user_id', $user->id)->get();

        foreach ($v2rayConfigs as $v2rayConfig) {
            if (!array_key_exists($v2rayConfig->id, $marzbarnUsers)) continue;
            $v2rayConfig->setConfig($marzbarnUsers[$v2rayConfig->id]);
        }

        return $v2rayConfigs->all();
    }
}
