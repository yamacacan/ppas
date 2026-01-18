<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityCategory extends Model
{
    protected $table = 'activity_categories';
    
    public $timestamps = false;

    protected $fillable = [
        'activity_id',
        'category_id',
        'matched_keyword',
        'match_type',
        'confidence_score',
        'is_manual',
        'tagged_at',
    ];

    protected $casts = [
        'confidence_score' => 'float',
        'is_manual' => 'boolean',
        'tagged_at' => 'datetime',
    ];

    // İlişkiler

    /**
     * Tag'in ait olduğu aktivite
     */
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    /**
     * Tag'in ait olduğu kategori
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
