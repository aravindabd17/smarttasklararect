<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
    public function test_user_can_login()
    {
        $user=User::factory()->create([
            'password'=>Hash::make('password123')
        ]);
        $response=$this->postJson("/api/login",[
            'email'=>$user->email,
            'password'=>'password123'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'user'=>[
                'id',
                'name',
                'email'
            ],'token'
        ]);
    }
    public function test_login_fails_with_invalid_password()
    {
        $user=User::factory()->create([
            'password'=>Hash::make("password123")
        ]);

        $response=$this->postJson("/api/login",[
            'email'=>$user->email,
            'password'=>'wrong password'
        ]);
        $response->assertStatus(401);
        $response->assertJson([
             "msg"=>"invalid credentials"
        ]);
    }
    public function test_only_authenticated_user_can_view_profile()
    {
        $user=User::factory()->create();
        $token=$user->createToken('test-token')->plainTextToken;

        $response=$this->withHeader(
            'Authorization',
            'Bearer '.$token
        )->getJson("/api/profile");

        $response->assertStatus(200);
        $response->assertJson([
            'id'=>$user->id,
            'email'=>$user->email
        ]);
    }
    public function test_user_can_logout()
    {
        $user=User::factory()->create();
        $token=$user->createToken('test-token')->plainTextToken;

        $response=$this->withHeader('Authorization','Bearer '.$token)
        ->postJson('/api/logout');
        $response->assertStatus(200);
        $response->assertJson([
            "msg"=>"Logged out successfully!",
        ]);
    }
}
