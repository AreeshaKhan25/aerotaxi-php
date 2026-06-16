<?php
// Ported from: booking/transfer-details.blade.php
$__page_title = 'Transfer Details - AeroTAXI';

$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';
$departDate = $_GET['depart_date'] ?? '';
$departTime = $_GET['depart_time'] ?? '';
$vehicleId = $_GET['vehicle_id'] ?? '';
$totalPrice = $_GET['total_price'] ?? '';
$distance = $_GET['distance'] ?? '';
$duration = $_GET['duration'] ?? '';

$vehicle = Database::fetch('SELECT * FROM vehicles WHERE id = ?', [$vehicleId]);
if (!$vehicle) {
    redirect(url('/your-ride'));
}
?>
<?php require BASE_PATH . '/templates/layouts/header.php'; ?>
<?php $__page_title = 'Transfer Details - AeroTAXI'; ?>
<?php ob_start(); ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<div x-data="transferForm()" x-init="initMap()" class="min-h-screen bg-gray-50">

        
        <div class="bg-white border-b border-gray-100">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
                <div class="flex items-center justify-center">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-full bg-yellow-400 text-gray-900 font-bold text-sm flex items-center justify-center shadow-sm">1</div>
                        <span class="text-sm font-semibold text-gray-900 hidden sm:inline">Your ride</span>
                    </div>
                    <div class="w-12 sm:w-24 h-[2px] bg-yellow-400 mx-2 sm:mx-3"></div>
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-full bg-yellow-400 text-gray-900 font-bold text-sm flex items-center justify-center shadow-sm">2</div>
                        <span class="text-sm font-semibold text-gray-900 hidden sm:inline">Transfer details</span>
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

                
                <div class="w-full lg:w-[380px] flex-shrink-0">
                    <div class="lg:sticky lg:top-24 space-y-5">

                        <h2 class="text-xl font-bold text-gray-900">Your journey</h2>

                        
                        <div class="bg-cream rounded-2xl p-5 space-y-0">
                            <div class="flex items-start gap-3 py-2.5 border-b border-gray-200/60">
                                <span class="text-sm text-gray-400 font-medium w-14 flex-shrink-0">From</span>
                                <p class="text-sm font-semibold text-gray-900 min-w-0 truncate"><?= e($from) ?></p>
                            </div>
                            <div class="flex justify-end -my-3 relative z-10">
                                <div class="w-8 h-8 rounded-full bg-yellow-400 flex items-center justify-center shadow-sm">
                                    <img src="/images/swap-icon.png" alt="" class="w-6 h-6">
                                </div>
                            </div>
                            <div class="flex items-start gap-3 py-2.5 border-b border-gray-200/60">
                                <span class="text-sm text-gray-400 font-medium w-14 flex-shrink-0">To</span>
                                <p class="text-sm font-semibold text-gray-900 min-w-0 truncate"><?= e($to) ?></p>
                            </div>
                            <div class="flex items-center gap-3 py-2.5">
                                <span class="text-sm text-gray-400 font-medium w-14 flex-shrink-0">Depart</span>
                                <p class="text-sm font-semibold text-gray-900">
                                    <?php if ($departDate): ?>
                                        <?= e(format_date($departDate, 'l')) ?>
                                        <span class="mx-1 text-gray-300">|</span>
                                        <?= e(format_date($departDate, 'j M Y')) ?>
                                        <?php if ($departTime): ?>
                                            <span class="mx-1 text-gray-300">|</span><?= e($departTime) ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>

                        
                        <div>
                            <h3 class="text-sm font-bold text-gray-900 mb-2">Map view</h3>
                            <div id="routeMap" class="w-full h-44 rounded-xl overflow-hidden border border-gray-200 bg-gray-100"></div>
                        </div>

                        
                        <div class="flex gap-6">
                            <div>
                                <p class="text-xs text-gray-400 mb-0.5">Estimated duration</p>
                                <p class="text-base font-bold text-gray-900"><?= e($duration ?: '—') ?></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-0.5">Distance</p>
                                <p class="text-base font-bold text-gray-900"><?= e($distance ?: '—') ?></p>
                            </div>
                        </div>

                        
                        <div>
                            <h3 class="text-sm font-bold text-gray-900 mb-3">Your ride</h3>
                            <div class="bg-white rounded-xl border border-gray-100 p-4">
                                <div class="flex items-center gap-4">
                                    <img src="<?= e(url($vehicle->image)) ?>" alt="<?= e($vehicle->name) ?>" class="h-16 object-contain">
                                    <div class="flex-1">
                                        <p class="font-bold text-gray-900"><?= e($vehicle->name) ?></p>
                                        <div class="flex items-center gap-3 text-xs text-gray-500 mt-1">
                                            <span><i class="fa-solid fa-users text-gray-400"></i> <?= e($vehicle->passengers) ?></span>
                                            <span><i class="fa-solid fa-suitcase-rolling text-gray-400"></i> <?= e($vehicle->suitcases) ?></span>
                                        </div>
                                    </div>
                                    <p class="text-lg font-bold text-gray-900">&pound;<?= e(number_format($totalPrice, 2)) ?></p>
                                </div>
                            </div>
                        </div>

                        
                        <div class="bg-yellow-50 border-2 border-yellow-400 rounded-xl px-5 py-3 flex items-center justify-between">
                            <span class="font-bold text-gray-900">Total</span>
                            <span class="text-xl font-bold text-gray-900" x-text="'£' + totalWithExtras.toFixed(2)"></span>
                        </div>

                    </div>
                </div>

                
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-8">Booking details</h1>

                    <form action="<?= base_url('booking') ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="from_location" value="<?= e($from) ?>">
                        <input type="hidden" name="to_location" value="<?= e($to) ?>">
                        <input type="hidden" name="depart_date" value="<?= e($departDate) ?>">
                        <input type="hidden" name="depart_time" value="<?= e($departTime) ?>">
                        <input type="hidden" name="vehicle_id" value="<?= e($vehicle->id) ?>">
                        <input type="hidden" name="total_price" :value="totalWithExtras.toFixed(2)">

                        <div class="space-y-8">

                            
                            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                                <h2 class="text-lg font-bold text-gray-900 mb-2">Transfer</h2>
                                <p class="text-sm text-gray-500 mb-5">Your driver keeps an eye on your flight status and will adjust the pickup time if your flight is delayed.</p>

                                <div class="space-y-4">
                                    
                                    <div x-show="flightStatus !== null" x-cloak x-transition>
                                        <div x-show="flightStatus === 'valid'" class="flex items-start gap-2 bg-green-50 border border-green-200 rounded-xl px-4 py-3">
                                            <i class="fa-solid fa-circle-check text-green-500 mt-0.5"></i>
                                            <div>
                                                <p class="text-sm font-semibold text-green-800">Flight verified</p>
                                                <p class="text-xs text-green-600 mt-0.5" x-text="flightInfo"></p>
                                            </div>
                                        </div>
                                        <div x-show="flightStatus === 'not_found'" class="flex items-start gap-2 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3">
                                            <i class="fa-solid fa-triangle-exclamation text-amber-500 mt-0.5"></i>
                                            <p class="text-sm text-amber-800">The flight was not found for the specified date. But if you're sure it is correct, feel free to proceed with the booking.</p>
                                        </div>
                                        <div x-show="flightStatus === 'error'" class="flex items-start gap-2 bg-red-50 border border-red-200 rounded-xl px-4 py-3">
                                            <i class="fa-solid fa-circle-xmark text-red-500 mt-0.5"></i>
                                            <p class="text-sm text-red-700">Unable to validate flight. You can still proceed with the booking.</p>
                                        </div>
                                    </div>

                                    <div class="flex gap-3">
                                        <input type="text" name="flight_number" x-model="flightNumber"
                                               placeholder="Flight Number (e.g. BA1234)"
                                               @keydown.enter.prevent="validateFlight()"
                                               class="flex-1 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent uppercase">
                                        <button type="button" @click="validateFlight()"
                                                :disabled="!flightNumber || flightValidating"
                                                :class="!flightNumber ? 'opacity-50 cursor-not-allowed' : 'hover:bg-yellow-100'"
                                                class="bg-cream text-gray-700 font-medium rounded-xl px-5 py-3 text-sm transition flex items-center gap-2 border border-gray-200">
                                            <i x-show="!flightValidating" class="fa-solid fa-plane text-xs"></i>
                                            <i x-show="flightValidating" class="fa-solid fa-spinner fa-spin text-xs"></i>
                                            <span x-text="flightValidating ? 'Checking...' : 'Validate'"></span>
                                        </button>
                                    </div>
                                    <textarea name="note_to_driver" rows="2" placeholder="Note to driver" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent resize-none"></textarea>
                                </div>
                            </div>

                            
                            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                                <h2 class="text-lg font-bold text-gray-900 mb-5">Passengers</h2>

                                <div class="flex flex-wrap gap-6 mb-6">
                                    
                                    <div class="flex items-center gap-3">
                                        <i class="fa-solid fa-users text-gray-400"></i>
                                        <button type="button" @click="passengers = Math.max(1, passengers - 1)" class="w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center text-gray-500 hover:bg-gray-50 transition">
                                            <i class="fa-solid fa-minus text-xs"></i>
                                        </button>
                                        <span class="w-6 text-center font-semibold text-gray-900" x-text="passengers"></span>
                                        <button type="button" @click="passengers = Math.min(<?= e($vehicle->passengers) ?>, passengers + 1)" class="w-8 h-8 rounded-full bg-yellow-400 hover:bg-yellow-500 flex items-center justify-center text-gray-900 transition">
                                            <i class="fa-solid fa-plus text-xs"></i>
                                        </button>
                                    </div>
                                    
                                    <div class="flex items-center gap-3">
                                        <i class="fa-solid fa-suitcase-rolling text-gray-400"></i>
                                        <button type="button" @click="suitcases = Math.max(0, suitcases - 1)" class="w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center text-gray-500 hover:bg-gray-50 transition">
                                            <i class="fa-solid fa-minus text-xs"></i>
                                        </button>
                                        <span class="w-6 text-center font-semibold text-gray-900" x-text="suitcases"></span>
                                        <button type="button" @click="suitcases = Math.min(<?= e($vehicle->suitcases) ?>, suitcases + 1)" class="w-8 h-8 rounded-full bg-yellow-400 hover:bg-yellow-500 flex items-center justify-center text-gray-900 transition">
                                            <i class="fa-solid fa-plus text-xs"></i>
                                        </button>
                                    </div>
                                    
                                    <div class="flex items-center gap-3">
                                        <i class="fa-solid fa-bag-shopping text-gray-400"></i>
                                        <button type="button" @click="handLuggage = Math.max(0, handLuggage - 1)" class="w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center text-gray-500 hover:bg-gray-50 transition">
                                            <i class="fa-solid fa-minus text-xs"></i>
                                        </button>
                                        <span class="w-6 text-center font-semibold text-gray-900" x-text="handLuggage"></span>
                                        <button type="button" @click="handLuggage++" class="w-8 h-8 rounded-full bg-yellow-400 hover:bg-yellow-500 flex items-center justify-center text-gray-900 transition">
                                            <i class="fa-solid fa-plus text-xs"></i>
                                        </button>
                                    </div>
                                </div>

                                <p class="text-sm text-gray-500 mb-4">The details of your lead passenger</p>

                                <div class="space-y-4">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <input type="text" name="first_name" required placeholder="First Name*" class="border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent">
                                        <input type="text" name="last_name" required placeholder="Last Name*" class="border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent">
                                    </div>
                                    <input type="email" name="email" required placeholder="Email*" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent">
                                    <div class="flex gap-2">
                                        <div class="relative" x-data="phoneCountry()">
                                            <button type="button" @click="open = !open" class="flex items-center gap-1.5 border border-gray-200 rounded-xl px-3 py-3 bg-gray-50 text-sm text-gray-700 hover:bg-gray-100 transition cursor-pointer h-full whitespace-nowrap">
                                                <span x-text="selectedFlag" class="text-base"></span>
                                                <span x-text="'+' + selectedCode" class="font-medium"></span>
                                                <i class="fa-solid fa-chevron-down text-[8px] text-gray-400 ml-0.5"></i>
                                            </button>
                                            <input type="hidden" name="country_code" :value="'+' + selectedCode">

                                            
                                            <div x-show="open" x-cloak @click.outside="open = false"
                                                 x-transition:enter="transition ease-out duration-150"
                                                 x-transition:enter-start="opacity-0 -translate-y-1"
                                                 x-transition:enter-end="opacity-100 translate-y-0"
                                                 class="absolute z-50 left-0 mt-1 w-64 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden">
                                                <div class="p-2 border-b border-gray-100">
                                                    <input type="text" x-model="search" placeholder="Search country..." @keydown.escape="open = false"
                                                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400" x-ref="countrySearch">
                                                </div>
                                                <div class="max-h-48 overflow-y-auto">
                                                    <template x-for="c in filteredCountries" :key="c.code">
                                                        <button type="button" @click="selectCountry(c)"
                                                                :class="selectedCode === c.code ? 'bg-yellow-50' : 'hover:bg-gray-50'"
                                                                class="w-full text-left px-3 py-2 text-sm flex items-center gap-2 transition-colors">
                                                            <span x-text="c.flag" class="text-base"></span>
                                                            <span class="flex-1 truncate" x-text="c.name"></span>
                                                            <span class="text-gray-400 text-xs" x-text="'+' + c.code"></span>
                                                        </button>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="tel" name="phone" required placeholder="Phone*" class="flex-1 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent">
                                    </div>
                                </div>
                            </div>

                            
                            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                                <h2 class="text-lg font-bold text-gray-900 mb-2">Optional extras</h2>
                                <p class="text-sm text-gray-500 mb-5">Flight tracking, child seats and cancellation are included for free.</p>

                                <div class="divide-y divide-gray-100">
                                    
                                    <div class="py-4 flex items-start justify-between gap-4">
                                        <div>
                                            <p class="font-bold text-sm text-gray-900">Meet & Greet</p>
                                            <p class="text-xs text-gray-500 mt-0.5">Your driver will greet you with your name on a sign at the airport terminal arrivals hall</p>
                                        </div>
                                        <div class="flex items-center gap-3 flex-shrink-0">
                                            <span class="text-sm font-semibold text-gray-900">&pound;10.00</span>
                                            <button type="button" @click="meetGreet = !meetGreet"
                                                    :class="meetGreet ? 'bg-yellow-400 text-gray-900 border-yellow-400' : 'bg-white text-gray-600 border-gray-300 hover:border-gray-400'"
                                                    class="text-xs font-semibold border rounded-lg px-3 py-1.5 transition" x-text="meetGreet ? 'Added' : 'Add'"></button>
                                        </div>
                                    </div>

                                    
                                    <div class="py-4">
                                        <div class="flex items-start justify-between gap-4 mb-3">
                                            <div>
                                                <p class="font-bold text-sm text-gray-900">Child seats (Subject to availability)</p>
                                                <p class="text-xs text-gray-500 mt-0.5">Travel with peace of mind</p>
                                            </div>
                                            <span class="text-sm font-semibold text-green-600 flex-shrink-0">Free</span>
                                        </div>
                                        <div class="space-y-2 ml-4">
                                            <template x-for="(seat, i) in childSeats" :key="i">
                                                <div class="flex items-center justify-between">
                                                    <span class="text-xs text-gray-600" x-text="seat.label"></span>
                                                    <button type="button" @click="seat.added = !seat.added"
                                                            :class="seat.added ? 'bg-yellow-400 text-gray-900 border-yellow-400' : 'bg-white text-gray-600 border-gray-300 hover:border-gray-400'"
                                                            class="text-xs font-semibold border rounded-lg px-3 py-1.5 transition" x-text="seat.added ? 'Added' : 'Add'"></button>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    
                                    <div class="py-4 flex items-start justify-between gap-4">
                                        <div>
                                            <p class="font-bold text-sm text-gray-900">Pet</p>
                                            <p class="text-xs text-gray-500 mt-0.5">Bring your furry friend with you in a pet carrier or leash</p>
                                        </div>
                                        <div class="flex items-center gap-3 flex-shrink-0">
                                            <span class="text-sm font-semibold text-gray-900">&pound;0.00</span>
                                            <button type="button" @click="pet = !pet"
                                                    :class="pet ? 'bg-yellow-400 text-gray-900 border-yellow-400' : 'bg-white text-gray-600 border-gray-300 hover:border-gray-400'"
                                                    class="text-xs font-semibold border rounded-lg px-3 py-1.5 transition" x-text="pet ? 'Added' : 'Add'"></button>
                                        </div>
                                    </div>

                                    
                                    <div class="py-4 flex items-start justify-between gap-4">
                                        <div>
                                            <p class="font-bold text-sm text-gray-900">Oversized item</p>
                                            <p class="text-xs text-gray-500 mt-0.5">Including a child buggy/golf bag/ski gear</p>
                                        </div>
                                        <div class="flex items-center gap-3 flex-shrink-0">
                                            <span class="text-sm font-semibold text-gray-900">&pound;0.00</span>
                                            <button type="button" @click="oversized = !oversized"
                                                    :class="oversized ? 'bg-yellow-400 text-gray-900 border-yellow-400' : 'bg-white text-gray-600 border-gray-300 hover:border-gray-400'"
                                                    class="text-xs font-semibold border rounded-lg px-3 py-1.5 transition" x-text="oversized ? 'Added' : 'Add'"></button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            
                            <div class="bg-yellow-50 rounded-2xl p-5 flex items-center gap-4 border border-yellow-200">
                                <div class="flex-1">
                                    <h3 class="font-bold text-gray-900 mb-1">Need to Adjust? We've Got You!</h3>
                                    <p class="text-sm text-gray-600">Get a 100% refund if you cancel 24 hours before your transfer, or easily change your booking if your plans shift.</p>
                                </div>
                                <div class="text-4xl flex-shrink-0">🧳</div>
                            </div>

                            
                            <div>
                                <label class="flex items-start gap-3 cursor-pointer">
                                    <input type="checkbox" name="agree_promotions" value="1" class="mt-1 w-4 h-4 rounded border-gray-300 text-yellow-500 focus:ring-yellow-400">
                                    <span class="text-sm text-gray-600">I agree to receive booking confirmations and occasional promotions. No spam.</span>
                                </label>
                            </div>

                            
                            <button type="submit"
                                    class="w-full bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-semibold rounded-xl px-6 py-4 transition-all hover:shadow-md active:scale-[0.98] text-base flex items-center justify-center gap-2">
                                Continue to payment <i class="fa-solid fa-chevron-right text-sm"></i>
                            </button>

                            
                            <div class="grid grid-cols-3 gap-4 text-center">
                                <div>
                                    <i class="fa-solid fa-lock text-xl text-gray-400 mb-2"></i>
                                    <p class="text-xs font-semibold text-gray-700">Your data is encrypted</p>
                                    <p class="text-[10px] text-gray-400 mt-0.5">Payments are 100% PCI compliant</p>
                                </div>
                                <div>
                                    <i class="fa-solid fa-rotate-left text-xl text-gray-400 mb-2"></i>
                                    <p class="text-xs font-semibold text-gray-700">Get a 100% refund</p>
                                    <p class="text-[10px] text-gray-400 mt-0.5">If you cancel 24 hours before your transfer.</p>
                                </div>
                                <div>
                                    <i class="fa-solid fa-circle-check text-xl text-gray-400 mb-2"></i>
                                    <p class="text-xs font-semibold text-gray-700">Receive confirmation</p>
                                    <p class="text-[10px] text-gray-400 mt-0.5">Directly after hitting confirm payment.</p>
                                </div>
                            </div>

                            
                            <div class="pb-4">
                                <button type="button" onclick="history.back()" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-900 border border-gray-200 rounded-xl px-5 py-2.5 hover:bg-gray-50 transition">
                                    <i class="fa-solid fa-chevron-left text-xs"></i> Previous
                                </button>
                            </div>

                        </div>
                    </form>
                </div>

            </div>
        </div>

    </div>

    <script>
        function transferForm() {
            return {
                passengers: 1,
                suitcases: 0,
                handLuggage: 0,
                meetGreet: false,
                pet: false,
                oversized: false,

                // Flight validation
                flightNumber: '',
                flightValidating: false,
                flightStatus: null, // null, 'valid', 'not_found', 'error'
                flightInfo: '',
                childSeats: [
                    { label: 'Baby seat (0-1y, 0-13kg)', added: false },
                    { label: 'Toddler seat (1-4y, 9-18kg)', added: false },
                    { label: 'Child seat (3-7y, 15-25kg)', added: false },
                    { label: 'Booster seat (6-12y, 22-36kg)', added: false },
                ],
                basePrice: <?= $totalPrice ?>,

                get totalWithExtras() {
                    let total = this.basePrice;
                    if (this.meetGreet) total += 10;
                    return total;
                },

                async validateFlight() {
                    const fn = this.flightNumber.trim().toUpperCase();
                    if (!fn) return;

                    this.flightValidating = true;
                    this.flightStatus = null;
                    this.flightInfo = '';

                    try {
                        const res = await fetch(`/api/flight/validate?flight_number=${encodeURIComponent(fn)}&date=<?= $departDate ?>`);
                        const data = await res.json();

                        if (data.found) {
                            this.flightStatus = 'valid';
                            this.flightInfo = data.info;
                        } else {
                            this.flightStatus = 'not_found';
                        }
                    } catch (e) {
                        this.flightStatus = 'error';
                    }

                    this.flightValidating = false;
                },

                map: null,

                initMap() {
                    this.$nextTick(() => {
                        this.map = L.map('routeMap', { zoomControl: false, attributionControl: true }).setView([51.505, -0.09], 7);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; OpenStreetMap'
                        }).addTo(this.map);
                        this.drawRoute();
                    });
                },

                async drawRoute() {
                    const from = <?= json_encode($from) ?>;
                    const to = <?= json_encode($to) ?>;

                    const [fc, tc] = await Promise.all([this.geocode(from), this.geocode(to)]);
                    if (!fc || !tc) return;

                    const startIcon = L.divIcon({ className: '', html: '<div style="width:12px;height:12px;border-radius:50%;background:#0C6291;border:3px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.3)"></div>', iconSize: [12,12], iconAnchor: [6,6] });
                    const endIcon = L.divIcon({ className: '', html: '<div style="width:12px;height:12px;border-radius:50%;background:#dc2626;border:3px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.3)"></div>', iconSize: [12,12], iconAnchor: [6,6] });

                    L.marker([fc.lat, fc.lon], { icon: startIcon }).addTo(this.map);
                    L.marker([tc.lat, tc.lon], { icon: endIcon }).addTo(this.map);

                    try {
                        const res = await fetch(`https://router.project-osrm.org/route/v1/driving/${fc.lon},${fc.lat};${tc.lon},${tc.lat}?overview=full&geometries=geojson`);
                        const data = await res.json();
                        if (data.routes && data.routes.length > 0) {
                            const coords = data.routes[0].geometry.coordinates.map(c => [c[1], c[0]]);
                            L.polyline(coords, { color: '#2563eb', weight: 4, opacity: 0.8, smoothFactor: 1 }).addTo(this.map);
                            this.map.fitBounds(L.polyline(coords).getBounds().pad(0.15));
                        }
                    } catch (e) {
                        this.map.fitBounds([[fc.lat, fc.lon], [tc.lat, tc.lon]], { padding: [20, 20] });
                    }
                },

                async geocode(q) {
                    try {
                        const r = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(q)}&countrycodes=gb&limit=1`, {headers:{'Accept-Language':'en'}});
                        const d = await r.json();
                        if (d.length) return { lat: parseFloat(d[0].lat), lon: parseFloat(d[0].lon) };
                    } catch(e) {}
                    return null;
                }
            }
        }
    </script>

    <script>
        function phoneCountry() {
            const countries = [
                { name: 'United Kingdom', code: '44', flag: '🇬🇧' },
                { name: 'United States', code: '1', flag: '🇺🇸' },
                { name: 'Ireland', code: '353', flag: '🇮🇪' },
                { name: 'France', code: '33', flag: '🇫🇷' },
                { name: 'Germany', code: '49', flag: '🇩🇪' },
                { name: 'Spain', code: '34', flag: '🇪🇸' },
                { name: 'Italy', code: '39', flag: '🇮🇹' },
                { name: 'Netherlands', code: '31', flag: '🇳🇱' },
                { name: 'Belgium', code: '32', flag: '🇧🇪' },
                { name: 'Portugal', code: '351', flag: '🇵🇹' },
                { name: 'Switzerland', code: '41', flag: '🇨🇭' },
                { name: 'Austria', code: '43', flag: '🇦🇹' },
                { name: 'Sweden', code: '46', flag: '🇸🇪' },
                { name: 'Norway', code: '47', flag: '🇳🇴' },
                { name: 'Denmark', code: '45', flag: '🇩🇰' },
                { name: 'Finland', code: '358', flag: '🇫🇮' },
                { name: 'Poland', code: '48', flag: '🇵🇱' },
                { name: 'Czech Republic', code: '420', flag: '🇨🇿' },
                { name: 'Greece', code: '30', flag: '🇬🇷' },
                { name: 'Turkey', code: '90', flag: '🇹🇷' },
                { name: 'Romania', code: '40', flag: '🇷🇴' },
                { name: 'Hungary', code: '36', flag: '🇭🇺' },
                { name: 'Croatia', code: '385', flag: '🇭🇷' },
                { name: 'India', code: '91', flag: '🇮🇳' },
                { name: 'Pakistan', code: '92', flag: '🇵🇰' },
                { name: 'Bangladesh', code: '880', flag: '🇧🇩' },
                { name: 'China', code: '86', flag: '🇨🇳' },
                { name: 'Japan', code: '81', flag: '🇯🇵' },
                { name: 'South Korea', code: '82', flag: '🇰🇷' },
                { name: 'Australia', code: '61', flag: '🇦🇺' },
                { name: 'New Zealand', code: '64', flag: '🇳🇿' },
                { name: 'Canada', code: '1', flag: '🇨🇦' },
                { name: 'Brazil', code: '55', flag: '🇧🇷' },
                { name: 'Mexico', code: '52', flag: '🇲🇽' },
                { name: 'South Africa', code: '27', flag: '🇿🇦' },
                { name: 'Nigeria', code: '234', flag: '🇳🇬' },
                { name: 'Kenya', code: '254', flag: '🇰🇪' },
                { name: 'Egypt', code: '20', flag: '🇪🇬' },
                { name: 'UAE', code: '971', flag: '🇦🇪' },
                { name: 'Saudi Arabia', code: '966', flag: '🇸🇦' },
                { name: 'Qatar', code: '974', flag: '🇶🇦' },
                { name: 'Kuwait', code: '965', flag: '🇰🇼' },
                { name: 'Singapore', code: '65', flag: '🇸🇬' },
                { name: 'Malaysia', code: '60', flag: '🇲🇾' },
                { name: 'Thailand', code: '66', flag: '🇹🇭' },
                { name: 'Philippines', code: '63', flag: '🇵🇭' },
                { name: 'Russia', code: '7', flag: '🇷🇺' },
                { name: 'Ukraine', code: '380', flag: '🇺🇦' },
                { name: 'Israel', code: '972', flag: '🇮🇱' },
                { name: 'Argentina', code: '54', flag: '🇦🇷' },
            ];

            return {
                open: false,
                search: '',
                countries: countries,
                selectedCode: '44',
                selectedFlag: '🇬🇧',

                get filteredCountries() {
                    if (!this.search) return this.countries;
                    const q = this.search.toLowerCase();
                    return this.countries.filter(c =>
                        c.name.toLowerCase().includes(q) || c.code.includes(q)
                    );
                },

                selectCountry(c) {
                    this.selectedCode = c.code;
                    this.selectedFlag = c.flag;
                    this.open = false;
                    this.search = '';
                }
            };
        }
    </script>

    <style>[x-cloak] { display: none !important; }</style>


<?php require BASE_PATH . '/templates/layouts/footer.php'; ?>