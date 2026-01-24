<?php

namespace App\Jobs;

use App\Models\Activity;
use App\Services\AutoTaggingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TagActivityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $activityId;

    public function __construct($activityId)
    {
        $this->activityId = $activityId;
        $this->queue = 'tagging';
    }

    public function handle(AutoTaggingService $autoTaggingService): void
    {
        try {
            $autoTaggingService->tagActivity($this->activityId);
        } catch (\Exception $e) {
            Log::error("TagActivityJob failed for Activity #{$this->activityId}: " . $e->getMessage());
            throw $e;
        }
    }
}
