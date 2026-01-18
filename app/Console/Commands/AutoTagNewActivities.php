<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Activity;
use App\Services\AutoTaggingService;
use Illuminate\Support\Facades\Log;

class AutoTagNewActivities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'performance:auto-tag-new {--limit=5000 : İşlenecek maksimum aktivite sayısı}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Taglenmemiş yeni aktiviteleri otomatik olarak etiketler';

    protected $autoTaggingService;

    public function __construct(AutoTaggingService $autoTaggingService)
    {
        parent::__construct();
        $this->autoTaggingService = $autoTaggingService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = $this->option('limit');
        
        $this->info("Otomatik tagleme başlatılıyor (Limit: $limit)...");
        
        // Taglenmemiş aktiviteleri al (en yeniler önce)
        $activities = Activity::untagged()
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();

        if ($activities->isEmpty()) {
            $this->info('Taglenecek yeni aktivite bulunamadı.');
            return;
        }

        $this->info("{$activities->count()} adet aktivite bulundu. İşleniyor...");
        
        $bar = $this->output->createProgressBar($activities->count());
        $bar->start();

        $taggedCount = 0;
        $errorCount = 0;

        foreach ($activities as $activity) {
            try {
                $tags = $this->autoTaggingService->tagActivity($activity->id);
                
                if (!empty($tags)) {
                    $taggedCount++;
                }
            } catch (\Exception $e) {
                $errorCount++;
                Log::error("Auto-tag command error for activity {$activity->id}: " . $e->getMessage());
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        
        $this->info("İşlem tamamlandı.");
        $this->info("Taglenen: $taggedCount");
        $this->info("Hata: $errorCount");
        $this->info("Taglenemeyen (Eşleşme yok): " . ($activities->count() - $taggedCount - $errorCount));
    }
}
