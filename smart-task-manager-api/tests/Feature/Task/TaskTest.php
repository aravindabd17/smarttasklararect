<?php

namespace Tests\Feature\Task;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
     public function test_get_all_task()
    {
       
       $user=User::factory()->create();
       Sanctum::actingAs($user);
       Task::factory()->count(5)->create([
        'user_id'=>$user->id
       ]);

       $response=$this->getJson("/api/tasks");
       $response->assertStatus(200);
       $response->assertJsonCount(5,'data');
    }
    public function test_user_can_store_task()
    {
        $user=User::factory()->create();
        Sanctum::actingAs($user);
        $response=$this->postJson("/api/tasks",[
            'title'=>'test title',
            'description'=>'test description',
            'status'=>'pending',
            'user_id'=>$user->id
        ]);
        $response->assertStatus(201);
       $this->assertDatabaseHas("tasks",[
        'title'=>'test title'
       ]);
    }
    public function test_user_can_show_task()
    {
        $user=User::factory()->create();
        Sanctum::actingAs($user);
        $task=Task::factory()->create([
            'user_id'=>$user->id
        ]);

        $response=$this->getJson("/api/tasks/{$task->id}");
        $response->assertStatus(200);
        $response->assertJson([
            'data'=>[
                'id'=>$task->id
            ]
        ]);
    }
    public function test_user_can_update_task()
    {
        $user=User::factory()->create();
        Sanctum::actingAs($user);
        $task=Task::factory()->create([
            'user_id'=>$user->id
        ]);
        
        $response=$this->putJson("/api/tasks/{$task->id}",[
            'title'=>'updated title',
            'description'=>$task->description,
            'user_id'=>$user->id,
            'status'=>'completed'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks',[
            'id'=>$task->id,
            'title'=>'updated title'
        ]);
    }
    public function test_user_can_delete_task()
    {
        $user=User::factory()->create();
        Sanctum::actingAs($user);
        $task=Task::factory()->create([
            'user_id'=>$user->id
        ]);
        $response=$this->deleteJson("/api/tasks/{$task->id}");
        $response->assertStatus(200);
        $this->assertSoftDeleted('tasks',[
            'id'=>$task->id
        ]);
    }
    public function test_task_require_title()
    {
        $user=User::factory()->create();
        Sanctum::actingAs($user);
        $response=$this->postJson('/api/tasks',[
            'description'=>'new description',
            'status'=>'pending',
            'user_id'=>$user->id
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrorFor('title');
    }
    public function test_only_authenticate_user_update()
    {
        $user=User::factory()->create();
        $task=Task::factory()->create([
            'user_id'=>$user->id
        ]);
        $newuser=User::factory()->create();
        Sanctum::actingAs($newuser);

        $response=$this->putJson("/api/tasks/{$task->id}",[
            'title'=>'updated_title',
            'user_id'=>$newuser->id,
            'description'=>'updated description',
            'status'=>'completed'
        ]);
        $response->assertStatus(403);
    }
    public function test_user_can_search_task()
    {
        $user=User::factory()->create();
        Sanctum::actingAs($user);

        Task::factory()->create([
            'title'=>'laravel',
            'description'=>'lara description',
            'status'=>'pending'
        ]);
         Task::factory()->create([
            'title'=>'react',
            'description'=>'react description',
            'status'=>'pending'
        ]);
        $response=$this->getJson("/api/tasks/?search=laravel");
        $response->assertStatus(200);
        $response->assertJsonCount(1,'data');
    }
    public function test_task_filter_by_status()
    {
        $user=User::factory()->create();
        Sanctum::actingAs($user);
        Task::factory()->create([
            'title'=>'update laravel',
            'status'=>'in-progress',
            'description'=>'updated description',
            'user_id'=>$user->id
        ]);
        Task::factory()->create([
            'title'=>'update react',
            'status'=>'in-progress',
            'description'=>'updated description',
            'user_id'=>$user->id
        ]);

        $response=$this->getJson("/api/tasks/?status=in-progress");
        // $response->dump();
        $response->assertStatus(200);
        $response->assertJsonCount(2,'data');
    }
}
