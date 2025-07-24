<?php

namespace App\Traits;

trait ValidationHelperTrait
{
    public function getTaskValidationRules(): array
    {
        return [
            'newTaskTitle' => 'required|string|min:3|max:255',
            'newTaskPriority' => 'required|in:low,medium,high',
            'selectedTags' => 'nullable|array',
            'selectedTags.*' => 'exists:tags,id',
        ];
    }

    public function getTagValidationRules(): array
    {
        return [
            'newTagName' => 'required|string|min:2|max:50|unique:tags,name',
            'newTagColor' => 'required|regex:/^#[0-9A-F]{6}$/i',
        ];
    }

    public function getUpdateTaskValidationRules(): array
    {
        return [
            'title' => 'sometimes|required|string|min:3|max:255',
            'priority' => 'sometimes|required|in:low,medium,high',
            'tags' => 'sometimes|nullable|array',
            'tags.*' => 'exists:tags,id',
        ];
    }

    public function validateColorFormat(string $color): bool
    {
        return preg_match('/^#[0-9A-F]{6}$/i', $color) === 1;
    }

    public function sanitizeInput(string $input): string
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}