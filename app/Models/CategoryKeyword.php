<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryKeyword extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'keyword',
        'match_type',
        'is_case_sensitive',
        'priority',
        'is_active',
        'is_alert',
        'alert_unit_id',
    ];

    protected $casts = [
        'is_case_sensitive' => 'boolean',
        'is_active' => 'boolean',
        'is_alert' => 'boolean',
        'priority' => 'integer',
    ];

    // İlişki

    /**
     * Keyword'ün ait olduğu kategori
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Bildirim gidecek birim
     */
    public function alertUnit()
    {
        return $this->belongsTo(Unit::class, 'alert_unit_id');
    }

    public function overrides()
    {
        return $this->hasMany(KeywordOverride::class, 'keyword_id');
    }

    public function alertExceptions()
    {
        return $this->hasMany(KeywordAlertException::class, 'keyword_id');
    }

    // Scope'lar

    /**
     * Sadece aktif keyword'leri getir
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Önceliğe göre sırala (yüksek önce)
     */
    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'desc');
    }

    // Helper Metodlar

    /**
     * Verilen metni eşleşme tipine göre kontrol eder
     * 
     * @param string $text Kontrol edilecek metin
     * @return bool Eşleşme var mı?
     */
    public function matches(string $text): bool
    {
        $keyword = $this->keyword;
        $haystack = $this->is_case_sensitive ? $text : strtolower($text);
        $needle = $this->is_case_sensitive ? $keyword : strtolower($keyword);

        switch ($this->match_type) {
            case 'exact':
                return $haystack === $needle;
            
            case 'contains':
                return str_contains($haystack, $needle);
            
            case 'starts_with':
                return str_starts_with($haystack, $needle);
            
            case 'ends_with':
                return str_ends_with($haystack, $needle);
            
            case 'regex':
                try {
                    return preg_match($keyword, $text) === 1;
                } catch (\Exception $e) {
                    \Log::error("Regex eşleşme hatası: " . $e->getMessage(), [
                        'keyword_id' => $this->id,
                        'keyword' => $keyword,
                    ]);
                    return false;
                }
            
            default:
                return false;
        }
    }

    /**
     * Match type'a göre güven skoru döndürür
     * 
     * @return float Güven skoru (0-100)
     */
    public function getConfidenceScore(): float
    {
        return match($this->match_type) {
            'exact' => 100.0,
            'contains' => 80.0,
            'starts_with' => 70.0,
            'ends_with' => 70.0,
            'regex' => 60.0,
            default => 50.0,
        };
    }
}
