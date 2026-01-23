<?php

namespace App\Http\Controllers;
use App\Models\Entity;
use App\Models\GapAnalysis;
use App\Models\Audits\Audit;
use App\Models\SurveyResult;
use Illuminate\Http\Request;
use App\Services\CategoryService;
use App\Services\StatisticsService;
use App\Models\Activity;
use App\Models\Category;
use App\Models\CategoryKeyword;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\ActivitySummary;

class DashboardController extends Controller
{
    protected $categoryService;
    protected $statisticsService;

    public function __construct(CategoryService $categoryService, StatisticsService $statisticsService)
    {
        $this->categoryService = $categoryService;
        $this->statisticsService = $statisticsService;
    }

    public function index()
    {
        $thirtyDaysAgo = now()->subDays(30);
        $yesterdayStr = now()->subDay()->toDateString();

        // 1. Bugünün Verisi (Hızlı Hesaplama + 5 Dakika Cache)
        $todayStatsData = $this->getTodaySummaries();

        // 2. Geçmiş Özetler (Summary Tablosu + 1 Saat Cache)
        $pastStats = Cache::remember('dashboard_past_stats_30d_v4', 3600, function () use ($thirtyDaysAgo, $yesterdayStr) {
            return ActivitySummary::where('date', '>=', $thirtyDaysAgo->toDateString())
                ->where('date', '<=', $yesterdayStr)
                ->whereNull('category_id')
                ->get();
        });

        // Üst Kart Hesaplamaları
        $totalWorkMs = $pastStats->where('category_type', 'work')->sum('total_duration_ms') + $todayStatsData['work_ms'];
        $totalOtherMs = $pastStats->where('category_type', 'other')->sum('total_duration_ms') + $todayStatsData['other_ms'];
        $totalUntaggedMs = $pastStats->where('category_type', 'untagged')->sum('total_duration_ms') + $todayStatsData['untagged_ms'];
        $totalDurationMs = $totalWorkMs + $totalOtherMs + $totalUntaggedMs;
        
        $workHours = round($totalWorkMs / (1000 * 60 * 60), 1);
        $otherHours = round($totalOtherMs / (1000 * 60 * 60), 1);
        $untaggedHours = round($totalUntaggedMs / (1000 * 60 * 60), 1);
        $totalHours = round($totalDurationMs / (1000 * 60 * 60), 1);
        $taggingRate = $totalDurationMs > 0 ? round((($totalWorkMs + $totalOtherMs) / $totalDurationMs) * 100, 1) : 0;

        // 3. Grafikler için Trend Verileri
        $trendStats = Cache::remember('dashboard_trend_7days_v4', 600, function () use ($todayStatsData) {
            $last7Days = []; $last7DaysWork = []; $last7DaysOther = []; $last7DaysUntagged = [];
            $summaries = ActivitySummary::where('date', '>=', now()->subDays(6)->toDateString())
                ->where('date', '<', today()->toDateString())
                ->whereNull('category_id')->get();

            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                if ($date === today()->toDateString()) {
                    $w = $todayStatsData['work_ms']; $o = $todayStatsData['other_ms']; $u = $todayStatsData['untagged_ms'];
                } else {
                    $daily = $summaries->where('date', $date);
                    $w = $daily->where('category_type', 'work')->sum('total_duration_ms');
                    $o = $daily->where('category_type', 'other')->sum('total_duration_ms');
                    $u = $daily->where('category_type', 'untagged')->sum('total_duration_ms');
                }
                $total = $w + $o + $u;
                $last7Days[] = ['date' => $date, 'count' => round($total / (1000 * 60 * 60), 2)];
                $last7DaysWork[] = round($w / (1000 * 60 * 60), 2);
                $last7DaysOther[] = round($o / (1000 * 60 * 60), 2);
                $last7DaysUntagged[] = round($u / (1000 * 60 * 60), 2);
            }
            return compact('last7Days', 'last7DaysWork', 'last7DaysOther', 'last7DaysUntagged');
        });
        extract($trendStats);

        // 4. İş Trendi (30 Gün)
        $last30Days = Cache::remember('dashboard_work_trend_30days_v4', 600, function () use ($todayStatsData) {
            $data = [];
            $summaries = ActivitySummary::where('date', '>=', now()->subDays(29)->toDateString())
                ->where('date', '<', today()->toDateString())
                ->whereNull('category_id')->where('category_type', 'work')->get();

            for ($i = 29; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $val = ($date === today()->toDateString()) ? $todayStatsData['work_ms'] : $summaries->where('date', $date)->sum('total_duration_ms');
                $data[] = ['date' => $date, 'count' => round($val / (1000 * 60 * 60), 2)];
            }
            return $data;
        });

        // 5. Saatlik Dağılım
        $hourlyDistribution = Cache::remember('dashboard_hourly_distribution_v4', 600, function () use ($todayStatsData) {
            $data = [];
            $summaries = ActivitySummary::where('date', '>=', now()->subDays(30)->toDateString())
                ->where('date', '<', today()->toDateString())
                ->whereNull('category_id')->where('category_type', 'work')->get();
            
            for ($hour = 0; $hour < 24; $hour++) {
                $pastTotal = $summaries->where('hour', $hour)->sum('total_duration_ms');
                $avg = ($pastTotal + ($todayStatsData['hourly_work'][$hour] ?? 0)) / 30;
                $data[] = round($avg / (1000 * 60 * 60), 2);
            }
            return $data;
        });

