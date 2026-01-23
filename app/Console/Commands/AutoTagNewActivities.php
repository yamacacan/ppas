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
        
        $this->info("Otomatik tagleme (asenkron) başlatılıyor (Limit: $limit)...");
        
        // Taglenmemiş aktiviteleri al
        $activities = Activity::untagged()
            ->select('id')
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();

        if ($activities->isEmpty()) {
            $this->info('Taglenecek yeni aktivite bulunamadı.');
            return;
        }

        $this->info("{$activities->count()} adet aktivite için tagleme işleri kuyruğa ekleniyor...");
        
        $bar = $this->output->createProgressBar($activities->count());
        $bar->start();

        foreach ($activities as $activity) {
            \App\Jobs\TagActivityJob::dispatch($activity->id)->onQueue('tagging');
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        
        $this->info("İşlem tamamlandı. Aktiviteler arka planda işlenecek.");
    }
}
