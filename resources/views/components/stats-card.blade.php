<!-- Stats Card Component -->
@props([
    'title' => '',
    'value' => '',
    'icon' => 'chart-line',
    'color' => 'primary',
    'subtitle' => null,
    'trend' => null
])

@php
    $colors = [
        'primary' => 'gradient-primary',
        'success' => 'gradient-success',
        'warning' => 'gradient-warning',
        'danger' => 'gradient-danger',
    ];
    
    $gradientClass = $colors[$color] ?? $colors['primary'];
@endphp

<div {{ $attributes->merge(['class' => 'stats-card group']) }}>
    <div class="flex items-center justify-between">
        <div class="flex-1">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">{{ $title }}</p>
            <h3 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $value }}</h3>
            
            @if($subtitle)
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ $subtitle }}
                </p>
            @endif
            
            @if($trend)
                <div class="flex items-center gap-1 mt-2 text-xs">
                    @if($trend['direction'] === 'up')
                        <i class="fas fa-arrow-up text-green-600"></i>
                        <span class="text-green-600 font-medium">{{ $trend['value'] }}</span>
                    @else
                        <i class="fas fa-arrow-down text-red-600"></i>
                        <span class="text-red-600 font-medium">{{ $trend['value'] }}</span>
                    @endif
                    <span class="text-gray-500">{{ $trend['label'] ?? '' }}</span>
                </div>
            @endif
        </div>
        
        <div class="icon-container {{ $gradientClass }}">
            <i class="fas fa-{{ $icon }} text-2xl"></i>
        </div>
    </div>
    
    @if($slot->isNotEmpty())
        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            {{ $slot }}
        </div>
    @endif
</div>
