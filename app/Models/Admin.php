<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Admin extends Model
{

    protected $fillable = [
        "username",
        "password",
        "uuid",
    ];

    protected $hidden = [
        "password",
    ];

    protected $casts = [
        "password" => "hashed"
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($q) {
            $q->uuid = $q->uuid ?? Str::uuid();
        });
    }
}
