@props([
    'label' => 'Priority',
    'currentValue' => null,
    'wireClick' => null,
    'error' => null,
    'required' => false
])

@php
    $priorities = [
        'low' => [
            'label' => 'Low',
            'color' => 'bg-green-500 border-green-500',
            'textColor' => 'text-green-700'
        ],
        'medium' => [
            'label' => 'Medium', 
            'color' => 'bg-yellow-500 border-yellow-500',
            'textColor' => 'text-yellow-700'
        ],
        'high' => [
            'label' => 'High',
            'color' => 'bg-red-500 border-red-500', 
            'textColor' => 'text-red-700'
        ]
    ];
@endphp

<div class="space-y-3">
    @if($label)
        <label class="block text-sm font-medium text-gray-700 text-center">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <div class="flex justify-center space-x-4">
        @foreach($priorities as $value => $priority)
            @php
                $isSelected = $currentValue === $value;
                $clickAction = $wireClick ? str_replace('{value}', $value, $wireClick) : null;
            @endphp
            
            <button 
                type="button"
                @if($clickAction) wire:click="{{ $clickAction }}" @endif
                class="flex items-center px-4 py-2 rounded-lg transition-all duration-200 hover:shadow-md cursor-pointer focus:outline-none {{ $isSelected ? $priority['color'] . ' text-white shadow-lg transform scale-105' : 'bg-gray-100 hover:bg-gray-200 text-gray-700' }}"
            >
                <div class="w-3 h-3 rounded-full mr-2 {{ $isSelected ? 'bg-white' : $priority['color'] }}"></div>
                <span class="text-sm font-medium">
                    {{ $priority['label'] }}
                </span>
            </button>
        @endforeach
    </div>
    
    @if($error)
        <p class="text-red-500 text-sm mt-1">{{ $error }}</p>
    @endif
</div>