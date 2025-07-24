<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasPriorityTrait;

class Task extends Model
{
    use HasPriorityTrait;

    protected $fillable = [
        'title',
        'priority',
        'is_completed',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
    ];

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'task_tag');
    }

    public function scopeActive($query)
    {
        return $query->where('is_completed', false);
    }
    
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    public static function baseQuery($completed = null, $priority = null)
    {
        return self::when(!is_null($completed), function($query) use ($completed) {
                return $query->where('is_completed', $completed);
            })
            ->when($priority, function($query, $priority) {
                return $query->where('priority', $priority);
            });
    }
}