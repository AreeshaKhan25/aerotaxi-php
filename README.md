<p align="center">
  <img src="public/images/Poster.png" alt="AeroTAXI Banner" width="100%">
</p>

<h1 align="center">AeroTAXI — Classical PHP Edition</h1>

<p align="center">
  A complete 1:1 port of the Laravel AeroTAXI application into classical PHP.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.x-777BB4?style=flat-square&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-Database-4479A1?style=flat-square&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/Stripe-Payments-008CDD?style=flat-square&logo=stripe&logoColor=white" alt="Stripe">
  <img src="https://img.shields.io/badge/TailwindCSS-UI-38B2AC?style=flat-square&logo=tailwindcss&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/License-Same%20as%20original-lightgrey?style=flat-square" alt="License">
</p>

---

## 📖 Overview

AeroTAXI (Classical PHP Edition) mirrors the structure and behavior of the original Laravel application using plain PHP, PDO, and a lightweight custom router — no framework dependencies required.

## 🚀 Quick Start

### 1. Database Setup

```bash
# Import the database schema
mysql -u root -p aerotaxi < setup/schema.sql

# Import seed data
mysql -u root -p aerotaxi < setup/seed.sql
```

### 2. Configuration

Edit `config/config.php` with your settings:

- Database credentials (host, name, user, password)
- Stripe keys (`pk_test_xxx`, `sk_test_xxx`)
- AviationStack API key
- Admin email for notifications
- Tawk.to property ID

### 3. Run the Application

```bash
# Using PHP built-in server
cd public
php -S localhost:8000

# Or use Apache/Nginx with the .htaccess rules
```

Visit: **http://localhost:8000**

## 🗂️ Project Structure

```
aerotaxi-php/
├── config/
│   └── config.php              # Database, Stripe, API credentials
├── core/
│   ├── database.php            # PDO singleton & query helpers
│   ├── helpers.php             # Session, CSRF, validation, formatting
│   └── router.php              # URL routing system
├── public/
│   ├── index.php               # Front controller
│   ├── .htaccess               # Apache rewrite rules
│   ├── css/app.css             # Tailwind overrides
│   └── images/                 # Copy from Laravel project
├── pages/
│   ├── home.php                # Homepage with booking form
│   ├── coverage.php            # Airport coverage
│   ├── help.php                # FAQ & support
│   ├── booking/
│   │   ├── check-prices.php
│   │   ├── transfer-details.php
│   │   ├── payment.php
│   │   ├── confirmation.php
│   │   └── lookup.php
│   └── legal/
│       ├── terms.php
│       ├── privacy-policy.php
│       ├── privacy-statement.php
│       └── cookie-policy.php
├── admin/
│   ├── login.php
│   ├── stats.php
│   ├── dashboard.php
│   ├── bookings.php
│   ├── booking-detail.php
│   ├── fleet.php
│   ├── zones-map.php
│   ├── contact-messages.php
│   ├── promotions.php
│   └── subscribers.php
├── api/
│   ├── airports-search.php
│   ├── flight-validate.php
│   └── contact-store.php
├── actions/
│   ├── booking-store.php
│   ├── booking-update.php
│   ├── admin-login.php
│   ├── admin-logout.php
│   ├── mark-notification-read.php
│   ├── mark-message-read.php
│   └── send-promotion.php
├── templates/
│   ├── layouts/
│   │   ├── header.php
│   │   └── footer.php
│   └── admin/
│       ├── header.php
│       └── footer.php
└── setup/
    ├── schema.sql
    └── seed.sql
```

## 📚 Implementation Guides

### Authentication System

Session-based authentication for the admin panel. Stores `admin_id` in `$_SESSION`:

```php
// Check if authenticated
if (!is_authenticated()) {
    redirect(url('/admin/login'));
}

// Get current admin
$admin = auth_admin();

// Logout
logout();
```

### Database Access

Use the `Database` class for all database operations:

