<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrowserData extends Model
{
    use HasFactory;

    protected $table = 'browser_datas';

    public $timestamps = false;

    protected $fillable = [
        'url',
        'title',
        'browser',
        'visit_time_utc',
        'base_url',
        'username',
        'domain',
        'user_sid',
        'motherboard_uuid',
        'created_at_utc',
        'received_at',
    ];

    protected $casts = [
        'visit_time_utc' => 'datetime',
        'created_at_utc' => 'datetime',
        'received_at' => 'datetime',
    ];
}
