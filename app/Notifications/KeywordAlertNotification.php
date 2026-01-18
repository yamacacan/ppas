<?php

namespace App\Notifications;

use App\Models\Activity;
use App\Models\CategoryKeyword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class KeywordAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $activity;
    protected $keyword;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Activity $activity, CategoryKeyword $keyword)
    {
        $this->activity = $activity;
        $this->keyword = $keyword;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database']; // Mail de eklenebilir: ['database', 'mail']
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Yasaklı/İzlenen Kelime Tespiti')
                    ->line('Kullanıcı: ' . $this->activity->username)
                    ->line('Tespit Edilen Kelime: ' . $this->keyword->keyword)
                    ->line('Aktivite: ' . ($this->activity->window_title ?: $this->activity->process_name))
                    ->line('Zaman: ' . $this->activity->start_time_utc)
                    ->action('Detayları Gör', url('/performance/activities')) // Link düzenlenebilir
                    ->line('Bu otomatik bir bildirimdir.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'activity_id' => $this->activity->id,
            'keyword_id' => $this->keyword->id,
            'keyword' => $this->keyword->keyword,
            'username' => $this->activity->username,
            'process_name' => $this->activity->process_name,
            'window_title' => $this->activity->title, // Modelde title veya window_title olabilir, activity modeline bakmalı. Activity modelinde 'title' var.
            'message' => "{$this->activity->username} kullanıcısı '{$this->keyword->keyword}' kelimesini içeren bir aktivitede bulundu.",
        ];
    }
}