```php
// Fetch single row
$booking = Database::fetch('SELECT * FROM bookings WHERE reference = ?', [$reference]);

// Fetch multiple rows
$bookings = Database::fetchAll('SELECT * FROM bookings WHERE status = ?', ['pending']);

// Insert
$id = Database::insert('bookings', [
    'reference' => 'ATH-' . strtoupper(bin2hex(random_bytes(4))),
    'from_location' => $from,
    'to_location' => $to,
    'total_price' => $price,
]);

// Update
Database::update('bookings', ['status' => 'confirmed'], ['id' => $id]);

// Count
$count = Database::count('bookings', ['status' => 'pending']);
```

### Stripe Integration

Create payment intents for bookings:

```php
require_once 'vendor/autoload.php';

$stripe = new \Stripe\StripeClient(STRIPE_SECRET);

$intent = $stripe->paymentIntents->create([
    'amount' => (int) round($price * 100), // in pence
    'currency' => 'gbp',
    'metadata' => [
        'booking_reference' => $reference,
        'from' => $from,
        'to' => $to,
    ],
]);

// On frontend (payment.php):
// const { clientSecret } = await fetch(...).then(r => r.json());
// stripe.confirmPayment({ clientSecret, elements });
```

### Template Pattern

All public pages follow this pattern:

```php
<?php
// 1. Require authentication if needed
require_auth();

// 2. Get data from database
$bookings = Database::fetchAll('SELECT * FROM bookings');

// 3. Include header
$title = 'Page Title';
include __DIR__ . '/../templates/layouts/header.php';
?>

<!-- 4. Page content -->
<section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Your content here -->
</section>

<?php
// 5. Include footer
include __DIR__ . '/../templates/layouts/footer.php';
```

### Form Handling & Validation

```php
// On GET - display form
$title = 'Contact Us';
include __DIR__ . '/../templates/layouts/header.php';
?>

<form method="POST" action="<?= url('/contact') ?>">
    <?= csrf_field() ?>

    <input type="text" name="name" value="<?= old('name') ?>" required>
    <input type="email" name="email" value="<?= old('email') ?>" required>

    <button type="submit">Submit</button>
</form>

<?php include __DIR__ . '/../templates/layouts/footer.php';

// On POST - handle submission (actions/contact-store.php)
?>
<?php

if (!verify_csrf()) {
    http_response_code(403);
    die('CSRF token mismatch');
}

$errors = validate($_POST, [
    'name' => 'required|min:2|max:255',
    'email' => 'required|email',
    'message' => 'required|min:10|max:2000',
]);

if (!empty($errors)) {
    set_old_values();
    flash_error('Please fix the errors below');
    redirect(url('/help'));
}

// Process form
Database::insert('contact_messages', [
    'name' => $_POST['name'],
    'email' => $_POST['email'],
    'message' => $_POST['message'],
]);

flash_success('Thank you for your message');
redirect(url('/'));
```

### AJAX API Endpoints

These return JSON for frontend use:

```php
// GET /api/airports/search?q=london
$query = trim($_GET['q'] ?? '');

if (strlen($query) < 2) {
    header('Content-Type: application/json');
    echo json_encode(['results' => []]);
    exit;
}

$results = Database::fetchAll(
    'SELECT id, code, name, city FROM airports WHERE name LIKE ? OR code LIKE ? LIMIT 10',
    ["%$query%", "%$query%"]
);

header('Content-Type: application/json');
echo json_encode(['results' => $results]);
```

## ✨ Key Features Implemented

**Public Pages**

- Home (with booking form & location autocomplete)
- Coverage (airport list)
- Help (FAQ + contact form)
- Legal pages (terms, privacy, etc.)
- Booking flow (4-step booking process)
- Booking lookup (search by reference)

**Admin Panel**

- Login/logout
- Dashboard with stats
- Booking management
- Fleet management
- Contact messages
- Subscriber management

**API Endpoints**

- Airport search (autocomplete)
- Flight validation (AviationStack)
- Contact form submission

