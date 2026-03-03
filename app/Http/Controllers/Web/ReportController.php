<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\GeneratedReport;
use App\Models\ComputerUser;
use App\Models\Unit;
use App\Jobs\GenerateReportJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function index()
    {
        $reports = auth()->user()->generatedReports()->latest()->paginate(20);
        $computerUsers = ComputerUser::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();
        
        return view('reports.index', compact('reports', 'computerUsers', 'units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:activities,user_detail,unit_performance,app_usage',
            'format' => 'required|in:pdf,xlsx',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'username' => 'nullable|string',
            'unit_id' => 'nullable|exists:units,id',
        ]);

        $report = auth()->user()->generatedReports()->create([
            'title' => $request->title,
            'type' => $request->type,
            'format' => $request->format,
            'status' => 'pending',
            'progress' => 0,
            'filters' => $request->only(['start_date', 'end_date', 'username', 'unit_id']),
        ]);

        GenerateReportJob::dispatch($report);

        return back()->with('success', 'Rapor talebiniz alındı. İlerlemeyi buradan takip edebilirsiniz.');
    }

    public function progress()
    {
        $reports = auth()->user()->generatedReports()
            ->whereIn('status', ['pending', 'processing'])
            ->get(['id', 'status', 'progress']);

        return response()->json($reports);
    }

    public function show($id)
    {
        $report = auth()->user()->generatedReports()->findOrFail($id);
        
        if ($report->status !== 'completed' || !$report->file_path) {
            return back()->with('error', 'Bu rapor henüz hazır değil.');
        }

        $ext = $report->format === 'excel' ? 'xlsx' : $report->format;
        return Storage::disk('public')->download($report->file_path, $report->title . '.' . $ext);
    }

    public function destroy($id)
    {
        $report = auth()->user()->generatedReports()->findOrFail($id);
        
        if ($report->file_path) {
            Storage::disk('public')->delete($report->file_path);
        }

        $report->delete();

        return back()->with('success', 'Rapor başarıyla silindi.');
    }
}
