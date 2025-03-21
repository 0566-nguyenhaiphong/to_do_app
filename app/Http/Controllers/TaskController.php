<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TaskController extends Controller
{
    // Create a new task
    public function store(Request $request)
    {
        $task = Task::create($request->all());
        return response()->json($task, 201);
    }

    // Get all tasks with filtering and pagination
    public function index(Request $request)
    {
        $cacheKey = 'tasks_' . $request->query('priority', 'all') . '_page_' . $request->query('page', 1);
        $tasks = Cache::remember($cacheKey, 60, function () use ($request) {
            $query = Task::with('dependencies');

            if ($request->has('priority')) {
                $query->where('priority', $request->priority);
            }
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
            if ($request->has('due_date')) {
                $query->where('due_date', $request->due_date);
            }
            return $query->paginate(10);
        });

        return response()->json($tasks);
    }

    public function update(Request $request, $id)
    {
        $task = Task::with('dependencies')->findOrFail($id);

        if ($request->status === 'completed') {
            foreach ($task->dependencies as $dependency) {
                if ($dependency->status !== 'completed') {
                    return response()->json([
                        'message' => 'Cannot complete this task because some dependencies are not completed.'
                    ], 400);
                }
            }
        }

        $task->update($request->all());

        return response()->json($task);
    }


    // Delete a task
    public function destroy($id)
    {
        Task::destroy($id);
        return response()->json(null, 204);
    }

    // Add a dependency to a task
    public function addDependency(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $dependency = Task::findOrFail($request->dependency_id);

        if ($task->hasCircularDependency($dependency->id)) {
            return response()->json([
                'message' => 'Cannot add dependency as it creates a circular dependency.'
            ], 400);
        }

        $task->dependencies()->attach($dependency);
        return response()->json($task);
    }

    // Remove a dependency from a task
    public function removeDependency(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $dependency = Task::findOrFail($request->dependency_id);
        $task->dependencies()->detach($dependency);
        return response()->json($task);
    }

    // Get all dependencies of a task
    public function getDependencies($id)
    {
        $task = Task::with('dependencies')->findOrFail($id);
        return response()->json($task->dependencies);
    }

    // Check if a task can start
    public function canStart($id)
    {
        $task = Task::with('dependencies')->findOrFail($id);
        return response()->json(['can_start' => $task->canStart()]);
    }
}
