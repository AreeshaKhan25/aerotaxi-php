<?php
/**
 * Action: Update Booking
 * POST /admin/bookings/{id}/update
 */

ensure_session();
require_admin();

// Verify CSRF
if (!verify_csrf()) {
    http_response_code(403);
    die('CSRF token mismatch');
}

if (empty($id)) {
    redirect(base_url('admin/jobs'));
}

$booking = Database::fetch('SELECT * FROM bookings WHERE id = ?', [$id]);
if (!$booking) {
    redirect(base_url('admin/jobs'));
}

// Validate input
$errors = validate($_POST, [
    'from_location' => 'required|max:500',
    'to_location' => 'required|max:500',
    'depart_date' => 'required',
    'passenger_name' => 'required|max:255',
    'email' => 'required|email|max:255',
    'vehicle_id' => 'required',
    'total_price' => 'required|numeric',
    'status' => 'required',
    'payment_status' => 'required',
]);

// Custom check for exists:vehicles,id
$vehicleId = $_POST['vehicle_id'] ?? null;
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
    Database::update('bookings', [
        'from_location' => $_POST['from_location'],
        'to_location' => $_POST['to_location'],
        'depart_date' => $_POST['depart_date'],
        'depart_time' => $_POST['depart_time'] ?? null,
        'flight_number' => $_POST['flight_number'] ?? null,
        'note_to_driver' => $_POST['note_to_driver'] ?? null,
        'passenger_name' => $_POST['passenger_name'],
        'email' => $_POST['email'],
        'country_code' => $_POST['country_code'] ?? null,
        'phone' => $_POST['phone'] ?? null,
        'vehicle_id' => $vehicleId,
        'total_price' => (float)$_POST['total_price'],
        'status' => $_POST['status'],
        'payment_status' => $_POST['payment_status'],
        'payment_id' => $_POST['payment_id'] ?? null,
        'updated_at' => date('Y-m-d H:i:s'),
    ], 'id = ?', [$id]);

    flash_success('Booking updated successfully.');
    redirect(base_url('admin/bookings/' . $id));

} catch (Exception $e) {
    log_error('Booking update error: ' . $e->getMessage());
    flash_error('An error occurred while updating the booking.');
    redirect_back();
}
