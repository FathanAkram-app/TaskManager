@props([
    'label' => null,
    'wireModel' => null,
    'wireClick' => null,
    'options' => [],
    'required' => false,
    'error' => null,
    'direction' => 'horizontal', // horizontal or vertical
    'size' => 'base',
    'currentValue' => null
])

@php
    $containerClasses = $direction === 'horizontal' ? 'flex space-x-4' : 'space-y-2';
    
    $sizeClasses = match($size) {
        'sm' => 'w-3 h-3',
        'lg' => 'w-5 h-5',
        default => 'w-4 h-4'
    };
@endphp

<div class="space-y-2">
    @if($label)
        <label class="block text-sm font-medium text-gray-700">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <div class="{{ $containerClasses }}">
        @foreach($options as $value => $optionData)
            @php
                $optionLabel = is_array($optionData) ? $optionData['label'] : $optionData;
                $optionColor = is_array($optionData) ? ($optionData['color'] ?? null) : null;
                $isSelected = $currentValue === $value;
                $clickAction = $wireClick ? str_replace('{value}', $value, $wireClick) : null;
            @endphp
            
            <button
                type="button"
                @if($clickAction) wire:click="{{ $clickAction }}" @endif
                class="flex items-center cursor-pointer focus:outline-none"
            >
                <div class="{{ $sizeClasses }} rounded-full border-2 mr-2 transition-colors {{ $isSelected ? ($optionColor ?? 'bg-blue-500 border-blue-500') : 'border-gray-300' }}"></div>
                
                <span class="text-sm {{ $isSelected ? 'font-medium' : 'text-gray-600' }}">
                    {{ $optionLabel }}
                </span>
            </button>
        @endforeach
    </div>
    
    @if($error)
        <p class="text-red-500 text-sm mt-1">{{ $error }}</p>
    @endif
</div>