<?php
// Ported from: home.blade.php
$airports = Database::fetchAll("SELECT * FROM airports ORDER BY sort_order LIMIT 10");
$vehicles = Database::fetchAll("SELECT * FROM vehicles ORDER BY sort_order");
$faqs = Database::fetchAll("SELECT * FROM faqs ORDER BY sort_order");
?>
<?php require BASE_PATH . '/templates/layouts/header.php'; ?>
<?php $__page_title = 'AeroTAXI - Reliable UK Airport Transfers'; ?>

    <section class="bg-white py-10 lg:py-16 overflow-hidden">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
                
                <div>
                    <h1 class="text-2xl sm:text-4xl lg:text-5xl font-bold text-gray-900 leading-tight mb-3 sm:mb-4">
                        Reliable and on-time UK airport taxi transfers
                    </h1>
                    <p class="text-gray-500 text-sm sm:text-lg mb-6 sm:mb-8">
                        Wherever you're headed, we and our trusted taxi partners will get you there comfortably and on time, guaranteed.
                    </p>

                <div class="bg-cream rounded-[20px] p-5 md:px-8 lg:py-7" x-data="bookingForm()" @click.outside="fromOpen = false; toOpen = false">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Book your ride</h2>

                    <form action="<?= base_url('your-ride') ?>" method="GET">

                        
                        <div class="relative">
                            <div class="relative">
                                <i class="fa-solid fa-location-dot absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                <input type="text" name="from" x-model="from" placeholder="From"
                                       @focus="onFromFocus()"
                                       @input.debounce.300ms="searchFrom()"
                                       @keydown.arrow-down.prevent="highlightNext('from')"
                                       @keydown.arrow-up.prevent="highlightPrev('from')"
                                       @keydown.enter.prevent="selectHighlighted('from')"
                                       @keydown.escape="fromOpen = false"
                                       autocomplete="off"
                                       class="w-full bg-white border border-gray-200 rounded-xl pl-10 pr-4 py-3.5 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent transition" required>
                                <i x-show="fromLoading" class="fa-solid fa-spinner fa-spin absolute right-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            </div>

                            
                            <div x-show="fromOpen && (fromAirports.length || fromPlaces.length)"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 -translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-64 overflow-y-auto">

                                
                                <template x-if="fromAirports.length">
                                    <div>
                                        <div class="px-3 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider bg-gray-50 rounded-t-xl">
                                            <i class="fa-solid fa-plane mr-1"></i> Airports
                                        </div>
                                        <template x-for="(airport, i) in fromAirports" :key="'fa-'+airport.id">
                                            <button type="button"
                                                    @click="selectLocation('from', airport.name + ' (' + airport.code + ')')"
                                                    @mouseenter="fromHighlight = i"
                                                    :class="{ 'bg-yellow-50': fromHighlight === i }"
                                                    class="w-full text-left px-4 py-2.5 hover:bg-yellow-50 flex items-center gap-3 transition-colors">
                                                <div class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                                    <i class="fa-solid fa-plane-departure text-primary text-xs"></i>
                                                </div>
                                                <div>
                                                    <span class="text-sm font-medium text-gray-800" x-text="airport.name"></span>
                                                    <span class="text-xs text-gray-400 ml-1" x-text="'(' + airport.code + ')'"></span>
                                                    <div class="text-xs text-gray-400" x-text="airport.city"></div>
                                                </div>
                                            </button>
                                        </template>
                                    </div>
                                </template>

                                
                                <template x-if="fromPlaces.length">
                                    <div>
                                        <div class="px-3 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider bg-gray-50"
                                             :class="{ 'rounded-t-xl': !fromAirports.length }">
                                            <i class="fa-solid fa-map-marker-alt mr-1"></i> Addresses
                                        </div>
                                        <template x-for="(place, i) in fromPlaces" :key="'fp-'+i">
                                            <button type="button"
                                                    @click="selectLocation('from', place.display_name)"
                                                    @mouseenter="fromHighlight = fromAirports.length + i"
                                                    :class="{ 'bg-yellow-50': fromHighlight === fromAirports.length + i }"
                                                    class="w-full text-left px-4 py-2.5 hover:bg-yellow-50 flex items-center gap-3 transition-colors">
                                                <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                                    <i class="fa-solid fa-location-dot text-gray-400 text-xs"></i>
                                                </div>
                                                <span class="text-sm text-gray-700 line-clamp-2" x-text="place.display_name"></span>
                                            </button>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>

                        
                        <div class="flex justify-end -my-4 relative z-20 pr-2">
                            <button type="button" @click="swap()"
                                    class="w-10 h-10 bg-yellow-400 hover:bg-yellow-500 rounded-full flex items-center justify-center shadow-sm transition-all hover:scale-110 active:scale-95 hover:rotate-180 duration-300">
                                <img src="/images/swap-icon.png" alt="Swap" class="w-8 h-8">
                            </button>
                        </div>

                        
                        <div class="relative">
                            <div class="relative">
                                <i class="fa-solid fa-flag-checkered absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                <input type="text" name="to" x-model="to" placeholder="To"
                                       @focus="onToFocus()"
                                       @input.debounce.300ms="searchTo()"
                                       @keydown.arrow-down.prevent="highlightNext('to')"
                                       @keydown.arrow-up.prevent="highlightPrev('to')"
                                       @keydown.enter.prevent="selectHighlighted('to')"
                                       @keydown.escape="toOpen = false"
                                       autocomplete="off"
                                       class="w-full bg-white border border-gray-200 rounded-xl pl-10 pr-4 py-3.5 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent transition" required>
                                <i x-show="toLoading" class="fa-solid fa-spinner fa-spin absolute right-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            </div>

                            
                            <div x-show="toOpen && (toAirports.length || toPlaces.length)"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 -translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-64 overflow-y-auto">

                                
                                <template x-if="toAirports.length">
                                    <div>
                                        <div class="px-3 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider bg-gray-50 rounded-t-xl">
                                            <i class="fa-solid fa-plane mr-1"></i> Airports
                                        </div>
                                        <template x-for="(airport, i) in toAirports" :key="'ta-'+airport.id">
                                            <button type="button"
                                                    @click="selectLocation('to', airport.name + ' (' + airport.code + ')')"
                                                    @mouseenter="toHighlight = i"
                                                    :class="{ 'bg-yellow-50': toHighlight === i }"
                                                    class="w-full text-left px-4 py-2.5 hover:bg-yellow-50 flex items-center gap-3 transition-colors">
                                                <div class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                                    <i class="fa-solid fa-plane-departure text-primary text-xs"></i>
                                                </div>
                                                <div>
                                                    <span class="text-sm font-medium text-gray-800" x-text="airport.name"></span>
                                                    <span class="text-xs text-gray-400 ml-1" x-text="'(' + airport.code + ')'"></span>
                                                    <div class="text-xs text-gray-400" x-text="airport.city"></div>
                                                </div>
                                            </button>
                                        </template>
                                    </div>
                                </template>

                                
                                <template x-if="toPlaces.length">
                                    <div>
                                        <div class="px-3 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider bg-gray-50"
                                             :class="{ 'rounded-t-xl': !toAirports.length }">
                                            <i class="fa-solid fa-map-marker-alt mr-1"></i> Addresses
                                        </div>
                                        <template x-for="(place, i) in toPlaces" :key="'tp-'+i">
                                            <button type="button"
                                                    @click="selectLocation('to', place.display_name)"
                                                    @mouseenter="toHighlight = toAirports.length + i"
                                                    :class="{ 'bg-yellow-50': toHighlight === toAirports.length + i }"
                                                    class="w-full text-left px-4 py-2.5 hover:bg-yellow-50 flex items-center gap-3 transition-colors">
                                                <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                                    <i class="fa-solid fa-location-dot text-gray-400 text-xs"></i>
                                                </div>
                                                <span class="text-sm text-gray-700 line-clamp-2" x-text="place.display_name"></span>
                                            </button>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>

                        
                        <div class="static lg:relative mt-3">
                            <i class="fa-regular fa-calendar absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                            <input type="text" readonly
                                   @click="openDatePicker()"
                                   :value="departDisplay"
                                   placeholder="Depart"
                                   class="w-full bg-white border border-gray-200 rounded-xl pl-10 pr-4 py-3.5 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent transition cursor-pointer">
                            
                            <input type="hidden" name="depart_date" :value="departDateValue">
                            <input type="hidden" name="depart_time" :value="departTimeValue">

                            
                            <div x-show="datePickerOpen" x-cloak
                                 @click.outside="datePickerOpen = false"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute z-[999] mt-2 bg-white border border-gray-200 rounded-2xl shadow-2xl overflow-hidden w-[calc(100%-2rem)] left-4 lg:left-auto lg:right-0 lg:bottom-0 lg:translate-x-[105%] lg:translate-y-[30%] lg:mt-0 lg:w-80">

                                
                                <div class="flex border-b border-gray-100">
                                    <button type="button" @click="pickerTab = 'date'"
                                            :class="pickerTab === 'date' ? 'border-yellow-400 text-gray-900' : 'border-transparent text-gray-400 hover:text-gray-600'"
                                            class="flex-1 flex items-center justify-center gap-2 py-3 text-sm font-semibold border-b-2 transition-colors">
                                        <i class="fa-regular fa-calendar"></i>
                                        <span>Date</span>
                                        <span x-show="selectedDay" class="text-xs font-normal text-gray-400" x-text="selectedDay ? (selectedDay + ' ' + monthNames[_selMonth || pickerMonth].substring(0,3)) : ''"></span>
                                    </button>
                                    <button type="button" @click="if(selectedDay) pickerTab = 'time'"
                                            :class="pickerTab === 'time' ? 'border-yellow-400 text-gray-900' : selectedDay ? 'border-transparent text-gray-400 hover:text-gray-600' : 'border-transparent text-gray-300 cursor-not-allowed'"
                                            class="flex-1 flex items-center justify-center gap-2 py-3 text-sm font-semibold border-b-2 transition-colors">
                                        <i class="fa-regular fa-clock"></i>
                                        <span>Time</span>
                                        <span class="text-xs font-normal text-gray-400" x-text="pickerHour + ':' + pickerMinute"></span>
                                    </button>
                                </div>

                                
                                <div x-show="pickerTab === 'date'" class="p-4">
                                    
                                    <div class="flex items-center justify-between mb-4">
                                        <button type="button" @click="prevMonth()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-600 transition">
                                            <i class="fa-solid fa-chevron-left text-sm"></i>
                                        </button>
                                        <div class="flex items-center gap-2">
                                            <select x-model.number="pickerMonth" @change="buildCalendar()"
                                                    class="bg-white border border-gray-200 rounded-lg px-3 py-1.5 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-yellow-400 cursor-pointer appearance-none pr-7 bg-[url('data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%20width%3D%2212%22%20height%3D%2212%22%20viewBox%3D%220%200%2012%2012%22%3E%3Cpath%20d%3D%22M6%208L1%203h10z%22%20fill%3D%22%23666%22/%3E%3C/svg%3E')] bg-[length:10px] bg-[position:right_8px_center] bg-no-repeat">
                                                <option value="0">January</option>
                                                <option value="1">February</option>
                                                <option value="2">March</option>
                                                <option value="3">April</option>
                                                <option value="4">May</option>
                                                <option value="5">June</option>
                                                <option value="6">July</option>
                                                <option value="7">August</option>
                                                <option value="8">September</option>
                                                <option value="9">October</option>
                                                <option value="10">November</option>
                                                <option value="11">December</option>
                                            </select>
                                            <select x-model.number="pickerYear" @change="buildCalendar()"
                                                    class="bg-white border border-gray-200 rounded-lg px-3 py-1.5 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-yellow-400 cursor-pointer appearance-none pr-7 bg-[url('data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%20width%3D%2212%22%20height%3D%2212%22%20viewBox%3D%220%200%2012%2012%22%3E%3Cpath%20d%3D%22M6%208L1%203h10z%22%20fill%3D%22%23666%22/%3E%3C/svg%3E')] bg-[length:10px] bg-[position:right_8px_center] bg-no-repeat">
                                                <template x-for="y in yearOptions" :key="y">
                                                    <option :value="y" x-text="y" :selected="y === pickerYear"></option>
                                                </template>
                                            </select>
                                        </div>
                                        <button type="button" @click="nextMonth()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-600 transition">
                                            <i class="fa-solid fa-chevron-right text-sm"></i>
                                        </button>
                                    </div>

                                    
                                    <div class="grid grid-cols-7 mb-1">
                                        <template x-for="day in ['SU','MO','TU','WE','TH','FR','SA']" :key="day">
                                            <div class="text-center text-xs font-semibold text-gray-400 py-1" x-text="day"></div>
                                        </template>
                                    </div>

                                    
                                    <div class="grid grid-cols-7">
                                        <template x-for="(cell, idx) in calendarCells" :key="idx">
                                            <button type="button"
                                                    @click="cell.day && !cell.disabled && selectDay(cell.day)"
                                                    :disabled="cell.disabled || !cell.day"
                                                    :class="{
                                                        'text-gray-300 cursor-default': !cell.day || cell.disabled,
                                                        'hover:bg-yellow-50 cursor-pointer text-gray-700': cell.day && !cell.disabled && !cell.selected,
                                                        'bg-yellow-400 text-gray-900 font-bold hover:bg-yellow-500 rounded-lg shadow-sm': cell.selected,
                                                        'font-semibold text-primary': cell.today && !cell.selected
                                                    }"
                                                    class="w-full aspect-square flex items-center justify-center text-sm rounded-lg transition-colors">
                                                <span x-text="cell.day || ''"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>

                                
                                <div x-show="pickerTab === 'time'" class="p-5">
                                    
                                    <div class="text-center mb-5">
                                        <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Selected Date</p>
                                        <p class="text-sm font-semibold text-gray-700" x-text="formatSelectedDate()"></p>
                                    </div>

                                    
                                    <div class="flex items-center justify-center gap-1">
                                        
                                        <div class="relative">
                                            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider text-center mb-2">Hour</p>
                                            <div class="time-scroll-col relative h-[210px] w-16 overflow-hidden rounded-xl bg-gray-50 border border-gray-200"
                                                 x-ref="hourCol">
                                                
                                                <div class="pointer-events-none absolute inset-x-0 top-[75px] h-[42px] bg-yellow-400/15 border-y border-yellow-400/30 rounded-md z-10"></div>
                                                <div class="time-scroll-inner overflow-y-auto h-full snap-y snap-mandatory scroll-smooth hide-scrollbar"
                                                     x-ref="hourScroll"
                                                     @scroll.debounce.100ms="snapHour()">
                                                    
                                                    <div class="h-[84px]"></div>
                                                    <template x-for="h in hours" :key="'h'+h">
                                                        <button type="button"
                                                                @click="pickerHour = h; scrollToHour(h)"
                                                                :class="pickerHour === h ? 'text-gray-900 font-bold text-base' : 'text-gray-400 text-sm'"
                                                                class="w-full h-[42px] flex items-center justify-center snap-center transition-all duration-150 hover:text-gray-600"
                                                                x-text="h"
                                                                :data-hour="h">
                                                        </button>
                                                    </template>
                                                    <div class="h-[84px]"></div>
                                                </div>
                                            </div>
                                        </div>

                                        
                                        <div class="flex flex-col items-center justify-center pt-6">
                                            <span class="text-2xl font-bold text-gray-300">:</span>
                                        </div>

                                        
                                        <div class="relative">
                                            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider text-center mb-2">Min</p>
                                            <div class="time-scroll-col relative h-[210px] w-16 overflow-hidden rounded-xl bg-gray-50 border border-gray-200"
                                                 x-ref="minCol">
                                                
                                                <div class="pointer-events-none absolute inset-x-0 top-[75px] h-[42px] bg-yellow-400/15 border-y border-yellow-400/30 rounded-md z-10"></div>
                                                <div class="time-scroll-inner overflow-y-auto h-full snap-y snap-mandatory scroll-smooth hide-scrollbar"
                                                     x-ref="minScroll"
                                                     @scroll.debounce.100ms="snapMinute()">
                                                    <div class="h-[84px]"></div>
                                                    <template x-for="m in minutes" :key="'m'+m">
                                                        <button type="button"
                                                                @click="pickerMinute = m; scrollToMinute(m)"
                                                                :class="pickerMinute === m ? 'text-gray-900 font-bold text-base' : 'text-gray-400 text-sm'"
                                                                class="w-full h-[42px] flex items-center justify-center snap-center transition-all duration-150 hover:text-gray-600"
                                                                x-text="m"
                                                                :data-minute="m">
                                                        </button>
                                                    </template>
                                                    <div class="h-[84px]"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    
                                    <div class="mt-5 text-center">
                                        <div class="inline-flex items-center gap-2 bg-gray-50 rounded-xl px-5 py-2.5 border border-gray-200">
                                            <i class="fa-regular fa-clock text-gray-400"></i>
                                            <span class="text-lg font-bold text-gray-800 tracking-wide" x-text="pickerHour + ':' + pickerMinute"></span>
                                        </div>
                                    </div>
                                </div>

                                
                                <div class="px-4 pb-4">
                                    <button type="button"
                                            @click="pickerTab === 'date' && selectedDay ? (pickerTab = 'time', $nextTick(() => { scrollToHour(pickerHour); scrollToMinute(pickerMinute); })) : confirmDateTime()"
                                            :disabled="!selectedDay"
                                            :class="selectedDay ? 'bg-lightgreen hover:bg-green-200 text-gray-900' : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
                                            class="w-full py-3 rounded-xl font-semibold text-sm transition-colors">
                                        <span x-text="pickerTab === 'date' ? 'Next: Pick Time' : 'Select'"></span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 mt-3">
                            <button type="submit"
                                    class="bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-semibold rounded-xl px-8 py-3.5 transition-all hover:shadow-md active:scale-[0.98] whitespace-nowrap">
                                Check Prices
                            </button>
                            <p class="text-xs text-gray-500 leading-relaxed">
                                <span class="text-green-600 mr-1">&#10003;</span>
                                24/7 customer support, cancellation before 24 hours, flight tracking and child seats are included for free
                            </p>
                        </div>
                    </form>
                </div>
                </div>

                
                <div class="hidden lg:flex justify-center lg:justify-end">
                    <img src="/images/banner-image.svg" alt="Airport Transfer" class="w-full max-w-md xl:max-w-lg">
                </div>
            </div>
        </div>
    </section>

    <script>
        function bookingForm() {
            const now = new Date();
            return {
                // ---- Location fields ----
                from: '',
                to: '',
                fromOpen: false,
                toOpen: false,
                fromLoading: false,
                toLoading: false,
                fromAirports: [],
                fromPlaces: [],
                toAirports: [],
                toPlaces: [],
                fromHighlight: -1,
                toHighlight: -1,

                // ---- Date+Time picker ----
                datePickerOpen: false,
                pickerTab: 'date',
                pickerMonth: now.getMonth(),
                pickerYear: now.getFullYear(),
                selectedDay: null,
                _selMonth: now.getMonth(),
                _selYear: now.getFullYear(),
                pickerHour: String(now.getHours()).padStart(2, '0'),
                pickerMinute: String(Math.ceil(now.getMinutes() / 5) * 5 % 60).padStart(2, '0'),
                calendarCells: [],
                departDisplay: '',
                departDateValue: '',
                departTimeValue: '',

                monthNames: ['January','February','March','April','May','June','July','August','September','October','November','December'],
                hours: Array.from({length: 24}, (_, i) => String(i).padStart(2, '0')),
                minutes: Array.from({length: 12}, (_, i) => String(i * 5).padStart(2, '0')),

                get yearOptions() {
                    const y = new Date().getFullYear();
                    return [y, y+1, y+2];
                },

                get todayStr() {
                    const t = new Date();
                    return `${t.getFullYear()}-${t.getMonth()}-${t.getDate()}`;
                },

                init() {
                    // Ensure current month/year on init
                    const today = new Date();
                    this.pickerMonth = today.getMonth();
                    this.pickerYear = today.getFullYear();
                    this.buildCalendar();
                    // Watch for both from+to filled => auto-open date picker
                    this.$watch('to', (val) => {
                        if (val && this.from && !this.departDateValue) {
                            this.$nextTick(() => {
                                setTimeout(() => {
                                    if (!this.toOpen && !this.fromOpen) {
                                        this.openDatePicker();
                                    }
                                }, 400);
                            });
                        }
                    });
                },

                openDatePicker() {
                    this.fromOpen = false;
                    this.toOpen = false;
                    this.pickerTab = 'date';
                    if (!this.selectedDay) {
                        const t = new Date();
                        this.pickerMonth = t.getMonth();
                        this.pickerYear = t.getFullYear();
                    }
                    this.datePickerOpen = true;
                    this.buildCalendar();
                },

                buildCalendar() {
                    const firstDay = new Date(this.pickerYear, this.pickerMonth, 1).getDay();
                    const daysInMonth = new Date(this.pickerYear, this.pickerMonth + 1, 0).getDate();
                    const today = new Date();
                    today.setHours(0,0,0,0);
                    const cells = [];

                    // Empty cells before first day
                    for (let i = 0; i < firstDay; i++) {
                        cells.push({ day: null, disabled: true, selected: false, today: false });
                    }

                    for (let d = 1; d <= daysInMonth; d++) {
                        const cellDate = new Date(this.pickerYear, this.pickerMonth, d);
                        const isPast = cellDate < today;
                        const isToday = cellDate.getTime() === today.getTime();
                        const isSelected = this.selectedDay === d
                            && this.pickerMonth === this._selMonth
                            && this.pickerYear === this._selYear;

                        cells.push({
                            day: d,
                            disabled: isPast,
                            selected: isSelected,
                            today: isToday
                        });
                    }

                    this.calendarCells = cells;
                },

                prevMonth() {
                    if (this.pickerMonth === 0) {
                        this.pickerMonth = 11;
                        this.pickerYear--;
                    } else {
                        this.pickerMonth--;
                    }
                    this.buildCalendar();
                },

                nextMonth() {
                    if (this.pickerMonth === 11) {
                        this.pickerMonth = 0;
                        this.pickerYear++;
                    } else {
                        this.pickerMonth++;
                    }
                    this.buildCalendar();
                },

                selectDay(day) {
                    this.selectedDay = day;
                    this._selMonth = this.pickerMonth;
                    this._selYear = this.pickerYear;
                    this.buildCalendar();
                    // Auto-switch to time tab after selecting a date
                    this.$nextTick(() => {
                        setTimeout(() => {
                            this.pickerTab = 'time';
                            this.$nextTick(() => {
                                this.scrollToHour(this.pickerHour);
                                this.scrollToMinute(this.pickerMinute);
                            });
                        }, 250);
                    });
                },

                formatSelectedDate() {
                    if (!this.selectedDay) return '';
                    const d = new Date(this._selYear, this._selMonth, this.selectedDay);
                    const days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
                    return `${days[d.getDay()]}, ${this.selectedDay} ${this.monthNames[this._selMonth]} ${this._selYear}`;
                },

                confirmDateTime() {
                    if (!this.selectedDay) return;
                    const m = String(this._selMonth + 1).padStart(2, '0');
                    const d = String(this.selectedDay).padStart(2, '0');
                    this.departDateValue = `${this._selYear}-${m}-${d}`;
                    this.departTimeValue = `${this.pickerHour}:${this.pickerMinute}`;
                    this.departDisplay = `${this.formatSelectedDate()} at ${this.pickerHour}:${this.pickerMinute}`;
                    this.datePickerOpen = false;
                },

                // ---- Time scroll helpers ----
                scrollToHour(h) {
                    const el = this.$refs.hourScroll;
                    if (!el) return;
                    const idx = this.hours.indexOf(h);
                    if (idx >= 0) el.scrollTo({ top: idx * 42, behavior: 'smooth' });
                },

                scrollToMinute(m) {
                    const el = this.$refs.minScroll;
                    if (!el) return;
                    const idx = this.minutes.indexOf(m);
                    if (idx >= 0) el.scrollTo({ top: idx * 42, behavior: 'smooth' });
                },

                snapHour() {
                    const el = this.$refs.hourScroll;
                    if (!el) return;
                    const idx = Math.round(el.scrollTop / 42);
                    const clamped = Math.max(0, Math.min(idx, this.hours.length - 1));
                    this.pickerHour = this.hours[clamped];
                },

                snapMinute() {
                    const el = this.$refs.minScroll;
                    if (!el) return;
                    const idx = Math.round(el.scrollTop / 42);
                    const clamped = Math.max(0, Math.min(idx, this.minutes.length - 1));
                    this.pickerMinute = this.minutes[clamped];
                },

                // ---- Location swap ----
                swap() {
                    let temp = this.from;
                    this.from = this.to;
                    this.to = temp;
                },

                // ---- Location autocomplete ----
                async onFromFocus() {
                    this.fromHighlight = -1;
                    this.toOpen = false;
                    this.datePickerOpen = false;
                    if (this.from.length === 0) {
                        await this.fetchAirports('from', '');
                        this.fromPlaces = [];
                        this.fromOpen = true;
                    } else {
                        this.searchFrom();
                    }
                },

                async onToFocus() {
                    this.toHighlight = -1;
                    this.fromOpen = false;
                    this.datePickerOpen = false;
                    if (this.to.length === 0) {
                        await this.fetchAirports('to', '');
                        this.toPlaces = [];
                        this.toOpen = true;
                    } else {
                        this.searchTo();
                    }
                },

                async searchFrom() {
                    this.fromHighlight = -1;
                    const q = this.from.trim();
                    if (q.length === 0) {
                        await this.fetchAirports('from', '');
                        this.fromPlaces = [];
                        this.fromOpen = true;
                        return;
                    }
                    this.fromLoading = true;
                    await Promise.all([
                        this.fetchAirports('from', q),
                        this.fetchNominatim('from', q)
                    ]);
                    this.fromLoading = false;
                    this.fromOpen = true;
                },

                async searchTo() {
                    this.toHighlight = -1;
                    const q = this.to.trim();
                    if (q.length === 0) {
                        await this.fetchAirports('to', '');
                        this.toPlaces = [];
                        this.toOpen = true;
                        return;
                    }
                    this.toLoading = true;
                    await Promise.all([
                        this.fetchAirports('to', q),
                        this.fetchNominatim('to', q)
                    ]);
                    this.toLoading = false;
                    this.toOpen = true;
                },

                async fetchAirports(field, query) {
                    try {
                        const res = await fetch(`/api/airports/search?q=${encodeURIComponent(query)}`);
                        const data = await res.json();
                        if (field === 'from') this.fromAirports = data;
                        else this.toAirports = data;
                    } catch (e) {
                        if (field === 'from') this.fromAirports = [];
                        else this.toAirports = [];
                    }
                },

                async fetchNominatim(field, query) {
                    if (query.length < 3) {
                        if (field === 'from') this.fromPlaces = [];
                        else this.toPlaces = [];
                        return;
                    }
                    try {
                        const res = await fetch(
                            `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&countrycodes=gb&limit=5&addressdetails=1`,
                            { headers: { 'Accept-Language': 'en' } }
                        );
                        const data = await res.json();
                        if (field === 'from') this.fromPlaces = data;
                        else this.toPlaces = data;
                    } catch (e) {
                        if (field === 'from') this.fromPlaces = [];
                        else this.toPlaces = [];
                    }
                },

                selectLocation(field, value) {
                    if (field === 'from') {
                        this.from = value;
                        this.fromOpen = false;
                    } else {
                        this.to = value;
                        this.toOpen = false;
                    }
                },

                highlightNext(field) {
                    const airports = field === 'from' ? this.fromAirports : this.toAirports;
                    const places = field === 'from' ? this.fromPlaces : this.toPlaces;
                    const total = airports.length + places.length;
                    if (field === 'from') this.fromHighlight = (this.fromHighlight + 1) % total;
                    else this.toHighlight = (this.toHighlight + 1) % total;
                },

                highlightPrev(field) {
                    const airports = field === 'from' ? this.fromAirports : this.toAirports;
                    const places = field === 'from' ? this.fromPlaces : this.toPlaces;
                    const total = airports.length + places.length;
                    if (field === 'from') this.fromHighlight = this.fromHighlight <= 0 ? total - 1 : this.fromHighlight - 1;
                    else this.toHighlight = this.toHighlight <= 0 ? total - 1 : this.toHighlight - 1;
                },

                selectHighlighted(field) {
                    const airports = field === 'from' ? this.fromAirports : this.toAirports;
                    const places = field === 'from' ? this.fromPlaces : this.toPlaces;
                    const idx = field === 'from' ? this.fromHighlight : this.toHighlight;
                    if (idx < 0) return;
                    if (idx < airports.length) {
                        const a = airports[idx];
                        this.selectLocation(field, a.name + ' (' + a.code + ')');
                    } else {
                        const p = places[idx - airports.length];
                        this.selectLocation(field, p.display_name);
                    }
                }
            }
        }

        function slider(refName) {
            return {
                atStart: true,
                atEnd: false,
                init() {
                    this.$nextTick(() => this.updateBounds());
                },
                updateBounds() {
                    const el = this.$refs[refName];
                    if (!el) return;
                    this.atStart = el.scrollLeft <= 5;
                    this.atEnd = el.scrollLeft + el.clientWidth >= el.scrollWidth - 5;
                },
                scrollLeft() {
                    const el = this.$refs[refName];
                    if (el) el.scrollBy({ left: -el.clientWidth * 0.75, behavior: 'smooth' });
                },
                scrollRight() {
                    const el = this.$refs[refName];
                    if (el) el.scrollBy({ left: el.clientWidth * 0.75, behavior: 'smooth' });
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        /* Hide scrollbar but keep scrollable */
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
    </style>

    
    <section class="py-12 sm:py-16 lg:py-20 bg-lightgreen overflow-hidden">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-xl sm:text-3xl lg:text-4xl font-bold text-gray-900 text-center mb-8 sm:mb-14">
                Reasons to book with AeroTAXI
            </h2>

            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-8 sm:gap-x-12 sm:gap-y-14">

                
                <div class="text-center">
                    <div class="flex justify-center mb-4">
                        <i class="fa-regular fa-clock text-3xl sm:text-5xl text-gray-800"></i>
                    </div>
                    <h3 class="font-bold text-lg text-gray-900 mb-2">24/7 Service</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">Our services are available around the clock to ensure all your travel needs are covered.</p>
                </div>

                
                <div class="text-center">
                    <div class="flex justify-center mb-4">
                        <i class="fa-regular fa-star text-3xl sm:text-5xl text-gray-800"></i>
                    </div>
                    <h3 class="font-bold text-lg text-gray-900 mb-2">Reliable, insured suppliers</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">We work with vetted and licensed local taxi drivers and offer 24/7 support for a safe, seamless journey.</p>
                </div>

                
                <div class="text-center">
                    <div class="flex justify-center mb-4">
                        <i class="fa-regular fa-handshake text-3xl sm:text-5xl text-gray-800"></i>
                    </div>
                    <h3 class="font-bold text-lg text-gray-900 mb-2">Meet & Greet</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">Our taxi drivers welcome you at arrivals for a smooth start to your journey.</p>
                </div>

                
                <div class="text-center">
                    <div class="flex justify-center mb-4">
                        <i class="fa-solid fa-plane-departure text-3xl sm:text-5xl text-gray-800"></i>
                    </div>
                    <h3 class="font-bold text-lg text-gray-900 mb-2">Free flight tracking</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">Your driver tracks your flight and adjusts pickup for delays, ensuring a smooth arrival.</p>
                </div>

                
                <div class="text-center">
                    <div class="flex justify-center mb-4">
                        <i class="fa-solid fa-wallet text-3xl sm:text-5xl text-gray-800"></i>
                    </div>
                    <h3 class="font-bold text-lg text-gray-900 mb-2">Hassle-Free Refunds</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">Get a 100% refund if you cancel 24 hours before your transfer.</p>
                </div>

                
                <div class="text-center">
                    <div class="flex justify-center mb-4">
                        <i class="fa-solid fa-magnifying-glass text-3xl sm:text-5xl text-gray-800"></i>
                    </div>
                    <h3 class="font-bold text-lg text-gray-900 mb-2">Transparent Pricing</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">No hidden fees, just affordable and competitive rates.</p>
                </div>

            </div>

            
            <div class="text-center mt-14">
                <a href="#" onclick="window.scrollTo({top:0,behavior:'smooth'}); return false;"
                   class="inline-block bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-semibold rounded-xl px-10 py-3.5 transition-all hover:shadow-md active:scale-[0.98]">
                    Book your journey
                </a>
            </div>
        </div>
    </section>

    
    <section class="py-12 sm:py-16 lg:py-20 bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-center">

                
                <div class="flex justify-center">
                    <img src="/images/how-it-works.png" alt="Person using mobile app"
                         class="w-full rounded-2xl object-cover">
                </div>

                
                <div class="lg:col-span-2">
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-4 sm:mb-6 leading-tight">
                        Reliable service &ndash; always low prices
                    </h2>
                    <p class="text-sm sm:text-base text-gray-600 leading-relaxed mb-6 sm:mb-10">
                        We provide premium airport transfers to and from all airports across the UK. Our service is designed to eliminate the common hassles of pre- or post-flight travel. You won't need to decipher complicated maps or worry about language barriers. Instead, you'll enjoy a comfortable ride directly to your destination. Our professional suppliers and their hand-picked drivers are local experts who can track your flight to account for any delays. They're also a great resource for local tips and advice.
                    </p>

                    
                    <h3 class="text-2xl font-bold text-gray-900 mb-8">How it works</h3>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-2">
                        
                        <div class="text-center">
                            <div class="w-14 h-14 sm:w-16 sm:h-16 mx-auto bg-cream rounded-2xl flex items-center justify-center mb-2">
                                <i class="fa-solid fa-location-dot text-xl sm:text-2xl text-gray-800"></i>
                            </div>
                            <p class="text-xs sm:text-sm font-semibold text-gray-800">Select a route</p>
                        </div>

                        
                        <div class="text-center">
                            <div class="w-14 h-14 sm:w-16 sm:h-16 mx-auto bg-cream rounded-2xl flex items-center justify-center mb-2">
                                <i class="fa-solid fa-car-side text-xl sm:text-2xl text-gray-800"></i>
                            </div>
                            <p class="text-xs sm:text-sm font-semibold text-gray-800">Choose a car</p>
                        </div>

                        
                        <div class="text-center">
                            <div class="w-14 h-14 sm:w-16 sm:h-16 mx-auto bg-cream rounded-2xl flex items-center justify-center mb-2">
                                <i class="fa-solid fa-user-pen text-xl sm:text-2xl text-gray-800"></i>
                            </div>
                            <p class="text-xs sm:text-sm font-semibold text-gray-800">Fill in details</p>
                        </div>

                        
                        <div class="text-center">
                            <div class="w-14 h-14 sm:w-16 sm:h-16 mx-auto bg-cream rounded-2xl flex items-center justify-center mb-2">
                                <i class="fa-solid fa-circle-check text-xl sm:text-2xl text-gray-800"></i>
                            </div>
                            <p class="text-sm font-semibold text-gray-800">Confirmed</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    
    <section class="py-16 bg-cream overflow-hidden" x-data="slider('airports')">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-xl sm:text-3xl font-bold text-gray-900">Airports we cover</h2>
                <div class="flex items-center gap-2">
                    <button @click="scrollLeft()" class="w-10 h-10 rounded-full border border-gray-300 bg-white hover:bg-gray-50 flex items-center justify-center text-gray-600 hover:text-gray-900 transition-all hover:shadow-sm disabled:opacity-30 disabled:cursor-not-allowed" :disabled="atStart">
                        <i class="fa-solid fa-chevron-left text-sm"></i>
                    </button>
                    <button @click="scrollRight()" class="w-10 h-10 rounded-full border border-gray-300 bg-white hover:bg-gray-50 flex items-center justify-center text-gray-600 hover:text-gray-900 transition-all hover:shadow-sm disabled:opacity-30 disabled:cursor-not-allowed" :disabled="atEnd">
                        <i class="fa-solid fa-chevron-right text-sm"></i>
                    </button>
                </div>
            </div>

            <div class="relative">
                <div x-ref="airports" @scroll="updateBounds()" class="flex gap-5 overflow-x-auto scroll-smooth hide-scrollbar pb-2">
                    <?php foreach ($airports as $airport): ?>
                    <div class="flex-shrink-0 w-44 sm:w-56">
                        <div class="bg-white rounded-2xl overflow-hidden border border-gray-100 hover:shadow-lg transition-all duration-300 group h-full">
                            <div class="h-32 bg-gray-50 flex items-center justify-center p-4">
                                <?php if ($airport->image): ?>
                                <img src="<?= e(url($airport->image)) ?>" alt="<?= e($airport->name) ?>" class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform duration-300">
                                <?php endif; ?>
                            </div>
                            <div class="p-4">
                                <h3 class="font-bold text-gray-900 text-sm"><?= e($airport->name) ?></h3>
                                <p class="text-xs text-gray-400 mt-0.5"><?= e($airport->city) ?></p>
                                <p class="text-xs text-gray-500 mt-2 leading-relaxed"><?= e($airport->description) ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    
                    <div class="flex-shrink-0 w-44 sm:w-56">
                        <a href="<?= base_url('coverage') ?>" class="bg-white rounded-2xl border border-gray-100 hover:shadow-lg transition-all duration-300 h-full flex flex-col items-center justify-center p-8 group">
                            <div class="w-14 h-14 rounded-full bg-primary/10 flex items-center justify-center mb-4 group-hover:bg-primary/20 transition">
                                <i class="fa-solid fa-arrow-right text-primary text-lg"></i>
                            </div>
                            <p class="font-semibold text-primary text-sm">See all UK airports</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    
    <section class="py-16 bg-white overflow-hidden" x-data="slider('vehicles')">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-xl sm:text-3xl font-bold text-gray-900 mb-1">Choose your ride</h2>
                    <p class="text-gray-500">Starting prices based on distance</p>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="scrollLeft()" class="w-10 h-10 rounded-full border border-gray-300 bg-white hover:bg-gray-50 flex items-center justify-center text-gray-600 hover:text-gray-900 transition-all hover:shadow-sm disabled:opacity-30 disabled:cursor-not-allowed" :disabled="atStart">
                        <i class="fa-solid fa-chevron-left text-sm"></i>
                    </button>
                    <button @click="scrollRight()" class="w-10 h-10 rounded-full border border-gray-300 bg-white hover:bg-gray-50 flex items-center justify-center text-gray-600 hover:text-gray-900 transition-all hover:shadow-sm disabled:opacity-30 disabled:cursor-not-allowed" :disabled="atEnd">
                        <i class="fa-solid fa-chevron-right text-sm"></i>
                    </button>
                </div>
            </div>

            <div class="relative">
                <div x-ref="vehicles" @scroll="updateBounds()" class="flex gap-5 overflow-x-auto scroll-smooth hide-scrollbar pb-2">
                    <?php foreach ($vehicles as $vehicle): ?>
                    <div class="flex-shrink-0 w-48 sm:w-64">
                        <div class="bg-cream rounded-2xl p-5 hover:shadow-lg transition-all duration-300 group h-full border border-transparent hover:border-gray-200">
                            <div class="h-28 flex items-center justify-center mb-4">
                                <?php if ($vehicle->image): ?>
                                <img src="<?= e(url($vehicle->image)) ?>" alt="<?= e($vehicle->name) ?>" class="max-h-full object-contain group-hover:scale-105 transition-transform duration-300">
                                <?php endif; ?>
                            </div>
                            <h3 class="font-bold text-gray-900 text-base"><?= e($vehicle->name) ?></h3>
                            <?php if ($vehicle->car_model): ?>
                            <span class="inline-block text-[10px] text-gray-500 bg-gray-100 border border-gray-200 rounded-full px-2 py-0.5 mt-1"><?= e($vehicle->car_model) ?></span>
                            <?php endif; ?>
                            <p class="text-xs text-gray-500 mt-1 leading-relaxed"><?= e($vehicle->description) ?></p>
                            <div class="flex items-center gap-3 mt-3 text-xs text-gray-500">
                                <span class="flex items-center gap-1"><i class="fa-solid fa-user text-gray-400"></i> <?= e($vehicle->passengers) ?></span>
                                <span class="flex items-center gap-1"><i class="fa-solid fa-suitcase-rolling text-gray-400"></i> <?= e($vehicle->suitcases) ?></span>
                            </div>
                            <p class="text-lg font-bold text-gray-900 mt-3">From &pound;<?= e(number_format($vehicle->price, 2)) ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    
    <section class="py-12 sm:py-16 bg-white overflow-hidden">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-xl sm:text-3xl font-bold text-gray-900 mb-6 sm:mb-8 text-center">Frequently Asked Questions</h2>

            <div class="divide-y divide-gray-200">
                <?php foreach ($faqs as $index => $faq): ?>
                    <div x-data="{ open: false }" class="py-4">
                        <button @click="open = !open" class="flex items-center justify-between w-full text-left">
                            <span class="font-semibold text-gray-900"><?= e($faq->question) ?></span>
                            <i class="fa-solid fa-chevron-down text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" x-collapse class="mt-3 text-gray-600 text-sm leading-relaxed">
                            <?= e($faq->answer) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>


<?php require BASE_PATH . '/templates/layouts/footer.php'; ?>