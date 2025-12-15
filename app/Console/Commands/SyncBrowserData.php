<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Activity;
use App\Models\BrowserData;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SyncBrowserData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'performance:sync-browser-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Syncs URLs from browser_datas to activities based on user and time matching.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting browser data sync...');

        // URL'i olmayan ve son 24 saatte oluşturulmuş aktiviteleri al (performans için limitli)
        // Eğer tüm geçmişi taramak gerekirse tarihi kaldırabiliriz ama çok yavaş olabilir.
        $activities = Activity::whereNull('url')
            ->whereNotNull('start_time_utc')
            ->where('start_time_utc', '>=', Carbon::now()->subHours(24))
            ->orderBy('id', 'desc')
            ->chunk(100, function ($activities) {
                foreach ($activities as $activity) {
                    $this->syncActivity($activity);
                }
            });

        $this->info('Browser data sync completed.');
        return Command::SUCCESS;
    }

    protected function syncActivity(Activity $activity)
    {
        // Eşleşme kriterleri:
        // 1. KESİN: Aynı kullanıcı (username)
        // 2. KESİN: Zaman penceresi (Activity start_time +/- 15 sn)
        // 3. PUANLAMA: Metin Benzerliği (Title vs Title/URL)
        
        $startTime = $activity->start_time_utc;
        $windowSeconds = 15; // Biraz daha genişletelim
        
        $query = BrowserData::where('username', $activity->username)
            ->where('visit_time_utc', '>=', $startTime->copy()->subSeconds($windowSeconds))
            ->where('visit_time_utc', '<=', $startTime->copy()->addSeconds($windowSeconds));
            
        if ($activity->motherboard_uuid) {
            $query->where('motherboard_uuid', $activity->motherboard_uuid);
        }

        $candidates = $query->get();
        
        if ($candidates->isEmpty()) {
            return;
        }
        
        $bestMatch = null;
        $highestScore = 0;
        
        foreach ($candidates as $candidate) {
            $score = 0;
            
            // 1. Zaman Puanı (0-50 puan)
            // Ne kadar yakınsa o kadar iyi
            $diff = abs($startTime->diffInSeconds($candidate->visit_time_utc));
            $timeScore = max(0, 50 - ($diff * 3)); 
            $score += $timeScore;

            // 2. Metin Benzerlik Puanı (0-50 puan)
            // Activity Title ile Browser Title veya URL karşılaştır
            $textScore = 0;
            if ($activity->title && ($candidate->title || $candidate->url)) {
                // Basit kelime eşleşmesi kontrolü
                $activityWords = array_filter(explode(' ', strtolower($activity->title)), function($w) {
                    return strlen($w) > 3; // Önemsiz kısa kelimeleri at
                });
                
                $browserText = strtolower(($candidate->title ?? '') . ' ' . $candidate->url);
                
                $matchCount = 0;
                foreach ($activityWords as $word) {
                    if (str_contains($browserText, $word)) {
                        $matchCount++;
                    }
                }
                
                if (count($activityWords) > 0) {
                    $ratio = $matchCount / count($activityWords);
                    $textScore = $ratio * 50;
                }
            } else {
                // Title yoksa zaman puanına göre karar ver, ama puanı düşük tut
                $textScore = 10; 
            }
            
            $score += $textScore;
            
            // Debug için log açılabilir
            // $this->info("Candidate: {$candidate->url}, Score: $score (Time: $timeScore, Text: $textScore)");

            if ($score > $highestScore) {
                $highestScore = $score;
                $bestMatch = $candidate;
            }
        }
        
        // Eşik değeri: Sadece yeterince güvenilir eşleşmeleri kaydet
        // Örn: 30 puan altı şüpheli olabilir
        if ($bestMatch && $highestScore > 25) {
            $this->info("Match found for Activity #{$activity->id} ({$activity->process_name}) -> URL: {$bestMatch->url} (Score: " . number_format($highestScore, 1) . ")");
            
            $activity->url = $bestMatch->url;
            $activity->base_url = $bestMatch->base_url;
            $activity->browser = $bestMatch->browser;
            
            if (empty($activity->title) && !empty($bestMatch->title)) {
                $activity->title = $bestMatch->title;
            }
            
            $activity->save();
            
            try {
                $activity->autoTag();
            } catch (\Exception $e) {
                Log::error("Auto-tagging failed: " . $e->getMessage());
            }
        }
    }
}
