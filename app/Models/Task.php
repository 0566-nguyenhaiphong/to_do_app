<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TaskDueNotification;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'due_date',
        'priority',
        'status',
    ];

    public function dependencies()
    {
        return $this->belongsToMany(Task::class, 'task_dependencies', 'task_id', 'dependency_id');
    }

    public function canStart()
    {
        foreach ($this->dependencies as $dependency) {
            if ($dependency->status !== 'completed') {
                return false;
            }
        }
        return true;
    }

    // Detect circular dependencies
    public function hasCircularDependency($dependencyId)
    {
        $visited = [];

        if ($this->detectCycle($this->id, $dependencyId, $visited)) {
            return true;
        }

        return $this->detectCycle($dependencyId, $this->id, $visited);
    }

    private function detectCycle($taskId, $dependencyId, &$visited)
    {
        if (isset($visited[$taskId])) {
            return true;
        }

        $visited[$taskId] = true;

        $task = Task::with('dependencies')->find($taskId);

        foreach ($task->dependencies as $dependency) {
            if ($dependency->id == $dependencyId || $this->detectCycle($dependency->id, $dependencyId, $visited)) {
                return true;
            }
        }

        unset($visited[$taskId]);
        return false;
    }

    // Check for due or overdue tasks and send notifications
    public static function checkDueTasks()
    {
        $tasks = self::where('status', '!=', 'completed')
            ->where('due_date', '<=', now())
            ->get();

        foreach ($tasks as $task) {
            Notification::send($task->user, new TaskDueNotification($task));
        }
    }
}
