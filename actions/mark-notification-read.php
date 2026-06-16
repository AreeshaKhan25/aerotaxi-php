<?php
/**
 * Action: Mark Notifications as Read
 * POST /admin/notifications/mark-read
 */

ensure_session();
require_admin();

// Verify CSRF
if (!verify_csrf()) {
    http_response_code(403);
    die('CSRF token mismatch');
}

Database::update('admin_notifications', ['read' => 1], '`read` = 0');

redirect_back();
