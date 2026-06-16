<?php
/**
 * Action: Admin Logout
 * POST /admin/logout
 */

// Verify CSRF
if (!verify_csrf()) {
    http_response_code(403);
    die('CSRF token mismatch');
}

ensure_session();
unset($_SESSION['admin_id']);

// Regenerate session ID for security
session_regenerate_id(true);

redirect(url('/admin/login'));
