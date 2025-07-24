<?php

namespace App\Queries;

use App\Models\Task;

class TaskQuery
{
    public static function getActiveTasks()
    {
        return Task::baseQuery(false)
            ->with('tags')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public static function getCompletedTasks()
    {
        return Task::baseQuery(true)
            ->with('tags')
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    public static function paginateTasks($perPage = 15, $completed = null)
    {
        return Task::baseQuery($completed)
            ->with('tags')
            ->latest()
            ->paginate($perPage);
    }

    public static function fetchTaskDetail($taskId)
    {
        return Task::baseQuery()
            ->with(['tags'])
            ->findOrFail($taskId);
    }

    public static function getTasksByPriority($priority)
    {
        return Task::baseQuery(false, $priority)
            ->with('tags')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public static function countActiveTasksByPriority()
    {
        return Task::baseQuery(false)
            ->selectRaw('priority, COUNT(*) as count')
            ->groupBy('priority')
            ->pluck('count', 'priority')
            ->toArray();
    }

    public static function countTasks($completed = null)
    {
        return Task::baseQuery($completed)->count();
    }
}