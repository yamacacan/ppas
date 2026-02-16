<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessSummary extends Model
{
    use HasFactory;

    protected $table = 'process_summaries';

    public $timestamps = false;

    protected $fillable = [
        'date',
        'username',
        'motherboard_uuid',
        'process_name',
        'total_duration_ms',
        'activity_count',
    ];

    protected $casts = [
        'date' => 'date',
        'total_duration_ms' => 'integer',
        'activity_count' => 'integer',
    ];
}
