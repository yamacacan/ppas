<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\Activity;
use App\Services\StatisticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UnitStatisticsController extends Controller
{
    protected $statisticsService;

    public function __construct(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }

    public function index()
    {
        // Cache key: units_with_stats_v2
        $units = Cache::remember('units_with_stats_v2', 300, function () {
            // Tüm birimleri ve kullanıcı sayılarını al
            $units = Unit::withCount('computerUsers')
                ->orderBy('name')
                ->get();

            // Birim bazlı süre ve aktivite sayılarını toplu halde çek (JOIN ile)
            // Sadece tekil eşleşme (username + uuid) yapıyoruz
            $stats = \DB::table('activity_summaries')
                ->join('computer_users', function($join) {
                    $join->on('activity_summaries.username', '=', 'computer_users.username')
                         ->on('activity_summaries.motherboard_uuid', '=', 'computer_users.motherboard_uuid');
                })
                ->select(
                    'computer_users.unit_id',
                    \DB::raw('SUM(activity_summaries.activity_count) as activity_count'),
                    \DB::raw('SUM(activity_summaries.total_duration_ms) as total_duration')
                )
                ->whereNotNull('computer_users.unit_id')
                ->groupBy('computer_users.unit_id')
                ->get()
                ->keyBy('unit_id');

            // İstatistikleri birimlerle eşleştir
            return $units->map(function ($unit) use ($stats) {
                $unitStats = $stats->get($unit->id);
                
                $unit->activity_count = $unitStats->activity_count ?? 0;
                $unit->total_duration_hours = $unitStats ? round($unitStats->total_duration / (1000 * 60 * 60), 1) : 0;
                
                return $unit;
            });
        });

        return view('performance.units.index', compact('units'));
    }

    public function show($id, Request $request)
    {
        $unit = Unit::withCount('computerUsers')->findOrFail($id);
        
        // Filtreleri hazırla
        $filters = $request->all();
        $filters['unit_id'] = $unit->id; // StatisticsService bu filtreyi kullanacak
        
        // İstatistikler
        $topCategories = $this->statisticsService->getCategoryStatistics($filters, 5);
        $topCategories = collect($topCategories['categories']);
        
        $workOtherRatio = $this->statisticsService->getWorkOtherRatio($filters);
        
        $workingHourStats = $this->statisticsService->getWorkingHourStats($filters);
        $weeklyRhythm = $this->statisticsService->getWeeklyRhythm($filters);
        $topKeywords = collect($this->statisticsService->getTopKeywords($filters, 10))->map(fn($item) => (object) $item);
        $topProcesses = collect($this->statisticsService->getTopProcesses($filters, 10))->map(fn($item) => (object) $item);

        $userStats = \DB::table('activity_summaries')
            ->select(
                'username',
                'motherboard_uuid',
                \DB::raw('SUM(activity_count) as activity_count'),
                \DB::raw('SUM(total_duration_ms) as total_duration_ms')
            )
            ->whereIn('username', $unit->computerUsers->pluck('username'))
            ->groupBy('username', 'motherboard_uuid')
            ->get()
            ->keyBy(function($item) {
                return $item->username . '|' . $item->motherboard_uuid;
            });

        $computerUsers = $unit->computerUsers->map(function($user) use ($userStats) {
            $stats = $userStats->get($user->username . '|' . $user->motherboard_uuid);
            $user->activities_count = $stats->activity_count ?? 0;
            $user->total_duration_ms = $stats->total_duration_ms ?? 0;
            $user->total_duration_hours = ($user->total_duration_ms ?? 0) / (1000 * 60 * 60);
            return $user;
        });

        return view('performance.units.show', compact(
            'unit', 
            'topCategories', 
            'workOtherRatio', 
            'workingHourStats',
            'weeklyRhythm',
            'topKeywords',
            'topProcesses',
            'filters',
            'computerUsers'
        ));
    }
}
