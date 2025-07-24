<?php

namespace App\Traits;

trait HasPriorityTrait
{
    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'low' => 'bg-green-500',
            'medium' => 'bg-yellow-500',
            'high' => 'bg-red-500',
            default => 'bg-gray-500'
        };
    }

    public function getPriorityTextColorAttribute(): string
    {
        return match($this->priority) {
            'low' => 'text-green-700',
            'medium' => 'text-yellow-700',
            'high' => 'text-red-700',
            default => 'text-gray-700'
        };
    }

    public function getPriorityLevelAttribute(): int
    {
        return match($this->priority) {
            'low' => 1,
            'medium' => 2,
            'high' => 3,
            default => 0
        };
    }

    public function scopeByPriorityLevel($query, int $level)
    {
        $priority = match($level) {
            1 => 'low',
            2 => 'medium',
            3 => 'high',
            default => null
        };

        return $priority ? $query->where('priority', $priority) : $query;
    }

    public static function getPriorityOptions(): array
    {
        return [
            'low' => 'Low Priority',
            'medium' => 'Medium Priority',
            'high' => 'High Priority',
        ];
    }
}