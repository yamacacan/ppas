<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiClient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'token',
        'aes_key',
        'is_active',
        'last_used_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    public static function generateToken()
    {
        return bin2hex(random_bytes(32));
    }

    public static function generateAesKey()
    {
        return bin2hex(random_bytes(16));
    }
}