<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FirmSettings extends Model
{
    use HasFactory;

    protected $table = 'firm_settings';

    protected $fillable = [
        'firm_name',
        'address',
        'email',
        'logo_path',
        'work_start_time',
        'work_end_time'
    ];

    /**
     * Get the singleton firm settings instance.
     */
    public static function instance()
    {
        return static::first() ?? new static([
            'work_start_time' => '09:00:00',
            'work_end_time' => '18:00:00',
        ]);
    }
}
