@props([
    'label' => null,
    'error' => null,
    'id' => 'datepicker-' . uniqid(),
    'placeholder' => 'Select date',
    'minDate' => null,
    'maxDate' => null,
    'format' => 'Y-m-d',
    'displayFormat' => 'd.m.Y',
    'value' => null,
])

<div
    class="w-full relative"
    x-data="{
        value: '{{ $value }}',
        showDatepicker: false,
        month: '',
        year: '',
        no_of_days: [],
        blankdays: [],
        days: ['Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt', 'Paz'],
        months: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'],
        
        // Positioning
        top: 0,
        left: 0,
        
        initDate() {
            let today = new Date();
            if (this.value) {
                const dateParts = this.value.split('-');
                if(dateParts.length === 3) {
                     this.month = parseInt(dateParts[1]) - 1;
                     this.year = parseInt(dateParts[0]);
                }
            } else {
                this.month = today.getMonth();
                this.year = today.getFullYear();
            }
            // Ensure types are numbers for strict comparison if needed, though JS loose comparison usually works.
            this.month = parseInt(this.month);
            this.year = parseInt(this.year);

            this.getNoOfDays();
            
            // Re-calculate position on window resize/scroll to keep it attached
            window.addEventListener('resize', () => this.updatePosition());
            window.addEventListener('scroll', () => this.updatePosition(), true);
        },

        updatePosition() {
            if (!this.showDatepicker) return;
            
            this.$nextTick(() => {
                const input = this.$refs.input;
                const dropdown = this.$refs.dropdown;
                
                if (input && dropdown) {
                    const rect = input.getBoundingClientRect();
                    const dropdownRect = dropdown.getBoundingClientRect();
                    const viewportHeight = window.innerHeight;
                    
                    // Default: below
                    let top = rect.bottom + window.scrollY + 5;
                    let left = rect.left + window.scrollX;
                    
                    // Check overflow bottom
                    if (rect.bottom + dropdownRect.height > viewportHeight) {
                        // Flip to top if enough space
                        if (rect.top - dropdownRect.height > 0) {
                             top = rect.top + window.scrollY - dropdownRect.height - 5;
                        }
                    }
                    
                    this.top = top;
                    this.left = left;
                }
            });
        },

        toggleDatepicker() {
            this.showDatepicker = !this.showDatepicker;
            if(this.showDatepicker) {
                this.updatePosition();
            }
        },

        closeDatepicker() {
            this.showDatepicker = false;
        },

        isToday(date) {
            const today = new Date();
            const d = new Date(this.year, this.month, date);
            return today.toDateString() === d.toDateString();
        },
        
        isSelected(date) {
            if (!this.value) return false;
            const d = new Date(this.year, this.month, date);
            const dateParts = this.value.split('-');
            const selectedDate = new Date(parseInt(dateParts[0]), parseInt(dateParts[1]) - 1, parseInt(dateParts[2]));
            return d.toDateString() === selectedDate.toDateString();
        },

        getDateValue(date) {
            let selectedDate = new Date(this.year, this.month, date);
            this.year = selectedDate.getFullYear();
            this.month = selectedDate.getMonth();
            
            let year = this.year;
            let month = ('0' + (this.month + 1)).slice(-2);
            let day = ('0' + selectedDate.getDate()).slice(-2);
            
            this.value = `${year}-${month}-${day}`;
            this.showDatepicker = false;
        },

        getDisplayValue() {
            if (!this.value) return '';
            const dateParts = this.value.split('-');
            const year = dateParts[0];
            const month = dateParts[1];
            const day = dateParts[2];
            if ('{{ $displayFormat }}' === 'd.m.Y') {
                return `${day}.${month}.${year}`;
            }
            return this.value; 
        },

        getNoOfDays() {
            let daysInMonth = new Date(this.year, this.month + 1, 0).getDate();
            let dayOfWeek = new Date(this.year, this.month).getDay();
            let blankdaysArray = [];
            let dayIndex = dayOfWeek === 0 ? 6 : dayOfWeek - 1;
            if (dayIndex > 0) {
                for ( var i=1; i <= dayIndex; i++) {
                    blankdaysArray.push(i);
                }
            }
            let daysArray = [];
            for ( var i=1; i <= daysInMonth; i++) {
                daysArray.push(i);
            }
            this.blankdays = blankdaysArray;
            this.no_of_days = daysArray;
        },
        
        get years() {
            let currentYear = new Date().getFullYear();
            let years = [];
            for (let i = currentYear - 100; i <= currentYear + 10; i++) {
                years.push(i);
            }
            return years;
        }
    }"
    x-init="initDate()"
    x-cloak
    @click.away="closeDatepicker()"
