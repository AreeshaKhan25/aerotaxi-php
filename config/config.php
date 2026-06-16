<?php

// Load .env variables if .env file exists
if (file_exists(dirname(__DIR__) . '/.env')) {
    $lines = file(dirname(__DIR__) . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) {
            continue;
        }
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $key = trim($parts[0]);
            $value = trim($parts[1]);
            $value = trim($value, '"\'');
            putenv("{$key}={$value}");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}

/**
 * AeroTAXI - Classical PHP Configuration
 */

// ===== DATABASE CONFIGURATION =====
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'aerotaxi');
define('DB_DATABASE', getenv('DB_NAME') ?: 'aerotaxi');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_USERNAME', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_PASSWORD', getenv('DB_PASS') ?: '');
define('DB_CHARSET', 'utf8mb4');

// ===== STRIPE CONFIGURATION =====
define('STRIPE_KEY', getenv('STRIPE_KEY') ?: 'pk_test_REPLACE_WITH_YOUR_KEY');
define('STRIPE_SECRET', getenv('STRIPE_SECRET') ?: 'sk_test_REPLACE_WITH_YOUR_SECRET');

// ===== AVIATIONSTACK CONFIGURATION =====
define('AVIATIONSTACK_KEY', getenv('AVIATIONSTACK_KEY') ?: '');

// ===== APP CONFIGURATION =====
define('APP_NAME', 'AeroTAXI');
define('APP_URL', getenv('APP_URL') ?: 'http://localhost:8000');
define('APP_ENV', getenv('APP_ENV') ?: 'development');
define('APP_DEBUG', (bool)(getenv('APP_DEBUG') ?: false));

// ===== ADMIN CONFIGURATION =====
define('ADMIN_EMAIL', getenv('ADMIN_EMAIL') ?: 'admin@aerotaxi.com');

// ===== SESSION CONFIGURATION =====
define('SESSION_LIFETIME', 120); // minutes

// ===== CSRF CONFIGURATION =====
define('CSRF_TOKEN_LENGTH', 32);

// ===== EMAIL CONFIGURATION =====
define('MAIL_MAILER', getenv('MAIL_MAILER') ?: 'smtp');
define('MAIL_HOST', getenv('MAIL_HOST') ?: 'smtp.gmail.com');
define('MAIL_PORT', (int)(getenv('MAIL_PORT') ?: 587));
define('MAIL_USERNAME', getenv('MAIL_USERNAME') ?: '');
define('MAIL_PASSWORD', getenv('MAIL_PASSWORD') ?: '');
define('MAIL_ENCRYPTION', getenv('MAIL_ENCRYPTION') ?: 'tls');
define('MAIL_FROM_ADDRESS', getenv('MAIL_FROM_ADDRESS') ?: 'supportaerotaxi@gmail.com');
define('MAIL_FROM', getenv('MAIL_FROM_ADDRESS') ?: 'supportaerotaxi@gmail.com');
define('MAIL_FROM_NAME', getenv('MAIL_FROM_NAME') ?: 'Aero Taxi');

// ===== TAWK.TO CONFIGURATION =====
define('TAWK_PROPERTY_ID', getenv('TAWK_PROPERTY_ID') ?: 'your_tawk_property_id');

// ===== TIME ZONE =====
date_default_timezone_set('Europe/London');

// ===== BASE PATH =====
define('BASE_PATH', dirname(__DIR__));
