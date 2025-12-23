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
        // 1. İstatistik Kartları (Son 30 Gün)
        $thirtyDaysAgo = now()->subDays(30);

        $totalCategories = Category::count();
        $totalKeywords = CategoryKeyword::count();
        $totalActivities = Activity::count();
        
        $totalDuration = Activity::where('start_time_utc', '>=', $thirtyDaysAgo)->sum('duration_ms');
        
        // İş ve Diğer Ayrımı
        $workCategories = Category::where('type', 'work')->pluck('id');
        $otherCategories = Category::where('type', 'other')->pluck('id');

        $workDuration = Activity::where('start_time_utc', '>=', $thirtyDaysAgo)
            ->whereHas('categories', function($q) use ($workCategories) {
                $q->whereIn('categories.id', $workCategories);
            })->sum('duration_ms');

        $otherDuration = Activity::where('start_time_utc', '>=', $thirtyDaysAgo)
            ->whereHas('categories', function($q) use ($otherCategories) {
                $q->whereIn('categories.id', $otherCategories);
            })->sum('duration_ms');
        
        $untaggedDuration = Activity::untagged()
            ->where('start_time_utc', '>=', $thirtyDaysAgo)
            ->sum('duration_ms');
        
        $workHours = round($workDuration / (1000 * 60 * 60), 2);
        $otherHours = round($otherDuration / (1000 * 60 * 60), 2);
        $untaggedHours = round($untaggedDuration / (1000 * 60 * 60), 2);
        $totalHours = round($totalDuration / (1000 * 60 * 60), 2);
        
        $taggingRate = $totalDuration > 0 ? round((($workDuration + $otherDuration) / $totalDuration) * 100, 2) : 0;

        // 2. Son 7 Günlük Çoklu Trend (Toplam, İş, Diğer, Tanımsız)
        $last7Days = [];
        $last7DaysWork = [];
        $last7DaysOther = [];
        $last7DaysUntagged = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            
            $totalDaily = Activity::whereDate('start_time_utc', $date)->sum('duration_ms');
            
            // Work (İş) aktiviteleri
            $workDaily = Activity::whereDate('start_time_utc', $date)
                ->whereHas('categories', function($q) use ($workCategories) {
                    $q->whereIn('categories.id', $workCategories);
                })->sum('duration_ms');
            
            // Other (Diğer) aktiviteleri
            $otherDaily = Activity::whereDate('start_time_utc', $date)
                ->whereHas('categories', function($q) use ($otherCategories) {
                    $q->whereIn('categories.id', $otherCategories);
                })->sum('duration_ms');

            $untaggedDaily = Activity::untagged()->whereDate('start_time_utc', $date)->sum('duration_ms');
            
            $last7Days[] = [
                'date' => $date,
                'count' => round($totalDaily / (1000 * 60 * 60), 2),
            ];
            $last7DaysWork[] = round($workDaily / (1000 * 60 * 60), 2);
            $last7DaysOther[] = round($otherDaily / (1000 * 60 * 60), 2);
            $last7DaysUntagged[] = round($untaggedDaily / (1000 * 60 * 60), 2);
        }

        // 3. Son 30 Günlük İş Performans Trendi
        $last30Days = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dailyWorkDuration = Activity::whereDate('start_time_utc', $date)
                ->whereHas('categories', function($query) use ($workCategories) {
                    $query->whereIn('categories.id', $workCategories);
                })
                ->sum('duration_ms');
                
            $last30Days[] = [
                'date' => $date,
                'count' => round($dailyWorkDuration / (1000 * 60 * 60), 2),
            ];
        }

        // 4. Saatlik Dağılım (24 saat - 30 günlük ortalama - Sadece İş)
        $hourlyDistribution = [];
        $thirtyDaysAgo = now()->subDays(30);
        
        for ($hour = 0; $hour < 24; $hour++) {
            // Son 30 gündeki o saatteki toplam aktivite süresi
            $totalHourlyDuration = Activity::where('start_time_utc', '>=', $thirtyDaysAgo)
                ->whereRaw('HOUR(start_time_utc) = ?', [$hour])
                ->whereHas('categories', function($query) use ($workCategories) {
                    $query->whereIn('categories.id', $workCategories);
                })
                ->sum('duration_ms');
                
            // 30 güne bölerek ortalama alıyoruz
            $hourlyAvg = $totalHourlyDuration / 30;
            $hourlyDistribution[] = round($hourlyAvg / (1000 * 60 * 60), 2);
        }

        // 5. İş/Diğer Dağılımı (Son 30 Gün)
        $workDuration30 = Activity::where('start_time_utc', '>=', $thirtyDaysAgo)
            ->whereHas('categories', function($q) use ($workCategories) {
                $q->whereIn('categories.id', $workCategories);
            })->sum('duration_ms');
            
        $otherDuration30 = Activity::where('start_time_utc', '>=', $thirtyDaysAgo)
            ->whereHas('categories', function($q) use ($otherCategories) {
                $q->whereIn('categories.id', $otherCategories);
            })->sum('duration_ms');

        $untaggedDuration30 = Activity::untagged()
            ->where('start_time_utc', '>=', $thirtyDaysAgo)
            ->sum('duration_ms');

        $workOtherRatio30 = [
            'work' => ['duration_hours' => round($workDuration30 / (1000 * 60 * 60), 2)],
            'other' => ['duration_hours' => round($otherDuration30 / (1000 * 60 * 60), 2)],
            'untagged' => ['duration_hours' => round($untaggedDuration30 / (1000 * 60 * 60), 2)],
        ];

        // 6. Top Charts (Categories, Keywords, Processes)
        $topCategories = $this->statisticsService->getCategoryStatistics([], 8);
        $topCategories = collect($topCategories['categories']);

        $topKeywords = CategoryKeyword::with('category')
            ->active()
            ->get()
            ->map(function($keyword) {
                $matchCount = Activity::where(function($query) use ($keyword) {
                    $query->where('process_name', 'LIKE', '%' . $keyword->keyword . '%')
                          ->orWhere('title', 'LIKE', '%' . $keyword->keyword . '%');
                })->count();
                $keyword->match_count = $matchCount;
                return $keyword;
            })
            ->sortByDesc('match_count')
            ->take(10);

        $topProcesses = Activity::select('process_name')
            ->selectRaw('COUNT(*) as activity_count')
            ->selectRaw('SUM(duration_ms) as total_duration')
            ->groupBy('process_name')
            ->orderByDesc('total_duration')
            ->limit(10)
            ->get()
            ->map(function($item) {
                $item->total_hours = round($item->total_duration / (1000 * 60 * 60), 2);
                return $item;
            });

        // 7. Bugün Özeti
        $todayStats = [
            'total' => round(Activity::whereDate('start_time_utc', today())->sum('duration_ms') / (1000 * 60 * 60), 2),
            'work' => round(Activity::whereDate('start_time_utc', today())
                ->whereHas('categories', fn($q) => $q->whereIn('categories.id', $workCategories))
                ->sum('duration_ms') / (1000 * 60 * 60), 2),
            'activities' => Activity::whereDate('start_time_utc', today())->count(),
        ];

        return view('dashboard', compact(
            'totalCategories',
            'totalKeywords',
            'totalActivities',
            'totalHours',
            'workHours',
            'otherHours',
            'untaggedHours',
            'taggingRate',
            'last7Days',
            'last7DaysWork',
            'last7DaysOther',
            'last7DaysUntagged',
            'last30Days',
            'hourlyDistribution',
            'workOtherRatio30',
            'topCategories',
            'topKeywords',
            'topProcesses',
            'todayStats'
        ));
    }
}
