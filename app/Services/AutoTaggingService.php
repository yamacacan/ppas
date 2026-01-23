<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\CategoryKeyword;
use Illuminate\Support\Facades\Log;

class AutoTaggingService
{
    /**
     * Tek bir aktiviteyi otomatik tagler
     * 
     * @param int $activityId
     * @return array Tagleme sonuçları
     */
    public function tagActivity(int $activityId): array
    {
        $activity = Activity::find($activityId);
        
        if (!$activity) {
            return [
                'success' => false,
                'message' => 'Aktivite bulunamadı',
            ];
        }
        
        $matches = $this->matchKeywords($activity);
        
        if (empty($matches)) {
            return [
                'success' => true,
                'message' => 'Eşleşen keyword bulunamadı',
                'tagged_count' => 0,
            ];
        }
        
        $taggedCount = 0;
        
        foreach ($matches as $match) {
            // Bu kategoriye zaten taglenmiş mi kontrol et
            if (!$activity->categories()->where('category_id', $match['category_id'])->exists()) {
                $activity->categories()->attach($match['category_id'], [
                    'matched_keyword' => $match['keyword'],
                    'match_type' => $match['match_type'],
                    'confidence_score' => $match['confidence_score'],
                    'is_manual' => false,
                    'tagged_at' => now(),
                ]);
                
                $taggedCount++;
                
                // Alert kontrolü
                $this->checkAndSendAlert($activity, $match['keyword_model']);
            }
        }
        
        Log::info("Aktivite taglendi", [
            'activity_id' => $activityId,
            'tagged_count' => $taggedCount,
            'matches' => \Illuminate\Support\Arr::except($matches, ['keyword_model']), // Log'da model objesini gizle
        ]);
        
        return [
            'success' => true,
            'message' => "{$taggedCount} kategoriye taglendi",
            'tagged_count' => $taggedCount,
            'matches' => $matches,
        ];
    }

    /**
     * Taglenmemiş aktiviteleri toplu olarak tagler
     * 
     * @param int $limit Maksimum kaç aktivite tagleneceği
     * @return array İstatistikler
     */
    public function tagUntaggedActivities(int $limit = 100): array
    {
        $activities = Activity::untagged()->limit($limit)->get();
        
        $totalTagged = 0;
        $totalProcessed = 0;
        
        foreach ($activities as $activity) {
            $result = $this->tagActivity($activity->id);
            $totalProcessed++;
            
            if ($result['success'] && $result['tagged_count'] > 0) {
                $totalTagged++;
            }
        }
        
        return [
            'processed' => $totalProcessed,
            'tagged' => $totalTagged,
            'skipped' => $totalProcessed - $totalTagged,
        ];
    }

    protected $cachedKeywords = null;

