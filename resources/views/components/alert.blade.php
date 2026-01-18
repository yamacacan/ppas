<!-- Alert Component -->
@props([
    'type' => 'info',
    'dismissible' => true,
    'icon' => true
])

@php
    $types = [
        'success' => [
            'bg' => 'bg-green-50 dark:bg-green-900/20',
            'border' => 'border-green-200 dark:border-green-800',
            'text' => 'text-green-800 dark:text-green-200',
            'icon' => 'fa-check-circle',
            'icon_color' => 'text-green-600 dark:text-green-400'
        ],
        'error' => [
            'bg' => 'bg-red-50 dark:bg-red-900/20',
            'border' => 'border-red-200 dark:border-red-800',
            'text' => 'text-red-800 dark:text-red-200',
            'icon' => 'fa-exclamation-circle',
            'icon_color' => 'text-red-600 dark:text-red-400'
        ],
        'warning' => [
            'bg' => 'bg-yellow-50 dark:bg-yellow-900/20',
            'border' => 'border-yellow-200 dark:border-yellow-800',
            'text' => 'text-yellow-800 dark:text-yellow-200',
            'icon' => 'fa-exclamation-triangle',
            'icon_color' => 'text-yellow-600 dark:text-yellow-400'
        ],
        'info' => [
            'bg' => 'bg-blue-50 dark:bg-blue-900/20',
            'border' => 'border-blue-200 dark:border-blue-800',
            'text' => 'text-blue-800 dark:text-blue-200',
            'icon' => 'fa-info-circle',
            'icon_color' => 'text-blue-600 dark:text-blue-400'
        ]
    ];
    
    $config = $types[$type] ?? $types['info'];
@endphp

<div {{ $attributes->merge(['class' => "{$config['bg']} border {$config['border']} rounded-xl p-4 flex items-start gap-3 animate-slide-in"]) }}>
    @if($icon)
        <i class="fas {{ $config['icon'] }} {{ $config['icon_color'] }} text-xl"></i>
    @endif
    
    <div class="flex-1 {{ $config['text'] }}">
        {{ $slot }}
    </div>
    
    @if($dismissible)
        <button onclick="this.parentElement.remove()" 
                class="{{ $config['icon_color'] }} hover:opacity-70 transition-opacity">
            <i class="fas fa-times"></i>
        </button>
    @endif
</div>