**Integrations**

- Stripe payments
- AviationStack flight validation
- Nominatim (address/location search)
- Tawk.to live chat

## 🧰 Frontend Libraries

- **Tailwind CSS** — Utility-first CSS via CDN
- **Alpine.js** — Lightweight reactive JavaScript
- **Leaflet.js** — Interactive maps
- **Font Awesome** — Icons

## 🔐 Database Credentials

Default admin account (created by `seed.sql`):

- Email: `admin@aerotaxi.com`
- Password: `admin123` (hash: `$2y$12$afPr0iTAB/nrZYMb5iHcEO/4dn6mqZ4HZLMq5vwjeDxwPzNVoicZK`)

> ⚠️ Change these credentials before deploying to production.

## 🧩 Common Patterns

**Redirect with Message**

```php
redirect_with_message(url('/admin/stats'), 'success', 'Booking updated successfully');
```

**Price Formatting**

```php
echo format_price($booking['total_price']); // Outputs: £45.50
```

**Date Formatting**

```php
echo format_date($booking['depart_date']); // Outputs: 15 Jun 2026
```

**Generate CSRF Token**

```php
echo csrf_field(); // Outputs: <input type="hidden" name="_csrf_token" value="...">
```

## 📦 Deployment Notes

### Production Checklist

- [ ] Update `APP_DEBUG` to `false` in `config.php`
- [ ] Generate strong CSRF token length (32 bytes default)
- [ ] Set up proper error logging
- [ ] Configure Stripe keys (production keys, not test keys)
- [ ] Set up AviationStack API key
- [ ] Configure email sending (SMTP or `mail()`)
- [ ] Set `SESSION_LIFETIME` appropriately
- [ ] Copy `images/` directory from Laravel project
- [ ] Verify `.htaccess` is working (or configure nginx)
- [ ] Test all routes and integrations

### Email Sending

Currently uses PHP's `mail()` function. For production SMTP, use PHPMailer:

```bash
composer require phpmailer/phpmailer
```

```php
$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->Host = 'smtp.example.com';
$mail->SMTPAuth = true;
$mail->Username = 'your-email@example.com';
$mail->Password = 'your-password';
$mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
$mail->addAddress($booking['email']);
$mail->Subject = 'Booking Confirmation';
$mail->Body = 'Your booking is confirmed...';
$mail->send();
```

## 🧪 Testing

```bash
# Check PHP syntax
php -l public/index.php
php -l core/database.php
php -l core/helpers.php
php -l core/router.php

# Start dev server
cd public && php -S localhost:8000

# Test endpoints
curl http://localhost:8000/
curl http://localhost:8000/api/airports/search?q=london
```

## 🔄 Migration from Laravel

The structure mirrors the Laravel version exactly:

| Laravel                   | Classical PHP                                 |
| -------------------------- | --------------------------------------------- |
| `routes/web.php`          | `core/router.php` + `public/index.php`        |
| `app/Http/Controllers/*`  | `pages/*` + `admin/*` + `api/*` + `actions/*` |
| `resources/views/*`       | `pages/*` + `templates/*`                     |
| `config/app.php` + `.env` | `config/config.php`                           |
| `app/Models/*`            | Database queries in pages/actions             |
| Laravel facades           | PDO + global functions                        |
| Blade templating          | PHP includes                                  |
| Laravel validation        | Custom `validate()` function                  |

## 📄 Support & Documentation

For detailed implementation of specific pages, refer to the Laravel source:

- Pages: `d:\006\web\aerotaxi\resources\views\`
- Controllers: `d:\006\web\aerotaxi\app\Http\Controllers\`
- Models: `d:\006\web\aerotaxi\app\Models\`

All UI/UX is preserved exactly from the Laravel version using Tailwind CSS and Alpine.js.

## 📜 License

Same as original AeroTAXI project.

---

<p align="center">Made with ❤️ using PHP, MySQL, and Tailwind CSS</p>