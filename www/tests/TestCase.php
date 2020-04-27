<?php

use App\Http\Resources\Task as TaskResource;

abstract class TestCase extends Laravel\Lumen\Testing\TestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    public function create(string $model, array $attributes = [])
    {
    	$tasks = factory("App\\$model")->create($attributes);

    	return new TaskResource($tasks);
    }
}