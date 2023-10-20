<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class V2rayConfig extends Model
{

    public array|null $config_data = null;

    protected $fillable = [
        "remark",
        "size",
        "enabled_at",
        "expired_at",
        "days",
        "price",
        "user_id",
        "admin_id",
        'marzban_config_username'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function inbounds() {
        return $this->belongsToMany(Inbound::class);
    }

    public function setConfig(array $config)
    {

        $this->config_data = [
            "status" => $config["status"],
            "expire" => $config["expire"],
            "used_traffic" => $config["used_traffic"],
            "links" => $config["links"],
        ];
    }

    public function getDaysTimestampAttribute()
    {
        return $this->days * 24 * 60 * 60;
    }

    public function getSizeBytesAttribute()
    {
        return $this->size * 1073741824;
    }

}
