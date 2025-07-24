@props([
    'show' => false,
    'title' => null,
    'maxWidth' => 'md',
    'closeable' => true,
    'onClose' => null
])

@php
    $maxWidthClasses = match($maxWidth) {
        'xs' => 'max-w-xs',
        'sm' => 'max-w-sm',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        '3xl' => 'max-w-3xl',
        '4xl' => 'max-w-4xl',
        '5xl' => 'max-w-5xl',
        '6xl' => 'max-w-6xl',
        '7xl' => 'max-w-7xl',
        default => 'max-w-md'
    };
@endphp

@if($show)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-xl {{ $maxWidthClasses }} w-full">
            @if($title || $closeable)
                <div class="flex items-center justify-between p-6 border-b border-gray-200">
                    @if($title)
                        <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
                    @endif
                    
                    @if($closeable)
                        <button 
                            type="button"
                            @if($onClose) wire:click="{{ $onClose }}" @endif
                            class="text-gray-400 hover:text-gray-600 focus:outline-none"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    @endif
                </div>
            @endif
            
            <div class="p-6">
                {{ $slot }}
            </div>
        </div>
    </div>
@endif