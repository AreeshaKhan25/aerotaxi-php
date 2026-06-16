<?php
/**
 * Action: Admin Login
 * POST /admin/login
 */

// Verify CSRF
if (!verify_csrf()) {
    http_response_code(403);
    die('CSRF token mismatch');
}

// Validate input
$errors = validate($_POST, [
    'email' => 'required|email',
    'password' => 'required',
]);

if (!empty($errors)) {
    set_old_values();
    flash_errors($errors);
    redirect_back();
}

$email = trim($_POST['email']);
$password = $_POST['password'];

$admin = Database::fetch('SELECT * FROM admins WHERE email = ?', [$email]);

if ($admin && password_verify($password, $admin->password)) {
    ensure_session();
    $_SESSION['admin_id'] = $admin->id;
    clear_old_values();
    redirect(url('/admin'));
} else {
    set_old_values();
    flash_error('The provided credentials do not match our records.');
    redirect_back();
}
