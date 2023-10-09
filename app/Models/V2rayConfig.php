<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class V2rayConfig extends Model
{
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
