<?php

namespace App\Http\Controllers;

use App\Events\TaskCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Notifications\TaskCreatedNotification;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TaskController extends Controller
{

    public function __construct(private TaskService $taskService){}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $search=$request->search;
        // $status=$request->status;
        // $userId=$request->user_id;
        // $sort=$request->sort??"latest";

        // $task=Task::with('user')
        // ->when($search,fn($query)=>$query->search($search))
        // ->when($status,fn($query)=>$query->where("status",$status))
        // ->when($userId,fn($query)=>$query->where("user_id",$userId))
        // ->when($sort=="latest",fn($query)=>$query->latest())
        // ->when($sort=="oldest",fn($query)=>$query->oldest())
        // ->paginate(10);

        $task=$this->taskService->getAll(
            $request->query()
        );

        // return response()->json($request->query())
        return TaskResource::collection($task);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $task=$this->taskService->create($request->validated());
        event(new TaskCreated($task));
        Cache::flush();
        // $task->user->notify(new TaskCreatedNotification($task));
        return new TaskResource($task);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        return Cache::remember("task".$task->id,300,function() use($task){
            $task->load('user');
            return new TaskResource($task);
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->authorize("update",$task);
        $task=$this->taskService->update($task,$request->validated());
        Cache::flush();
        return new TaskResource($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $this->authorize("delete",$task);
        $this->taskService->delete($task);
        Cache::flush();
        return response()->json(['message'=>'Task deleted successfully']);
    }
}
