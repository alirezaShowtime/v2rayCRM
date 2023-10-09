<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Settings extends Model
{
    protected $table = "settings";

    protected $fillable = [
        "key",
        "value",
    ];

    public static function getValue(string $key)
    {
        try {
            $model = Settings::where('key', $key)->firstOrFail();
            if ($model->value == null) throw new \Exception();
            return $model->value;

        } catch (\Exception $e) {
            throw new \Exception("$key undefined or null");
        }
    }

    public static function setValue(string $key, string $value): bool
    {
        return DB::table("settings")->updateOrInsert(['key' => $key], ['value' => $value]);
    }

    public static function getMarzbanAccessToken()
    {
        return self::getValue("marzbanAccessToken");
    }

    public static function setMarzbanAccessToken(string $token): bool
    {
        $token = str_replace(["bearer", "Bearer"], "", $token);
        $key = "marzbanAccessToken";
        return self::setValue($key, $token);
    }

}
