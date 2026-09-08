<?php
namespace App\Actions\Task;

use App\DTOs\TaskData;
use App\Jobs\BroadcastJobCreated;
use App\Models\Task;
use App\Repositories\TaskRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeleteTaskAction
{
    public function __construct(private TaskRepository $taskRepository){}
    public function execute(Task $task)
    {
        return DB::transaction(function() use($task){
            if($task->attachment && Storage::disk('public')->exists($task->attachment))
            {
                Storage::disk('public')->delete($task->attachment);
            }   
            return $this->taskRepository->delete($task);
        });
    }
}