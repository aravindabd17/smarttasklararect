<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search=request('search');
        $task=Task::with('user')
        ->when($search,function($query,$search){
            $query->where('title','like',"%{$search}%");
        })
        ->latest()
        ->paginate(10);
        return TaskResource::collection($task);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $task=Task::create($request->validated());
        return new TaskResource($task);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        return new TaskResource($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $task->update($request->validated());
        return new TaskResource($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(['message'=>'Task deleted successfully']);
    }
}
