<?php

namespace App\Notifications;

use App\Models\GeneratedReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReportReadyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $report;

    public function __construct(GeneratedReport $report)
    {
        $this->report = $report;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Raporunuz Hazır: ' . $this->report->title)
                    ->line('İstemiş olduğunuz rapor başarıyla oluşturuldu.')
                    ->action('Raporu İndir', url($this->report->download_url))
                    ->line('PPAS Performans Takip Sistemi');
    }

    public function toArray($notifiable)
    {
        return [
            'report_id' => $this->report->id,
            'title' => $this->report->title,
            'message' => 'Raporunuz hazır ve indirilebilir.',
            'action_url' => route('reports.show', $this->report->id),
            'type' => 'success',
        ];
    }
}
