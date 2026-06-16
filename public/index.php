<?php

/**
 * AeroTAXI - Classical PHP Front Controller
 * 
 * All requests are routed through this file via .htaccess
 */

// Start session
session_start();

// Load configuration
require_once __DIR__ . '/../config/config.php';

// Load Composer Autoloader if available, otherwise manually load core classes
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} else {
    require_once __DIR__ . '/../core/database.php';
    require_once __DIR__ . '/../core/router.php';
    require_once __DIR__ . '/../core/helpers.php';
}

// Set old values for form re-population
set_old_values();

// Create router instance and register routes
$route = router();

// ===== PUBLIC ROUTES =====
$route->get('/', 'pages/home.php');
$route->get('/coverage', 'pages/coverage.php');
$route->get('/help', 'pages/help.php');
$route->get('/your-ride', 'pages/booking/check-prices.php');
$route->get('/transfer-details', 'pages/booking/transfer-details.php');
$route->post('/booking', 'actions/booking-store.php');
$route->get('/payment', 'pages/booking/payment.php');
$route->get('/booking/confirmation/{reference}', 'pages/booking/confirmation.php');
$route->get('/booking/lookup', 'pages/booking/lookup.php');
$route->post('/contact', 'actions/contact-store.php');
$route->post('/subscribe', 'actions/subscribe.php');

// ===== LEGAL PAGES =====
$route->get('/terms-of-service', 'pages/legal/terms.php');
$route->get('/legal/terms', 'pages/legal/terms.php');

$route->get('/privacy-policy', 'pages/legal/privacy-policy.php');
$route->get('/legal/privacy-policy', 'pages/legal/privacy-policy.php');

$route->get('/privacy-statement', 'pages/legal/privacy-statement.php');
$route->get('/legal/privacy-statement', 'pages/legal/privacy-statement.php');

$route->get('/cookie-policy', 'pages/legal/cookie-policy.php');
$route->get('/legal/cookie-policy', 'pages/legal/cookie-policy.php');

// ===== API ENDPOINTS =====
$route->get('/api/flight/validate', 'api/flight-validate.php');
$route->get('/api/airports/search', 'api/airports-search.php');

// ===== ADMIN ROUTES =====
$route->get('/admin/login', 'admin/login.php');
$route->post('/admin/login', 'actions/admin-login.php');
$route->post('/admin/logout', 'actions/admin-logout.php');
$route->get('/admin', 'admin/stats.php');
$route->get('/admin/jobs', 'admin/dashboard.php');
$route->get('/admin/bookings', 'admin/bookings.php');
$route->get('/admin/bookings/{id}', 'admin/booking-detail.php');
$route->post('/admin/bookings/{id}/update', 'actions/booking-update.php');
$route->get('/admin/fleet', 'admin/fleet.php');
$route->get('/admin/zones-map', 'admin/zones-map.php');
$route->get('/admin/contact-messages', 'admin/contact-messages.php');
$route->post('/admin/contact-messages/{id}/mark-read', 'actions/mark-message-read.php');
$route->get('/admin/promotions', 'admin/promotions.php');
$route->post('/admin/promotions/send', 'actions/send-promotion.php');
$route->get('/admin/subscribers', 'admin/subscribers.php');
$route->get('/admin/notifications', 'api/admin-notifications.php');
$route->post('/admin/notifications/mark-read', 'actions/mark-notification-read.php');

// Dispatch the route
$route->dispatch();
