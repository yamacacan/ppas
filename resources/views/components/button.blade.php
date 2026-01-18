<!-- Button Component -->
@props([
    'type' => 'primary',
    'size' => 'md',
    'href' => null,
    'icon' => null,
    'iconPosition' => 'left'
])

@php
    $types = [
        'primary' => 'bg-primary-500 text-white hover:bg-primary-600 focus:ring-primary-500 shadow-md hover:shadow-lg',
        'secondary' => 'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100 hover:bg-gray-300 dark:hover:bg-gray-600 focus:ring-gray-500',
        'success' => 'gradient-success text-white hover:opacity-90 focus:ring-green-500 shadow-md hover:shadow-lg',
        'danger' => 'gradient-danger text-white hover:opacity-90 focus:ring-red-500 shadow-md hover:shadow-lg',
        'warning' => 'gradient-warning text-white hover:opacity-90 focus:ring-yellow-500 shadow-md hover:shadow-lg',
        'outline' => 'border-2 border-primary-500 text-primary-500 hover:bg-primary-50 dark:hover:bg-primary-900/20'
    ];
    
    $sizes = [
        'xs' => 'px-2 py-1 text-xs',
        'sm' => 'px-3 py-1.5 text-sm',
        'md' => 'px-4 py-2 text-base',
        'lg' => 'px-6 py-3 text-lg',
        'xl' => 'px-8 py-4 text-xl'
    ];
    
    $baseClasses = 'inline-flex items-center justify-center gap-2 rounded-lg font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';
    $classes = $baseClasses . ' ' . ($types[$type] ?? $types['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon && $iconPosition === 'left')
            <i class="fas fa-{{ $icon }}"></i>
        @endif
        
        {{ $slot }}
        
        @if($icon && $iconPosition === 'right')
            <i class="fas fa-{{ $icon }}"></i>
        @endif
    </a>
@else
    <button {{ $attributes->merge(['class' => $classes, 'type' => 'button']) }}>
        @if($icon && $iconPosition === 'left')
            <i class="fas fa-{{ $icon }}"></i>
        @endif
        
        {{ $slot }}
        
        @if($icon && $iconPosition === 'right')
            <i class="fas fa-{{ $icon }}"></i>
        @endif
    </button>
@endif
