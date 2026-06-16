<?php
/**
 * Action: Create Booking
 * POST /booking
 */

// Verify CSRF
if (!verify_csrf()) {
    http_response_code(403);
    die('CSRF token mismatch');
}

// Validate input
$errors = validate($_POST, [
    'from_location' => 'required',
    'to_location' => 'required',
    'depart_date' => 'required',
    'vehicle_id' => 'required',
    'first_name' => 'required|max:255',
    'last_name' => 'required|max:255',
    'email' => 'required|email|max:255',
    'phone' => 'required|max:50',
]);

// Custom check for exists:vehicles,id
$vehicleId = $_POST['vehicle_id'] ?? null;
$vehicle = null;
if ($vehicleId) {
    $vehicle = Database::fetch('SELECT * FROM vehicles WHERE id = ?', [$vehicleId]);
    if (!$vehicle) {
        $errors['vehicle_id'] = 'The selected vehicle is invalid.';
    }
}

if (!empty($errors)) {
    set_old_values();
    flash_errors($errors);
    redirect_back();
}

try {
    $passengerName = trim(($_POST['first_name'] ?? '') . ' ' . ($_POST['last_name'] ?? ''));
    $reference = generate_reference();
    $totalPrice = !empty($_POST['total_price']) ? (float)$_POST['total_price'] : (float)$vehicle->price;

    // Save subscriber if opted in
    if (!empty($_POST['agree_promotions'])) {
        $email = trim($_POST['email']);
        $exists = Database::fetch('SELECT * FROM subscribers WHERE email = ?', [$email]);
        if (!$exists) {
            $subId = Database::insert('subscribers', [
                'email' => $email,
                'name' => $passengerName,
                'active' => 1,
            ]);
            
            // Insert admin notification for subscriber
            Database::insert('admin_notifications', [
                'type' => 'subscriber',
                'message' => "New subscriber: {$email}",
                'data' => json_encode(['subscriber_id' => $subId, 'email' => $email]),
                'read' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    // Insert booking
    $bookingId = Database::insert('bookings', [
        'reference' => $reference,
        'from_location' => $_POST['from_location'],
        'to_location' => $_POST['to_location'],
        'depart_date' => $_POST['depart_date'],
        'depart_time' => $_POST['depart_time'] ?? null,
        'vehicle_id' => $vehicleId,
        'passenger_name' => $passengerName,
        'email' => $_POST['email'],
        'phone' => $_POST['phone'],
        'flight_number' => $_POST['flight_number'] ?? null,
        'note_to_driver' => $_POST['note_to_driver'] ?? null,
        'country_code' => $_POST['country_code'] ?? null,
        'status' => 'pending',
        'payment_status' => 'unpaid',
        'total_price' => $totalPrice,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ]);

    clear_old_values();
    redirect(url('/payment?reference=' . $reference));

} catch (Exception $e) {
    log_error('Booking store error: ' . $e->getMessage());
    flash_error('An error occurred while saving your booking. Please try again.');
    redirect_back();
}
