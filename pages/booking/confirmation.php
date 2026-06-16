<?php
// Ported from: booking/confirmation.blade.php
$__page_title = 'Booking Confirmation - AeroTAXI';

// $reference is extracted from the route matches (e.g. $matches['reference'])
if (empty($reference)) {
    redirect(url('/'));
}

$booking = Database::fetch('SELECT * FROM bookings WHERE reference = ?', [$reference]);
if (!$booking) {
    redirect(url('/'));
}

// Load vehicle
$booking->vehicle = Database::fetch('SELECT * FROM vehicles WHERE id = ?', [$booking->vehicle_id]);

// If status is pending, mark it confirmed/paid and create notifications
if ($booking->status === 'pending') {
    Database::update('bookings', ['status' => 'confirmed', 'payment_status' => 'paid'], 'id = ?', [$booking->id]);
    
    // Insert admin notification
    Database::insert('admin_notifications', [
        'type' => 'booking',
        'message' => "New booking {$booking->reference} from {$booking->passenger_name}",
        'data' => json_encode(['booking_id' => $booking->id, 'reference' => $booking->reference]),
        'read' => false,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ]);

    // Send customer booking confirmation email
    try {
        $customerSubject = 'Booking Confirmed - ' . $booking->reference . ' | AeroTAXI';
        $customerBody = render_email('booking-confirmation', ['booking' => $booking]);
        send_mail($booking->email, $customerSubject, $customerBody);
    } catch (\Exception $e) {
        log_error("Failed to send customer confirmation email: " . $e->getMessage());
    }

    // Send admin notification email(s)
    try {
        $adminEmailsStr = getenv('ADMIN_EMAILS') ?: ADMIN_EMAIL;
        $adminEmails = array_filter(array_map('trim', explode(',', $adminEmailsStr)));
        $adminSubject = 'New Booking Received - ' . $booking->reference;
        $adminBody = render_email('admin-booking-notification', ['booking' => $booking]);
        
        foreach ($adminEmails as $adminEmail) {
            send_mail($adminEmail, $adminSubject, $adminBody);
        }
    } catch (\Exception $e) {
        log_error("Failed to send admin notification email: " . $e->getMessage());
    }
}
?>
<?php require BASE_PATH . '/templates/layouts/header.php'; ?>
<?php $__page_title = 'Booking Confirmed - AeroTAXI'; ?>
<div class="min-h-screen bg-gray-50">

        
        <div class="bg-white border-b border-gray-100">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-center gap-2 sm:gap-4">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-green-500 text-white font-bold text-sm flex items-center justify-center">
                            <i class="fa-solid fa-check text-xs"></i>
                        </div>
                        <span class="text-sm font-medium text-gray-400 hidden sm:inline">Your ride</span>
                    </div>
                    <div class="w-8 sm:w-16 h-px bg-green-300"></div>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-green-500 text-white font-bold text-sm flex items-center justify-center">
                            <i class="fa-solid fa-check text-xs"></i>
                        </div>
                        <span class="text-sm font-medium text-gray-400 hidden sm:inline">Transfer details</span>
                    </div>
                    <div class="w-8 sm:w-16 h-px bg-green-300"></div>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-green-500 text-white font-bold text-sm flex items-center justify-center">
                            <i class="fa-solid fa-check text-xs"></i>
                        </div>
                        <span class="text-sm font-semibold text-gray-900 hidden sm:inline">Confirmed</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

            
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center mb-8">
                <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-5">
                    <i class="fa-solid fa-check text-2xl text-green-600"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Booking Confirmed!</h1>
                <p class="text-gray-500 mb-6">Your airport transfer has been booked successfully. A confirmation email will be sent to <strong class="text-gray-700"><?= e($booking->email) ?></strong>.</p>

                <div class="inline-flex items-center gap-2 bg-yellow-50 border border-yellow-200 rounded-xl px-5 py-3 mb-6">
                    <span class="text-sm text-gray-500">Booking Reference:</span>
                    <span class="text-lg font-bold text-gray-900 tracking-wider"><?= e($booking->reference) ?></span>
                </div>
            </div>

            
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Booking Details</h2>

                <div class="space-y-3">
                    <div class="flex items-start justify-between py-2 border-b border-gray-50">
                        <span class="text-sm text-gray-500">From</span>
                        <span class="text-sm font-medium text-gray-800 text-right max-w-[250px]"><?= e($booking->from_location) ?></span>
                    </div>
                    <div class="flex items-start justify-between py-2 border-b border-gray-50">
                        <span class="text-sm text-gray-500">To</span>
                        <span class="text-sm font-medium text-gray-800 text-right max-w-[250px]"><?= e($booking->to_location) ?></span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-50">
                        <span class="text-sm text-gray-500">Date</span>
                        <span class="text-sm font-medium text-gray-800">
                            <?= e(format_date($booking->depart_date, 'D, d M Y')) ?><?php if($booking->depart_time): ?> at <?= e($booking->depart_time) ?><?php endif; ?>
                        </span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-50">
                        <span class="text-sm text-gray-500">Vehicle</span>
                        <div class="flex items-center gap-2">
                            <?php if ($booking->vehicle): ?>
                             <img src="<?= e(url($booking->vehicle->image)) ?>" alt="<?= e($booking->vehicle->name) ?>" class="h-8 object-contain">
                            <span class="text-sm font-medium text-gray-800"><?= e($booking->vehicle->name) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-50">
                        <span class="text-sm text-gray-500">Passenger</span>
                        <span class="text-sm font-medium text-gray-800"><?= e($booking->passenger_name) ?></span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-50">
                        <span class="text-sm text-gray-500">Email</span>
                        <span class="text-sm font-medium text-gray-800"><?= e($booking->email) ?></span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-50">
                        <span class="text-sm text-gray-500">Phone</span>
                        <span class="text-sm font-medium text-gray-800"><?= e($booking->phone) ?></span>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <span class="text-sm font-semibold text-gray-700">Total Price</span>
                        <span class="text-xl font-bold text-gray-900">&pound;<?= e(number_format($booking->total_price, 2)) ?></span>
                    </div>
                </div>

                
                <div class="bg-green-50 rounded-xl p-4 mt-4">
                    <div class="flex flex-wrap gap-x-4 gap-y-1.5 text-xs text-green-700">
                        <span><i class="fa-solid fa-check mr-1"></i> Meet & Greet included</span>
                        <span><i class="fa-solid fa-check mr-1"></i> Free cancellation (24h before)</span>
                        <span><i class="fa-solid fa-check mr-1"></i> Free flight tracking</span>
                        <span><i class="fa-solid fa-check mr-1"></i> 24/7 customer support</span>
                    </div>
                </div>
            </div>

            
            <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
                <a href="<?= base_url('/') ?>"
                   class="inline-flex items-center justify-center gap-2 bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-semibold rounded-xl px-6 py-3 transition-all hover:shadow-md">
                    <i class="fa-solid fa-house text-sm"></i> Back to Home
                </a>
                <a href="<?= base_url('help') ?>#contact-us"
                   class="inline-flex items-center justify-center gap-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-medium rounded-xl px-6 py-3 transition-all">
                    <i class="fa-solid fa-headset text-sm"></i> Contact Support
                </a>
            </div>

        </div>
    </div>


<?php require BASE_PATH . '/templates/layouts/footer.php'; ?>