@props([
    'label' => null, 
    'error' => null,
    'options' => [], 
    'placeholder' => 'Seçiniz',
    'id' => null,
    'name' => null,
    'value' => null,
    'searchable' => true
])

<div 
    x-data="{
        open: false,
        search: '',
        selected: {{ json_encode($value) }},
        options: {{ json_encode($options) }},
        dropdownStyle: { position: 'absolute', top: '0px', left: '0px', width: '100%' },
        get filteredOptions() {
            if (this.search === '') return this.options;
            const lowerSearch = this.search.toLowerCase();
            return Object.fromEntries(
                Object.entries(this.options).filter(([key, value]) => 
                    String(value).toLowerCase().includes(lowerSearch)
                )
            );
        },
        get selectedLabel() {
            return this.options[this.selected] || '{{ $placeholder }}';
        },
        select(key) {
            this.selected = key;
            this.open = false;
            this.search = '';
            this.$nextTick(() => {
                const input = this.$refs.input;
                if(input) input.dispatchEvent(new Event('change', { bubbles: true }));
            });
        },
        calculatePosition() {
            if (this.open) {
                const trigger = this.$refs.trigger.getBoundingClientRect();
                this.dropdownStyle = {
                    position: 'fixed',
                    top: (trigger.bottom) + 'px',
                    left: trigger.left + 'px',
                    width: trigger.width + 'px',
                    zIndex: 9999
                };
            }
        },
        init() {
            this.$watch('open', value => {
                if (value) {
                    this.$nextTick(() => this.calculatePosition());
                    window.addEventListener('resize', this.calculatePosition.bind(this));
                    window.addEventListener('scroll', this.calculatePosition.bind(this), true);
                } else {
                    window.removeEventListener('resize', this.calculatePosition.bind(this));
                    window.removeEventListener('scroll', this.calculatePosition.bind(this), true);
                }
            });
        }
    }"
    @click.outside="if($refs.dropdown && !$refs.dropdown.contains($event.target)) open = false"
    {{ $attributes->merge(['class' => 'w-full']) }}
>
    @if($label)
        <label 
            @if($id) for="{{ $id }}" @endif
            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5 transition-colors"
        >
            {{ $label }}
        </label>
    @endif

    <div class="relative">
        <!-- Trigger -->
        <button 
            x-ref="trigger"
            type="button"
            @click="open = !open"
            class="w-full px-4 py-2.5 rounded-lg border text-left flex justify-between items-center transition-all duration-200
            {{ $error
                ? 'border-red-500 focus:ring-2 focus:ring-red-200 dark:focus:ring-red-900/30'
                : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 dark:focus:ring-primary-900/30'
            }} bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
        >
            <span x-text="selectedLabel" :class="{'text-gray-400 dark:text-gray-500': !selected}"></span>
            <i class="fas fa-chevron-down text-xs text-gray-500 dark:text-gray-400 transition-transform duration-200" :class="{'rotate-180': open}"></i>
        </button>

        <!-- Dropdown -->
        <template x-teleport="body">
            <div 
                x-show="open" 
                :style="dropdownStyle"
                x-ref="dropdown"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="fixed mt-1 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden"
                style="display: none;"
            >
                @if($searchable)
                    <div class="p-2 border-b border-gray-100 dark:border-gray-700">
                        <input 
                            x-model="search"
                            type="text" 
                            class="w-full px-3 py-1.5 text-sm rounded-md border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-primary-500"
                            placeholder="Ara..."
                            x-bind:autofocus="open"
                        >
                    </div>
                @endif
                
                <div class="max-h-60 overflow-y-auto">
                    <template x-for="(label, value) in filteredOptions" :key="value">
                        <div 
                            @click="select(value)"
                            class="px-4 py-2 text-sm cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 text-gray-700 dark:text-gray-300"
                            :class="{'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400': selected == value}"
                        >
                            <span x-text="label"></span>
                        </div>
                    </template>
                    <div x-show="Object.keys(filteredOptions).length === 0" class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 text-center">
                        Sonuç bulunamadı.
                    </div>
                </div>
            </div>
        </template>

        <!-- Hidden Input for Form Submission -->
        <input type="hidden" :name="'{{ $name }}'" :value="selected" @if($id) id="{{ $id }}" @endif x-ref="input">
    </div>

    @if($error)
        <p class="mt-1 text-xs text-red-500 animate-fade-in-down">
            <i class="fas fa-exclamation-circle mr-1"></i>
            {{ is_array($error) ? $error[0] : $error }}
        </p>
    @endif
</div>
