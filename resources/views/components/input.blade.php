@props([
    'label' => null, 
    'error' => null,
    'type' => 'text',
    'id' => null
])

<div {{ $attributes->merge(['class' => 'w-full']) }}>
    @if($label)
        <label 
            @if($id) for="{{ $id }}" @endif
            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5 transition-colors"
        >
            {{ $label }}
        </label>
    @endif
    <div class="relative">
        <input
            type="{{ $type }}"
            @if($id) id="{{ $id }}" @endif
            {{ $attributes->whereDoesntStartWith('class') }}
            class="w-full px-4 py-2.5 rounded-lg border bg-white dark:bg-gray-800 text-gray-900 dark:text-white 
            placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-200
            {{ $error
                ? 'border-red-500 focus:ring-2 focus:ring-red-200 dark:focus:ring-red-900/30'
                : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 dark:focus:ring-primary-900/30'
            }}
            outline-none disabled:opacity-50 disabled:cursor-not-allowed"
        />
        
        @if($type === 'password')
            <button 
                type="button"
                onclick="
                    const input = this.previousElementSibling;
                    if (input.type === 'password') {
                        input.type = 'text';
                        this.innerHTML = '<i class=\'fas fa-eye-slash\'></i>';
                    } else {
                        input.type = 'password';
                        this.innerHTML = '<i class=\'fas fa-eye\'></i>';
                    }
                "
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none"
            >
                <i class="fas fa-eye"></i>
            </button>
        @endif
    </div>
    @if($error)
        <p class="mt-1 text-xs text-red-500 animate-fade-in-down">
            <i class="fas fa-exclamation-circle mr-1"></i>
            {{ is_array($error) ? $error[0] : $error }}
        </p>
    @endif
</div>
