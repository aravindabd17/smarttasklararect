<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UserLoginRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        try {
            $user=User::create($request->validated());
            $token=$user->createToken('api-token')->plainTextToken;
            return response()->json([
                "user"=>$user,
                "token"=>$token
            ],201);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                "msg"=>"Something went wrong!",
                // 'exception' => get_class($e),
            ]);
        }

    }
    public function login(UserLoginRequest $request)
    {
        $user=User::where("email",$request->email)->first();
        if(!$user || !Hash::check($request->password,$user->password))
        {
            return response()->json([
                "msg"=>"invalid credentials"
            ],401);
        }
        $token=$user->createToken('api-token')->plainTextToken;
        return response()->json([
            "user"=>$user,
            "token"=>$token,
        ]);
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            "msg"=>"Logged out successfully!",
        ],200);
    }
    public function profile(Request $request)
    {
        return response()->json($request->user());
    }
}
