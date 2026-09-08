<?php
namespace App\Actions\Task;

use App\DTOs\TaskData;
use App\Jobs\BroadcastJobCreated;
use App\Models\Task;
use App\Repositories\TaskRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UpdateTaskAction
{
    public function __construct(private TaskRepository $taskRepository){}
    public function execute(Task $task,TaskData $taskdata)
    {
        return DB::transaction(function() use($task,$taskdata){
            $data = $taskdata->toArray();
            if(isset($taskdata->attachment))
            {
                if($task->attachment && Storage::disk('public')->exists($task->attachment))
                {
                    Storage::disk('public')->delete($task->attachment);
                }
                $data['attachment']=$taskdata->attachment->store('tasks','public');
            }
            $updatedtask= $this->taskRepository->update($task,$data);
            return $updatedtask;
        });
    }
}