<?php

use Faker\Factory;
use Laravel\Lumen\Testing\DatabaseMigrations;

class TaskControllerTest extends \TestCase
{
    use DatabaseMigrations;
    /**
     *@test
     */
    public function should_fail_creating_a_task_with_status_400_invalid_input()
    {
        $faker = Factory::create();

        $response = $this->json('POST', '/api/v1/tasks', [
            'tosk' => $faker->text(140)
        ]);

        $response->assertEquals(400, $this->response->status());
    }

    /**
     *@test
     */
    public function should_create_a_task()
    {
        $faker = Factory::create();

        $response = $this->json('POST', '/api/v1/tasks', [
            'task' => $task = $faker->text(140)
        ]);

        $response->seeJsonStructure([
            'id', 'task', 'is_done', 'is_deleted'
        ])
        ->seeJson([
            'task' => $task,
            'is_done' => false,
            'is_deleted' => false
        ])
        ->assertEquals(201, $this->response->status());

        $this->seeInDatabase('task', [
            'task' => $task,
            'is_done' => false,
            'is_deleted' => false
        ]);
    }

    /**
     *@test
     */
    public function should_return_all_tasks()
    {
        $response = $this->json('GET', '/api/v1/tasks');

        $response->seeJsonStructure([
            '*' => ['id', 'task', 'is_done', 'is_deleted']
        ])
        ->assertEquals(200, $this->response->status());
    }

    /**
     *@test
     */
    public function should_fail_deleting_a_task_with_status_404()
    {
        $response = $this->json('DELETE', "/api/v1/tasks/-1");

        $response->assertEquals(404, $this->response->status());
    }

    /**
     *@test
     */
    public function should_delete_the_tasks_created_for_test()
    {
        $task = $this->create('Task');

        $response = $this->json('DELETE', "/api/v1/tasks/$task->id");

        $response->assertEquals(200, $this->response->status());

        $this->seeInDatabase('task', [
            'id' => $task->id,
            'is_deleted' => true
        ]);
    }

    /**
     *@test
     */
    public function should_fail_updating_a_task_with_status_404()
    {
        $response = $this->json('PUT', "/api/v1/tasks/-1");

        $response->assertEquals(404, $this->response->status());
    }

    /**
     *@test
     */
    public function should_update_the_task_created_for_test()
    {
        $task = $this->create('Task');

        $response = $this->json('PUT', "/api/v1/tasks/$task->id", [
            'task' => $task->task . '_updated',
            'is_done' => true
        ]);

        $response->assertEquals(200, $this->response->status());

        $this->seeInDatabase('task', [
            'id' => $task->id,
            'task' => $task->task . '_updated',
            'is_done' => true
        ]);
    }
}