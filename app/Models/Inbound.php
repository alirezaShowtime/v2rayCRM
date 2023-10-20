<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inbound extends Model
{


    protected $fillabel = [
        "name",
        "type",
    ];


    public function v2rayConfigs()
    {
        return $this->belongsToMany(V2rayConfig::class);
    }
}
