<div class="min-h-screen bg-gray-50 p-4">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">📝 Task Manager</h1>
            <p class="text-gray-600">Simple & Beautiful Task Management</p>
        </div>

        <!-- Add Task Card -->
        <div class="bg-white rounded-lg shadow-md border border-gray-200 p-4 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4 text-center">✨ Add New Task</h2>
        
        <!-- Task Input with Tags -->
        <div class="mb-4">
            <div class="space-y-3">
                
                <!-- Input with Tags Dropdown -->
                <div class="flex justify-center items-center space-x-2">
                    <div class="flex">
                        <x-form.input
                            wireModel="newTaskTitle"
                            placeholder="What needs to be done?"
                            class="rounded-r-none border-r-0"
                            :error="null"
                        />
                        
                        <!-- Tags Dropdown Button -->
                        <div class="relative">
                            <button 
                                type="button"
                                wire:click="$toggle('showTagsDropdown')"
                                class="px-4 py-3 text-sm text-gray-700 bg-gray-50 border border-gray-300 rounded-r-lg hover:bg-gray-100 flex items-center space-x-2 border-l-0 h-12"
                            >
                                <span>🏷️</span>
                                <span>{{ count($selectedTags) > 0 ? count($selectedTags) : 'Tags' }}</span>
                                <svg class="w-4 h-4 {{ $showTagsDropdown ? 'rotate-180' : '' }} transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            

                            @if($showTagsDropdown)
                                <div class="absolute right-0 mt-2 w-64 bg-white border border-gray-200 rounded-lg shadow-lg z-10">
                                    <div class="p-2 max-h-48 overflow-y-auto">
                                        @forelse($this->tags as $tag)
                                            <label for="tag_{{ $tag->id }}" class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer">
                                                <input 
                                                    type="checkbox" 
                                                    id="tag_{{ $tag->id }}"
                                                    name="selectedTags[]"
                                                    value="{{ $tag->id }}"
                                                    wire:model.live="selectedTags"
                                                    class="w-4 h-4 text-blue-600 rounded border-gray-300"
                                                >
                                                <div class="w-3 h-3 rounded-full ml-3 mr-2" style="background-color: {{ $tag->color }}"></div>
                                                <span class="text-sm text-gray-700">{{ $tag->name }}</span>
                                            </label>
                                        @empty
                                            <p class="text-sm text-gray-500 p-2">No tags yet</p>
                                        @endforelse
                                    </div>
                                    <div class="border-t border-gray-200 p-2">
                                        <x-form.button
                                            wireClick="openAddTagModal"
                                            variant="ghost"
                                            size="sm"
                                            class="w-full justify-start"
                                        >
                                            <span class="mr-2">+</span>
                                            Add New Tag
                                        </x-form.button>
                                    </div>
                                </div>
                            @endif
                            
                        </div>
                        <x-form.button
                            wireClick="addTask"
                            :variant="$this->canAddTask() ? 'success' : 'secondary'"
                            :disabled="!$this->canAddTask()"
                            class="ml-2 shadow-lg"
                            size="base"
                        >
                            ✨ Add Task
                        </x-form.button>
                    </div>
                </div>
                
                <!-- Selected Tags Display -->
                @if(count($selectedTags) > 0)
                    <div class="flex flex-wrap justify-center gap-1 mt-3">
                        @foreach($this->tags as $tag)
                            @if(in_array($tag->id, $selectedTags))
                                <span 
                                    class="inline-flex items-center px-2 py-1 rounded text-xs font-medium text-white"
                                    style="background-color: {{ $tag->color }}"
                                >
                                    {{ $tag->name }}
                                    <button 
                                        type="button"
                                        wire:click="removeTag({{ $tag->id }})"
                                        class="ml-1 text-white hover:text-gray-200"
                                    >
                                        ×
                                    </button>
                                </span>
                            @endif
                        @endforeach
                    </div>
                @endif
                
                @error('newTaskTitle') 
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p> 
                @enderror
                
            </div>
            
        </div>
        

        <!-- Priority & Actions Row -->
        <div class="flex flex-col items-center gap-4">
            <!-- Priority Selection -->
            <x-form.priority-selector
                :current-value="$newTaskPriority"
                wireClick="$set('newTaskPriority', '{value}')"
                :error="$errors->first('newTaskPriority')"
                label=""
                required
            />
        </div>
    </div>

        <!-- Tasks List -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-3 border-b border-gray-200">
                <h2 class="text-base font-medium text-gray-900">Active Tasks ({{ count($this->tasks) }})</h2>
            </div>

            <div class="divide-y divide-gray-200">
                @forelse($this->tasks as $task)
                    <div wire:key="task-{{ $task->id }}" class="p-3 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start space-x-4">
                            <!-- Checkbox -->
                            <button 
                                wire:click="completeTask({{ $task->id }})"
                                class="mt-1 w-5 h-5 border-2 border-gray-300 rounded hover:border-green-500 hover:bg-green-50 flex items-center justify-center group transition-colors"
                            >
                                <svg class="w-3 h-3 text-green-600 opacity-0 group-hover:opacity-100" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </button>

                            <!-- Task Content -->
                            <div class="flex-1 min-w-0">
                                <!-- Priority & Title -->
                                <div class="flex items-center space-x-3 mb-2">
                                    <div class="w-2 h-2 rounded-full {{ $task->priority_color }}"></div>
                                    <span class="text-xs font-medium {{ $task->priority_text_color }} uppercase tracking-wide">{{ $task->priority }}</span>
                                </div>

                                <!-- Task Title (Editable) -->
                                @if($editingTaskId === $task->id)
                                    <div class="flex items-center space-x-2 mb-2">
                                        <x-form.input
                                            wireModel="editingTaskTitle"
                                            class="flex-1"
                                            wire:keydown.enter="updateTask"
                                            wire:keydown.escape="cancelEditing"
                                            autofocus
                                        />
                                        <x-form.button
                                            wireClick="updateTask"
                                            variant="success"
                                            size="xs"
                                        >
                                            ✓
                                        </x-form.button>
                                        <x-form.button
                                            wireClick="cancelEditing"
                                            variant="ghost"
                                            size="xs"
                                        >
                                            ✕
                                        </x-form.button>
                                    </div>
                                @else
                                    <h3 class="text-gray-900 font-medium mb-2 cursor-pointer hover:text-blue-600" wire:click="startEditing({{ $task->id }}, '{{ addslashes($task->title) }}')">
                                        {{ $task->title }}
                                    </h3>
                                @endif

                                <!-- Tags -->
                                @if($task->tags->count() > 0)
                                    <div class="flex flex-wrap gap-1 mb-2">
                                        @foreach($task->tags as $tag)
                                            <span 
                                                class="inline-flex items-center px-2 py-1 rounded text-xs font-medium text-white"
                                                style="background-color: {{ $tag->color }}"
                                            >
                                                {{ $tag->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Date -->
                                <p class="text-xs text-gray-500">
                                    Created {{ $task->created_at->diffForHumans() }}
                                </p>
                            </div>

                            <!-- Actions -->
                            <div class="flex space-x-1">
                                <button 
                                    wire:click="startEditing({{ $task->id }}, '{{ addslashes($task->title) }}')"
                                    class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors"
                                    title="Edit"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <button 
                                    wire:click="confirmDelete({{ $task->id }})"
                                    class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition-colors"
                                    title="Delete"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <div class="text-6xl mb-4">📝</div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No tasks yet</h3>
                        <p class="text-gray-500">Add your first task above to get started!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <x-ui.modal
        :show="$showDeleteModal"
        title="Delete Task?"
        max-width="sm"
        on-close="cancelDelete"
    >
        <div class="text-center">
            <div class="text-4xl mb-4">⚠️</div>
            <p class="text-gray-600 mb-6">This action cannot be undone.</p>
            
            <div class="flex space-x-3">
                <x-form.button
                    wireClick="cancelDelete"
                    variant="ghost"
                    class="flex-1"
                >
                    Cancel
                </x-form.button>
                <x-form.button
                    wireClick="deleteTask"
                    variant="danger"
                    class="flex-1"
                >
                    Delete
                </x-form.button>
            </div>
        </div>
    </x-ui.modal>

    <!-- Add Tag Modal -->
    <x-ui.modal
        :show="$showAddTagModal"
        title="Add New Tag"
        max-width="sm"
        on-close="cancelAddTag"
    >
        <div class="space-y-4">
            <x-form.input
                label="Tag Name"
                placeholder="Enter tag name"
                wireModel="newTagName"
                :error="$errors->first('newTagName')"
                required
            />
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                <div class="flex items-center space-x-3">
                    <input 
                        type="color" 
                        id="newTagColor"
                        name="newTagColor"
                        wire:model="newTagColor"
                        class="w-10 h-10 border border-gray-300 rounded cursor-pointer"
                    >
                    <x-form.input
                        wireModel="newTagColor"
                        placeholder="#3B82F6"
                        :error="$errors->first('newTagColor')"
                        class="flex-1"
                    />
                </div>
            </div>
        </div>
        
        <div class="flex space-x-3 mt-6">
            <x-form.button
                wireClick="cancelAddTag"
                variant="ghost"
                class="flex-1"
            >
                Cancel
            </x-form.button>
            <x-form.button
                wireClick="addTag"
                variant="primary"
                class="flex-1"
            >
                Add Tag
            </x-form.button>
        </div>
    </x-ui.modal>
</div>
