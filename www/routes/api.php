<?php

$router->group(['namespace' => 'Api', 'prefix' => 'api/v1'],
	function() use ($router) {
		$router->post('/tasks', 'TaskController@addTask');
		$router->get('/tasks', 'TaskController@getTasks');
		$router->delete('/tasks/{taskId}', 'TaskController@deleteTask');
		$router->put('/tasks/{taskId}', 'TaskController@updateTask');
});
