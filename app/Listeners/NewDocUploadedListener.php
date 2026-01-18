<?php

namespace App\Listeners;

use App\Events\NewDocUploadedEvent;
use App\Models\User;
use App\Notifications\NewDocUploadedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class NewDocUploadedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(NewDocUploadedEvent $event): void
    {
        $users=User::all();
        Notification::send($users,new NewDocUploadedNotification($event->file));

    }
}
