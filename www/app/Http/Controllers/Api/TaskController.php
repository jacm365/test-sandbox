<?php

namespace App\Http\Controllers\Api;

use App\Task;
use App\Http\Resources\Task as TaskResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TaskController extends Controller
{
    public function addTask(Request $request)
    {
        try {
            $this->validate($request, [
                'task' => 'required|max:255'
            ]);

            $task = Task::create([
                'task' => $request->task,
                'is_done' => false,
                'is_deleted' => false
            ]);

            return response()->json(new TaskResource($task), 201);
        } catch(\Exception $e) {
            return response()->json([], 400);
        }
    }

    public function getTasks()
    {
        $tasks = Task::where('is_deleted', '!=', true)->get();

        return response()->json(TaskResource::collection($tasks), 200);
    }

    public function deleteTask(int $taskId)
    {
        try {
            $task = Task::findOrFail($taskId);
            $task->is_deleted = true;
            $task->save();
            return response()->json([], 200);
        } catch(\Exception $e) {
            return response()->json([], 404);
        }
    }

    public function updateTask(Request $request, int $taskId)
    {
        try {
            $this->validate($request, [
                'task' => 'required|max:255'
            ]);

            $task = Task::findOrFail($taskId);

            $task->update([
                'task' => $request->task,
                'is_done' => $request->is_done
            ]);

            return response()->json([], 200);
        } catch(\Exception $e) {
            return response()->json([], 404);
        }
    }
}