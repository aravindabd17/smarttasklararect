<?php
namespace tests\Feature\Task;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskLimitRequestTest extends TestCase
{
    use RefreshDatabase;
    public function test_first_thirty_can_access_tasks()
    {
        $user=User::factory()->create();
        Sanctum::actingAs($user);

        for($i=0;$i<30;$i++)
        {
            $this->getJson("/api/tasks");
        }
        $response=$this->getJson("/api/tasks");
        $response->assertStatus(429);
    }
}