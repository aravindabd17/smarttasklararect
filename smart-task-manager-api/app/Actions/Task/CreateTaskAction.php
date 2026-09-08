<?php
namespace App\Actions\Task;

use App\DTOs\TaskData;
use App\Jobs\BroadcastJobCreated;
use App\Repositories\TaskRepository;
use Illuminate\Support\Facades\DB;

class CreateTaskAction
{
    public function __construct(private TaskRepository $taskRepository){}
    public function execute(TaskData $taskdata)
    {
        return DB::transaction(function() use($taskdata)
        {
            $data=$taskdata->toArray();
            if($taskdata->attachment)
            {
                $data['attachment']=$taskdata->attachment->store('tasks','public');
            }
            $task=$this->taskRepository->store($data);
            BroadcastJobCreated::dispatch($task)->afterCommit();
            return $task;
        });   
    }
}