<?php

namespace App\Http\Controllers;

use App\Models\SystemHardware;
use App\Models\InstalledApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ComputerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get the latest hardware info for each unique motherboard_uuid
        $computers = SystemHardware::select('system_hardware.*')
            ->join(DB::raw('(SELECT motherboard_uuid, MAX(collected_at) as max_date FROM system_hardware GROUP BY motherboard_uuid) as latest'), function($join) {
                $join->on('system_hardware.motherboard_uuid', '=', 'latest.motherboard_uuid')
                     ->on('system_hardware.collected_at', '=', 'latest.max_date');
            })
            ->orderBy('collected_at', 'desc')
            ->orderBy('collected_at', 'desc')
            ->paginate(10);

        return view('performance.computers.index', compact('computers'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $uuid)
    {
        // Get the latest hardware info for this UUID
        $computer = SystemHardware::where('motherboard_uuid', $uuid)
            ->orderBy('collected_at', 'desc')
            ->firstOrFail();

        // Get installed apps for this UUID
        $latestAppScan = InstalledApp::where('motherboard_uuid', $uuid)
            ->max('collected_at');

        $apps = [];
        if ($latestAppScan) {
            $apps = InstalledApp::where('motherboard_uuid', $uuid)
                ->where('collected_at', $latestAppScan)
                ->when($request->input('search'), function ($query, $search) {
                    return $query->where('app_name', 'like', "%{$search}%");
                })
                ->orderBy('app_name')
                ->paginate(10)
                ->withQueryString();
        } else {
             $apps = InstalledApp::where('motherboard_uuid', $uuid) // Fallback empty paginator
                ->whereRaw('1 = 0')
                ->paginate(10);
        }

        return view('performance.computers.show', compact('computer', 'apps'));
    }
}
