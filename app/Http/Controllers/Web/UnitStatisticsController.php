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
        // Cache key: units_with_stats
        // Cache Duration: 30 minutes (1800 seconds)
        $units = Cache::remember('units_with_stats', 200, function () {
            // Birim listesi ve özet istatistikleri
            $units = Unit::withCount('computerUsers') 
                ->orderBy('name')
                ->get();
    
            // Her birim için temel istatistikleri hesapla
            return $units->map(function ($unit) {
                // Bu birime ait kullanıcıların aktiviteleri
                $activityStats = Activity::whereHas('computerUser', function ($q) use ($unit) {
                    $q->where('unit_id', $unit->id);
                })
                ->selectRaw('COUNT(*) as count, SUM(duration_ms) as total_duration')
                ->first();
    
                $unit->activity_count = $activityStats->count ?? 0;
                $unit->total_duration_hours = $activityStats->total_duration ? round($activityStats->total_duration / (1000 * 60 * 60), 1) : 0;
                
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

        $computerUsers = \App\Models\ComputerUser::where('unit_id', $unit->id)
            ->withCount('activities')
            ->get()
            ->map(function($user) {
                $user->total_duration_hours = $user->activities()->sum('duration_ms') / (1000 * 60 * 60);
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
