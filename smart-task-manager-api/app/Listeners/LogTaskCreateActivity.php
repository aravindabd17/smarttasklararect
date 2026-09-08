<?php

namespace App\Listeners;

use App\Events\TaskCreated;
use App\Models\TaskLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogTaskCreateActivity
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
        TaskLog::create([
            'title'=>$event->task->title,
            'description'=>$event->task->description,
            'user_id'=>$event->task->user_id,
            'task_id'=>$event->task->id
        ]);
    }
}
