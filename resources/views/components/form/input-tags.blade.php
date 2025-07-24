@props([
    'label' => null,
    'placeholder' => 'Enter text...',
    'wireModel' => null,
    'tags' => [],
    'selectedTags' => [],
    'showDropdown' => false,
    'onAddTag' => null,
    'onRemoveTag' => null,
    'onToggleDropdown' => null,
    'error' => null,
    'required' => false
])

<div class="space-y-1">
    @if($label)
        <label class="block text-sm font-medium text-gray-700">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <!-- Input with Tags Dropdown -->
    <div class="flex">
        <input 
            type="text" 
            placeholder="{{ $placeholder }}"
            @if($wireModel) wire:model.live="{{ $wireModel }}" @endif
            @if($required) required @endif
            class="flex-1 px-4 py-3 border border-gray-300 rounded-l-lg text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent border-r-0 h-12 {{ $error ? 'border-red-500 focus:ring-red-500' : '' }}"
        >
        
        <!-- Tags Dropdown Button -->
        <div class="relative">
            <button 
                type="button"
                @if($onToggleDropdown) wire:click="{{ $onToggleDropdown }}" @endif
                class="px-4 py-3 text-sm text-gray-700 bg-gray-50 border border-gray-300 rounded-r-lg hover:bg-gray-100 flex items-center space-x-2 border-l-0 h-12"
            >
                <span>🏷️</span>
                <span>{{ count($selectedTags) > 0 ? count($selectedTags) : 'Tags' }}</span>
                <svg class="w-4 h-4 {{ $showDropdown ? 'rotate-180' : '' }} transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            @if($showDropdown)
                <div class="absolute right-0 mt-2 w-64 bg-white border border-gray-200 rounded-lg shadow-lg z-10">
                    <div class="p-2 max-h-48 overflow-y-auto">
                        @forelse($tags as $tag)
                            <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer">
                                <input 
                                    type="checkbox" 
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
                        <button 
                            type="button"
                            @if($onAddTag) wire:click="{{ $onAddTag }}" @endif
                            class="w-full text-left p-2 text-sm text-blue-600 hover:bg-blue-50 rounded flex items-center"
                        >
                            <span class="mr-2">+</span>
                            Add New Tag
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Selected Tags Display -->
    @if(count($selectedTags) > 0)
        <div class="flex flex-wrap gap-1 mt-2">
            @foreach($tags as $tag)
                @if(in_array($tag->id, $selectedTags))
                    <span 
                        class="inline-flex items-center px-2 py-1 rounded text-xs font-medium text-white"
                        style="background-color: {{ $tag->color }}"
                    >
                        {{ $tag->name }}
                        <button 
                            type="button"
                            @if($onRemoveTag) wire:click="{{ $onRemoveTag }}({{ $tag->id }})" @endif
                            class="ml-1 text-white hover:text-gray-200"
                        >
                            ×
                        </button>
                    </span>
                @endif
            @endforeach
        </div>
    @endif
    
    @if($error)
        <p class="text-red-500 text-sm mt-1">{{ $error }}</p>
    @endif
</div>