>
    @if($label)
        <label 
            for="{{ $id }}" 
            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5 transition-colors"
        >
            {{ $label }}
        </label>
    @endif

    <div class="relative" x-ref="container">
        <!-- Main Input -->
        <div class="relative">
            <input 
                type="text"
                x-model="getDisplayValue()"
                readonly
                @click="toggleDatepicker()"
                class="w-full px-4 py-2.5 pl-10 rounded-lg border bg-white dark:bg-gray-800 text-gray-900 dark:text-white 
                placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-200 cursor-pointer
                {{ $error
                    ? 'border-red-500 focus:ring-2 focus:ring-red-200 dark:focus:ring-red-900/30'
                    : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 dark:focus:ring-primary-900/30'
                }}
                outline-none"
                placeholder="{{ $placeholder }}"
                x-ref="input"
            >
            <input 
                type="hidden" 
                name="{{ $attributes->get('name') }}" 
                x-model="value" 
                id="{{ $id }}"
            >
            
            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 pointer-events-none">
                <i class="fas fa-calendar"></i>
            </div>
            
            <!-- Chevron Icon -->
            <div class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                 <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
        </div>

        <!-- Datepicker Dropdown -->
        <template x-teleport="body">
            <div 
                x-ref="dropdown"
                class="absolute w-72 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-100 dark:border-gray-700 z-[9999] p-4"
                x-show="showDatepicker"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95"
                @click.stop
                :style="`top: ${top}px; left: ${left}px`"
                style="display: none;"
            >
                <!-- Header -->
                <div class="flex items-center justify-between mb-4 gap-2">
                    <div class="flex gap-2 w-full">
                        <!-- Month Select -->
                        <select 
                            x-model.number="month" 
                            @change="getNoOfDays()"
                            class="w-1/2 bg-gray-50 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block p-2 cursor-pointer"
                        >
                            <template x-for="(m, index) in months" :key="index">
                                <option :value="index" x-text="m" :selected="month === index"></option>
                            </template>
                        </select>
                        
                        <!-- Year Select -->
                        <select 
                            x-model.number="year" 
                            @change="getNoOfDays()"
                            class="w-1/2 bg-gray-50 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block p-2 cursor-pointer"
                        >
                            <template x-for="y in years" :key="y">
                                <option :value="y" x-text="y" :selected="year === y"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <!-- Weekdays -->
                <div class="flex flex-wrap mb-2">
                    <template x-for="(day, index) in days" :key="index">
                        <div style="width: 14.28%" class="px-1 text-center">
                            <div x-text="day" class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide"></div>
                        </div>
                    </template>
                </div>

                <!-- Days Grid -->
                <div class="flex flex-wrap -mx-1">
                    <template x-for="blankday in blankdays">
                        <div style="width: 14.28%" class="text-center border p-1 border-transparent text-sm"></div>
                    </template>
                    
                    <template x-for="(date, dateIndex) in no_of_days" :key="dateIndex">
                        <div style="width: 14.28%" class="px-1 mb-1">
                            <div
                                @click="getDateValue(date)"
                                x-text="date"
                                class="cursor-pointer text-center text-sm leading-8 rounded-full transition-colors duration-200"
                                :class="{
                                    'bg-primary-500 text-white shadow-md': isSelected(date),
                                    'text-gray-700 dark:text-gray-200 hover:bg-primary-50 dark:hover:bg-gray-700': !isSelected(date),
                                    'bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-300 font-semibold transition-none': isToday(date) && !isSelected(date)
                                }"
                            ></div>
                        </div>
                    </template>
                </div>
            </div>
        </template>
    </div>
    
    @if($error)
        <p class="mt-1 text-xs text-red-500 animate-fade-in-down">
            <i class="fas fa-exclamation-circle mr-1"></i>
            {{ is_array($error) ? $error[0] : $error }}
        </p>
    @endif
</div>
