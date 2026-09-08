<?php

namespace App\Observers;

use App\Models\Task;
use App\Services\ActivityLogService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TaskObserver
{
    public function __construct(protected ActivityLogService $activitylogservice)
    {
    }
    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task): void
    {
        Cache::increment('cache_task_version');
        $this->activitylogservice->log(
            "Create",
            $task,
            "Created Task ".$task->title
        );
    }

    /**
     * Handle the Task "updated" event.
     */
    public function updated(Task $task): void
    {
        Cache::increment('cache_task_version');
        $this->activitylogservice->log(
            "Create",
            $task,
            "Created Task ".$task->title
        );
    }

    /**
     * Handle the Task "deleted" event.
     */
    public function deleted(Task $task): void
    {
        Cache::increment('cache_task_version');
        $this->activitylogservice->log(
            "Delete",
            $task,
            "Deleted Task ".$task->title
        );
    }

    /**
     * Handle the Task "restored" event.
     */
    public function restored(Task $task): void
    {
        //
    }

    /**
     * Handle the Task "force deleted" event.
     */
    public function forceDeleted(Task $task): void
    {
        //
    }
}
