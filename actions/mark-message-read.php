<?php
/**
 * Action: Mark Contact Message as Read
 * POST /admin/contact-messages/{id}/mark-read
 */

ensure_session();
require_admin();

// Verify CSRF
if (!verify_csrf()) {
    http_response_code(403);
    die('CSRF token mismatch');
}

if (empty($id)) {
    redirect(base_url('admin/contact-messages'));
}

Database::update('contact_messages', ['read' => 1], 'id = ?', [$id]);

flash_success('Message marked as read.');
redirect(base_url('admin/contact-messages'));
