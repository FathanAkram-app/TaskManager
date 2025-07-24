<?php

namespace App\Traits;

trait ModalHandlerTrait
{
    public $showDeleteModal = false;
    public $showAddTagModal = false;
    public $itemToDelete = null;
    public $taskToDelete = null;

    public function openDeleteModal($itemId)
    {
        $this->itemToDelete = $itemId;
        $this->taskToDelete = $itemId; // For backward compatibility
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->reset(['itemToDelete', 'taskToDelete', 'showDeleteModal']);
    }

    public function openAddTagModal()
    {
        $this->showAddTagModal = true;
        $this->resetTagForm();
    }

    public function closeAddTagModal()
    {
        $this->showAddTagModal = false;
        $this->resetTagForm();
    }

    protected function resetTagForm()
    {
        $this->newTagName = '';
        $this->newTagColor = '#3B82F6';
    }

    public function resetModals()
    {
        $this->reset([
            'showDeleteModal',
            'showAddTagModal',
            'itemToDelete'
        ]);
    }
}