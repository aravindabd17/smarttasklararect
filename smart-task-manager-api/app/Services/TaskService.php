<?php
namespace App\Services;

use App\DTOs\TaskData;
use App\Events\TaskCreatedBroadcast;
use App\Jobs\BroadcastJobCreated;
use App\Models\Task;
use App\Repositories\Contract\TaskRepositoryInterface;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class TaskService 
{
    public function __construct(private TaskRepositoryInterface $taskRepository){}
    public function getAll(array $filters)
    {
        $version=Cache::get("cache_task_version",1);
        $cacheKey="Task".$version.md5(json_encode($filters));
        return Cache::remember($cacheKey,300,function() use($filters){
            return $this->taskRepository->getAll($filters);
        });
    }
    public function create(TaskData $taskdata):Task
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
    public function update(Task $task,array $data):Task 
    {
        return DB::transaction(function() use($task,$data){
            if(isset($data['attachment']))
            {
                if($task->attachment && Storage::disk('public')->exists($task->attachment))
                {
                    Storage::disk('public')->delete($task->attachment);
                }
                $data['attachment']=$data['attachment']->store('tasks','public');
            }
            $updatedtask= $this->taskRepository->update($task,$data);
            return $updatedtask;
        });
         
    }
    public function delete(Task $task):bool 
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