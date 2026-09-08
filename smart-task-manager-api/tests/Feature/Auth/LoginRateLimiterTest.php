<?php
namespace tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginRateLimiterTest extends TestCase
{
    use RefreshDatabase;
    public function test_login_rate_limiter_block_after_five_attempts()
    {
        $user=User::factory()->create([
            'email'=>'aravindbr902@gmail.com',
            'password'=>bcrypt('password')
        ]);

        for($i=0;$i<5;$i++)
        {
           $this->postJson("/api/login",[
                'email'=>'aravindbr902@gmail.com',
                'password'=>bcrypt('password')
           ]);
        }

        $response=$this->postJson("/api/login",[
            'email'=>'aravindbr902@gmail.com',
            'password'=>bcrypt('password')
        ]);
        
        $response->assertStatus(429);
    }
}