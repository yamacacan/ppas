<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $table = 'activities';
    
    // Laravel timestamps kullanmıyoruz çünkü tablo 'received_at' kullanıyor
    public $timestamps = false;

    protected $fillable = [
        'evt',
        'activity_type',
        'process_name',
        'title',
        'start_time_utc',
        'end_time_utc',
        'duration_ms',
        'username',
        'domain',
        'user_sid',
        'motherboard_uuid',
        'url',
        'browser',
        'base_url',
        'created_at_utc',
    ];

    protected $casts = [
        'start_time_utc' => 'datetime',
        'end_time_utc' => 'datetime',
        'created_at_utc' => 'datetime',
        'received_at' => 'datetime',
        'duration_ms' => 'integer',
    ];

    // İlişki

    /**
     * Aktivitenin ait olduğu bilgisayar kullanıcısı
     */
    public function computerUser()
    {
         return $this->belongsTo(ComputerUser::class, 'username', 'username')
                    ->where('motherboard_uuid', $this->motherboard_uuid);
    }


    /**
     * Aktivitenin ait olduğu kategoriler (many-to-many)
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'activity_categories')
            ->withPivot('matched_keyword', 'match_type', 'confidence_score', 'is_manual', 'tagged_at')
            ->as('tag');
    }

    // Accessor'lar

    /**
     * Süreyi saniye cinsinden döndür
     * duration_ms varsa onu kullan, yoksa start/end time farkından hesapla
     */
    public function getDurationSecondsAttribute(): int
    {
        if ($this->duration_ms) {
            return (int) ($this->duration_ms / 1000);
        }
        
        // duration_ms yoksa start ve end time'dan hesapla
        if ($this->start_time_utc && $this->end_time_utc) {
            return $this->end_time_utc->diffInSeconds($this->start_time_utc);
        }
        
        return 0;
    }

    /**
     * Süreyi formatlanmış string olarak döndür (HH:MM:SS)
     */
    public function getDurationFormattedAttribute(): string
    {
        $seconds = $this->duration_seconds;
        
        if ($seconds === 0) {
            return '-';
        }
        
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;
        
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }

    // Scope'lar


    /**
     * Taglenmiş aktiviteler
     */
    public function scopeTagged($query)
    {
        return $query->has('categories');
    }

    /**
     * Taglenmemiş aktiviteler
     */
    public function scopeUntagged($query)
    {
        return $query->doesntHave('categories');
    }

    /**
     * Belirli bir kategoriye göre filtrele
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->whereHas('categories', function($q) use ($categoryId) {
            $q->where('categories.id', $categoryId);
        });
    }

    // Helper Metodlar

    /**
     * Bu aktiviteyi otomatik tagler
     * AutoTaggingService kullanarak tagleme yapar
     * 
     * @return array Taglenen kategori bilgileri
     */
    public function autoTag(): array
    {
        $service = app(\App\Services\AutoTaggingService::class);
        return $service->tagActivity($this->id);
    }

    /**
     * Manuel olarak kategori tagleme
     * 
     * @param int $categoryId Kategori ID
     * @param string|null $note Opsiyonel not
     * @return void
     */
    public function tagManually(int $categoryId, ?string $note = null): void
    {
        // Zaten taglenmiş mi kontrol et
        if ($this->categories()->where('category_id', $categoryId)->exists()) {
            return;
        }

        $this->categories()->attach($categoryId, [
            'matched_keyword' => $note,
            'match_type' => 'manual',
            'confidence_score' => 100.0,
            'is_manual' => true,
            'tagged_at' => now(),
        ]);
    }

    /**
     * Kategoriden tag kaldır
     * 
     * @param int $categoryId Kategori ID
     * @return void
     */
    public function removeTag(int $categoryId): void
    {
        $this->categories()->detach($categoryId);
    }
}
