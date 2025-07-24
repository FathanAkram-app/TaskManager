# 🔄 Livewire Lifecycle & Task Manager Guide

A comprehensive guide to understanding Livewire lifecycle methods and the Task Manager application architecture.

## 🏗️ Application Overview

**Tech Stack:** Laravel 11 + Livewire 3 + Tailwind CSS + MySQL  
**Architecture:** TALL Stack (Tailwind, Alpine.js, Laravel, Livewire)  
**Pattern:** Component-based reactive UI without JavaScript

## 🔄 Complete Livewire Lifecycle Explained

### 1. 🚀 Component Initialization
```php
public function mount()
{
    // 🎯 WHEN: Component is first loaded/instantiated
    // 🎯 PURPOSE: Initialize component state, set default values
    // 🎯 RUNS: Once per component lifecycle
    
    $this->resetForm(); // Set initial form state
}
```

### 2. 💧 Hydration Process
```php
public function hydrate()
{
    // 🎯 WHEN: After component is restored from session
    // 🎯 PURPOSE: Reconnect services, restore state
    // 🎯 RUNS: On every request after session restore
    
    // Example: Reconnect to external APIs
    // Example: Restore computed properties
}

public function boot()
{
    // 🎯 WHEN: Every request, before other methods
    // 🎯 PURPOSE: Set up global state, initialize services
    // 🎯 RUNS: First method called on every request
    
    // Example: Set up authentication context
    // Example: Load configuration
}
```

### 3. ⚡ Property Update Lifecycle
```php
public function updating($property, $value)
{
    // 🎯 WHEN: BEFORE property is updated (interceptor)
    // 🎯 PURPOSE: Sanitize input, validate, transform data
    // 🎯 RETURN: Modified value or original value
    // 🎯 CAN: Prevent update by returning different value
    
    if ($property === 'newTaskTitle') {
        return trim($value); // Clean whitespace
    }
    
    if ($property === 'newTagColor' && !str_starts_with($value, '#')) {
        return '#' . $value; // Ensure hex format
    }
    
    return $value;
}

public function updated($property, $value)
{
    // 🎯 WHEN: AFTER property is updated (reactive)
    // 🎯 PURPOSE: Side effects, validation, trigger updates
    // 🎯 RETURN: void (property already changed)
    // 🎯 CANNOT: Change the property value
    
    match($property) {
        'newTaskTitle' => $this->validateTaskTitle(),
        'newTaskPriority' => $this->validateTaskPriority(),
        'selectedTags' => $this->handleTagsUpdate(),
        default => null
    };
}
```

### 4. 🎨 Rendering Process
```php
public function rendering()
{
    // 🎯 WHEN: Before render() method is called
    // 🎯 PURPOSE: Prepare data for view, cleanup temporary state
    // 🎯 RUNS: Every time component needs to re-render
    
    $this->clearValidationErrors();
}

public function rendered()
{
    // 🎯 WHEN: After HTML is generated and sent to browser
    // 🎯 PURPOSE: JavaScript interactions, DOM manipulation
    // 🎯 RUNS: After each render cycle
    
    $this->dispatch('component-rendered');
}
```

### 5. 🏪 Dehydration Process
```php
public function dehydrate()
{
    // 🎯 WHEN: Before component state is saved to session
    // 🎯 PURPOSE: Clean sensitive data, prepare for storage
    // 🎯 RUNS: At end of request before session storage
    
    if ($this->showAddTagModal || $this->showDeleteModal) {
        $this->dispatch('modal-state-changed');
    }
}
```

### 6. ⚠️ Exception Handling
```php
public function exception($e, $stopPropagation)
{
    // 🎯 WHEN: Any component method throws exception
    // 🎯 PURPOSE: Graceful error handling, user feedback
    // 🎯 RUNS: When errors occur in component methods
    
    $this->dispatch('notify-error', 'An error occurred: ' . $e->getMessage());
    $this->resetForm(); // Reset to safe state
    $stopPropagation(); // Don't let Laravel handle it
}
```

## 📊 Lifecycle Flow Diagram
```
Request Start
     ↓
[boot()] ← Called first, every request
     ↓
[hydrate()] ← Restore from session
     ↓
[updating()] ← Before property changes
     ↓
Property Update
     ↓
[updated()] ← After property changes
     ↓
[rendering()] ← Before view render
     ↓
[render()] ← Generate HTML
     ↓
[rendered()] ← After HTML sent
     ↓
[dehydrate()] ← Before session save
     ↓
Request End
```

## 🎯 Component Properties Deep Dive

