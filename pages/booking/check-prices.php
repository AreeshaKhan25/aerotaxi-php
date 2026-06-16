<?php
// Ported from: booking/check-prices.blade.php
$__page_title = 'Your Ride - AeroTAXI';

$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';
$departDate = $_GET['depart_date'] ?? '';
$departTime = $_GET['depart_time'] ?? '';
$vehicles = Database::fetchAll('SELECT * FROM vehicles ORDER BY sort_order');
?>
<?php require BASE_PATH . '/templates/layouts/header.php'; ?>
<?php ob_start(); ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<div x-data="rideSelector()" x-init="initMap()" class="min-h-screen bg-white">

        
        <div class="bg-white border-b border-gray-100">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
                <div class="flex items-center justify-center">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-full bg-yellow-400 text-gray-900 font-bold text-sm flex items-center justify-center shadow-sm">1</div>
                        <span class="text-sm font-semibold text-gray-900 hidden sm:inline">Your ride</span>
                    </div>
                    <div class="w-12 sm:w-24 h-[2px] bg-yellow-400 mx-2 sm:mx-3"></div>
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-full bg-gray-100 text-gray-400 font-bold text-sm flex items-center justify-center border border-gray-200">2</div>
                        <span class="text-sm font-medium text-gray-400 hidden sm:inline">Transfer details</span>
                    </div>
                    <div class="w-12 sm:w-24 h-[2px] bg-gray-200 mx-2 sm:mx-3"></div>
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-full bg-gray-100 text-gray-400 font-bold text-sm flex items-center justify-center border border-gray-200">3</div>
                        <span class="text-sm font-medium text-gray-400 hidden sm:inline">Payment</span>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col lg:flex-row gap-8">

                
                <div class="w-full lg:w-[400px] flex-shrink-0">
                    <div class="lg:sticky lg:top-24 space-y-5">

                        <h2 class="text-xl font-bold text-gray-900">Your journey</h2>

                        
                        <div class="bg-cream rounded-2xl p-5">

                            
                            <div class="relative border-b border-gray-200/60 py-2">
                                <div class="relative">
                                    <i class="fa-solid fa-location-dot absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                                    <input type="text" x-model="from" placeholder="From"
                                           @focus="onFromFocus()"
                                           @input.debounce.300ms="searchFrom()"
                                           @keydown.arrow-down.prevent="highlightNext('from')"
                                           @keydown.arrow-up.prevent="highlightPrev('from')"
                                           @keydown.enter.prevent="selectHighlighted('from')"
                                           @keydown.escape="fromOpen = false"
                                           autocomplete="off"
                                           class="w-full bg-white border border-gray-200 rounded-xl pl-9 pr-8 py-2.5 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent transition">
                                    <i x-show="fromLoading" class="fa-solid fa-spinner fa-spin absolute right-8 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                                    <button x-show="from.length > 0" x-cloak type="button" @click="from = ''; routeMiles = 0; routeDistance = ''; routeDuration = ''; fromOpen = false"
                                            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-300 hover:text-gray-500 transition">
                                        <i class="fa-solid fa-xmark text-xs"></i>
                                    </button>
                                </div>
                                
                                <div x-show="fromOpen && (fromAirports.length || fromPlaces.length)"
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 -translate-y-1"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     @mousedown.prevent
                                     class="absolute z-[999] left-0 right-0 top-full mt-1 bg-white border border-gray-200 rounded-xl shadow-xl max-h-64 overflow-y-auto">
                                    <template x-if="fromAirports.length">
                                        <div>
                                            <div class="px-3 py-1.5 text-[10px] font-semibold text-gray-400 uppercase tracking-wider bg-gray-50 rounded-t-xl"><i class="fa-solid fa-plane mr-1"></i> Airports</div>
                                            <template x-for="(airport, i) in fromAirports" :key="'fa-'+airport.id">
                                                <button type="button" @click="selectLocation('from', airport.name + ' (' + airport.code + ')')" @mouseenter="fromHighlight = i"
                                                        :class="{ 'bg-yellow-50': fromHighlight === i }"
                                                        class="w-full text-left px-3 py-2 hover:bg-yellow-50 flex items-center gap-2 transition-colors">
                                                    <div class="w-7 h-7 bg-primary/10 rounded-lg flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-plane-departure text-primary text-[10px]"></i></div>
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
                                            <div class="px-3 py-1.5 text-[10px] font-semibold text-gray-400 uppercase tracking-wider bg-gray-50" :class="{ 'rounded-t-xl': !fromAirports.length }"><i class="fa-solid fa-map-marker-alt mr-1"></i> Addresses</div>
                                            <template x-for="(place, i) in fromPlaces" :key="'fp-'+i">
                                                <button type="button" @click="selectLocation('from', place.display_name)" @mouseenter="fromHighlight = fromAirports.length + i"
                                                        :class="{ 'bg-yellow-50': fromHighlight === fromAirports.length + i }"
                                                        class="w-full text-left px-3 py-2 hover:bg-yellow-50 flex items-center gap-2 transition-colors">
                                                    <div class="w-7 h-7 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-location-dot text-gray-400 text-[10px]"></i></div>
                                                    <span class="text-sm text-gray-700 line-clamp-2" x-text="place.display_name"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            
                            <div class="flex justify-end -my-3 relative z-20 pr-1">
                                <button type="button" @click="swapLocations()" class="w-8 h-8 rounded-full bg-yellow-400 hover:bg-yellow-500 flex items-center justify-center shadow-sm transition-all hover:scale-110 active:scale-95 hover:rotate-180 duration-300">
                                    <img src="/images/swap-icon.png" alt="Swap" class="w-5 h-5">
                                </button>
                            </div>

                            
                            <div class="relative border-b border-gray-200/60 py-2">
                                <div class="relative">
                                    <i class="fa-solid fa-flag-checkered absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                                    <input type="text" x-model="to" placeholder="To"
                                           @focus="onToFocus()"
                                           @input.debounce.300ms="searchTo()"
                                           @keydown.arrow-down.prevent="highlightNext('to')"
                                           @keydown.arrow-up.prevent="highlightPrev('to')"
                                           @keydown.enter.prevent="selectHighlighted('to')"
                                           @keydown.escape="toOpen = false"
                                           autocomplete="off"
                                           class="w-full bg-white border border-gray-200 rounded-xl pl-9 pr-8 py-2.5 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent transition">
                                    <i x-show="toLoading" class="fa-solid fa-spinner fa-spin absolute right-8 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                                    <button x-show="to.length > 0" x-cloak type="button" @click="to = ''; routeMiles = 0; routeDistance = ''; routeDuration = ''; toOpen = false"
                                            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-300 hover:text-gray-500 transition">
                                        <i class="fa-solid fa-xmark text-xs"></i>
                                    </button>
                                </div>
                                
                                <div x-show="toOpen && (toAirports.length || toPlaces.length)"
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 -translate-y-1"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     @mousedown.prevent
                                     class="absolute z-[999] left-0 right-0 top-full mt-1 bg-white border border-gray-200 rounded-xl shadow-xl max-h-64 overflow-y-auto">
                                    <template x-if="toAirports.length">
                                        <div>
                                            <div class="px-3 py-1.5 text-[10px] font-semibold text-gray-400 uppercase tracking-wider bg-gray-50 rounded-t-xl"><i class="fa-solid fa-plane mr-1"></i> Airports</div>
                                            <template x-for="(airport, i) in toAirports" :key="'ta-'+airport.id">
                                                <button type="button" @click="selectLocation('to', airport.name + ' (' + airport.code + ')')" @mouseenter="toHighlight = i"
                                                        :class="{ 'bg-yellow-50': toHighlight === i }"
                                                        class="w-full text-left px-3 py-2 hover:bg-yellow-50 flex items-center gap-2 transition-colors">
                                                    <div class="w-7 h-7 bg-primary/10 rounded-lg flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-plane-departure text-primary text-[10px]"></i></div>
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
                                            <div class="px-3 py-1.5 text-[10px] font-semibold text-gray-400 uppercase tracking-wider bg-gray-50" :class="{ 'rounded-t-xl': !toAirports.length }"><i class="fa-solid fa-map-marker-alt mr-1"></i> Addresses</div>
                                            <template x-for="(place, i) in toPlaces" :key="'tp-'+i">
                                                <button type="button" @click="selectLocation('to', place.display_name)" @mouseenter="toHighlight = toAirports.length + i"
                                                        :class="{ 'bg-yellow-50': toHighlight === toAirports.length + i }"
                                                        class="w-full text-left px-3 py-2 hover:bg-yellow-50 flex items-center gap-2 transition-colors">
                                                    <div class="w-7 h-7 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-location-dot text-gray-400 text-[10px]"></i></div>
                                                    <span class="text-sm text-gray-700 line-clamp-2" x-text="place.display_name"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            
                            <div class="relative py-2">
                                <div class="relative">
                                    <i class="fa-regular fa-calendar absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs z-10"></i>
                                    <input type="text" readonly
                                           @click="openDatePicker()"
                                           :value="departDisplay"
                                           placeholder="Depart"
                                           class="w-full bg-white border border-gray-200 rounded-xl pl-9 pr-4 py-2.5 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent transition cursor-pointer">
                                </div>
                                
                                <div x-show="datePickerOpen" x-cloak
                                     @click.outside="datePickerOpen = false"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95"
                                     class="absolute z-[999] top-0 left-0 right-0 mt-1 lg:left-auto lg:top-0 lg:translate-x-[105%] lg:-translate-y-[40%] lg:w-80 bg-white border border-gray-200 rounded-2xl shadow-2xl overflow-hidden">

                                    
                                    <div class="flex border-b border-gray-100">
                                        <button type="button" @click="pickerTab = 'date'"
                                                :class="pickerTab === 'date' ? 'border-yellow-400 text-gray-900' : 'border-transparent text-gray-400 hover:text-gray-600'"
                                                class="flex-1 flex items-center justify-center gap-2 py-3 text-sm font-semibold border-b-2 transition-colors">
                                            <i class="fa-regular fa-calendar"></i> Date
                                            <span x-show="selectedDay" class="text-xs font-normal text-gray-400" x-text="selectedDay ? (selectedDay + ' ' + monthNames[_selMonth || pickerMonth].substring(0,3)) : ''"></span>
                                        </button>
                                        <button type="button" @click="if(selectedDay) pickerTab = 'time'"
                                                :class="pickerTab === 'time' ? 'border-yellow-400 text-gray-900' : selectedDay ? 'border-transparent text-gray-400 hover:text-gray-600' : 'border-transparent text-gray-300 cursor-not-allowed'"
                                                class="flex-1 flex items-center justify-center gap-2 py-3 text-sm font-semibold border-b-2 transition-colors">
                                            <i class="fa-regular fa-clock"></i> Time
                                            <span class="text-xs font-normal text-gray-400" x-text="pickerHour + ':' + pickerMinute"></span>
                                        </button>
                                    </div>

                                    
                                    <div x-show="pickerTab === 'date'" class="p-4">
                                        <div class="flex items-center justify-between mb-4">
                                            <button type="button" @click="prevMonth()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-600 transition"><i class="fa-solid fa-chevron-left text-sm"></i></button>
                                            <div class="flex items-center gap-2">
                                                <select x-model.number="pickerMonth" @change="buildCalendar()" class="bg-white border border-gray-200 rounded-lg px-2 py-1 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-yellow-400 cursor-pointer">
                                                    <option value="0">January</option><option value="1">February</option><option value="2">March</option>
                                                    <option value="3">April</option><option value="4">May</option><option value="5">June</option>
                                                    <option value="6">July</option><option value="7">August</option><option value="8">September</option>
                                                    <option value="9">October</option><option value="10">November</option><option value="11">December</option>
                                                </select>
                                                <select x-model.number="pickerYear" @change="buildCalendar()" class="bg-white border border-gray-200 rounded-lg px-2 py-1 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-yellow-400 cursor-pointer">
                                                    <template x-for="y in yearOptions" :key="y"><option :value="y" x-text="y" :selected="y === pickerYear"></option></template>
                                                </select>
                                            </div>
                                            <button type="button" @click="nextMonth()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-600 transition"><i class="fa-solid fa-chevron-right text-sm"></i></button>
                                        </div>
                                        <div class="grid grid-cols-7 mb-1">
                                            <template x-for="day in ['SU','MO','TU','WE','TH','FR','SA']" :key="day">
                                                <div class="text-center text-xs font-semibold text-gray-400 py-1" x-text="day"></div>
                                            </template>
                                        </div>
                                        <div class="grid grid-cols-7">
                                            <template x-for="(cell, idx) in calendarCells" :key="idx">
                                                <button type="button" @click="cell.day && !cell.disabled && selectDay(cell.day)" :disabled="cell.disabled || !cell.day"
                                                        :class="{'text-gray-300 cursor-default': !cell.day || cell.disabled, 'hover:bg-yellow-50 cursor-pointer text-gray-700': cell.day && !cell.disabled && !cell.selected, 'bg-yellow-400 text-gray-900 font-bold hover:bg-yellow-500 rounded-lg shadow-sm': cell.selected, 'font-semibold text-primary': cell.today && !cell.selected}"
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
                                                <div class="relative h-[210px] w-16 overflow-hidden rounded-xl bg-gray-50 border border-gray-200">
                                                    <div class="pointer-events-none absolute inset-x-0 top-[75px] h-[42px] bg-yellow-400/15 border-y border-yellow-400/30 rounded-md z-10"></div>
                                                    <div class="overflow-y-auto h-full snap-y snap-mandatory scroll-smooth hide-scrollbar" x-ref="hourScroll" @scroll.debounce.100ms="snapHour()">
                                                        <div class="h-[84px]"></div>
                                                        <template x-for="h in hours" :key="'h'+h">
                                                            <button type="button" @click="pickerHour = h; scrollToHour(h)" :class="pickerHour === h ? 'text-gray-900 font-bold text-base' : 'text-gray-400 text-sm'" class="w-full h-[42px] flex items-center justify-center snap-center transition-all duration-150 hover:text-gray-600" x-text="h"></button>
                                                        </template>
                                                        <div class="h-[84px]"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex flex-col items-center justify-center pt-6"><span class="text-2xl font-bold text-gray-300">:</span></div>
                                            <div class="relative">
                                                <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider text-center mb-2">Min</p>
                                                <div class="relative h-[210px] w-16 overflow-hidden rounded-xl bg-gray-50 border border-gray-200">
                                                    <div class="pointer-events-none absolute inset-x-0 top-[75px] h-[42px] bg-yellow-400/15 border-y border-yellow-400/30 rounded-md z-10"></div>
                                                    <div class="overflow-y-auto h-full snap-y snap-mandatory scroll-smooth hide-scrollbar" x-ref="minScroll" @scroll.debounce.100ms="snapMinute()">
                                                        <div class="h-[84px]"></div>
                                                        <template x-for="m in minutes" :key="'m'+m">
                                                            <button type="button" @click="pickerMinute = m; scrollToMinute(m)" :class="pickerMinute === m ? 'text-gray-900 font-bold text-base' : 'text-gray-400 text-sm'" class="w-full h-[42px] flex items-center justify-center snap-center transition-all duration-150 hover:text-gray-600" x-text="m"></button>
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

                        </div>

                        
                        <div>
                            <h3 class="text-sm font-bold text-gray-900 mb-2">Map view</h3>
                            <div id="routeMap" class="w-full h-52 rounded-xl overflow-hidden border border-gray-200 bg-gray-100"></div>
                        </div>

                        
                        <div class="flex gap-6">
                            <div>
                                <p class="text-xs text-gray-400 mb-0.5">Estimated duration</p>
                                <p class="text-base font-bold text-gray-900" x-text="routeDuration || '—'"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-0.5">Distance</p>
                                <p class="text-base font-bold text-gray-900" x-text="routeDistance || '—'"></p>
                            </div>
                        </div>

                    </div>
                </div>

                
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-6">Your ride</h1>

                    <div class="divide-y divide-gray-100">
                        <?php foreach ($vehicles as $vehicle): ?>
                        <div class="py-6 first:pt-0">
                            <div class="flex flex-col sm:flex-row items-start gap-5">
                                <div class="w-full sm:w-48 h-32 flex-shrink-0 flex items-center justify-center">
                                    <img src="<?= e(url($vehicle->image)) ?>" alt="<?= e($vehicle->name) ?>" class="max-h-full max-w-full object-contain">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2 mb-3">
                                        <h3 class="text-xl font-bold text-gray-900"><?= e($vehicle->name) ?></h3>
                                        <?php if ($vehicle->car_model): ?>
                                        <span class="text-xs text-gray-600 bg-gray-100 border border-gray-200 rounded-full px-2.5 py-1 whitespace-nowrap font-medium"><?= e($vehicle->car_model) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex items-center gap-4 text-sm text-gray-500">
                                        <span class="flex items-center gap-1.5"><i class="fa-solid fa-users text-xs text-gray-400"></i> <?= e($vehicle->passengers) ?></span>
                                        <span class="flex items-center gap-1.5"><i class="fa-solid fa-suitcase-rolling text-xs text-gray-400"></i> <?= e($vehicle->suitcases) ?></span>
                                        <span class="flex items-center gap-1.5"><i class="fa-solid fa-bag-shopping text-xs text-gray-400"></i> <?= e($vehicle->suitcases) ?></span>
                                    </div>
                                </div>
                                <div class="flex sm:flex-col items-center sm:items-end gap-4 sm:gap-3 w-full sm:w-auto flex-shrink-0">
                                    <p class="text-2xl font-bold text-gray-900">
                                        &pound;<span x-text="getVehiclePrice(<?= e($vehicle->id) ?>)"><?= e(number_format($vehicle->price, 2)) ?></span>
                                    </p>
                                    <button @click="selectVehicle(<?= e($vehicle->id) ?>)"
                                            :class="selectedVehicleId === <?= e($vehicle->id) ?>
                                                ? 'bg-yellow-400 text-gray-900 border-yellow-400'
                                                : 'bg-yellow-400 hover:bg-yellow-500 text-gray-900 border-yellow-400'"
                                            class="inline-flex items-center gap-2 font-semibold rounded-xl px-6 py-2.5 border-2 transition-all hover:shadow-md active:scale-[0.97]">
                                        <template x-if="selectedVehicleId === <?= e($vehicle->id) ?>">
                                            <span class="flex items-center gap-2"><i class="fa-solid fa-check text-sm"></i> Selected</span>
                                        </template>
                                        <template x-if="selectedVehicleId !== <?= e($vehicle->id) ?>">
                                            <span class="flex items-center gap-2">Select <i class="fa-solid fa-chevron-right text-xs"></i></span>
                                        </template>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>
        </div>

        
        <div x-show="selectedVehicleId" x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-y-full opacity-0"
             x-transition:enter-end="translate-y-0 opacity-100"
             class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-[0_-4px_20px_rgba(0,0,0,0.08)] z-40">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <img :src="selectedVehicleImage" class="h-12 object-contain hidden sm:block" :alt="selectedVehicleName">
                        <div>
                            <p class="text-sm text-gray-500">Selected: <span class="font-semibold text-gray-900" x-text="selectedVehicleName"></span></p>
                            <p class="text-lg font-bold text-gray-900">&pound;<span x-text="selectedVehiclePrice"></span></p>
                        </div>
                    </div>
                    <button @click="goToTransferDetails()"
                            class="bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-semibold rounded-xl px-8 py-3 transition-all hover:shadow-md active:scale-[0.98] flex items-center gap-2">
                        Continue <i class="fa-solid fa-arrow-right text-sm"></i>
                    </button>
                </div>
            </div>
        </div>

        

    </div>

    <script>
        function rideSelector() {
            const vehicles = <?= json_encode($vehicles) ?>;
            const now = new Date();
            return {
                vehicles: vehicles,
                from: <?= json_encode($from ?? '') ?>,
                to: <?= json_encode($to ?? '') ?>,
                // Date+time picker
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
                departDisplay: <?= json_encode(($departDate ? format_date($departDate, 'D, d M Y') : '') . ($departTime ? ' at ' . $departTime : '')) ?>,
                departDate: <?= json_encode($departDate ?? '') ?>,
                departTime: <?= json_encode($departTime ?? '') ?>,
                departDateValue: <?= json_encode($departDate ?? '') ?>,
                departTimeValue: <?= json_encode($departTime ?? '') ?>,
                todayISO: now.toISOString().split('T')[0],
                monthNames: ['January','February','March','April','May','June','July','August','September','October','November','December'],
                hours: Array.from({length: 24}, (_, i) => String(i).padStart(2, '0')),
                minutes: Array.from({length: 12}, (_, i) => String(i * 5).padStart(2, '0')),
                get yearOptions() { const y = new Date().getFullYear(); return [y, y+1, y+2]; },

                // Vehicle selection
                selectedVehicleId: null,
                selectedVehicleName: '',
                selectedVehiclePrice: '',
                selectedVehicleImage: '',

                // Route miles for pricing
                routeMiles: 0,

                // Location dropdowns (same names as home page)
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

                // Map & route
                map: null,
                routeLayer: null,
                markersLayer: null,
                routeDuration: '',
                routeDistance: '',

                // Pricing lookup table from real booking data (median prices at key distances)
                // [miles, saloon, estate, executive, pc, empv, minibus, 16pax]
                pricingTable: [
                    [3,   34,  39,  45,  45,  64,  67,  155],
                    [5,   38,  42,  48,  48,  66,  70,  155],
                    [8,   52,  58,  68,  68,  93,  98,  198],
                    [10,  60,  68,  78,  78, 106, 112,  228],
                    [13,  72,  82,  95,  95, 130, 136,  280],
                    [15,  82,  93, 108, 108, 148, 155,  310],
                    [18,  88, 100, 115, 115, 158, 150,  305],
                    [20,  92, 105, 120, 120, 165, 155,  315],
                    [25,  97, 111, 130, 130, 178, 158,  325],
                    [30,  102, 117, 137, 137, 188, 162,  332],
                    [35,  108, 124, 145, 145, 198, 165,  338],
                    [40, 113, 130, 152, 152, 210, 168,  345],
                    [50, 124, 143, 167, 167, 231, 184,  372],
                    [60, 133, 153, 179, 179, 248, 196,  396],
                    [70, 142, 162, 190, 190, 264, 209,  422],
                    [80, 152, 175, 206, 206, 285, 225,  455],
                    [100,178, 205, 242, 242, 335, 265,  535],
                ],

                // Vehicle slug -> column index in pricing table
                vehiclePricingIndex: {
                    'saloon': 1, 'estate': 2, 'executive': 3, 'people-carrier': 4,
                    'executive-people-carrier': 5, 'minibus': 6, '16pax': 7
                },

                getVehiclePrice(id) {
                    const v = this.vehicles.find(v => v.id === id);
                    if (!v) return '0.00';
                    return this.calcVehiclePrice(v);
                },

                calcVehiclePrice(v) {
                    if (this.routeMiles > 0) {
                        const col = this.vehiclePricingIndex[v.slug];
                        if (col === undefined) return parseFloat(v.price).toFixed(2);

                        const miles = this.routeMiles;
                        const table = this.pricingTable;

                        // Below minimum distance
                        if (miles <= table[0][0]) return table[0][col].toFixed(2);
                        // Above maximum distance - extrapolate from last two rows
                        if (miles >= table[table.length-1][0]) {
                            const l2 = table[table.length-2], l1 = table[table.length-1];
                            const rate = (l1[col] - l2[col]) / (l1[0] - l2[0]);
                            return Math.round(l1[col] + rate * (miles - l1[0])).toFixed(2);
                        }

                        // Interpolate between two nearest rows
                        for (let i = 0; i < table.length - 1; i++) {
                            if (miles >= table[i][0] && miles < table[i+1][0]) {
                                const lo = table[i], hi = table[i+1];
                                const frac = (miles - lo[0]) / (hi[0] - lo[0]);
                                const price = lo[col] + frac * (hi[col] - lo[col]);
                                return Math.round(price).toFixed(2);
                            }
                        }
                        return parseFloat(v.price).toFixed(2);
                    }
                    // Fallback: show minimum price when no route calculated yet
                    return parseFloat(v.price).toFixed(2);
                },

                selectVehicle(id) {
                    this.selectedVehicleId = id;
                    const v = this.vehicles.find(v => v.id === id);
                    if (v) {
                        this.selectedVehicleName = v.name;
                        this.selectedVehiclePrice = this.calcVehiclePrice(v);
                        this.selectedVehicleImage = v.image;
                    }
                },

                goToTransferDetails() {
                    const params = new URLSearchParams({
                        from: this.from,
                        to: this.to,
                        depart_date: this.departDate,
                        depart_time: this.departTime,
                        vehicle_id: this.selectedVehicleId,
                        total_price: this.selectedVehiclePrice,
                        distance: this.routeDistance,
                        duration: this.routeDuration
                    });
                    window.location.href = '/transfer-details?' + params.toString();
                },

                // --- Date picker methods (same as home page) ---
                init() {
                    const today = new Date();
                    this.pickerMonth = today.getMonth();
                    this.pickerYear = today.getFullYear();
                    this.buildCalendar();
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
                    const today = new Date(); today.setHours(0,0,0,0);
                    const cells = [];
                    for (let i = 0; i < firstDay; i++) cells.push({ day: null, disabled: true, selected: false, today: false });
                    for (let d = 1; d <= daysInMonth; d++) {
                        const cellDate = new Date(this.pickerYear, this.pickerMonth, d);
                        cells.push({
                            day: d, disabled: cellDate < today, today: cellDate.getTime() === today.getTime(),
                            selected: this.selectedDay === d && this.pickerMonth === this._selMonth && this.pickerYear === this._selYear
                        });
                    }
                    this.calendarCells = cells;
                },

                prevMonth() { if (this.pickerMonth === 0) { this.pickerMonth = 11; this.pickerYear--; } else { this.pickerMonth--; } this.buildCalendar(); },
                nextMonth() { if (this.pickerMonth === 11) { this.pickerMonth = 0; this.pickerYear++; } else { this.pickerMonth++; } this.buildCalendar(); },

                selectDay(day) {
                    this.selectedDay = day; this._selMonth = this.pickerMonth; this._selYear = this.pickerYear; this.buildCalendar();
                    this.$nextTick(() => { setTimeout(() => { this.pickerTab = 'time'; this.$nextTick(() => { this.scrollToHour(this.pickerHour); this.scrollToMinute(this.pickerMinute); }); }, 250); });
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
                    this.departDate = `${this._selYear}-${m}-${d}`;
                    this.departDateValue = this.departDate;
                    this.departTime = `${this.pickerHour}:${this.pickerMinute}`;
                    this.departTimeValue = this.departTime;
                    this.departDisplay = `${this.formatSelectedDate()} at ${this.pickerHour}:${this.pickerMinute}`;
                    this.datePickerOpen = false;
                },

                scrollToHour(h) { const el = this.$refs.hourScroll; if (!el) return; const idx = this.hours.indexOf(h); if (idx >= 0) el.scrollTo({ top: idx * 42, behavior: 'smooth' }); },
                scrollToMinute(m) { const el = this.$refs.minScroll; if (!el) return; const idx = this.minutes.indexOf(m); if (idx >= 0) el.scrollTo({ top: idx * 42, behavior: 'smooth' }); },
                snapHour() { const el = this.$refs.hourScroll; if (!el) return; const idx = Math.round(el.scrollTop / 42); this.pickerHour = this.hours[Math.max(0, Math.min(idx, this.hours.length - 1))]; },
                snapMinute() { const el = this.$refs.minScroll; if (!el) return; const idx = Math.round(el.scrollTop / 42); this.pickerMinute = this.minutes[Math.max(0, Math.min(idx, this.minutes.length - 1))]; },

                swapLocations() {
                    let t = this.from;
                    this.from = this.to;
                    this.to = t;
                    this.calcRoute();
                },

                // --- Location autocomplete (same as home page) ---
                async onFromFocus() {
                    this.fromHighlight = -1;
                    this.toOpen = false;
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
                    await Promise.all([this.fetchAirports('from', q), this.fetchNominatim('from', q)]);
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
                    await Promise.all([this.fetchAirports('to', q), this.fetchNominatim('to', q)]);
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
                    this.calcRoute();
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
                },

                // --- Map & Route ---
                initMap() {
                    this.$nextTick(() => {
                        this.map = L.map('routeMap', { zoomControl: false, attributionControl: true }).setView([51.505, -0.09], 7);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; OpenStreetMap'
                        }).addTo(this.map);
                        L.control.zoom({ position: 'bottomright' }).addTo(this.map);
                        this.routeLayer = L.layerGroup().addTo(this.map);
                        this.markersLayer = L.layerGroup().addTo(this.map);

                        if (this.from && this.to) {
                            this.calcRoute();
                        }
                    });
                },

                async calcRoute() {
                    if (!this.from || !this.to || !this.map) return;

                    // Geocode both locations
                    const [fromCoord, toCoord] = await Promise.all([
                        this.geocode(this.from),
                        this.geocode(this.to)
                    ]);

                    if (!fromCoord || !toCoord) {
                        this.routeDuration = 'Unable to calculate';
                        this.routeDistance = 'Unable to calculate';
                        return;
                    }

                    // Clear old layers
                    this.routeLayer.clearLayers();
                    this.markersLayer.clearLayers();

                    // Custom markers
                    const startIcon = L.divIcon({
                        className: '',
                        html: '<div style="width:14px;height:14px;border-radius:50%;background:#0C6291;border:3px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.3)"></div>',
                        iconSize: [14, 14], iconAnchor: [7, 7]
                    });
                    const endIcon = L.divIcon({
                        className: '',
                        html: '<div style="width:14px;height:14px;border-radius:50%;background:#dc2626;border:3px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.3)"></div>',
                        iconSize: [14, 14], iconAnchor: [7, 7]
                    });

                    L.marker([fromCoord.lat, fromCoord.lon], { icon: startIcon }).addTo(this.markersLayer);
                    L.marker([toCoord.lat, toCoord.lon], { icon: endIcon }).addTo(this.markersLayer);

                    // Get route from OSRM
                    try {
                        const url = `https://router.project-osrm.org/route/v1/driving/${fromCoord.lon},${fromCoord.lat};${toCoord.lon},${toCoord.lat}?overview=full&geometries=geojson`;
                        const res = await fetch(url);
                        const data = await res.json();

                        if (data.routes && data.routes.length > 0) {
                            const route = data.routes[0];

                            // Draw blue route line
                            const coords = route.geometry.coordinates.map(c => [c[1], c[0]]);
                            L.polyline(coords, {
                                color: '#2563eb', weight: 4, opacity: 0.8,
                                smoothFactor: 1, lineCap: 'round'
                            }).addTo(this.routeLayer);

                            // Fit map bounds
                            this.map.fitBounds(L.polyline(coords).getBounds().pad(0.15));

                            // Calculate duration & distance
                            const dur = route.duration; // seconds
                            const dist = route.distance; // meters
                            const hours = Math.floor(dur / 3600);
                            const mins = Math.round((dur % 3600) / 60);
                            this.routeDuration = hours > 0 ? `${hours}h ${mins}m` : `${mins}m`;
                            const miles = (dist / 1609.344) + 1.5;
                            this.routeMiles = miles;
                            this.routeDistance = `${miles.toFixed(1)} mi`;

                            // Recalc selected vehicle price if one is selected
                            if (this.selectedVehicleId) {
                                const sv = this.vehicles.find(v => v.id === this.selectedVehicleId);
                                if (sv) this.selectedVehiclePrice = this.calcVehiclePrice(sv);
                            }
                        }
                    } catch (e) {
                        // Fallback: straight line
                        L.polyline([[fromCoord.lat, fromCoord.lon], [toCoord.lat, toCoord.lon]], {
                            color: '#2563eb', weight: 3, opacity: 0.6, dashArray: '8 4'
                        }).addTo(this.routeLayer);
                        this.map.fitBounds([[fromCoord.lat, fromCoord.lon], [toCoord.lat, toCoord.lon]], { padding: [30, 30] });
                        this.routeDuration = '—';
                        this.routeDistance = '—';
                    }
                },

                async geocode(query) {
                    try {
                        const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&countrycodes=gb&limit=1`, {headers:{'Accept-Language':'en'}});
                        const data = await res.json();
                        if (data.length > 0) return { lat: parseFloat(data[0].lat), lon: parseFloat(data[0].lon) };
                    } catch(e) {}
                    return null;
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .leaflet-top, .leaflet-bottom, .leaflet-control { z-index: 400 !important; }
    </style>


<?php require BASE_PATH . '/templates/layouts/footer.php'; ?>