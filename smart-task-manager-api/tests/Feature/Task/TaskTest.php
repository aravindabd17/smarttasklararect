<?php

namespace Tests\Feature\Task;

use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskCreatedNotification;
use Illuminate\Container\Attributes\Storage as AttributesStorage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskTest extends TestCase
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
        Task::factory()->create([
            'title'=>'update js',
            'status'=>'completed',
            'description'=>'updated js description',
            'user_id'=>$user->id
        ]);

        $response=$this->getJson("/api/tasks/?status=in-progress");
        // $response->dump();
        $response->assertStatus(200);
        $response->assertJsonCount(2,'data');
    }

    function test_task_are_paginated()
    {
        $user=User::factory()->create();
        Sanctum::actingAs($user);

        Task::factory()->count(15)->create([
            'user_id'=>$user->id
        ]);

        $response=$this->getJson("/api/tasks");
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'links',
            'meta'
        ]);
    }
    public function test_user_can_attach_document()
    {
        Storage::fake('public');

        $user=User::factory()->create();
        Sanctum::actingAs($user);

        // $file=UploadedFile::fake()->image("test.jpg");
        $file=UploadedFile::fake()->create('resume.pdf',100,'application/pdf');

        $response=$this->post("/api/tasks",[
            'title'=>'task title',
            'description'=>'description',
            'status'=>'completed',
            'user_id'=>$user->id,
            'attachment'=>$file
        ]);

        $response->assertStatus(201);
        $attachment=$response->json('data.attachement');
        Storage::disk('public')->assertExists($attachment);
    }
    public function test_notification_sent_when_task_created()
    {
        Notification::fake();

        $user=User::factory()->create();
        Sanctum::actingAs($user);

        $response=$this->postJson("/api/tasks",[
            'title'=>'notify title',
            'description'=>'notify description',
            'user_id'=>$user->id,
            'status'=>'completed'
        ]);
        $response->assertStatus(201);
        Notification::assertSentTo(
            $user,
            TaskCreatedNotification::class,
            function($notifications){
                return $notifications->task->title="notify title";
            }
        );
    }
    public function test_user_cannot_update_another_user_task()
    {
        $user=User::factory()->create();
        $task=Task::factory()->create([
            'user_id'=>$user->id
        ]);
        $anotheruser=User::factory()->create();
        Sanctum::actingAs($anotheruser);
        $response=$this->putJson("/api/tasks/{$task->id}",[
            'title'=>'updated title',
            'description'=>'updated description',
            'user_id'=>$anotheruser->id,
            'status'=>'completed'
        ]);

        $response->assertForbidden();
    }
    public function test_owner_user_can_update_task()
    {
        $user=User::factory()->create();
        $task=Task::factory()->create([
            'user_id'=>$user->id
        ]);
        Sanctum::actingAs($user);
        $response=$this->putJson("/api/tasks/{$task->id}",[
            'title'=>'updated title',
            'description'=>'updated description',
            'user_id'=>$user->id,
            'status'=>'completed',
        ]);
        $response->assertOk();
    }
    public function test_admin_can_access_dashboard()
    {
        $user=User::factory()->create([
            'is_admin'=>true,
        ]);
        Sanctum::actingAs($user);
        $response=$this->getJson("/api/admin/dashboard");

        $response->assertOk()
        ->assertJson([
            'msg'=>'admin dashboard',
        ]);
    }
    public function test_normal_user_cannot_access_dashboard()
    {
        $user=User::factory()->create([
            'is_admin'=>false
        ]);
        Sanctum::actingAs($user);

        $response=$this->getJson("/api/admin/dashboard");

        $response->assertForbidden();
    }
}
