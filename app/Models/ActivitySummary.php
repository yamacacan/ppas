<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivitySummary extends Model
{
    use HasFactory;

    protected $table = 'activity_summaries';

    protected $fillable = [
        'date',
        'summary_type',
        'category_id',
        'type_name',
        'total_duration_seconds',
        'activity_count',
        'unique_users',
    ];

    protected $casts = [
        'date' => 'date',
        'total_duration_seconds' => 'integer',
        'activity_count' => 'integer',
        'unique_users' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
