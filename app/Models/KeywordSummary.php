<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeywordSummary extends Model
{
    use HasFactory;

    protected $table = 'keyword_summaries';

    public $timestamps = false;

    protected $fillable = [
        'date',
        'username',
        'motherboard_uuid',
        'keyword',
        'category_id',
        'category_name',
        'match_count',
        'total_duration_ms',
    ];

    protected $casts = [
        'date' => 'date',
        'match_count' => 'integer',
        'total_duration_ms' => 'integer',
    ];
}
