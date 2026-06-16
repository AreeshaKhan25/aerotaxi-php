<?php
// Ported from: booking/payment.blade.php
$__page_title = 'Secure Payment - AeroTAXI';

$reference = $_GET['reference'] ?? '';
$booking = Database::fetch('SELECT * FROM bookings WHERE reference = ?', [$reference]);
if (!$booking) {
    redirect(url('/'));
}

// Load vehicle relation
$booking->vehicle = Database::fetch('SELECT * FROM vehicles WHERE id = ?', [$booking->vehicle_id]);

// Stripe integration setup
$stripeKey = STRIPE_KEY;
$clientSecret = null;

if ($stripeKey && $stripeKey !== 'pk_test_REPLACE_WITH_YOUR_KEY' && !empty(STRIPE_SECRET)) {
    try {
        require_once BASE_PATH . '/vendor/autoload.php';
        $stripe = new \Stripe\StripeClient(STRIPE_SECRET);
        $intent = $stripe->paymentIntents->create([
            'amount' => (int) round($booking->total_price * 100),
            'currency' => 'gbp',
            'metadata' => [
                'booking_reference' => $booking->reference,
                'from' => $booking->from_location,
                'to' => $booking->to_location,
            ],
        ]);
        $clientSecret = $intent->client_secret;
    } catch (\Exception $e) {
        log_error('Stripe error: ' . $e->getMessage());
        // Will fall back to demo mode
    }
}
?>
<?php require BASE_PATH . '/templates/layouts/header.php'; ?>
<?php $__page_title = 'Payment - AeroTAXI'; ?>
<?php ob_start(); ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://js.stripe.com/v3/"></script>
<div x-data="paymentPage()" x-init="initMap(); initStripe()" class="min-h-screen bg-gray-50">

        
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
                    <div class="w-12 sm:w-24 h-[2px] bg-yellow-400 mx-2 sm:mx-3"></div>
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-full bg-yellow-400 text-gray-900 font-bold text-sm flex items-center justify-center shadow-sm">3</div>
                        <span class="text-sm font-semibold text-gray-900 hidden sm:inline">Payment</span>
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
                                <p class="text-sm font-semibold text-gray-900 min-w-0 truncate"><?= e($booking->from_location) ?></p>
                            </div>
                            <div class="flex justify-end -my-3 relative z-10">
                                <div class="w-8 h-8 rounded-full bg-yellow-400 flex items-center justify-center shadow-sm">
                                    <img src="/images/swap-icon.png" alt="" class="w-6 h-6">
                                </div>
                            </div>
                            <div class="flex items-start gap-3 py-2.5 border-b border-gray-200/60">
                                <span class="text-sm text-gray-400 font-medium w-14 flex-shrink-0">To</span>
                                <p class="text-sm font-semibold text-gray-900 min-w-0 truncate"><?= e($booking->to_location) ?></p>
                            </div>
                            <div class="flex items-center gap-3 py-2.5">
                                <span class="text-sm text-gray-400 font-medium w-14 flex-shrink-0">Depart</span>
                                <p class="text-sm font-semibold text-gray-900">
                                    <?= e(format_date($booking->depart_date, 'l')) ?>
                                    <span class="mx-1 text-gray-300">|</span>
                                    <?= e(format_date($booking->depart_date, 'j M Y')) ?>
                                    <?php if ($booking->depart_time): ?>
                                        <span class="mx-1 text-gray-300">|</span><?= e($booking->depart_time) ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>

                        
                        <div>
                            <h3 class="text-sm font-bold text-gray-900 mb-2">Map view</h3>
                            <div id="routeMap" class="w-full h-44 rounded-xl overflow-hidden border border-gray-200 bg-gray-100"></div>
                        </div>

                        
                        <div>
                            <h3 class="text-sm font-bold text-gray-900 mb-3">Your ride</h3>
                            <div class="bg-white rounded-xl border border-gray-100 p-4">
                                <div class="flex items-center gap-4">
                                    <?php if ($booking->vehicle): ?>
                                    <img src="<?= e(url($booking->vehicle->image)) ?>" alt="<?= e($booking->vehicle->name) ?>" class="h-16 object-contain">
                                    <div class="flex-1">
                                        <p class="font-bold text-gray-900"><?= e($booking->vehicle->name) ?></p>
                                        <div class="flex items-center gap-3 text-xs text-gray-500 mt-1">
                                            <span><i class="fa-solid fa-users text-gray-400"></i> <?= e($booking->vehicle->passengers) ?></span>
                                            <span><i class="fa-solid fa-suitcase-rolling text-gray-400"></i> <?= e($booking->vehicle->suitcases) ?></span>
                                        </div>
                                    </div>
                                    <p class="text-lg font-bold text-gray-900">&pound;<?= e(number_format($booking->total_price, 2)) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="bg-yellow-50 border-2 border-yellow-400 rounded-xl px-5 py-3 flex items-center justify-between">
                            <span class="font-bold text-gray-900">Total</span>
                            <span class="text-xl font-bold text-gray-900">&pound;<?= e(number_format($booking->total_price, 2)) ?></span>
                        </div>

                    </div>
                </div>

                
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-8">Payment</h1>

                    <div class="space-y-8">

                        
                        <div class="bg-white rounded-2xl border border-gray-100 p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-lg font-bold text-gray-900">Payment details</h2>
                                <div class="flex items-center gap-1 bg-gray-900 text-white text-xs font-medium rounded px-2.5 py-1">
                                    Powered by <span class="font-bold ml-1">stripe</span>
                                </div>
                            </div>

                            
                            <div id="payment-element" class="mb-6 min-h-[200px]"></div>

                            
                            <div x-show="paymentError" x-cloak class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 mb-4">
                                <p class="text-sm text-red-700 flex items-center gap-2">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    <span x-text="paymentError"></span>
                                </p>
                            </div>
                        </div>

                        
                        <div class="bg-white rounded-2xl border border-gray-100 p-6">
                            <div class="flex flex-col sm:flex-row gap-4">
                                <div class="flex-1">
                                    <h2 class="text-lg font-bold text-gray-900 mb-2">Confirm payment</h2>
                                    <p class="text-sm text-gray-500">
                                        By clicking 'Confirm payment', your booking will be finalized and the total amount will be charged.
                                        You also agree to our
                                        <a href="<?= base_url('terms-of-service') ?>" class="text-primary underline hover:no-underline">Terms of Service</a> and
                                        <a href="<?= base_url('privacy-policy') ?>" class="text-primary underline hover:no-underline">Privacy Policy</a>.
                                    </p>
                                </div>
                            </div>

                            <div class="mt-4">
                                <label class="flex items-start gap-3 cursor-pointer">
                                    <input type="checkbox" x-model="agreePromotions" class="mt-1 w-4 h-4 rounded border-gray-300 text-yellow-500 focus:ring-yellow-400">
                                    <span class="text-sm text-gray-600">I agree to receive booking confirmations and occasional promotions. No spam.</span>
                                </label>
                            </div>

                            <button @click="confirmPayment()" :disabled="processing"
                                    :class="processing ? 'opacity-70 cursor-not-allowed' : 'hover:bg-yellow-500 hover:shadow-md active:scale-[0.98]'"
                                    class="w-full mt-6 bg-yellow-400 text-gray-900 font-semibold rounded-xl px-6 py-4 transition-all text-base flex items-center justify-center gap-2">
                                <i x-show="processing" class="fa-solid fa-spinner fa-spin"></i>
                                <span x-text="processing ? processingText : 'Confirm payment'"></span>
                                <i x-show="!processing" class="fa-solid fa-chevron-right text-sm"></i>
                            </button>
                            <p x-show="processing" x-cloak class="text-center text-xs text-gray-400 mt-2">Please don't close this page. This usually takes 5-10 seconds.</p>
                        </div>

                        
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
                                <i class="fa-solid fa-envelope-circle-check text-xl text-gray-400 mb-2"></i>
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
                </div>

            </div>
        </div>

    </div>

    <script>
        function paymentPage() {
            return {
                stripe: null,
                elements: null,
                paymentElement: null,
                processing: false,
                processingText: 'Processing...',
                paymentError: '',
                agreePromotions: false,
                map: null,

                async initStripe() {
                    const publishableKey = <?= json_encode($stripeKey) ?>;

                    if (!publishableKey || publishableKey === 'pk_test_REPLACE_WITH_YOUR_KEY') {
                        // Demo mode - show a message in the payment element area
                        document.getElementById('payment-element').innerHTML = `
                            <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center">
                                <i class="fa-solid fa-credit-card text-4xl text-gray-300 mb-3"></i>
                                <p class="font-semibold text-gray-700 mb-1">Stripe Payment Form</p>
                                <p class="text-sm text-gray-500 mb-4">Add your Stripe API keys in .env to enable live payments</p>
                                <div class="space-y-3 max-w-sm mx-auto text-left">
                                    <div>
                                        <label class="text-xs text-gray-500">Card number</label>
                                        <input type="text" value="4242 4242 4242 4242" disabled class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-500">
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="text-xs text-gray-500">Expiry date</label>
                                            <input type="text" value="12/28" disabled class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-500">
                                        </div>
                                        <div>
                                            <label class="text-xs text-gray-500">CVC</label>
                                            <input type="text" value="123" disabled class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-500">
                                        </div>
                                    </div>
                                </div>
                                <p class="text-xs text-amber-600 mt-4"><i class="fa-solid fa-info-circle mr-1"></i> Demo mode - no charges will be made</p>
                            </div>`;
                        return;
                    }

                    this.stripe = Stripe(publishableKey);

                    const clientSecret = <?= json_encode($clientSecret) ?>;
                    if (!clientSecret) {
                        document.getElementById('payment-element').innerHTML = '<p class="text-red-500 text-sm text-center py-4">Unable to initialize payment. Please refresh and try again.</p>';
                        return;
                    }

                    this.elements = this.stripe.elements({
                        clientSecret,
                        appearance: {
                            theme: 'stripe',
                            variables: {
                                colorPrimary: '#FACC15',
                                colorBackground: '#ffffff',
                                colorText: '#1f2937',
                                borderRadius: '12px',
                                fontFamily: 'system-ui, sans-serif'
                            }
                        }
                    });

                    this.paymentElement = this.elements.create('payment', {
                        layout: 'tabs'
                    });
                    this.paymentElement.mount('#payment-element');
                },

                async confirmPayment() {
                    this.paymentError = '';

                    // Demo mode
                    if (!this.stripe) {
                        this.processing = true;
                        window.location.href = '/booking/confirmation/' + <?= json_encode($booking->reference) ?>;
                        return;
                    }

                    // Validate the payment element first
                    const { error: submitError } = await this.elements.submit();
                    if (submitError) {
                        this.paymentError = submitError.message;
                        return;
                    }

                    this.processing = true;
                    this.processingText = 'Verifying card...';
                    setTimeout(() => { if (this.processing) this.processingText = 'Processing payment...'; }, 2000);
                    setTimeout(() => { if (this.processing) this.processingText = 'Almost done...'; }, 5000);

                    try {
                        const { error } = await this.stripe.confirmPayment({
                            elements: this.elements,
                            confirmParams: {
                                return_url: window.location.origin + '/booking/confirmation/' + <?= json_encode($booking->reference) ?>,
                            }
                        });

                        if (error) {
                            this.paymentError = error.message;
                            this.processing = false;
                        }
                    } catch (e) {
                        this.paymentError = 'Payment failed. Please check your card details and try again.';
                        this.processing = false;
                    }
                },

                initMap() {
                    this.$nextTick(() => {
                        this.map = L.map('routeMap', { zoomControl: false }).setView([51.505, -0.09], 7);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; OpenStreetMap'
                        }).addTo(this.map);
                        this.drawRoute();
                    });
                },

                async drawRoute() {
                    const from = <?= json_encode($booking->from_location) ?>;
                    const to = <?= json_encode($booking->to_location) ?>;
                    const [fc, tc] = await Promise.all([this.geocode(from), this.geocode(to)]);
                    if (!fc || !tc) return;
                    const si = L.divIcon({ className:'', html:'<div style="width:12px;height:12px;border-radius:50%;background:#0C6291;border:3px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.3)"></div>', iconSize:[12,12], iconAnchor:[6,6] });
                    const ei = L.divIcon({ className:'', html:'<div style="width:12px;height:12px;border-radius:50%;background:#dc2626;border:3px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.3)"></div>', iconSize:[12,12], iconAnchor:[6,6] });
                    L.marker([fc.lat,fc.lon],{icon:si}).addTo(this.map);
                    L.marker([tc.lat,tc.lon],{icon:ei}).addTo(this.map);
                    try {
                        const r = await fetch(`https://router.project-osrm.org/route/v1/driving/${fc.lon},${fc.lat};${tc.lon},${tc.lat}?overview=full&geometries=geojson`);
                        const d = await r.json();
                        if(d.routes&&d.routes.length) {
                            const coords = d.routes[0].geometry.coordinates.map(c=>[c[1],c[0]]);
                            L.polyline(coords,{color:'#2563eb',weight:4,opacity:0.8}).addTo(this.map);
                            this.map.fitBounds(L.polyline(coords).getBounds().pad(0.15));
                        }
                    } catch(e) { this.map.fitBounds([[fc.lat,fc.lon],[tc.lat,tc.lon]],{padding:[20,20]}); }
                },

                async geocode(q) {
                    try { const r=await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(q)}&countrycodes=gb&limit=1`,{headers:{'Accept-Language':'en'}}); const d=await r.json(); if(d.length) return {lat:parseFloat(d[0].lat),lon:parseFloat(d[0].lon)}; } catch(e) {}
                    return null;
                }
            }
        }
    </script>

    <style>[x-cloak] { display: none !important; }</style>


<?php require BASE_PATH . '/templates/layouts/footer.php'; ?>