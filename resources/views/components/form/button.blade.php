@props([
    'variant' => 'primary',
    'size' => 'base',
    'type' => 'button',
    'disabled' => false,
    'wireClick' => null,
    'loading' => false,
    'icon' => null,
    'iconPosition' => 'left'
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';
    
    $variantClasses = match($variant) {
        'primary' => 'bg-blue-600 hover:bg-blue-700 text-white focus:ring-blue-500',
        'secondary' => 'bg-gray-200 hover:bg-gray-300 text-gray-700 focus:ring-gray-500',
        'danger' => 'bg-red-600 hover:bg-red-700 text-white focus:ring-red-500',
        'success' => 'bg-green-600 hover:bg-green-700 text-white focus:ring-green-500',
        'warning' => 'bg-yellow-600 hover:bg-yellow-700 text-white focus:ring-yellow-500',
        'ghost' => 'bg-transparent hover:bg-gray-100 text-gray-700 focus:ring-gray-500',
        'outline' => 'border-2 border-gray-300 hover:border-gray-400 bg-white text-gray-700 focus:ring-gray-500',
        default => 'bg-blue-600 hover:bg-blue-700 text-white focus:ring-blue-500'
    };
    
    $sizeClasses = match($size) {
        'xs' => 'px-2 py-1 text-xs rounded',
        'sm' => 'px-3 py-2 text-sm rounded-md',
        'lg' => 'px-6 py-3 text-lg rounded-lg',
        'xl' => 'px-8 py-4 text-xl rounded-lg',
        default => 'px-4 py-2 text-sm rounded-lg'
    };
    
    $buttonClasses = "{$baseClasses} {$variantClasses} {$sizeClasses}";
    
    if ($disabled) {
        $buttonClasses .= ' cursor-not-allowed opacity-50';
    }
@endphp

<button
    type="{{ $type }}"
    @if($wireClick) wire:click="{{ $wireClick }}" @endif
    @if($disabled) disabled @endif
    {{ $attributes->merge(['class' => $buttonClasses]) }}
>
    @if($loading)
        <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Loading...
    @else
        @if($icon && $iconPosition === 'left')
            <span class="mr-2">{!! $icon !!}</span>
        @endif
        
        {{ $slot }}
        
        @if($icon && $iconPosition === 'right')
            <span class="ml-2">{!! $icon !!}</span>
        @endif
    @endif
</button>