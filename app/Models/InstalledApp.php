<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstalledApp extends Model
{
    use HasFactory;

    protected $table = 'installed_apps';

    protected $fillable = [
        'app_name',
        'username',
        'domain',
        'user_sid',
        'motherboard_uuid',
        'machine_name',
        'collected_at'
    ];

    public $timestamps = false;
}
