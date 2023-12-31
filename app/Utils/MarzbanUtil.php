<?php

namespace App\Utils;

use App\Exceptions\MarzbanException;
use App\Models\Settings;
use App\Models\User;
use App\Models\V2rayConfig;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;

class MarzbanUtil
{

    private static function generateConfigUsername(V2rayConfig $v2rayConfig)
    {

        $name = $v2rayConfig->user->username;
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

        $url = str_starts_with($url, '/') ? $url : "/" . $url;

        $base = env("MARZBAN_URL");

        if (str_ends_with($base, '/')) {
            return substr($base, 0, -1) . $url;
        }

        return $base . $url;
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
            throw new MarzbanException("login is not successful", MarzbanException::LOGIN_FAILED);
        }

        if (!Settings::setMarzbanAccessToken($res->json("access_token"))) {
            throw new MarzbanException("We can not save access token in database", MarzbanException::SAVE_TOKEN_FAILED);
        }
    }

    public static function addConfig(V2rayConfig $v2rayConfig): V2rayConfig
    {
        $v2rayConfig->marzban_config_username = self::generateConfigUsername($v2rayConfig);
        $v2rayConfig->enabled_at = now();

        $inbundsOfConfig = $v2rayConfig->inbounds()->get();

        $inbunds = [];
        $proxis = [];

        foreach ($inbundsOfConfig as  $inbound) {

            $inbunds[$inbound->type][] = $inbound->name;
        }

        if (in_array("vmess", array_keys($inbunds))) {
            $proxis["vmess"] = [];
        }
        if (in_array("trojan", array_keys($inbunds))) {
            $proxis["trojan"] = [];
        }
        if (in_array("shadowsocks", array_keys($inbunds))) {
            $proxis["shadowsocks"] = [
                "method" => "chacha20-poly1305",
            ];
        }
        if (in_array("vless", array_keys($inbunds))) {
            $proxis["vless"] = [
                "flow" => "",
            ];
        }

        $body = [
            "status" => "active",
            "username" => $v2rayConfig->marzban_config_username,
            "data_limit" => $v2rayConfig->sizeBytes,
            "data_limit_reset_strategy" => "no_reset",
            "expire" => $v2rayConfig->enabled_at->timestamp + $v2rayConfig->daysTimestamp,
            "inbounds" => $inbunds,
            "proxies" => $proxis,
        ];

        $res = Http::withHeaders(self::defaultHeader())->post(self::getUrl('user'), $body);

        switch ($res->status()) {
            case 409:
                throw new MarzbanException("the config was created with id = $v2rayConfig->id", MarzbanException::CONFIG_ALREADY_ADDED);
            case 403:
                self::login();
                self::addConfig($v2rayConfig);
        }

        if (!$res->ok()) {
            throw new MarzbanException("we could not enable this config", MarzbanException::CREATE_CONFIG_FAILED);
        }

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
            throw new MarzbanException("v2rayConfig not found", MarzbanException::CONFIG_NOT_FOUND);
        }

        if ($v2rayConfig->marzban_config_username == null) {
            return $v2rayConfig;
        }

        $res = Http::withHeaders(self::defaultHeader())->get(self::getUrl("user/$v2rayConfig->marzban_config_username"));

        switch ($res->status()) {
            case 404:
                throw new MarzbanException("config not found", MarzbanException::CONFIG_NOT_FOUND);
            case 403:
                self::login();
                self::getConfig($v2rayConfig);
                break;
        }

        $v2rayConfig->setConfig($res->json());

        return $v2rayConfig;
    }

    public static function getConfigs(
        User|int|null    $user,
        int|null    $offset = null,
        int|null    $limit = null,
        string      $sort = "asc",
        string|null $status = null,
    ): array {

        $user = is_int($user) ? User::find($user) : $user;

        $queryParams = [
            "offset" => $offset,
            "limit" => $limit,
            "status" => $status,
        ];

        if ($user !== null) {

            $queryParams["username"] = $user->username;
            $queryParams["sort"] = $sort == "asc" ? "username" : "-username";
        }


        $query = "";

        foreach ($queryParams as $k => $v) {

            if ($v === null) continue;
            $query .= "&$k=$v";
        }

        $query = substr_replace($query, "?", 0, 1);

        $res = Http::withHeaders(self::defaultHeader())->get(self::getUrl("users$query"));

        if ($res->status() == 403) {
            self::login();
            self::getConfigs($user);
        }

        if (!$res->ok()) throw new MarzbanException("get configs is failed", MarzbanException::GET_CONFIGS_FAILED);

        $marzbarnUsers = [];

        foreach ($res->json("users") as $config) {

            $r = preg_match("/(\d+)ID_\w+/", $config["username"], $matched);

            if ($r === false || $r === 0) {
                continue;
            }
            $marzbarnUsers[$matched[1]] = $config;
        }

        $v2rayConfigs = $user == null
            ? V2rayConfig::orderBy("id", $sort)
            : V2rayConfig::where('user_id', $user->id)->orderBy("id", $sort);

        if ($offset != null) {
            $v2rayConfigs->skip($offset);
        }
        if ($limit != null) {
            $v2rayConfigs->limit($limit);
        }
        switch ($status) {
            case "active":
                $v2rayConfigs->whereNot('enabled_at', null);
                break;
            case "disabled":
                $v2rayConfigs->where('enabled_at', null);
                break;
            case "expired":
                $v2rayConfigs->whereDate('expired_at', "<", now());
        }

        $v2rayConfigs = $v2rayConfigs->get();

        foreach ($v2rayConfigs as $v2rayConfig) {
            if (!array_key_exists($v2rayConfig->id, $marzbarnUsers)) continue;
            $v2rayConfig->setConfig($marzbarnUsers[$v2rayConfig->id]);
        }

        return $v2rayConfigs->all();
    }
}
