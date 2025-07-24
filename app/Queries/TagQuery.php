<?php

namespace App\Queries;

use App\Models\Tag;

class TagQuery
{
    public static function getTags($search = null)
    {
        return Tag::baseQuery($search)
            ->orderBy('name')
            ->get();
    }

    public static function paginateTags($perPage = 15, $search = null)
    {
        return Tag::baseQuery($search)
            ->withCount('tasks')
            ->orderBy('name')
            ->paginate($perPage);
    }

    public static function fetchTagDetail($tagId)
    {
        return Tag::baseQuery()
            ->with(['tasks'])
            ->findOrFail($tagId);
    }

    public static function getPopularTags($limit = 10)
    {
        return Tag::withCount('tasks')
            ->orderBy('tasks_count', 'desc')
            ->limit($limit)
            ->get();
    }

    public static function getTagsWithTaskCount()
    {
        return Tag::withCount(['tasks' => function ($query) {
                $query->where('is_completed', false);
            }])
            ->orderBy('name')
            ->get();
    }

    public static function countTags()
    {
        return Tag::count();
    }

    public static function searchTags($search)
    {
        return Tag::baseQuery($search)
            ->limit(5)
            ->get();
    }
}