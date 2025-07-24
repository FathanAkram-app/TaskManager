<?php

namespace App\Services;

use App\Models\Task;
use App\Queries\TaskQuery;

class TaskService
{
    public function __construct()
    {
        //
    }

    public function createTask(array $data): Task
    {
        $task = Task::create([
            'title' => $data['title'],
            'priority' => $data['priority'],
            'is_completed' => false,
        ]);

        if (!empty($data['tags'])) {
            $task->tags()->sync($data['tags']);
        }

        return $task;
    }

    public function updateTask(int $taskId, array $data): Task
    {
        $task = Task::findOrFail($taskId);
        
        $task->update([
            'title' => $data['title'] ?? $task->title,
            'priority' => $data['priority'] ?? $task->priority,
        ]);

        if (isset($data['tags'])) {
            $task->tags()->sync($data['tags']);
        }

        return $task->fresh();
    }

    public function completeTask(int $taskId): Task
    {
        $task = Task::findOrFail($taskId);
        $task->update(['is_completed' => true]);
        
        return $task;
    }

    public function deleteTask(int $taskId): bool
    {
        $task = Task::findOrFail($taskId);
        
        // Detach all tags before deleting
        $task->tags()->detach();
        
        return $task->delete();
    }

    public function getActiveTasks()
    {
        return TaskQuery::getActiveTasks();
    }

    public function getTasksByPriority(string $priority)
    {
        return TaskQuery::getTasksByPriority($priority);
    }

    public function getTaskStats(): array
    {
        return [
            'total_active' => TaskQuery::countTasks(false),
            'total_completed' => TaskQuery::countTasks(true),
            'by_priority' => TaskQuery::countActiveTasksByPriority(),
        ];
    }
}