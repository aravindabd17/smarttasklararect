<?php

namespace App\Http\Controllers;

use App\Actions\Task\CreateTaskAction;
use App\Actions\Task\DeleteTaskAction;
use App\Actions\Task\UpdateTaskAction;
use App\DTOs\TaskData;
use App\Events\TaskCompleted;
use App\Events\TaskCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\TaskResource;
use App\Models\ActivityLog;
use App\Models\Task;
use App\Notifications\TaskCreatedNotification;
use App\Services\ActivityLogService;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TaskController extends Controller
{
    public function __construct(
        private TaskService $taskService,
        private CreateTaskAction $createtaskaction,
        private UpdateTaskAction $updatetaskaction,
        private DeleteTaskAction $deletetaskaction
    ){}
    /**
     * @OA\Get(
     *     path="/api/tasks",
     *     operationId="getTasks",
     *     tags={"Tasks"},
     *     summary="Get all tasks",
     *     description="Returns paginated list of tasks",
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         required=false,
     *         description="Search by title",
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=false,
     *         description="Filter by status",
     *         @OA\Schema(
     *             type="string",
     *             enum={"pending","in-progress","completed"}
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Tasks fetched successfully"
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
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

        // $filters = $request->only([
        //     'search'
        // ]);
        $filters = $request->only(['search', 'status', 'priority','page']);

        $task=$this->taskService->getAll($filters);
        // return response()->json($request->query())
        return TaskResource::collection($task);
    }

    /**
     * @OA\Post(
     *     path="/api/tasks",
     *     tags={"Tasks"},
     *     summary="Create new task",
     *     security={{"sanctum":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title","status"},
     *
     *             @OA\Property(
     *                 property="title",
     *                 type="string",
     *                 example="Learn Laravel"
     *             ),
     *
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 example="Complete Week 8"
     *             ),
     *
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="pending"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Task created successfully"
     *     )
     * )
     */
    public function store(StoreTaskRequest $request)
    {
        $task=$this->createtaskaction->execute(TaskData::fromRequest($request));
        event(new TaskCreated($task));
        Cache::flush();
        return new TaskResource($task);
    }

    /**
     * @OA\Get(
     *     path="/api/tasks/{id}",
     *     operationId="showTask",
     *     tags={"Tasks"},
     *     summary="Get a single task",
     *     description="Returns a single task by ID",
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Task ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Task retrieved successfully"
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Task not found"
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function show(Task $task)
    {
        return Cache::remember("task".$task->id,300,function() use($task){
            $task->load('user');
            return new TaskResource($task);
        });
    }

    /**
     * @OA\Put(
     *     path="/api/tasks/{id}",
     *     operationId="updateTask",
     *     tags={"Tasks"},
     *     summary="Update an existing task",
     *     description="Updates an existing task",
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Task ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="title",
     *                     type="string",
     *                     example="Updated Laravel Task"
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                     example="Updated description"
     *                 ),
     *                 @OA\Property(
     *                     property="status",
     *                     type="string",
     *                     enum={"pending","in-progress","completed"},
     *                     example="completed"
     *                 ),
     *                 @OA\Property(
     *                     property="attachment",
     *                     type="string",
     *                     format="binary"
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Task updated successfully"
     *     ),
     *
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized"
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Task not found"
     *     )
     * )
     */
    public function update(StoreTaskRequest $request, Task $task)
    {
        if($task->user_id !==auth()->id())
            abort(403,"You are not authorized to update this");
        $this->authorize("update",$task);
        $updatetask=$this->updatetaskaction->execute($task,TaskData::fromRequest($request));
        if($task->status=="completed")
        {
            event(new TaskCompleted($updatetask));
        }
        Cache::flush();
        return new TaskResource($updatetask);
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * @OA\Delete(
     *     path="/api/tasks/{id}",
     *     operationId="deleteTask",
     *     tags={"Tasks"},
     *     summary="Delete a task",
     *     description="Soft deletes a task",
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Task ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Task deleted successfully"
     *     ),
     *
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized"
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Task not found"
     *     )
     * )
     */
    public function destroy(Task $task)
    {
        if($task->user_id !==auth()->id())
            abort(403,"You are not authorized to delete this");
        $this->authorize("delete",$task);
        $this->taskService->delete($task);
        Cache::flush();
        return response()->json(['message'=>'Task deleted successfully']);
    }
}
