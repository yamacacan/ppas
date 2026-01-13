<?php

namespace App\Services;
use App\Models\FirmSettings;
use App\Models\Activity;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class StatisticsService
{
    /**
     * Tüm kategorilerin istatistiklerini getir
     * 
     * @param array $filters Filtreleme parametreleri (start_date, end_date, username vb.)
     * @return array
     */
    /**
     * Tüm kategorilerin istatistiklerini getir
     * 
     * @param array $filters Filtreleme parametreleri (start_date, end_date, username vb.)
     * @param int|null $limit Limit (opsiyonel)
     * @return array
     */
    public function getCategoryStatistics(array $filters = [], ?int $limit = null): array
    {
        $cacheKey = $this->getCacheKey('category_stats', $filters + ['limit' => $limit]);

        return Cache::remember($cacheKey, 3600, function () use ($filters, $limit) {
            $query = $this->applyFilters(Activity::query(), $filters);
            
            $statsQuery = DB::table('activity_categories')
                ->join('activities', 'activity_categories.activity_id', '=', 'activities.id')
                ->join('categories', 'activity_categories.category_id', '=', 'categories.id')
                ->select(
                    'categories.id',
                    'categories.name',
                    'categories.type',
                    DB::raw('COUNT(DISTINCT activity_categories.activity_id) as activity_count'),
                    DB::raw('SUM(activities.duration_ms) as total_duration_ms'),
                    DB::raw('AVG(activity_categories.confidence_score) as avg_confidence')
                )
                ->groupBy('categories.id', 'categories.name', 'categories.type')
                ->orderBy('total_duration_ms', 'desc');

            // Apply filters to the query
            $statsQuery = $this->applyFilters($statsQuery, $filters);

            if ($limit) {
                $statsQuery->limit($limit);
            }
                
            $stats = $statsQuery->get();
            
            // Eğer limit varsa, genel toplamı hesaplamak için ayrı sorgu gerekebilir
            // Ancak performans için şimdilik sadece çekilenlerin toplamını kullanacağız
            $totalDurationMs = $stats->sum('total_duration_ms');
            
            return [
                'categories' => $stats->map(function($stat) use ($totalDurationMs) {
                    $durationSeconds = $stat->total_duration_ms ? $stat->total_duration_ms / 1000 : 0;
                    $avgDurationSeconds = $stat->activity_count > 0 ? $durationSeconds / $stat->activity_count : 0;
                    
                    return [
                        'id' => $stat->id,
                        'name' => $stat->name,
                        'type' => $stat->type,
                        'activity_count' => $stat->activity_count,
                        'total_duration_ms' => $stat->total_duration_ms ?? 0,
                        'total_duration_seconds' => round($durationSeconds, 0),
                        'total_duration_hours' => round($durationSeconds / 3600, 2),
                        'avg_duration_seconds' => round($avgDurationSeconds, 0),
                        'avg_confidence' => round($stat->avg_confidence ?? 0, 2),
                        'percentage' => $totalDurationMs > 0 ? round(($stat->total_duration_ms / $totalDurationMs) * 100, 2) : 0,
                    ];
                }),
                'total_activities' => $stats->sum('activity_count'),
                'total_duration_hours' => round($stats->sum('total_duration_ms') / (1000 * 60 * 60), 2),
            ];
        });
    }

    /**
     * Belirli bir kategorinin istatistiklerini getir
     * 
     * @param int $categoryId
     * @param array $filters
     * @return array|null
     */
    public function getCategoryById(int $categoryId, array $filters = []): ?array
    {
        $category = Category::find($categoryId);
        
        if (!$category) {
            return null;
        }
        
        $query = Activity::byCategory($categoryId);
        $query = $this->applyFilters($query, $filters);
        
        $activityCount = $query->count();
        $totalDuration = $query->sum('duration_ms');
        
        // Alt kategorilerin istatistikleri
        $childrenStats = [];
        foreach ($category->children as $child) {
            $childStats = $this->getCategoryById($child->id, $filters);
            if ($childStats) {
                $childrenStats[] = $childStats;
            }
        }
        
        return [
            'id' => $category->id,
            'name' => $category->name,
            'type' => $category->type,
            'full_path' => $category->getFullPath(),
            'activity_count' => $activityCount,
            'total_duration_ms' => $totalDuration,
            'total_duration_hours' => round($totalDuration / (1000 * 60 * 60), 2),
            'children' => $childrenStats,
        ];
    }

    /**
     * Tagleme başarı oranını hesapla
     * 
     * @param array $filters
     * @return array
     */
    public function getTaggingRate(array $filters = []): array
    {
        $cacheKey = $this->getCacheKey('tagging_rate', $filters);

        return Cache::remember($cacheKey, 1800, function () use ($filters) {
            $query = $this->applyFilters(Activity::query(), $filters);
            
            // Tüm hesaplamaları süre (duraiton_ms) üzerinden yap
            $totalDuration = (clone $query)->sum('duration_ms');
            $taggedDuration = (clone $query)->tagged()->sum('duration_ms');
            $untaggedDuration = (count($filters) > 0) ? (clone $query)->untagged()->sum('duration_ms') : ($totalDuration - $taggedDuration);
            
            // Otomatik ve manuel ayrımı (Süre bazlı)
            $autoTaggedDuration = (clone $query)
                ->whereHas('categories', function($q) {
                    $q->where('activity_categories.is_manual', false);
                })
                ->sum('duration_ms');
                
            $manualTaggedDuration = (clone $query)
                ->whereHas('categories', function($q) {
                    $q->where('activity_categories.is_manual', true);
                })
                ->sum('duration_ms');
            
            // Ms -> Saat çevrimi için katsayı
            $divisor = 1000 * 60 * 60;

            $taggingRate = $totalDuration > 0 
                ? round(($taggedDuration / $totalDuration) * 100, 2) 
                : 0;
                
            $autoPercentage = $totalDuration > 0 ? round(($autoTaggedDuration / $totalDuration) * 100, 2) : 0;
            $manualPercentage = $totalDuration > 0 ? round(($manualTaggedDuration / $totalDuration) * 100, 2) : 0;
            
            return [
                // Değerleri saat cinsinden döndürüyoruz
                'total' => round($totalDuration / $divisor, 2),
                'tagged' => round($taggedDuration / $divisor, 2),
                'untagged' => round($untaggedDuration / $divisor, 2),
                'auto_tagged' => round($autoTaggedDuration / $divisor, 2),
                'manual_tagged' => round($manualTaggedDuration / $divisor, 2),
                'tagging_rate' => $taggingRate,
                'auto_percentage' => $autoPercentage,
                'manual_percentage' => $manualPercentage,
                'unit' => 'Saat'
            ];
        });
    }

    /**
     * Cache key oluşturucu
     */
    private function getCacheKey(string $prefix, array $filters): string
    {
        // Sıralı dizi oluşturarak key'in tutarlı olmasını sağla
        ksort($filters);
        return $prefix . ':' . md5(json_encode($filters));
    }

    /**
     * Zaman dilimine göre kategori dağılımını getir
     * 
     * @param string $startDate
     * @param string $endDate
     * @param string $groupBy 'day', 'week', 'month'
     * @return array
     */
    public function getTimeDistribution(string $startDate, string $endDate, string $groupBy = 'day'): array
    {
        $dateFormat = match($groupBy) {
            'hour' => '%Y-%m-%d %H:00',
            'day' => '%Y-%m-%d',
            'week' => '%Y-%U',
            'month' => '%Y-%m',
            default => '%Y-%m-%d',
        };
        
        // Temel istatistikler (Toplam süre, ortalama süre, toplam sayı)
        $stats = DB::table('activities')
            ->whereBetween('start_time_utc', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->select(
                DB::raw("DATE_FORMAT(start_time_utc, '{$dateFormat}') as period"),
                DB::raw('COUNT(*) as activity_count'),
                DB::raw('SUM(COALESCE(duration_ms, 0)) as total_duration_ms'),
                DB::raw('AVG(COALESCE(duration_ms, 0)) as avg_duration_ms')
            )
            ->groupBy('period')
            ->orderBy('period')
            ->get();
        
        // Work Duration per period
        $workStats = DB::table('activities')
            ->whereBetween('start_time_utc', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('activity_categories')
                      ->join('categories', 'activity_categories.category_id', '=', 'categories.id')
                      ->whereColumn('activity_categories.activity_id', 'activities.id')
                      ->where('categories.type', 'work');
            })
            ->select(
                DB::raw("DATE_FORMAT(start_time_utc, '{$dateFormat}') as period"),
                DB::raw('SUM(duration_ms) as duration')
            )
            ->groupBy('period')
            ->pluck('duration', 'period');

        // Other Duration per period
        $otherStats = DB::table('activities')
            ->whereBetween('start_time_utc', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('activity_categories')
                      ->join('categories', 'activity_categories.category_id', '=', 'categories.id')
                      ->whereColumn('activity_categories.activity_id', 'activities.id')
                      ->where('categories.type', 'work');
            })
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('activity_categories')
                      ->join('categories', 'activity_categories.category_id', '=', 'categories.id')
                      ->whereColumn('activity_categories.activity_id', 'activities.id')
                      ->where('categories.type', 'other');
            })
            ->select(
                DB::raw("DATE_FORMAT(start_time_utc, '{$dateFormat}') as period"),
                DB::raw('SUM(duration_ms) as duration')
            )
            ->groupBy('period')
            ->pluck('duration', 'period');
        
        return $stats->map(function($stat) use ($workStats, $otherStats) {
            $durationSeconds = $stat->total_duration_ms ? $stat->total_duration_ms / 1000 : 0;
            $avgDurationSeconds = $stat->avg_duration_ms ? $stat->avg_duration_ms / 1000 : 0;
            
            // Work/Other duration in hours for display? Or seconds?
            // Usually charts want consistent units. Let's provide seconds to be safe or hours.
            // Let's provide hours as that's likely the requested unit for "duration based", or just seconds and format in frontend.
            // Re-reading: "tüm grafikler... süre bazlı"
            // Since I changed TaggingRate to hours, consistency is good.
            // But TimeDistribution view might be expecting specific values.
            // Let's check what it uses: it expects 'work_count' and 'other_count' keys originally.
            // I will return hours in those keys to switch the data source of the chart.
            
            $workDuration = $workStats[$stat->period] ?? 0;
            $otherDuration = $otherStats[$stat->period] ?? 0;
            
            return [
                'period' => $stat->period,
                'activity_count' => $stat->activity_count,
                'total_duration_seconds' => round($durationSeconds, 0),
                'total_duration_hours' => round($durationSeconds / 3600, 2),
                'avg_duration_seconds' => round($avgDurationSeconds, 0),
                'work_count' => round($workDuration / (1000 * 60 * 60), 2), // Now represents Hours
                'other_count' => round($otherDuration / (1000 * 60 * 60), 2), // Now represents Hours
                'unit' => 'Saat'
            ];
        })->toArray();
    }

    /**
     * İş/Diğer dağılımı (work/other ratio)
     * 
     * @param array $filters
     * @return array
     */
    public function getWorkOtherRatio(array $filters = []): array
    {
        $query = $this->applyFilters(Activity::query(), $filters);
        
        // Tek bir sorgu ile Work ve Other istatistiklerini alalım
        // Bu, 4 ayrı sorgu yerine 2 sorgu (biri work, biri other) yapmaktan daha iyi olabilir
        // Ancak Eloquent/Query Builder ile karmaşık conditional aggregation yapmak zor olabilir
        // Bu yüzden optimize edilmiş 2 sorgu kullanacağız.
        
        // 1. Work Stats
        $workStats = (clone $query)
            ->whereHas('categories', function($q) {
                $q->where('type', 'work');
            })
            ->selectRaw('COUNT(*) as count, SUM(duration_ms) as duration')
            ->first();
            
        $workCount = $workStats->count ?? 0;
        $workDuration = $workStats->duration ?? 0;

        // 2. Other Stats (Work olmayan ama Other olanlar)
        $otherStats = (clone $query)
            ->whereDoesntHave('categories', function($q) {
                $q->where('type', 'work');
            })
            ->whereHas('categories', function($q) {
                $q->where('type', 'other');
            })
            ->selectRaw('COUNT(*) as count, SUM(duration_ms) as duration')
            ->first();

        $otherCount = $otherStats->count ?? 0;
        $otherDuration = $otherStats->duration ?? 0;
        
        // Toplam (Sadece iş ve diğer olarak sınıflandırılmışlar)
        $totalDuration = $workDuration + $otherDuration;
        $totalCount = $workCount + $otherCount;
        
        return [
            'work' => [
                'activity_count' => $workCount,
                'duration_hours' => round($workDuration / (1000 * 60 * 60), 2),
                'avg_duration_minutes' => $workCount > 0 ? round($workDuration / (1000 * 60) / $workCount, 1) : 0,
                'percentage' => $totalDuration > 0 ? round(($workDuration / $totalDuration) * 100, 2) : 0,
            ],
            'other' => [
                'activity_count' => $otherCount,
                'duration_hours' => round($otherDuration / (1000 * 60 * 60), 2),
                'avg_duration_minutes' => $otherCount > 0 ? round($otherDuration / (1000 * 60) / $otherCount, 1) : 0,
                'percentage' => $totalDuration > 0 ? round(($otherDuration / $totalDuration) * 100, 2) : 0,
            ],
            'total' => [
                'activity_count' => $totalCount,
                'duration_hours' => round($totalDuration / (1000 * 60 * 60), 2),
            ],
        ];
    }

    /**
     * Query'ye filtreleri uygula
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function applyFilters($query, array $filters)
    {
        if (!empty($filters['start_date'])) {
            $query->where('activities.start_time_utc', '>=', $filters['start_date']);
        }
        
        if (!empty($filters['end_date'])) {
            $query->where('activities.start_time_utc', '<=', $filters['end_date']);
        }
        
        if (!empty($filters['username'])) {
            $query->where('activities.username', $filters['username']);
        }

        if (!empty($filters['motherboard_uuid'])) {
            $query->where('activities.motherboard_uuid', $filters['motherboard_uuid']);
        }
        
        if (!empty($filters['activity_type'])) {
            $query->where('activities.activity_type', $filters['activity_type']);
        }
        
        if (!empty($filters['unit_id'])) {
            $query->whereExists(function ($q) use ($filters) {
                $q->select(DB::raw(1))
                  ->from('computer_users')
                  ->whereColumn('computer_users.username', 'activities.username')
                  ->whereColumn('computer_users.motherboard_uuid', 'activities.motherboard_uuid')
                  ->where('computer_users.unit_id', $filters['unit_id']);
            });
        }
        
        return $query;
    }
    /**
     * Mesai saatleri analizi
     */
    public function getWorkingHourStats(array $filters): array
    {
        $cacheKey = $this->getCacheKey('working_hours', $filters);

        return Cache::remember($cacheKey, 3600, function () use ($filters) {
            $query = $this->applyFilters(Activity::query(), $filters);
            
            // UTC+3 (TR) varsayımı ile saat dilimi ayarı
            // Mesai saatlerini veritabanından al
            $settings = \App\Models\FirmSettings::instance();
            $startTime = $settings->work_start_time; // '09:00:00'
            $endTime = $settings->work_end_time; // '18:00:00'
            
            $stats = (clone $query)->selectRaw("
                SUM(CASE 
                    WHEN TIME(ADDTIME(start_time_utc, '03:00:00')) >= ? AND TIME(ADDTIME(start_time_utc, '03:00:00')) < ? 
                    THEN duration_ms ELSE 0 END) as total_working_hours_duration,
                SUM(CASE 
                    WHEN TIME(ADDTIME(start_time_utc, '03:00:00')) < ? OR TIME(ADDTIME(start_time_utc, '03:00:00')) >= ? 
                    THEN duration_ms ELSE 0 END) as total_outside_hours_duration
            ", [$startTime, $endTime, $startTime, $endTime])->first();

            // Sadece 'Work' aktiviteleri için
            $workQuery = (clone $query)->whereHas('categories', function($q) {
                $q->where('type', 'work');
            });

            $workStats = $workQuery->selectRaw("
                SUM(CASE 
                    WHEN TIME(ADDTIME(start_time_utc, '03:00:00')) >= ? AND TIME(ADDTIME(start_time_utc, '03:00:00')) < ? 
                    THEN duration_ms ELSE 0 END) as work_working_hours_duration,
                SUM(CASE 
                    WHEN TIME(ADDTIME(start_time_utc, '03:00:00')) < ? OR TIME(ADDTIME(start_time_utc, '03:00:00')) >= ? 
                    THEN duration_ms ELSE 0 END) as work_outside_hours_duration
            ", [$startTime, $endTime, $startTime, $endTime])->first();

            $divisor = 1000 * 60 * 60; // Saate çevir

            return [
                'working_hours' => [
                    'total' => round(($stats->total_working_hours_duration ?? 0) / $divisor, 2),
                    'work' => round(($workStats->work_working_hours_duration ?? 0) / $divisor, 2),
                ],
                'outside_hours' => [
                    'total' => round(($stats->total_outside_hours_duration ?? 0) / $divisor, 2),
                    'work' => round(($workStats->work_outside_hours_duration ?? 0) / $divisor, 2),
                ]
            ];
        });
    }

    /**
     * Haftalık Ritim (Haftanın günlerine göre İş aktivitesi ortalaması)
     */
    public function getWeeklyRhythm(array $filters): array
    {
        $cacheKey = $this->getCacheKey('weekly_rhythm', $filters);

        return Cache::remember($cacheKey, 3600, function () use ($filters) {
            $query = Activity::query();
            $query = $this->applyFilters($query, $filters);

            // Sadece İş aktiviteleri
            $query->whereHas('categories', function($q) {
                $q->where('type', 'work');
            });

            // DAYOFWEEK: 1=Sunday, 2=Monday, ..., 7=Saturday
            // Biz Pazartesi (2) -> Pazar (1) sıralaması istiyoruz
            
            $results = $query->selectRaw("
                DAYOFWEEK(ADDTIME(start_time_utc, '03:00:00')) as day_num,
                SUM(duration_ms) as total_duration,
                COUNT(DISTINCT DATE(ADDTIME(start_time_utc, '03:00:00'))) as unique_days
            ")
            ->groupBy('day_num')
            ->get();

            $days = [
                2 => 'Pazartesi',
                3 => 'Salı',
                4 => 'Çarşamba',
                5 => 'Perşembe',
                6 => 'Cuma',
                7 => 'Cumartesi',
                1 => 'Pazar'
            ];

            $output = [];
            foreach ($days as $num => $name) {
                $record = $results->firstWhere('day_num', $num);
                $totalDurationHours = $record ? ($record->total_duration / (1000 * 60 * 60)) : 0;
                $uniqueDays = $record ? $record->unique_days : 1; // Sıfıra bölme hatası olmasın
                
                // Ortalama: Toplam Süre / O günün kaç kere yaşandığı (Filtre aralığında kaç Pazartesi var?)
                $avgHours = $uniqueDays > 0 ? $totalDurationHours / $uniqueDays : 0;

                $output[] = [
                    'day' => $name,
                    'avg_hours' => round($avgHours, 2),
                    'total_hours' => round($totalDurationHours, 2)
                ];
            }

            return $output;
        });
    }

    /**
     * En çok kullanılan Keywordler
     */
    public function getTopKeywords(array $filters, int $limit = 5): array
    {
        $cacheKey = $this->getCacheKey('top_keywords', $filters + ['limit' => $limit]);

        return Cache::remember($cacheKey, 3600, function () use ($filters, $limit) {
            $query = DB::table('activity_categories')
                ->join('activities', 'activity_categories.activity_id', '=', 'activities.id')
                ->whereNotNull('activity_categories.matched_keyword')
                ->where('activity_categories.matched_keyword', '!=', '');

            // Apply filters manually to builder since applyFilters expects Eloquent
            if (!empty($filters['start_date'])) $query->where('activities.start_time_utc', '>=', $filters['start_date']);
            if (!empty($filters['end_date'])) $query->where('activities.start_time_utc', '<=', $filters['end_date']);
            if (!empty($filters['username'])) $query->where('activities.username', $filters['username']);
            if (!empty($filters['motherboard_uuid'])) $query->where('activities.motherboard_uuid', $filters['motherboard_uuid']);

            return $query->select(
                    'activity_categories.matched_keyword as keyword',
                    DB::raw('COUNT(*) as usage_count'),
                    DB::raw('SUM(activities.duration_ms) as total_duration')
                )
                ->groupBy('keyword')
                ->orderBy('total_duration', 'desc')
                ->limit($limit)
                ->get()
                ->map(function($item) {
                    return [
                        'keyword' => $item->keyword,
                        'count' => $item->usage_count,
                        'duration_hours' => round($item->total_duration / (1000 * 60 * 60), 2)
                    ];
                })
                ->toArray();
        });
    }

    /**
     * En çok kullanılan Uygulamalar (Process Name)
     */
    public function getTopProcesses(array $filters, int $limit = 5): array
    {
        $cacheKey = $this->getCacheKey('top_processes', $filters + ['limit' => $limit]);

        return Cache::remember($cacheKey, 3600, function () use ($filters, $limit) {
            $query = $this->applyFilters(Activity::query(), $filters);
            
            return $query->select(
                    'process_name',
                    DB::raw('SUM(duration_ms) as total_duration')
                )
                ->groupBy('process_name')
                ->orderBy('total_duration', 'desc')
                ->limit($limit)
                ->get()
                ->map(function($item) {
                    return [
                        'process_name' => $item->process_name,
                        'duration_hours' => round($item->total_duration / (1000 * 60 * 60), 2)
                    ];
                })
                ->toArray();
        });
    }
}

