<?php

namespace App\Notifications;

use App\Models\File;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class NewDocUploadedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public $file;
    public function __construct(File $file)
    {
        $this->file=$file;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'file_name'=>$this->file->file_name->title,
            'message'=>' isimli doküman yüklenmiştir',
            'url'=>$this->file->url,
        ];
    }
}