        // 6. Top Charts (Blade Uyumlu)
        $topCharts = Cache::remember('dashboard_top_charts_30d_v4', 3600, function () use ($thirtyDaysAgo) {
            $cats = ActivitySummary::whereNotNull('category_id')
                ->where('date', '>=', $thirtyDaysAgo->toDateString())
                ->with('category')
                ->select('category_id', DB::raw('SUM(total_duration_ms) as total_duration_ms'), DB::raw('SUM(activity_count) as activity_count'))
                ->groupBy('category_id')
                ->orderByDesc('total_duration_ms')
                ->limit(8)->get()
                ->map(fn($s) => (object)[
                    'id' => $s->category_id,
                    'name' => $s->category->name ?? 'Unknown',
                    'type' => $s->category->type ?? 'other',
                    'total_duration_hours' => round($s->total_duration_ms / (1000 * 60 * 60), 2),
                    'activity_count' => $s->activity_count,
                ]);

            $keys = DB::table('activity_categories')
                ->join('categories', 'activity_categories.category_id', '=', 'categories.id')
                ->select('activity_categories.matched_keyword as keyword', 'categories.name as category_name', DB::raw('COUNT(*) as match_count'))
                ->whereNotNull('activity_categories.matched_keyword')->where('activity_categories.matched_keyword', '!=', '')
                ->where('activity_categories.tagged_at', '>=', $thirtyDaysAgo)
                ->groupBy('activity_categories.matched_keyword', 'categories.name')
                ->orderByDesc('match_count')->limit(10)->get()
                ->map(fn($k) => (object)[
                    'keyword' => $k->keyword,
                    'category' => (object)['name' => $k->category_name],
                    'match_type' => 'Otomatik',
                    'match_count' => $k->match_count
                ]);

            $procs = Activity::select('process_name')
                ->selectRaw('COUNT(*) as activity_count, SUM(duration_ms) as total_duration')
                ->groupBy('process_name')->orderByDesc('total_duration')->limit(10)->get()
                ->map(fn($i) => (object)[
                    'process_name' => $i->process_name,
                    'activity_count' => $i->activity_count,
                    'total_hours' => round($i->total_duration / (1000 * 60 * 60), 2)
                ]);

            return ['categories' => $cats, 'keywords' => $keys, 'processes' => $procs];
        });

        return view('dashboard', [
            'totalCategories' => Category::count(),
            'totalKeywords' => CategoryKeyword::count(),
            'totalActivities' => ActivitySummary::sum('activity_count') + $todayStatsData['activity_count'],
            'totalHours' => $totalHours, 'workHours' => $workHours, 'otherHours' => $otherHours, 'untaggedHours' => $untaggedHours,
            'taggingRate' => $taggingRate, 'last7Days' => $last7Days, 'last7DaysWork' => $last7DaysWork, 'last7DaysOther' => $last7DaysOther, 'last7DaysUntagged' => $last7DaysUntagged,
            'last30Days' => $last30Days, 'hourlyDistribution' => $hourlyDistribution,
            'topCategories' => $topCharts['categories'], 'topKeywords' => $topCharts['keywords'], 'topProcesses' => $topCharts['processes'],
            'todayStats' => [
                'total' => round(($todayStatsData['work_ms'] + $todayStatsData['other_ms'] + $todayStatsData['untagged_ms']) / (1000 * 60 * 60), 1),
                'work' => round($todayStatsData['work_ms'] / (1000 * 60 * 60), 1),
                'activities' => $todayStatsData['activity_count'],
            ],
            'workOtherRatio30' => ['work' => ['duration_hours' => $workHours], 'other' => ['duration_hours' => $otherHours], 'untagged' => ['duration_hours' => $untaggedHours]]
        ]);
    }

    private function getTodaySummaries()
    {
        return Cache::remember('dashboard_today_summaries_v4', 300, function () {
            $today = today();
            $workCategoryIds = Category::where('type', 'work')->pluck('id')->toArray();
            $otherCategoryIds = Category::where('type', 'other')->pluck('id')->toArray();

            $work_ms = Activity::where('start_time_utc', '>=', $today)
                ->whereHas('categories', fn($q) => $q->whereIn('categories.id', $workCategoryIds))->sum('duration_ms');

            $other_ms = Activity::where('start_time_utc', '>=', $today)
                ->whereHas('categories', fn($q) => $q->whereIn('categories.id', $otherCategoryIds))->sum('duration_ms');

            $untagged_ms = Activity::where('start_time_utc', '>=', $today)->untagged()->sum('duration_ms');
            $activity_count = Activity::where('start_time_utc', '>=', $today)->count();

            $hourlyRaw = Activity::where('start_time_utc', '>=', $today)
                ->whereHas('categories', fn($q) => $q->whereIn('categories.id', $workCategoryIds))
                ->select(DB::raw('HOUR(start_time_utc) as hour, SUM(duration_ms) as total_ms'))
                ->groupBy('hour')->pluck('total_ms', 'hour')->toArray();

            $hourly_work = [];
            for ($h = 0; $h < 24; $h++) { $hourly_work[$h] = $hourlyRaw[$h] ?? 0; }

            return [
                'work_ms' => (int)$work_ms, 'other_ms' => (int)$other_ms, 'untagged_ms' => (int)$untagged_ms,
                'activity_count' => (int)$activity_count, 'hourly_work' => $hourly_work
            ];
        });
    }
}
