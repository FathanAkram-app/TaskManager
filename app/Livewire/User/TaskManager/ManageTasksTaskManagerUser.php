<?php

namespace App\Livewire\User\TaskManager;

use App\Models\Task;
use App\Models\Tag;
use App\Traits\ValidationHelperTrait;
use App\Traits\ModalHandlerTrait;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;

#[Title('Task Manager - Manage Tasks')]
class ManageTasksTaskManagerUser extends Component
{
    use ValidationHelperTrait, ModalHandlerTrait;

    // Public properties
    public $newTaskTitle = '';
    public $newTaskPriority = '';
    public $selectedTags = [];
    
    // Editing properties
    public $editingTaskId = null;
    public $editingTaskTitle = '';
    
    // Tag properties
    public $newTagName = '';
    public $newTagColor = '#3B82F6';
    public $showTagsDropdown = false;

    public function mount()
    {
        $this->resetForm();
    }

    public function hydrate()
    {
        // Called after component is hydrated from session
    }

    public function boot()
    {
        // Called on every request, before any other lifecycle methods
    }

    public function updated($property, $value)
    {
        match($property) {
            'newTaskTitle' => $this->validateTaskTitle(),
            'newTaskPriority' => $this->validateTaskPriority(),
            'selectedTags' => $this->handleTagsUpdate(),
            'newTagName' => $this->validateTagName(),
            'newTagColor' => $this->validateTagColor(),
            default => null
        };
    }

    public function updating($property, $value)
    {
        if ($property === 'newTaskTitle') {
            return trim($value);
        }
        
        if ($property === 'newTagColor' && !str_starts_with($value, '#')) {
            return '#' . $value;
        }
        
        return $value;
    }

    public function rendering()
    {
        $this->clearValidationErrors();
    }

    public function rendered()
    {
        $this->dispatch('component-rendered');
    }

    public function dehydrate()
    {
        if ($this->showAddTagModal || $this->showDeleteModal) {
            $this->dispatch('modal-state-changed');
        }
    }

    // Computed properties
    public function getTasksProperty()
    {
        return Task::with('tags')
            ->active()
            ->latest()
            ->get();
    }

    public function getTagsProperty()
    {
        return Tag::orderBy('name')->get();
    }

    // Task Actions
    public function addTask()
    {
        $this->validate($this->getTaskValidationRules());

        try {
            $task = Task::create([
                'title' => $this->newTaskTitle,
                'priority' => $this->newTaskPriority,
                'is_completed' => false,
            ]);

            if (!empty($this->selectedTags)) {
                $task->tags()->attach($this->selectedTags);
            }

            $this->dispatch('task-created', $task->id);
            $this->resetForm();
        } catch (\Exception $e) {
            $this->addError('newTaskTitle', 'Failed to create task: ' . $e->getMessage());
        }
    }

    public function completeTask($taskId)
    {
        try {
            $task = Task::findOrFail($taskId);
            $task->update(['is_completed' => true]);
            $this->dispatch('task-completed', $taskId);
        } catch (\Exception $e) {
            $this->dispatch('notify-error', 'Failed to complete task: ' . $e->getMessage());
        }
    }

    public function startEditing($taskId, $currentTitle)
    {
        $this->editingTaskId = $taskId;
        $this->editingTaskTitle = $currentTitle;
    }

    public function updateTask()
    {
        $this->validate([
            'editingTaskTitle' => 'required|min:3|max:255',
        ]);

        try {
            $task = Task::findOrFail($this->editingTaskId);
            $task->update(['title' => $this->editingTaskTitle]);
            
            $this->reset(['editingTaskId', 'editingTaskTitle']);
        } catch (\Exception $e) {
            $this->addError('editingTaskTitle', 'Failed to update task: ' . $e->getMessage());
        }
    }

    public function cancelEditing()
    {
        $this->reset(['editingTaskId', 'editingTaskTitle']);
    }

    public function confirmDelete($taskId)
    {
        $this->openDeleteModal($taskId);
    }

    public function deleteTask()
    {
        if ($this->itemToDelete) {
            try {
                $task = Task::findOrFail($this->itemToDelete);
                $task->delete();
                $this->closeDeleteModal();
            } catch (\Exception $e) {
                $this->dispatch('notify-error', 'Failed to delete task: ' . $e->getMessage());
                $this->closeDeleteModal();
            }
        }
    }

    public function cancelDelete()
    {
        $this->closeDeleteModal();
    }

    // Tag Actions
    public function addTag()
    {
        $this->validate($this->getTagValidationRules());

        try {
            $tag = Tag::create([
                'name' => $this->newTagName,
                'color' => $this->newTagColor,
            ]);

            $this->selectedTags[] = $tag->id;
            $this->closeAddTagModal();
            $this->showTagsDropdown = true;
            $this->dispatch('tag-created', $tag->id);
        } catch (\Exception $e) {
            $this->addError('newTagName', 'Failed to create tag: ' . $e->getMessage());
        }
    }

    public function cancelAddTag()
    {
        $this->closeAddTagModal();
    }

    public function removeTag($tagId)
    {
        $this->selectedTags = array_values(array_filter($this->selectedTags, fn($id) => $id != $tagId));
    }

    // Helper methods
    public function canAddTask()
    {
        return !empty($this->newTaskTitle) && !empty($this->newTaskPriority);
    }

    protected function resetForm()
    {
        $this->reset(['newTaskTitle', 'newTaskPriority', 'selectedTags']);
        $this->newTagColor = '#3B82F6';
        $this->showTagsDropdown = false;
    }

    protected function validateTaskTitle()
    {
        if (!empty($this->newTaskTitle)) {
            $this->resetErrorBag('newTaskTitle');
        }
    }

    protected function validateTaskPriority()
    {
        if (!empty($this->newTaskPriority)) {
            $this->resetErrorBag('newTaskPriority');
        }
    }

    protected function handleTagsUpdate()
    {
        if (empty($this->selectedTags)) {
            $this->showTagsDropdown = false;
        }
        
        $this->dispatch('tags-updated', count($this->selectedTags));
    }

    protected function validateTagName()
    {
        if (!empty($this->newTagName)) {
            $this->resetErrorBag('newTagName');
        }
    }

    protected function validateTagColor()
    {
        if (preg_match('/^#[0-9A-F]{6}$/i', $this->newTagColor)) {
            $this->resetErrorBag('newTagColor');
        }
    }

    protected function clearValidationErrors()
    {
        // Clear old validation errors before rendering
    }

    // Event Listeners
    #[On('task-created')]
    public function onTaskCreated($taskId)
    {
        $this->dispatch('notify', 'Task created successfully!');
    }

    #[On('refresh-tasks')]
    public function refreshTasks()
    {
        unset($this->tasks);
    }

    #[On('close-modals')]
    public function closeAllModals()
    {
        $this->showDeleteModal = false;
        $this->showAddTagModal = false;
        $this->showTagsDropdown = false;
    }

    // Exception handling
    public function exception($e, $stopPropagation)
    {
        $this->dispatch('notify-error', 'An error occurred: ' . $e->getMessage());
        $this->resetForm();
        $stopPropagation();
    }

    public function render()
    {
        return view('livewire.user.task-manager.manage-tasks-task-manager-user')
            ->layout('layouts.app');
    }
}