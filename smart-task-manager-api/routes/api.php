<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;


Route::apiResource('users',UserController::class);


Route::post("/register",[AuthController::class,'register']);
Route::post("/login",[AuthController::class,'login']);

Route::middleware('auth:sanctum')->group(function(){
    Route::apiResource('tasks',TaskController::class);
    Route::post("logout",[AuthController::class,'logout']);
    Route::get("profile",[AuthController::class,'profile']);
});

Route::get("/cache-redis",function(){
    Redis::incr("name");
    return Redis::get("name");
});

