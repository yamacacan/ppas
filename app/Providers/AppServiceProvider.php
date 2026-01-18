<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Activity;
use App\Observers\ActivityObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Activity Observer'ı kaydet
        // Yeni aktivite eklendiğinde otomatik tagleme yapılacak
        Activity::observe(ActivityObserver::class);
    }
}
