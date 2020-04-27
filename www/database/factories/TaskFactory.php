<?php

$factory->define(App\Task::class, function (Faker\Generator $faker) {
    return [
        'task' => $faker->text(140),
        'is_done' => false,
        'is_deleted' => false
    ];
});
