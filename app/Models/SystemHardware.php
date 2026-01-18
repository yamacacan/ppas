<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemHardware extends Model
{
    use HasFactory;

    protected $table = 'system_hardware';

    protected $fillable = [
        'hostname',
        'username',
        'domain',
        'user_sid',
        'os_version',
        'model',
        'serial_number',
        'motherboard_uuid',
        'disk_total_gb',
        'disk_used_gb',
        'disk_free_gb',
        'disk_usage_percent',
        'ram_total_gb',
        'ram_used_gb',
        'ram_free_gb',
        'ram_usage_percent',
        'collected_at'
    ];

    public $timestamps = false;
}
