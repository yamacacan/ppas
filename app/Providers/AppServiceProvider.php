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

        // Dinamik Mail Ayarları
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('firm_settings')) {
                $settings = \App\Models\FirmSettings::instance();
                
                if ($settings->mail_host) {
                    config([
                        'mail.mailers.smtp.host'       => $settings->mail_host,
                        'mail.mailers.smtp.port'       => $settings->mail_port,
                        'mail.mailers.smtp.username'   => $settings->mail_username,
                        'mail.mailers.smtp.password'   => $settings->mail_password,
                        'mail.mailers.smtp.encryption' => $settings->mail_encryption,
                        'mail.from.address'            => $settings->mail_from_address,
                        'mail.from.name'                 => $settings->mail_from_name,
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Veritabanı henüz hazır değilse veya bağlantı hatası varsa sessizce devam et
        }
    }
}
