<?php 
namespace App\Repositories;

use App\Events\TaskCreatedBroadcast;
use App\Models\Task;
use App\Pipelines\SearchFilter;
use App\Pipelines\StatusFilter;
use App\Repositories\Contract\TaskRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;
use Override;

    class TaskRepository implements TaskRepositoryInterface
    {
        public function getAll(array $filters): LengthAwarePaginator
        {
            // return Task::with('user')
            // ->when($filters['search']??null,fn($query,$search)=>$query->search($search))
            // ->when($filters['status']??null,fn($query,$status)=>$query->where("status",$status))
            // ->when($filters['user_id']??null,fn($query,$userid)=>$query->where('user_id',$userid))
            // ->latest()
            // ->paginate(10);
            $query=Task::with('user');
            app(Pipeline::class)
            ->send($query)
            ->through([
                SearchFilter::class,
                StatusFilter::class,
            ])
            ->thenReturn();

            return $query->latest()->paginate(10);

        }
        public function find(Task $task): Task
        {
            return $task->load('user');
        }
        public function store(array $data):Task
        {
            $task=Task::create($data);
            return $task->load("user");
        }
        public function update(Task $task, array $data): Task
        {
            $task->update($data);
            return $task->load('user');
        }
        public function delete(Task $task): bool
        {
           return (bool) $task->delete();
        }
    }
