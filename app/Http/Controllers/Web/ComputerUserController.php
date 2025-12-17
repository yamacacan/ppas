<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ComputerUser;
use App\Models\Activity;
use App\Models\Unit;
use App\Services\StatisticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        // Veritabanındaki unique kullanıcıları bul ve computer_users tablosuna ekle (Sync)
        // Bu işlem normalde bir Job ile yapılmalı ama şimdilik burada yapalım
        $uniqueUsers = Activity::select('username', 'motherboard_uuid')
            ->distinct()
            ->get();

        foreach ($uniqueUsers as $user) {
            ComputerUser::firstOrCreate(
                [
                    'username' => $user->username,
                    'motherboard_uuid' => $user->motherboard_uuid
                ],
                ['name' => null]
            );
        }

        $users = ComputerUser::with('unit')
            ->withCount('activities')
            ->withSum('activities', 'duration_ms')
            ->get();

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