### Public Properties (Reactive State)
```php
// 🔄 REACTIVE: Changes automatically update UI
public $newTaskTitle = '';           // ← Bound to input field
public $newTaskPriority = '';        // ← Bound to radio buttons  
public $selectedTags = [];           // ← Bound to checkboxes
public $editingTaskId = null;        // ← Controls edit mode
public $showTagsDropdown = false;    // ← Controls dropdown visibility

// 🎯 WIRE MODEL BINDING:
// wire:model="newTaskTitle" ← Two-way binding
// wire:model.live="selectedTags" ← Instant updates
// wire:model.lazy="newTaskTitle" ← Update on blur
```

### Computed Properties (Dynamic Data)
```php
public function getTasksProperty()
{
    // 🎯 ACCESS: $this->tasks in PHP, $tasks in Blade
    // 🎯 CACHING: Cached until component re-renders
    // 🎯 REFRESH: Auto-refreshes when dependencies change
    
    return Task::with('tags')    // ← Eager load relationships
        ->active()               // ← Only incomplete tasks
        ->latest()              // ← Newest first
        ->get();
}

// 🔄 USAGE IN BLADE:
// @foreach($this->tasks as $task) ← Access computed property
// {{ count($this->tasks) }} ← Use in expressions
```

## 🎬 Action Methods Explained

### Task Management Flow
```php
public function addTask()
{
    // 🎯 TRIGGER: wire:click="addTask"
    // 🎯 FLOW: Validate → Create → Attach → Reset → Notify
    
    // 1️⃣ VALIDATION
    $this->validate($this->getTaskValidationRules());

    try {
        // 2️⃣ CREATE TASK
        $task = Task::create([
            'title' => $this->newTaskTitle,
            'priority' => $this->newTaskPriority,
            'is_completed' => false,
        ]);

        // 3️⃣ ATTACH TAGS (Many-to-Many)
        if (!empty($this->selectedTags)) {
            $task->tags()->attach($this->selectedTags);
        }

        // 4️⃣ SUCCESS FEEDBACK
        $this->dispatch('task-created', $task->id);
        
        // 5️⃣ RESET FORM
        $this->resetForm();
        
    } catch (\Exception $e) {
        // 6️⃣ ERROR HANDLING
        $this->addError('newTaskTitle', 'Failed to create task');
    }
}
```

### Inline Editing System
```php
public function startEditing($taskId, $currentTitle)
{
    // 🎯 TRIGGER: Click on task title
    $this->editingTaskId = $taskId;      // ← Mark which task
    $this->editingTaskTitle = $currentTitle; // ← Pre-fill value
}

public function updateTask()
{
    // 🎯 TRIGGER: Press Enter or click save
    // 🎯 KEYS: wire:keydown.enter="updateTask"
    
    $this->validate(['editingTaskTitle' => 'required|min:3|max:255']);
    
    $task = Task::findOrFail($this->editingTaskId);
    $task->update(['title' => $this->editingTaskTitle]);
    
    // Exit edit mode
    $this->reset(['editingTaskId', 'editingTaskTitle']);
}

public function cancelEditing()
{
    // 🎯 TRIGGER: Press Escape or click cancel
    // 🎯 KEYS: wire:keydown.escape="cancelEditing"
    
    $this->reset(['editingTaskId', 'editingTaskTitle']);
}
```

## 🎨 Blade Template Integration

### Property Binding Examples
```blade
{{-- ✅ REACTIVE INPUT BINDING --}}
<x-form.input
    wireModel="newTaskTitle"           {{-- Two-way binding --}}
    placeholder="What needs to be done?"
    :error="$errors->first('newTaskTitle')"
/>

{{-- ✅ LIVE CHECKBOX BINDING --}}
<input 
    type="checkbox" 
    wire:model.live="selectedTags"     {{-- Instant updates --}}
    value="{{ $tag->id }}"
/>

{{-- ✅ CONDITIONAL RENDERING --}}
@if($editingTaskId === $task->id)
    {{-- Edit Mode --}}
    <x-form.input 
        wireModel="editingTaskTitle"
        wire:keydown.enter="updateTask"
        wire:keydown.escape="cancelEditing"
    />
@else
    {{-- Display Mode --}}
    <h3 wire:click="startEditing({{ $task->id }}, '{{ addslashes($task->title) }}')">
        {{ $task->title }}
    </h3>
@endif
```

### Event Handling Patterns
```blade
{{-- ✅ METHOD CALLS --}}
<button wire:click="addTask">Add Task</button>
<button wire:click="completeTask({{ $task->id }})">Complete</button>
<button wire:click="confirmDelete({{ $task->id }})">Delete</button>

{{-- ✅ KEYBOARD EVENTS --}}
<input 
    wire:keydown.enter="updateTask"     {{-- Save on Enter --}}
    wire:keydown.escape="cancelEditing" {{-- Cancel on Escape --}}
/>

{{-- ✅ PROPERTY MANIPULATION --}}
<button wire:click="$set('newTaskPriority', 'high')">High Priority</button>
<button wire:click="$toggle('showTagsDropdown')">Toggle Dropdown</button>
<button wire:click="$refresh">Refresh Component</button>
```

