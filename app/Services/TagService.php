<?php

namespace App\Services;

use App\Models\Tag;
use App\Queries\TagQuery;

class TagService
{
    public function __construct()
    {
        //
    }

    public function createTag(array $data): Tag
    {
        return Tag::create([
            'name' => $data['name'],
            'color' => $data['color'] ?? '#3B82F6',
        ]);
    }

    public function updateTag(int $tagId, array $data): Tag
    {
        $tag = Tag::findOrFail($tagId);
        
        $tag->update([
            'name' => $data['name'] ?? $tag->name,
            'color' => $data['color'] ?? $tag->color,
        ]);

        return $tag->fresh();
    }

    public function deleteTag(int $tagId): bool
    {
        $tag = Tag::findOrFail($tagId);
        
        // Detach from all tasks before deleting
        $tag->tasks()->detach();
        
        return $tag->delete();
    }

    public function getAllTags()
    {
        return TagQuery::getTags();
    }

    public function searchTags(string $search)
    {
        return TagQuery::searchTags($search);
    }

    public function getPopularTags(int $limit = 10)
    {
        return TagQuery::getPopularTags($limit);
    }

    public function getTagsWithTaskCount()
    {
        return TagQuery::getTagsWithTaskCount();
    }

    public function validateTagData(array $data): array
    {
        $rules = [
            'name' => 'required|string|min:2|max:50|unique:tags,name',
            'color' => 'required|regex:/^#[0-9A-F]{6}$/i'
        ];

        return $rules;
    }
}