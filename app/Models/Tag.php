<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
        'name',
        'color',
    ];

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_tag');
    }

    public static function baseQuery($search = null)
    {
        return self::when($search, function($query, $search) {
            return $query->where('name', 'like', '%' . $search . '%');
        });
    }
}