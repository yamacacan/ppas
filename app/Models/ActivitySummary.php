<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivitySummary extends Model
{
    use HasFactory;

    protected $table = 'activity_summaries';

    public $timestamps = false;

    protected $fillable = [
        'date',
        'hour',
        'username',
        'motherboard_uuid',
        'category_type',
        'category_id',
        'is_manual',
        'total_duration_ms',
        'activity_count',
    ];

    protected $casts = [
        'date' => 'date',
        'hour' => 'integer',
        'total_duration_ms' => 'integer',
        'activity_count' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function computerUser()
    {
        return $this->belongsTo(ComputerUser::class, 'username', 'username');
    }

    public function getDateStringAttribute()
    {
        return $this->date->toDateString();
    }
}
