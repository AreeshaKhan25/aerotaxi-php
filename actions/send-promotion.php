<?php
/**
 * Action: Send Promotion
 * POST /admin/promotions/send
 */

ensure_session();
require_admin();

// Verify CSRF
if (!verify_csrf()) {
    http_response_code(403);
    die('CSRF token mismatch');
}

$errors = validate($_POST, [
    'subject' => 'required|max:255',
    'message' => 'required',
]);

if (!empty($errors)) {
    set_old_values();
    flash_errors($errors);
    redirect_back();
}

$subscribers = Database::fetchAll("SELECT * FROM subscribers WHERE active = 1");

if (empty($subscribers) || count($subscribers) === 0) {
    flash('promo_error', 'No active subscribers to send to.');
    redirect(base_url('admin/promotions'));
}

$subject = $_POST['subject'];
$message = $_POST['message'];

// Emulate queuing emails exactly like the Laravel controller
flash('promo_success', 'Promotion "' . $subject . '" queued for ' . count($subscribers) . ' subscribers.');
redirect(base_url('admin/promotions'));
