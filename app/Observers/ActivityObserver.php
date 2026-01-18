<?php

namespace App\Observers;

use App\Models\Activity;
use App\Services\AutoTaggingService;
use Illuminate\Support\Facades\Log;

class ActivityObserver
{
    protected $autoTaggingService;

    public function __construct(AutoTaggingService $autoTaggingService)
    {
        $this->autoTaggingService = $autoTaggingService;
    }

    /**
     * Handle the Activity "created" event.
     * Yeni aktivite eklendiğinde otomatik olarak taglenmeye çalışır
     */
    public function created(Activity $activity): void
    {
        try {
            // Aktivite oluşturulduktan hemen sonra otomatik tagle
            $this->autoTaggingService->tagActivity($activity->id);
            
            Log::info('Activity auto-tagged on creation', [
                'activity_id' => $activity->id,
                'process_name' => $activity->process_name,
                'title' => $activity->title,
            ]);
        } catch (\Exception $e) {
            // Hata olsa bile aktivite kaydını etkilemesin
            Log::error('Auto-tagging failed for new activity', [
                'activity_id' => $activity->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle the Activity "updated" event.
     */
    public function updated(Activity $activity): void
    {
        //
    }

    /**
     * Handle the Activity "deleted" event.
     */
    public function deleted(Activity $activity): void
    {
        //
    }

    /**
     * Handle the Activity "restored" event.
     */
    public function restored(Activity $activity): void
    {
        //
    }

    /**
     * Handle the Activity "force deleted" event.
     */
    public function forceDeleted(Activity $activity): void
    {
        //
    }
}
