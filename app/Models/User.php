<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'username',
        'is_blocked',
        'password',
        'uuid',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($q) {
            $q->uuid = $q->uuid ?? Str::uuid();
        });
    }

    public function configs()
    {
        return $this->hasMany(V2rayConfig::class);
    }

}
