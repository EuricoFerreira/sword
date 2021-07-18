<?php

namespace Tests\Feature;


use Tests\TestCase;
use App\User;
use App\Task;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Events\TaskCreated;
use Illuminate\Support\Facades\Event;

class TaskTest extends TestCase
{
    use DatabaseTransactions;

    public function testTaskCreatedAndDispatchEventSuccessfully()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user, 'api');

        Event::fake(TaskCreated::class);
        
        $taskData = [
            "summary" =>  "This is a summary",
            "date" => "2021-07-17 00:00:00",
        ];

        $this->json('POST', 'api/task', $taskData, ['Accept' => 'application/json'])
            ->assertStatus(201)
            ->assertJsonStructure([
                "task" => [
                    'summary',
                    'completed_at',
                    'user_id',
                    'updated_at',
                    'created_at',
                    'id',
                ],
                 "success",
        ]);

        Event::assertDispatched(TaskCreated::class);
    }

    public function testTechnicianOnlyCanSeeOwnTask() {

        $technician = factory(User::class)->create();

        $this->actingAs($technician, 'api');

        $tasks = factory(Task::class)->create([
            "user_id" => $technician->id
            ],[
            "summary" => "testing Task2",
            "user_id" => null
            ]
        );

        $this->json('GET', 'api/task', ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonCount(1,'tasks');

    }

    public function testManagerCanSeeAllTasks() {

        $manager = factory(User::class)->create([
            'isManager' => true
        ]);
        $this->actingAs($manager, 'api');

        Task::truncate();

        $tasks = factory(Task::class , 10)->create();
    
        $this->json('GET', 'api/task', ['Accept' => 'application/json'])
        ->assertStatus(200)
        ->assertJsonCount(10,'tasks');

    }

    public function testTechnicianCanUpdateHisTask()
    {
        $technician = factory(User::class)->create();
        $this->actingAs($technician, 'api');

        Task::truncate();

        $tasks = factory(Task::class)->create([
            'user_id' => $technician->id

        ]);

        $updatedTaskData = [
            "summary" => "This task has been updated",
            "date" => "2021-07-17 00:00:00"
        ];

        $this->json('PUT', 'api/task/1', $updatedTaskData, ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonFragment([
                'success' => true
            ]);
    }

    public function testTechnicianCannotDeleteTasks()
    {
        $technician = factory(User::class)->create();

        $this->actingAs($technician, 'api');
        Task::truncate();

        $tasks = factory(Task::class, 2)->create();

        $this->json('DELETE', 'api/task/1', ['Accept' => 'application/json'])
        ->assertStatus(403)
        ->assertJson([
            'success' => false,
            "message" => "You can't perform this action"
        ]);
        
    }

    public function testManagerCanDeleteTasks()
    {
        $manager = factory(User::class)->create(
            ['isManager' => true]
        );

        $this->actingAs($manager, 'api');
        Task::truncate();

        $tasks = factory(Task::class, 2)->create();

        $this->json('DELETE', 'api/task/1', ['Accept' => 'application/json'])
        ->assertStatus(200)
        ->assertJsonFragment([
            'success' => true
        ]);
    }
}
