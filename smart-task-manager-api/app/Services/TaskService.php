<?php
namespace App\Services;

use App\Models\Task;
use App\Repositories\Contract\TaskRepositoryInterface;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;


class TaskService 
{
    public function __construct(private TaskRepositoryInterface $taskRepository){}
    public function getAll(array $filters)
    {
        $cacheKey="Task".md5(json_encode($filters));
        return Cache::remember($cacheKey,300,function() use($filters){
            return $this->taskRepository->getAll($filters);
        });
    }
    public function create(array $data):Task
    {
        if(isset($data['attachment']))
        {
            $data['attachment']=$data['attachment']->store('tasks','public');
        }
        
        return $this->taskRepository->store($data);
    }
    public function update(Task $task,array $data):Task 
    {
         if(isset($data['attachment']))
        {
            if($task->attachment && Storage::disk('public')->exists($task->attachment))
            {
                Storage::disk('public')->delete($task->attachment);
            }
            $data['attachment']=$data['attachment']->store('tasks','public');
        }
        return $this->taskRepository->update($task,$data);
    }
    public function delete(Task $task):bool 
    {
        if($task->attachment && Storage::disk('public')->exists($task->attachment))
        {
            Storage::disk('public')->delete($task->attachment);
        }   
        return $this->taskRepository->delete($task);
    }
}