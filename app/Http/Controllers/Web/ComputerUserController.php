<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ComputerUser;
use App\Models\Activity;
use App\Models\Unit;
use App\Services\StatisticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ComputerUserController extends Controller
{
    protected $statisticsService;

    public function __construct(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }

    /**
     * Kullanıcı listesi
     */
    public function index()
    {
        // Cache Key: computer_users_list
        // Duration: 10 minutes
        $users = Cache::remember('computer_users_list', 200, function () {
            // Veritabanındaki unique kullanıcıları bul ve computer_users tablosuna ekle (Sync - Optimized)
            // Bu işlem artık her requestte değil, cache süresi dolduğunda bir kere çalışacak.
            // Daha ideali bunu bir job'a taşımaktır.
            
            // 1. Önce özet tablodan (activity_summaries) yeni kullanıcıları bul
            $newUsersFromSummaries = DB::table('activity_summaries')
                ->select('username', 'motherboard_uuid')
                ->distinct()
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('computer_users')
                        ->whereRaw('computer_users.username = activity_summaries.username')
                        ->whereRaw('computer_users.motherboard_uuid = activity_summaries.motherboard_uuid');
                });

            // 2. Sonra ham aktivitelerden (activities) son 24 saatteki yeni kullanıcıları bul (Hızlı olması için limitli zaman dilimi)
            $newUsersFromActivities = DB::table('activities')
                ->select('username', 'motherboard_uuid')
                ->distinct()
                ->where('start_time_utc', '>', now()->subHours(24))
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('computer_users')
                        ->whereRaw('computer_users.username = activities.username')
                        ->whereRaw('computer_users.motherboard_uuid = activities.motherboard_uuid');
                });

            // İkisini birleştir ve işle
            $allNewUsers = $newUsersFromSummaries->get()->merge($newUsersFromActivities->get())->unique(function ($item) {
                return $item->username . '|' . $item->motherboard_uuid;
            });
    
            foreach ($allNewUsers as $new) {
                // Hostname'i bulmaya çalış (system_hardware tablosundan en güncelini al)
                $hostname = DB::table('system_hardware')
                    ->where('motherboard_uuid', $new->motherboard_uuid)
                    ->orderBy('collected_at', 'desc')
                    ->value('hostname');

                ComputerUser::create([
                    'username' => $new->username,
                    'motherboard_uuid' => $new->motherboard_uuid,
                    'hostname' => $hostname,
                    'name' => null
                ]);
            }

            // Mevcut kullanıcılarda hostname eksikse güncellemeye çalış
            ComputerUser::whereNull('hostname')->each(function($user) {
                $hostname = DB::table('system_hardware')
                    ->where('motherboard_uuid', $user->motherboard_uuid)
                    ->orderBy('collected_at', 'desc')
                    ->value('hostname');
                
                if ($hostname) {
                    $user->update(['hostname' => $hostname]);
                }
            });
    
            $stats = DB::table('activity_summaries')
                ->select(
                    'username',
                    'motherboard_uuid',
                    DB::raw('SUM(activity_count) as activity_count'),
                    DB::raw('SUM(total_duration_ms) as total_duration_ms')
                )
                ->groupBy('username', 'motherboard_uuid')
                ->get()
                ->keyBy(fn($i) => $i->username . '|' . $i->motherboard_uuid);

            return ComputerUser::with('unit')->get()->map(function($user) use ($stats) {
                $userStats = $stats->get($user->username . '|' . $user->motherboard_uuid);
                $user->activities_count = $userStats->activity_count ?? 0;
                $user->activities_sum_duration_ms = $userStats->total_duration_ms ?? 0;
                return $user;
            });
        });

        return view('performance.computer_users.index', compact('users'));
    }

    /**
     * Kullanıcı düzenleme formu
     */
    public function edit($id)
    {
        $user = ComputerUser::findOrFail($id);
        $units = Unit::orderBy('name')->get();
        return view('performance.computer_users.edit', compact('user', 'units'));
    }

    /**
     * Kullanıcı güncelleme
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'unit_id' => 'nullable|exists:units,id',
        ]);

        $user = ComputerUser::findOrFail($id);
        $user->update([
            'name' => $request->name,
            'unit_id' => $request->unit_id,
        ]);

        return redirect()->route('computer-users.index')
            ->with('success', 'Kullanıcı bilgileri güncellendi.');
    }

    /**
     * Kullanıcı istatistikleri
     */
    public function show($id, Request $request)
    {
        $user = ComputerUser::findOrFail($id);
        
        // Filtreleri hazırla
        $filters = $request->all();
        $filters['username'] = $user->username;
        $filters['motherboard_uuid'] = $user->motherboard_uuid;
        
        $topCategories = $this->statisticsService->getCategoryStatistics($filters, 5);
        $topCategories = collect($topCategories['categories']);
        
        $workOtherRatio = $this->statisticsService->getWorkOtherRatio($filters);
        
        // Son aktiviteler
        $recentActivities = Activity::where('username', $user->username)
            ->where('motherboard_uuid', $user->motherboard_uuid)
            ->with('categories')
            ->orderBy('start_time_utc', 'desc')
            ->limit(20)
            ->get();
            
        // Yeni İstatistikler
        $workingHourStats = $this->statisticsService->getWorkingHourStats($filters);
        $weeklyRhythm = $this->statisticsService->getWeeklyRhythm($filters);
        $topKeywords = $this->statisticsService->getTopKeywords($filters, 10);
        $topProcesses = $this->statisticsService->getTopProcesses($filters, 10);

        return view('performance.computer_users.show', compact(
            'user', 
            'topCategories', 
            'workOtherRatio', 
            'recentActivities',
            'filters',
            'workingHourStats',
            'weeklyRhythm',
            'topKeywords',
            'topProcesses'
        ));
    }
}
