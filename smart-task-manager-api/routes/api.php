<?php

use App\Events\TaskCreatedBroadcast;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;


Route::apiResource('users',UserController::class);


Route::post("/register",[AuthController::class,'register']);
Route::post("/login",[AuthController::class,'login'])->middleware('throttle:login');

Route::middleware('auth:sanctum')->group(function(){
    Route::post("logout",[AuthController::class,'logout']);
    Route::get("profile",[AuthController::class,'profile']);
});

Route::get("/cache-redis",function(){
    Redis::incr("name");
    return Redis::get("name");
});

Route::middleware('auth:sanctum')->get("/admin/dashboard",function(){
    Gate::authorize('admin');
    return response()->json([
        'msg'=>'admin dashboard'
    ]);
});
Route::middleware(['auth:sanctum','throttle:task-api'])->group(function(){
    Route::apiResource('tasks',TaskController::class);
});

Route::get('/test-broadcast',function(){
    $task=Task::latest()->first();
    TaskCreatedBroadcast::dispatch($task);
    return "Broadcast send";
});

