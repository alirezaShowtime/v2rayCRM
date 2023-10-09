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
    ];

}
