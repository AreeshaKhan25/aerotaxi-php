<?php
/**
 * Action: Submit Contact Form
 * 
 * POST /contact
 * 
 * Validates and stores contact message, creates admin notification
 */

// Verify CSRF token
if (!verify_csrf()) {
    http_response_code(403);
    die('CSRF token mismatch');
}

// Validate input
$errors = validate($_POST, [
    'name' => 'required|min:2|max:255',
    'email' => 'required|email',
    'subject' => 'required|min:5|max:255',
    'message' => 'required|min:10|max:2000',
]);

if (!empty($errors)) {
    set_old_values();
    foreach ($errors as $field => $error) {
        flash_error($error);
    }
    redirect($_SERVER['HTTP_REFERER'] ?? url('/help'));
}

// Insert contact message
try {
    $id = Database::insert('contact_messages', [
        'name' => $_POST['name'],
        'email' => $_POST['email'],
        'subject' => $_POST['subject'],
        'message' => $_POST['message'],
        'read' => false,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ]);

    // Create admin notification
    Database::insert('admin_notifications', [
        'type' => 'contact_message',
        'message' => 'New contact message from ' . $_POST['name'],
        'data' => json_encode(['message_id' => $id, 'email' => $_POST['email']]),
        'read' => false,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ]);

    // Send email to admin
    try {
        $adminEmailsStr = getenv('ADMIN_EMAILS') ?: ADMIN_EMAIL;
        $adminEmails = array_filter(array_map('trim', explode(',', $adminEmailsStr)));
        $subject = 'New Contact Message - ' . $_POST['subject'];
        $body = "New contact message received:\n\n" .
                "Name: " . $_POST['name'] . "\n" .
                "Email: " . $_POST['email'] . "\n" .
                "Subject: " . $_POST['subject'] . "\n\n" .
                "Message:\n" . $_POST['message'];
        
        foreach ($adminEmails as $adminEmail) {
            send_mail($adminEmail, $subject, $body, false);
        }
    } catch (\Exception $e) {
        log_error("Failed to send admin contact form notification email: " . $e->getMessage());
    }

    clear_old_values();
    flash_success('Thank you for your message. We\'ll get back to you soon!');
    redirect(url('/help'));

} catch (Exception $e) {
    log_error('Contact form error: ' . $e->getMessage());
    flash_error('An error occurred. Please try again.');
    redirect($_SERVER['HTTP_REFERER'] ?? url('/help'));
}