    /**
     * Aktivite için keyword eşleştirme yapar
     * 
     * @param Activity $activity
     * @return array Eşleşen keyword'ler ve kategoriler
     */
    protected function matchKeywords(Activity $activity): array
    {
        $matches = [];
        
        // Bu aktiviteyi yapan ComputerUser'ı bul
        $computerUser = \App\Models\ComputerUser::where('username', $activity->username)
                            ->where('motherboard_uuid', $activity->motherboard_uuid)
                            ->first();

        // Birim atanmamışsa tagleme yapma
        if (!$computerUser || !$computerUser->unit_id) {
            return [];
        }

        // Tüm aktif keyword'leri priority'ye göre getir
        // Memory cache kullanarak her job'da DB'ye gitmeyi önleyebiliriz (mevcut request süresince)
        if ($this->cachedKeywords === null) {
            $this->cachedKeywords = CategoryKeyword::with(['category', 'overrides.category'])
                ->active()
                ->byPriority()
                ->get();
        }
        
        $keywords = $this->cachedKeywords;
            $matched = false;
            $matchedField = null;
            
            // Process name'de kontrol et
            if ($activity->process_name && $keyword->matches($activity->process_name)) {
                $matched = true;
                $matchedField = 'process_name';
            }
            
            // Title'da kontrol et
            if (!$matched && $activity->title && $keyword->matches($activity->title)) {
                $matched = true;
                $matchedField = 'title';
            }
            
            // URL'de kontrol et (browser aktiviteleri için)
            if (!$matched && $activity->url && $keyword->matches($activity->url)) {
                $matched = true;
                $matchedField = 'url';
            }
            
            if ($matched) {
                // Hedef kategoriyi belirle (Bağlamsal Kontrol)
                $targetCategoryId = $keyword->category_id;
                
                // Eğer keyword'ün kategorisi yoksa (Category silinmiş ama keyword kalmış)
                // Bu durumu logla ve atla, ya da varsayılan bir kategori ata.
                // Şimdilik atlıyoruz çünkü kategorisiz tagleme yapılamaz.
                if (!$targetCategoryId || !$keyword->category) {
                    continue;
                }

                $targetCategoryName = $keyword->category->name;
                
                if ($computerUser) {
                    // 1. Kullanıcı Bazlı Override Kontrolü
                    $userOverride = $keyword->overrides
                        ->where('computer_user_id', $computerUser->id)
                        ->first();
                        
                    if ($userOverride) {
                        $targetCategoryId = $userOverride->category_id;
                        $targetCategoryName = $userOverride->category->name;
                    } 
                    // 2. Birim Bazlı Override Kontrolü (Eğer kullanıcıda yoksa)
                    elseif ($computerUser->unit_id) {
                        $unitOverride = $keyword->overrides
                            ->where('unit_id', $computerUser->unit_id)
                            ->whereNull('computer_user_id')
                            ->first();
                            
                        if ($unitOverride) {
                            $targetCategoryId = $unitOverride->category_id;
                            $targetCategoryName = $unitOverride->category->name;
                        }
                    }
                }

                // Bu kategoriye daha önce eşleşme eklenmemiş mi kontrol et
                $categoryAlreadyMatched = false;
                foreach ($matches as $existingMatch) {
                    if ($existingMatch['category_id'] === $targetCategoryId) {
                        $categoryAlreadyMatched = true;
                        break;
                    }
                }
                
                // Eğer bu kategori için daha yüksek priority'li eşleşme yoksa ekle
                if (!$categoryAlreadyMatched) {
                    $matches[] = [
                        'category_id' => $targetCategoryId,
                        'category_name' => $targetCategoryName,
                        'keyword' => $keyword->keyword,
                        'keyword_model' => $keyword, // Notification için gerekli
                        'match_type' => $keyword->match_type,
                        'matched_field' => $matchedField,
                        'confidence_score' => $keyword->getConfidenceScore(),
                        'priority' => $keyword->priority,
                    ];
                    
                    // En yüksek öncelikli eşleşmeyi bulduk, diğerlerine bakmaya gerek yok
                    // Tek bir kategoriye taglenmesi istendiği için döngüyü kırıyoruz
                    break;
                }
            }
        }
        
        return $matches;
    }

    /**
     * Alert şartlarını kontrol eder ve bildirim gönderir
     */
    protected function checkAndSendAlert(Activity $activity, CategoryKeyword $keyword)
    {
        if ($keyword->is_alert && $keyword->alert_unit_id) {
            
            // İstisna kontrolü
            $computerUser = \App\Models\ComputerUser::where('username', $activity->username)
                ->where('motherboard_uuid', $activity->motherboard_uuid)
                ->first();

            if ($computerUser) {
                // 1. Kullanıcı Bazlı İstisna
                $isUserException = $keyword->alertExceptions()
                    ->where('computer_user_id', $computerUser->id)
                    ->exists();

                if ($isUserException) {
                    Log::info("Keyword alert iptal edildi (Kullanıcı İstisnası): {$computerUser->username} -> {$keyword->keyword}");
                    return;
                }

                // 2. Birim Bazlı İstisna
                if ($computerUser->unit_id) {
                    $isUnitException = $keyword->alertExceptions()
                        ->where('unit_id', $computerUser->unit_id)
                        ->whereNull('computer_user_id')
                        ->exists();

                    if ($isUnitException) {
                        Log::info("Keyword alert iptal edildi (Birim İstisnası): {$computerUser->unit->name} -> {$keyword->keyword}");
                        return;
                    }
                }
            }

            // Notification gönderilecek kullanıcıları bul
            $alertUnit = \App\Models\Unit::find($keyword->alert_unit_id);
            
            if ($alertUnit) {
                // Birimdeki kullanıcıları al
                $users = $alertUnit->users;
                
                // Bildirimi gönder
                if ($users->count() > 0) {
                    \Illuminate\Support\Facades\Notification::send($users, new \App\Notifications\KeywordAlertNotification($activity, $keyword));
                    
                    Log::info("Keyword alert bildirimi gönderildi", [
                        'keyword' => $keyword->keyword,
                        'unit' => $alertUnit->name,
                        'user_count' => $users->count()
                    ]);
                }
            }
        }
    }

    /**
     * Güven skoru hesaplama
     * 
     * @param string $matchType
     * @return float
     */
    protected function calculateConfidenceScore(string $matchType): float
    {
        return match($matchType) {
            'exact' => 100.0,
            'contains' => 80.0,
            'starts_with' => 70.0,
            'ends_with' => 70.0,
            'regex' => 60.0,
            default => 50.0,
        };
    }
}
