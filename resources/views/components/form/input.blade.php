@props([
    'label' => null,
    'type' => 'text',
    'placeholder' => '',
    'wireModel' => null,
    'required' => false,
    'error' => null,
    'rounded' => 'rounded-lg',
    'size' => 'base',
    'id' => null,
    'name' => null
])

@php
    $sizeClasses = match($size) {
        'sm' => 'px-3 py-2 text-sm',
        'lg' => 'px-5 py-4 text-lg',
        default => 'px-4 py-3'
    };
    
    $inputClasses = "w-full {$sizeClasses} border border-gray-300 {$rounded} text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent h-12";
    
    if ($error) {
        $inputClasses .= ' border-red-500 focus:ring-red-500';
    }
    
    // Generate unique ID if not provided
    $inputId = $id ?: ($wireModel ? $wireModel : uniqid('input_'));
    $inputName = $name ?: $wireModel;
@endphp

<div class="space-y-1">
    @if($label)
        <label for="{{ $inputId }}" class="block text-sm font-medium text-gray-700">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <input
        type="{{ $type }}"
        id="{{ $inputId }}"
        name="{{ $inputName }}"
        placeholder="{{ $placeholder }}"
        @if($wireModel) wire:model.live="{{ $wireModel }}" @endif
        @if($required) required @endif
        {{ $attributes->merge(['class' => $inputClasses]) }}
    >
    
    @if($error)
        <p class="text-red-500 text-sm mt-1">{{ $error }}</p>
    @endif
</div>