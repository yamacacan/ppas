<?php

namespace App\Services;
use App\Models\FirmSettings;
use App\Models\Activity;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\ActivitySummary;
use App\Models\ProcessSummary;
use App\Models\KeywordSummary;

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
        $cacheKey = $this->getCacheKey('category_stats_v3', $filters + ['limit' => $limit]);

        return Cache::remember($cacheKey, 300, function () use ($filters, $limit) {
            $query = ActivitySummary::whereNotNull('category_id')
                ->with('category')
                ->select(
                    'category_id',
                    DB::raw('SUM(activity_count) as activity_count'),
                    DB::raw('SUM(total_duration_ms) as total_duration_ms')
                );

            $query = $this->applySummaryFilters($query, $filters);

            $stats = $query->groupBy('category_id')
                ->orderByDesc('total_duration_ms')
                ->when($limit, fn($q) => $q->limit($limit))
                ->get();
            
            // Eğer limit varsa, genel toplamı hesaplamak için ayrı sorgu gerekebilir
            // Ancak performans için şimdilik sadece çekilenlerin toplamını kullanacağız
            $totalDurationMs = $stats->sum('total_duration_ms');
            
            return [
                'categories' => $stats->map(function($stat) use ($totalDurationMs) {
                    $durationSeconds = $stat->total_duration_ms ? $stat->total_duration_ms / 1000 : 0;
                    $avgDurationSeconds = $stat->activity_count > 0 ? $durationSeconds / $stat->activity_count : 0;
                    
                    return [
                        'id' => $stat->category_id,
                        'name' => $stat->category->name ?? 'Unknown',
                        'type' => $stat->category->type ?? 'other',
                        'activity_count' => $stat->activity_count,
                        'total_duration_ms' => $stat->total_duration_ms ?? 0,
                        'total_duration_seconds' => round($durationSeconds, 0),
                        'total_duration_hours' => round($durationSeconds / 3600, 2),
                        'avg_duration_seconds' => round($avgDurationSeconds, 0),
                        'avg_confidence' => 0, // Özet tabloda tutulmuyor
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
        $cacheKey = $this->getCacheKey('tagging_rate_v3', $filters);

        return Cache::remember($cacheKey, 1800, function () use ($filters) {
            $query = ActivitySummary::whereNull('category_id');
            $query = $this->applySummaryFilters($query, $filters);

            $stats = (clone $query)->select(
                'category_type',
                DB::raw('SUM(total_duration_ms) as duration')
            )->groupBy('category_type')->pluck('duration', 'category_type');

            $totalDuration = $stats->sum();
            $taggedDuration = ($stats['work'] ?? 0) + ($stats['other'] ?? 0);
            $untaggedDuration = $stats['untagged'] ?? 0;

            // Manuel/Otomatik ayrımı için Kategori bazlı özetlere bakmalıyız
            $manualQuery = ActivitySummary::whereNotNull('category_id')->where('is_manual', true);
            $manualQuery = $this->applySummaryFilters($manualQuery, $filters);
            
            $manualTaggedDuration = $manualQuery->sum('total_duration_ms');
            $autoTaggedDuration = $taggedDuration - $manualTaggedDuration;
            
            $divisor = 1000 * 60 * 60;

            $taggingRate = $totalDuration > 0 ? round(($taggedDuration / $totalDuration) * 100, 2) : 0;
            $autoPercentage = $totalDuration > 0 ? round(($autoTaggedDuration / $totalDuration) * 100, 2) : 0;
            $manualPercentage = $totalDuration > 0 ? round(($manualTaggedDuration / $totalDuration) * 100, 2) : 0;
            
            return [
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
    public function getTimeDistribution(string $startDate, string $endDate, string $groupBy = 'day', array $filters = []): array
    {
        $filters['start_date'] = $startDate;
        $filters['end_date'] = $endDate;

        // Özet tablodan verileri çekelim
        $query = ActivitySummary::whereNull('category_id');
        $query = $this->applySummaryFilters($query, $filters);

        $stats = (clone $query)
            ->select('date', DB::raw('SUM(activity_count) as activity_count'), DB::raw('SUM(total_duration_ms) as total_duration_ms'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // İş ve Diğer süreleri de periyot bazlı (günlük) çekelim
        $workStatsQuery = ActivitySummary::where('category_type', 'work');
        $workStatsQuery = $this->applySummaryFilters($workStatsQuery, $filters);
        $workStats = $workStatsQuery->groupBy('date')
            ->select('date', DB::raw('SUM(total_duration_ms) as duration'))
            ->get()
            ->pluck('duration', 'date_string');

        $otherStatsQuery = ActivitySummary::where('category_type', 'other');
        $otherStatsQuery = $this->applySummaryFilters($otherStatsQuery, $filters);
        $otherStats = $otherStatsQuery->groupBy('date')
            ->select('date', DB::raw('SUM(total_duration_ms) as duration'))
            ->get()
            ->pluck('duration', 'date_string');
        
        return $stats->map(function($stat) use ($workStats, $otherStats) {
            $dateStr = $stat->date->toDateString();
            $durationSeconds = $stat->total_duration_ms ? $stat->total_duration_ms / 1000 : 0;
            
            $workDuration = $workStats[$dateStr] ?? 0;
            $otherDuration = $otherStats[$dateStr] ?? 0;
            
            return [
                'period' => $dateStr,
                'activity_count' => (int)$stat->activity_count,
                'total_duration_seconds' => round($durationSeconds, 0),
                'total_duration_hours' => round($durationSeconds / 3600, 2),
                'avg_duration_seconds' => $stat->activity_count > 0 ? round($durationSeconds / $stat->activity_count, 0) : 0,
                'work_count' => round($workDuration / (1000 * 60 * 60), 2),
                'other_count' => round($otherDuration / (1000 * 60 * 60), 2),
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
        $query = ActivitySummary::whereNull('category_id');
        $query = $this->applySummaryFilters($query, $filters);

        $stats = $query->select(
            'category_type',
            DB::raw('SUM(activity_count) as count'),
            DB::raw('SUM(total_duration_ms) as duration')
        )->groupBy('category_type')->get()->keyBy('category_type');

        $workCount = $stats['work']->count ?? 0;
        $workDuration = $stats['work']->duration ?? 0;

        $otherCount = $stats['other']->count ?? 0;
        $otherDuration = $stats['other']->duration ?? 0;
        
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
        // Eski metod, Activity modeli için hala kullanılabilir ama özetler tercih edilmeli
        if (!empty($filters['start_date'])) $query->where('activities.start_time_utc', '>=', $filters['start_date']);
        if (!empty($filters['end_date'])) $query->where('activities.start_time_utc', '<=', $filters['end_date']);
        if (!empty($filters['username'])) $query->where('activities.username', $filters['username']);
        if (!empty($filters['motherboard_uuid'])) $query->where('activities.motherboard_uuid', $filters['motherboard_uuid']);
        if (!empty($filters['activity_type'])) $query->where('activities.activity_type', $filters['activity_type']);
        
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

    protected function applySummaryFilters($query, array $filters)
    {
        $table = $query->getModel()->getTable();

        if (!empty($filters['start_date'])) $query->where($table . '.date', '>=', $filters['start_date']);
        if (!empty($filters['end_date'])) $query->where($table . '.date', '<=', $filters['end_date']);
        if (!empty($filters['username'])) $query->where($table . '.username', $filters['username']);
        if (!empty($filters['motherboard_uuid'])) $query->where($table . '.motherboard_uuid', $filters['motherboard_uuid']);
        
        if (!empty($filters['unit_id'])) {
            $query->whereExists(function ($q) use ($filters, $table) {
                $q->select(DB::raw(1))
                  ->from('computer_users')
                  ->whereColumn('computer_users.username', $table . '.username')
                  ->whereColumn('computer_users.motherboard_uuid', $table . '.motherboard_uuid')
                  ->where('computer_users.unit_id', $filters['unit_id']);
            });
        }
        
        return $query;
    }

    /**
     * Mesai saatleri analizi (Özet tablo üzerinden)
     */
    public function getWorkingHourStats(array $filters): array
    {
        $cacheKey = $this->getCacheKey('working_hours_v2', $filters);

        return Cache::remember($cacheKey, 300, function () use ($filters) {
            $query = ActivitySummary::whereNull('category_id');
            $query = $this->applySummaryFilters($query, $filters);
            
            // TR Mesai: 09:00-18:00 (UTC 06:00-15:00)
            // Özet tabloda hour kolonu UTC olarak tutuluyor
            $stats = (clone $query)->selectRaw("
                SUM(CASE WHEN hour >= 6 AND hour < 15 THEN total_duration_ms ELSE 0 END) as total_working_hours_duration,
                SUM(CASE WHEN hour < 6 OR hour >= 15 THEN total_duration_ms ELSE 0 END) as total_outside_hours_duration
            ")->first();

            $workQuery = ActivitySummary::where('category_type', 'work');
            $workQuery = $this->applySummaryFilters($workQuery, $filters);

            $workStats = $workQuery->selectRaw("
                SUM(CASE WHEN hour >= 6 AND hour < 15 THEN total_duration_ms ELSE 0 END) as work_working_hours_duration,
                SUM(CASE WHEN hour < 6 OR hour >= 15 THEN total_duration_ms ELSE 0 END) as work_outside_hours_duration
            ")->first();

            $divisor = 1000 * 60 * 60;

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

    public function getWeeklyRhythm(array $filters): array
    {
        $cacheKey = $this->getCacheKey('weekly_rhythm_v2', $filters);

        return Cache::remember($cacheKey, 300, function () use ($filters) {
            $query = ActivitySummary::where('category_type', 'work');
            $query = $this->applySummaryFilters($query, $filters);

            // MySQL: DAYOFWEEK() 1=Sun, 2=Mon...
            $results = $query->selectRaw("
                DAYOFWEEK(date) as day_num,
                SUM(total_duration_ms) as total_duration,
                COUNT(DISTINCT date) as unique_days
            ")
            ->groupBy('day_num')
            ->get();

            $days = [2 => 'Pazartesi', 3 => 'Salı', 4 => 'Çarşamba', 5 => 'Perşembe', 6 => 'Cuma', 7 => 'Cumartesi', 1 => 'Pazar'];
            $output = [];
            foreach ($days as $num => $name) {
                $record = $results->firstWhere('day_num', $num);
                $totalHours = $record ? ($record->total_duration / (1000 * 60 * 60)) : 0;
                $uniqueDays = $record ? $record->unique_days : 1;
                $avgHours = $uniqueDays > 0 ? $totalHours / $uniqueDays : 0;

                $output[] = [
                    'day' => $name,
                    'avg_hours' => round($avgHours, 2),
                    'total_hours' => round($totalHours, 2)
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
        $cacheKey = $this->getCacheKey('top_keywords_v3', $filters + ['limit' => $limit]);

        return Cache::remember($cacheKey, 300, function () use ($filters, $limit) {
            $query = KeywordSummary::query();
            $query = $this->applySummaryFilters($query, $filters);

            return $query->select(
                    'keyword',
                    DB::raw('SUM(match_count) as usage_count'),
                    DB::raw('SUM(total_duration_ms) as total_duration')
                )
                ->groupBy('keyword')
                ->orderBy('usage_count', 'desc')
                ->limit($limit)
                ->get()
                ->map(function($item) {
                    return [
                        'keyword' => $item->keyword,
                        'count' => (int)$item->usage_count,
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
        $cacheKey = $this->getCacheKey('top_processes_v3', $filters + ['limit' => $limit]);

        return Cache::remember($cacheKey, 300, function () use ($filters, $limit) {
            $query = ProcessSummary::query();
            $query = $this->applySummaryFilters($query, $filters);

            return $query->select(
                    'process_name',
                    DB::raw('SUM(total_duration_ms) as total_duration')
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