### Loop Optimization
```blade
{{-- ✅ OPTIMIZED LOOPS --}}
@forelse($this->tasks as $task)
    <div wire:key="task-{{ $task->id }}"> {{-- Important for updates --}}
        <h3>{{ $task->title }}</h3>
        
        {{-- Tags relationship --}}
        @foreach($task->tags as $tag)
            <span 
                wire:key="tag-{{ $tag->id }}-task-{{ $task->id }}"
                style="background-color: {{ $tag->color }}"
            >
                {{ $tag->name }}
            </span>
        @endforeach
    </div>
@empty
    <div class="text-center py-8">
        <p>No tasks yet! Add your first task above.</p>
    </div>
@endforelse
```

## 🔧 Advanced Patterns

### Event System
```php
// 📤 DISPATCHING EVENTS
$this->dispatch('task-created', $task->id);        // To browser
$this->dispatch('notify', 'Success message');      // To other components
$this->dispatchTo('OtherComponent', 'refresh');    // To specific component

// 📥 LISTENING TO EVENTS
#[On('task-created')]
public function onTaskCreated($taskId)
{
    $this->dispatch('notify', 'Task created successfully!');
}

#[On('refresh-tasks')]
public function refreshTasks()
{
    unset($this->tasks); // Force recompute computed property
}
```

### Validation Patterns
```php
// 🔍 REAL-TIME VALIDATION
protected function validateTaskTitle()
{
    if (!empty($this->newTaskTitle)) {
        $this->resetErrorBag('newTaskTitle'); // Clear errors as user types
    }
}

// 📋 VALIDATION RULES
public function getTaskValidationRules(): array
{
    return [
        'newTaskTitle' => 'required|string|min:3|max:255',
        'newTaskPriority' => 'required|in:low,medium,high',
        'selectedTags' => 'nullable|array',
        'selectedTags.*' => 'exists:tags,id',
    ];
}

// ⚠️ ERROR HANDLING
try {
    $this->validate($rules);
    // ... success logic
} catch (ValidationException $e) {
    // Livewire handles this automatically
} catch (\Exception $e) {
    $this->addError('field', 'Custom error message');
}
```

### Performance Optimization
```php
// ✅ EFFICIENT UPDATES
$this->reset(['editingTaskId', 'editingTaskTitle']); // Only specific props
unset($this->tasks); // Force recompute computed property

// ✅ LAZY LOADING
public function loadTasks()
{
    // Load heavy data only when needed
    $this->tasks = Task::with('tags')->get();
}

// ✅ CONDITIONAL RENDERING
@if($this->showExpensiveComponent)
    @livewire('expensive-component')
@endif
```

## 🗄️ Database Integration

### Eloquent Relationships
```php
// 📋 TASK MODEL
class Task extends Model
{
    protected $fillable = ['title', 'priority', 'is_completed'];
    protected $casts = ['is_completed' => 'boolean'];
    
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'task_tag');
    }
    
    public function scopeActive($query)
    {
        return $query->where('is_completed', false);
    }
}

// 🏷️ TAG MODEL
class Tag extends Model
{
    protected $fillable = ['name', 'color'];
    
    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_tag');
    }
}
```

### Database Operations
```php
// ✅ CREATE WITH RELATIONSHIPS
$task = Task::create([
    'title' => $this->newTaskTitle,
    'priority' => $this->newTaskPriority,
]);
$task->tags()->attach($this->selectedTags); // Many-to-many

// ✅ QUERY WITH RELATIONSHIPS
Task::with('tags')              // Eager load to prevent N+1
    ->active()                  // Use scopes
    ->latest()                  // Order by created_at desc
    ->get();

// ✅ UPDATE RELATIONSHIPS
$task->tags()->sync($newTagIds);    // Replace all tags
$task->tags()->detach($tagId);      // Remove specific tag
$task->tags()->attach($tagId);      // Add specific tag
```

## 🎯 Best Practices Summary

### ✅ DO
- Use `wire:key` in loops for performance
- Validate all user input
- Handle exceptions gracefully  
- Use computed properties for dynamic data
- Reset forms after successful operations
- Use specific property resets over full component reset

### ❌ DON'T
- Put heavy logic in `updated()` methods
- Forget `wire:key` in dynamic lists
- Use computed properties (`getXProperty`) for expensive operations
- Neglect error handling in try-catch blocks
- Use public properties for sensitive data

### 🎯 Performance Tips
- Use `.lazy` modifier for non-critical inputs
- Use `.live` only when immediate feedback needed
- Implement loading states for slow operations
- Cache expensive computed properties
- Use database indexes for frequently queried columns

This guide provides complete understanding of Livewire lifecycle methods and how they power the Task Manager application's reactive features.