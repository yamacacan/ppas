<?php

namespace App\Jobs;

use App\Services\AutoTaggingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AutoTagActivitiesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $limit;

    /**
     * Create a new job instance.
     */
    public function __construct(int $limit = 100)
    {
        $this->limit = $limit;
    }

    /**
     * Execute the job.
     */
    public function handle(AutoTaggingService $autoTaggingService): void
    {
        Log::info("AutoTagActivitiesJob başlatıldı", ['limit' => $this->limit]);

        try {
            $result = $autoTaggingService->tagUntaggedActivities($this->limit);
            
            Log::info("AutoTagActivitiesJob tamamlandı", [
                'processed' => $result['processed'],
                'tagged' => $result['tagged'],
                'skipped' => $result['skipped'],
            ]);
        } catch (\Exception $e) {
            Log::error("AutoTagActivitiesJob hatası: " . $e->getMessage(), [
                'exception' => $e,
            ]);
            
            throw $e;
        }
    }
}
