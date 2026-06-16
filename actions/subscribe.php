<?php
/**
 * Action: Newsletter Subscribe
 * 
 * POST /subscribe
 * 
 * Validates email, stores subscription, and creates admin notification
 */

// Verify CSRF token
if (!verify_csrf()) {
    http_response_code(403);
    die('CSRF token mismatch');
}

// Validate input
$errors = validate($_POST, [
    'email' => 'required|email',
]);

if (!empty($errors)) {
    flash('subscribe_error', $errors['email'] ?? 'A valid email address is required.');
    redirect_back();
}

$email = trim($_POST['email']);

try {
    // Check if email already exists
    $exists = Database::fetch('SELECT * FROM subscribers WHERE email = ?', [$email]);
    
    if ($exists) {
        if (!$exists->active) {
            // Re-activate if inactive
            Database::update('subscribers', [
                'active' => 1,
                'updated_at' => date('Y-m-d H:i:s'),
            ], 'id = ?', [$exists->id]);
            flash('subscribe_success', 'Thank you for subscribing again!');
        } else {
            flash('subscribe_success', 'You are already subscribed to our newsletter!');
        }
    } else {
        // Insert new subscriber
        $subId = Database::insert('subscribers', [
            'email' => $email,
            'name' => null,
            'active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Create admin notification
        Database::insert('admin_notifications', [
            'type' => 'subscriber',
            'message' => "New subscriber: {$email}",
            'data' => json_encode(['subscriber_id' => $subId, 'email' => $email]),
            'read' => false,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        flash('subscribe_success', 'Thank you for subscribing!');
    }

    redirect_back();

} catch (Exception $e) {
    log_error('Subscribe error: ' . $e->getMessage());
    flash('subscribe_error', 'An error occurred. Please try again.');
    redirect_back();
}
