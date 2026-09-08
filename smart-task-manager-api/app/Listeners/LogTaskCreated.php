<?php

namespace App\Listeners;

use App\Events\TaskCreated;
use App\Mail\TaskCompletedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class LogTaskCreated
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
    public function handle(TaskCreated $event): void
    {
        Log::info("Task Created",[
            'task title'=>$event->task->title,
            'task id'=>$event->task->id
        ]);
        Mail::to($event->task->user->email)
        ->send(new TaskCompletedMail($event->task));
    }
}
