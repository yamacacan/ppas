<?php

namespace App\Jobs;

use App\Models\GeneratedReport;
use App\Services\ReportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $report;

    public $tries = 3;
    public $timeout = 600;

    public function __construct(GeneratedReport $report)
    {
        $this->report = $report;
    }

    public function handle(ReportService $reportService)
    {
        $reportService->generate($this->report);
    }

    public function failed(\Exception $exception)
    {
        $this->report->update([
            'status' => 'failed',
            'error_message' => $exception->getMessage(),
        ]);
    }
}
