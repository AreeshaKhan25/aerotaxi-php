<?php
// Admin page - requires authentication
// Ported from: admin/booking-detail.blade.php

// $id is extracted from route matches
if (empty($id)) {
    redirect(base_url('admin/jobs'));
}

$booking = Database::fetch('SELECT * FROM bookings WHERE id = ?', [$id]);
if (!$booking) {
    redirect(base_url('admin/jobs'));
}

$booking->vehicle = Database::fetch('SELECT * FROM vehicles WHERE id = ?', [$booking->vehicle_id]);
$vehicles = Database::fetchAll('SELECT * FROM vehicles ORDER BY sort_order');

$__page_title = 'Booking #' . $booking->reference;
?>
<?php require BASE_PATH . '/templates/admin/header.php'; ?>
<div class="mb-6 flex items-center justify-between">
        <a href="<?= e(($_SERVER['HTTP_REFERER'] ?? base_url('/'))) ?>" class="text-sm text-gray-500 hover:text-gray-700 transition-colors">
            <i class="fas fa-arrow-left mr-1"></i> Back
        </a>
        <span class="text-sm text-gray-400">Booking ID: <?= e($booking->id) ?></span>
    </div>

    <?php if (get_flash('success')): ?>
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
            <i class="fas fa-check-circle mr-1"></i> <?= e(get_flash('success')) ?>
        </div>
    <?php endif; ?>

    <?php $errs = get_errors(); if (!empty($errs)): ?>
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
            <ul class="list-disc list-inside">
                <?php foreach($errs as $error): ?><li><?= e($error) ?></li><?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="flex flex-wrap items-center gap-3 mb-6">
        <h1 class="text-xl font-bold text-gray-900 font-mono"><?= e($booking->reference) ?></h1>
        <?php
            $sc = ['confirmed'=>'green','pending'=>'yellow','new'=>'blue','completed'=>'green','cancelled'=>'red','assigned'=>'blue','bidding'=>'yellow'];
            $col = $sc[$booking->status] ?? 'gray';
        ?>
        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-<?= e($col) ?>-100 text-<?= e($col) ?>-700"><?= e(ucfirst($booking->status ?? 'new')) ?></span>
        <?php if ($booking->payment_status === 'paid'): ?>
            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Paid</span>
        <?php else: ?>
            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700"><?= e(ucfirst($booking->payment_status ?? 'Unpaid')) ?></span>
        <?php endif; ?>
    </div>

    <form method="POST" action="<?= e(route('admin.booking-update', $booking->id)) ?>">
        <?= csrf_field() ?>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-base font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-route text-blue-500"></i> Journey Information
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Pickup Location</label>
                        <input type="text" name="from_location" value="<?= e(old('from_location', $booking->from_location)) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Drop-off Location</label>
                        <input type="text" name="to_location" value="<?= e(old('to_location', $booking->to_location)) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Date</label>
                            <input type="date" name="depart_date" value="<?= e(old('depart_date', format_date($booking->depart_date, 'Y-m-d'))) ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Time</label>
                            <input type="time" name="depart_time" value="<?= e(old('depart_time', $booking->depart_time)) ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Flight Number</label>
                        <input type="text" name="flight_number" value="<?= e(old('flight_number', $booking->flight_number)) ?>" placeholder="e.g. BA1234"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent uppercase">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Note to Driver</label>
                        <textarea name="note_to_driver" rows="2" placeholder="Any special instructions..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"><?= e(old('note_to_driver', $booking->note_to_driver)) ?></textarea>
                    </div>
                </div>
            </div>

            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-base font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-user text-blue-500"></i> Passenger Information
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Full Name</label>
                        <input type="text" name="passenger_name" value="<?= e(old('passenger_name', $booking->passenger_name)) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Email</label>
                        <input type="email" name="email" value="<?= e(old('email', $booking->email)) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Country Code</label>
                            <input type="text" name="country_code" value="<?= e(old('country_code', $booking->country_code)) ?>" placeholder="+44"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Phone</label>
                            <input type="text" name="phone" value="<?= e(old('phone', $booking->phone)) ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-base font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-car text-blue-500"></i> Vehicle & Pricing
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Vehicle Type</label>
                        <select name="vehicle_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <?php foreach (\App\Models\Vehicle::orderBy('sort_order')->get() as $vehicle): ?>
                                <option value="<?= e($vehicle->id) ?>" <?= e($booking->vehicle_id == $vehicle->id ? 'selected' : '') ?>>
                                    <?= e($vehicle->name) ?> (<?= e($vehicle->car_model) ?>) - From £<?= e(number_format($vehicle->price, 2)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Total Price (£)</label>
                        <input type="number" name="total_price" step="0.01" min="0" value="<?= e(old('total_price', number_format($booking->total_price, 2, '.', ''))) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-base font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-cog text-blue-500"></i> Status & Payment
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Booking Status</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <?php foreach (['new','confirmed','pending','completed','cancelled','assigned','bidding'] as $s): ?>
                                <option value="<?= e($s) ?>" <?= e(($booking->status ?? 'new') === $s ? 'selected' : '') ?>><?= e(ucfirst($s)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Payment Status</label>
                        <select name="payment_status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <?php foreach (['unpaid','paid','refunded'] as $ps): ?>
                                <option value="<?= e($ps) ?>" <?= e(($booking->payment_status ?? 'unpaid') === $ps ? 'selected' : '') ?>><?= e(ucfirst($ps)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Payment ID</label>
                        <input type="text" name="payment_id" value="<?= e(old('payment_id', $booking->payment_id)) ?>" placeholder="Stripe/manual reference"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono text-xs">
                    </div>
                    <div class="grid grid-cols-2 gap-4 pt-2 border-t border-gray-100 text-xs text-gray-400">
                        <div>Created: <?= e(format_date($booking->created_at, 'd M Y H:i')) ?></div>
                        <div>Updated: <?= e(format_date($booking->updated_at, 'd M Y H:i')) ?></div>
                    </div>
                </div>
            </div>

        </div>

        
        <div class="mt-6 flex items-center gap-4">
            <button type="submit" class="inline-flex items-center px-8 py-3 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-save mr-2"></i> Save All Changes
            </button>
            <a href="<?= e(($_SERVER['HTTP_REFERER'] ?? base_url('/'))) ?>" class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
        </div>
    </form>


<?php require BASE_PATH . '/templates/admin/footer.php'; ?>