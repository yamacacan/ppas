<?php

namespace App\Services;

use App\Models\GeneratedReport;
use App\Models\Activity;
use App\Models\ActivitySummary;
use App\Models\ComputerUser;
use App\Models\Unit;
use App\Models\ProcessSummary;
use App\Notifications\ReportReadyNotification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Mpdf\Mpdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ActivitiesExport;

class ReportService
{
    public function generate(GeneratedReport $report)
    {
        $report->update(['status' => 'processing', 'progress' => 5]);

        try {
            $data = $this->collectData($report);
            $report->update(['progress' => 40]);

            $fileName = $this->getFileName($report);
            $filePath = 'reports/' . $fileName;

            if ($report->format === 'pdf') {
                $this->generatePdf($report, $data, $filePath);
            } else {
                $this->generateExcel($report, $data, $filePath);
            }

            $report->update(['progress' => 90]);

            $report->update([
                'status' => 'completed',
                'progress' => 100,
                'file_path' => $filePath,
                'completed_at' => now(),
            ]);

            $report->user->notify(new ReportReadyNotification($report));

        } catch (\Exception $e) {
            $report->update([
                'status' => 'failed',
                'progress' => 0,
                'error_message' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    protected function collectData(GeneratedReport $report)
    {
        $filters = $report->filters;
        $type = $report->type;

        switch ($type) {
            case 'user_detail':
                return $this->collectUserDetailData($filters);
            case 'unit_performance':
                return $this->collectUnitPerformanceData($filters);
            case 'app_usage':
                return $this->collectAppUsageData($filters);
            case 'activities':
            default:
                return $this->collectGeneralActivitiesData($filters);
        }
    }

    protected function collectGeneralActivitiesData($filters)
    {
        $query = ActivitySummary::query();
        if (!empty($filters['start_date'])) $query->where('date', '>=', $filters['start_date']);
        if (!empty($filters['end_date'])) $query->where('date', '<=', $filters['end_date']);
        
        $summaries = $query->get();
        $dailySummaries = $summaries->groupBy(function($item) {
            return $item->date->format('Y-m-d');
        })->map(function ($dayGroup) {
            return [
                'date' => $dayGroup->first()->date,
                'work_ms' => $dayGroup->where('category_type', 'work')->sum('total_duration_ms'),
                'other_ms' => $dayGroup->where('category_type', 'other')->sum('total_duration_ms'),
                'untagged_ms' => $dayGroup->where('category_type', 'untagged')->sum('total_duration_ms'),
                'total_ms' => $dayGroup->sum('total_duration_ms'),
                'activity_count' => $dayGroup->sum('activity_count'),
            ];
        })->sortByDesc('date');

        return [
            'template' => 'reports.templates.activity_report',
            'summaries' => $summaries,
            'daily_summaries' => $dailySummaries,
            'generated_at' => now(),
        ];
    }

    protected function collectUserDetailData($filters)
    {
        $username = $filters['username'] ?? null;
        $query = ActivitySummary::query();
        if ($username) $query->where('username', $username);
        if (!empty($filters['start_date'])) $query->where('date', '>=', $filters['start_date']);
        if (!empty($filters['end_date'])) $query->where('date', '<=', $filters['end_date']);

        $summaries = $query->get();
        $dailySummaries = $summaries->groupBy(function($item) {
            return $item->date->format('Y-m-d');
        })->map(function ($dayGroup) {
            return [
                'date' => $dayGroup->first()->date,
                'work_ms' => $dayGroup->where('category_type', 'work')->sum('total_duration_ms'),
                'other_ms' => $dayGroup->where('category_type', 'other')->sum('total_duration_ms'),
                'untagged_ms' => $dayGroup->where('category_type', 'untagged')->sum('total_duration_ms'),
                'total_ms' => $dayGroup->sum('total_duration_ms'),
                'activity_count' => $dayGroup->sum('activity_count'),
            ];
        })->sortByDesc('date');

        return [
            'template' => 'reports.templates.user_detail_report',
            'summaries' => $summaries,
            'daily_summaries' => $dailySummaries,
            'user' => ComputerUser::where('username', $username)->first(),
            'generated_at' => now(),
        ];
    }

    protected function collectUnitPerformanceData($filters)
    {
        $unitId = $filters['unit_id'] ?? null;
        $query = ActivitySummary::query();
        
        if ($unitId) {
            $usernames = ComputerUser::where('unit_id', $unitId)->pluck('username')->toArray();
            $query->whereIn('username', $usernames);
        }
        
        if (!empty($filters['start_date'])) $query->where('date', '>=', $filters['start_date']);
        if (!empty($filters['end_date'])) $query->where('date', '<=', $filters['end_date']);

        $summaries = $query->get();
        $dailySummaries = $summaries->groupBy(function($item) {
            return $item->date->format('Y-m-d');
        })->map(function ($dayGroup) {
            return [
                'date' => $dayGroup->first()->date,
                'work_ms' => $dayGroup->where('category_type', 'work')->sum('total_duration_ms'),
                'other_ms' => $dayGroup->where('category_type', 'other')->sum('total_duration_ms'),
                'untagged_ms' => $dayGroup->where('category_type', 'untagged')->sum('total_duration_ms'),
                'total_ms' => $dayGroup->sum('total_duration_ms'),
                'activity_count' => $dayGroup->sum('activity_count'),
            ];
        })->sortByDesc('date');

        return [
            'template' => 'reports.templates.unit_performance_report',
            'summaries' => $summaries,
            'daily_summaries' => $dailySummaries,
            'unit' => Unit::find($unitId),
            'generated_at' => now(),
        ];
    }

    protected function collectAppUsageData($filters)
    {
        $query = ProcessSummary::query();
        if (!empty($filters['start_date'])) $query->where('date', '>=', $filters['start_date']);
        if (!empty($filters['end_date'])) $query->where('date', '<=', $filters['end_date']);

        $procs = $query->select('process_name', \DB::raw('SUM(total_duration_ms) as total_duration'), \DB::raw('SUM(activity_count) as activity_count'))
            ->groupBy('process_name')
            ->orderByDesc('total_duration')
            ->limit(20)->get();

        return [
            'template' => 'reports.templates.app_usage_report',
            'processes' => $procs,
            'generated_at' => now(),
        ];
    }

    protected function generatePdf(GeneratedReport $report, $data, $filePath)
    {
        ini_set("pcre.backtrack_limit", "10000000");
        ini_set("memory_limit", "512M");

        $template = $data['template'] ?? 'reports.templates.activity_report';
        
        if (isset($data['summaries'])) {
            $data['summaries']->load('computerUser');
        }
        
        $html = View::make($template, array_merge($data, ['report' => $report]))->render();
        
        $mpdf = new Mpdf([
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
            'tempDir' => storage_path('app/public/temp_mpdf'),
        ]);

        if (!file_exists(storage_path('app/public/temp_mpdf'))) {
            mkdir(storage_path('app/public/temp_mpdf'), 0777, true);
        }
        
        // Split HTML into chunks by TR tag to avoid large string processing in mPDF
        $chunks = explode('</tr>', $html);
        foreach ($chunks as $index => $chunk) {
            if ($index < count($chunks) - 1) {
                $mpdf->WriteHTML($chunk . '</tr>');
            } else {
                $mpdf->WriteHTML($chunk);
            }
        }

        $content = $mpdf->Output('', 'S');
        Storage::disk('public')->put($filePath, $content);
    }

    protected function generateExcel(GeneratedReport $report, $data, $filePath)
    {
        if (isset($data['summaries'])) {
            $data['summaries']->load('computerUser');
        }
        Excel::store(new ActivitiesExport($data), $filePath, 'public');
    }

    protected function getFileName(GeneratedReport $report)
    {
        $ext = in_array($report->format, ['excel', 'xlsx']) ? 'xlsx' : $report->format;
        return Str::slug($report->title) . '_' . time() . '.' . $ext;
    }
}
