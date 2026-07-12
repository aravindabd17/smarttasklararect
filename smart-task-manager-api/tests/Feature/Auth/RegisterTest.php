<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_user_can_register():void 
    {
        $response=$this->postJson("/api/register",[
            "name"=>"aravind",
            "email"=>"aravindbr94@gmail.com",
            "password"=>"Aravind17",
            "password_confirmation"=>"Aravind17"
        ]);
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'user'=>[
                'name',
                'email',
                'updated_at',
                'created_at',
                'id'
            ],
            'token',
        ]);
        $this->assertDatabaseHas("users",[
            'email'=>'aravindbr94@gmail.com'
        ]);
    }
    public function test_register_validation_errors()
    {
        $response=$this->postJson('/api/register',[]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'name',
            'email',
            'password',
        ]);
    }
    public function test_duplicate_email_errors()
    {
        User::factory()->insert([
            'email'=>'aravind@example.com'
        ]);
        $response=$this->postJson("/api/register",[
            "name"=>"aravind",
            "email"=>"aravind@example.com",
            "password"=>"Aravind17",
            "password_confirmation"=>"Aravind17"
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }
}
