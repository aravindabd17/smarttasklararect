<?php

namespace App\Listeners;

use App\Events\TaskCompleted;
use App\Mail\TaskCompletedMail;
use App\Notifications\TaskCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendTaskCompletedMail
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
    public function handle(TaskCompleted $event): void
    {
        Mail::to($event->task->user->email)
        ->send(new TaskCompletedMail($event->task));
    }
}
