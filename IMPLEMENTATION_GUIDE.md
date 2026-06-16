# AeroTAXI PHP - Installation & Implementation Guide

## Installation

### Step 1: Clone and Setup

```bash
# Navigate to the project directory
cd d:\006\web\aerotaxi-php

# Copy environment file
copy .env.example .env

# Edit .env with your configuration
# - DB_HOST, DB_NAME, DB_USER, DB_PASS
# - STRIPE_KEY, STRIPE_SECRET
# - AVIATIONSTACK_KEY
# - etc.
```

### Step 2: Install Dependencies

```bash
# Install Stripe PHP SDK via Composer
composer install

# Or just install Stripe:
composer require stripe/stripe-php
```

### Step 3: Database Setup

```bash
# Create database and tables
mysql -u root -p < setup/schema.sql

# Insert seed data
mysql -u root -p < setup/seed.sql
```

**Default Admin Account:**

- Email: `admin@aerotaxi.com`
- Password: `admin123`

### Step 4: Start the Server

```bash
# Option A: PHP built-in server
cd public
php -S localhost:8001

# Option B: Apache with .htaccess (htdocs/aerotaxi-php/public)
# Copy the aerotaxi-php folder to your Apache htdocs
# Navigate to http://localhost/aerotaxi-php/public

# Option C: Nginx with rewrite rules
server {
    listen 80;
    server_name aerotaxi.local;
    root /var/www/aerotaxi-php/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

### Step 5: Test the Installation

```bash
# Check PHP syntax
php -l public/index.php
php -l core/database.php
php -l core/helpers.php

# Test main pages
# http://localhost:8000/
# http://localhost:8000/coverage
# http://localhost:8000/help
# http://localhost:8000/your-ride

# Test API endpoints
# http://localhost:8000/api/airports/search?q=london
# http://localhost:8000/api/flight/validate?flight_number=BA1234

# Test admin
# http://localhost:8000/admin/login
```

## Implementation Patterns

### Pattern 1: Simple Page (GET only)

Example: `pages/coverage.php`

```php
<?php
/**
 * Coverage Page - List of served airports
 */

// Fetch data
$airports = Database::fetchAll('SELECT * FROM airports ORDER BY sort_order');

// Set page title
$title = 'Airport Coverage - AeroTAXI';

// Include header
include __DIR__ . '/../templates/layouts/header.php';
?>

<!-- Your page HTML here -->

<?php include __DIR__ . '/../templates/layouts/footer.php'; ?>
```

### Pattern 2: Page with Form Submission

Example: `pages/help.php`

```php
<?php
/**
 * Help Page - FAQ + Contact Form
 */

$title = 'Help & Support - AeroTAXI';

// Fetch FAQs
$faqs = Database::fetchAll('SELECT * FROM faqs ORDER BY sort_order');

include __DIR__ . '/../templates/layouts/header.php';
?>

<!-- Display FAQs -->
<?php foreach ($faqs as $faq): ?>
    <div class="faq-item">
        <h3><?= e($faq['question']) ?></h3>
        <p><?= $faq['answer'] ?></p>
    </div>
<?php endforeach; ?>

<!-- Contact Form -->
<form method="POST" action="<?= url('/contact') ?>">
    <?= csrf_field() ?>

    <input type="text" name="name" placeholder="Your Name" value="<?= old('name') ?>" required>
    <input type="email" name="email" placeholder="Your Email" value="<?= old('email') ?>" required>
    <textarea name="message" placeholder="Your Message" required></textarea>

    <button type="submit">Send Message</button>
</form>

<?php include __DIR__ . '/../templates/layouts/footer.php'; ?>
```

### Pattern 3: Admin Page with Authentication

Example: `admin/stats.php`

```php
<?php
/**
 * Admin Stats Dashboard
 */

// Require authentication
require_auth();

// Get statistics from database
$totalBookings = Database::count('bookings');
$pendingBookings = Database::count('bookings', ['status' => 'pending']);
$confirmedBookings = Database::count('bookings', ['status' => 'confirmed']);
$totalRevenue = Database::fetch(
    'SELECT COALESCE(SUM(total_price), 0) as total FROM bookings WHERE payment_status = ?',
    ['paid']
)['total'];

// Get recent bookings
$recentBookings = Database::fetchAll(
    'SELECT b.*, v.name as vehicle_name FROM bookings b
     LEFT JOIN vehicles v ON b.vehicle_id = v.id
     ORDER BY b.created_at DESC LIMIT 10'
);

$title = 'Admin Dashboard - AeroTAXI';
include __DIR__ . '/../templates/admin/header.php';
?>

<!-- Dashboard Content -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
    <div class="stat-card">
        <h3>Total Bookings</h3>
        <p class="text-4xl font-bold"><?= $totalBookings ?></p>
    </div>
    <div class="stat-card">
        <h3>Pending</h3>
        <p class="text-4xl font-bold text-yellow-600"><?= $pendingBookings ?></p>
    </div>
    <div class="stat-card">
        <h3>Confirmed</h3>
        <p class="text-4xl font-bold text-green-600"><?= $confirmedBookings ?></p>
    </div>
    <div class="stat-card">
        <h3>Revenue</h3>
        <p class="text-4xl font-bold"><?= format_price($totalRevenue) ?></p>
    </div>
</div>

<!-- Recent Bookings Table -->
<table>
    <thead>
        <tr>
            <th>Reference</th>
            <th>Passenger</th>
            <th>Route</th>
            <th>Vehicle</th>
            <th>Total</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($recentBookings as $booking): ?>
            <tr>
                <td><a href="<?= url('/admin/booking/' . $booking['id']) ?>"><?= e($booking['reference']) ?></a></td>
                <td><?= e($booking['passenger_name']) ?></td>
                <td><?= e($booking['from_location']) ?> → <?= e($booking['to_location']) ?></td>
                <td><?= e($booking['vehicle_name']) ?></td>
                <td><?= format_price($booking['total_price']) ?></td>
                <td><span class="badge badge-<?= $booking['status'] ?>"><?= ucfirst($booking['status']) ?></span></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include __DIR__ . '/../templates/admin/footer.php'; ?>
```

### Pattern 4: Form Processing Action

Example: `actions/booking-store.php`

```php
<?php
/**
 * Action: Store Booking
 *
 * POST /booking
 * Creates a new booking and redirects to payment
 */

// Verify CSRF token
if (!verify_csrf()) {
    http_response_code(403);
    die('CSRF token mismatch');
}

// Validate input
$errors = validate($_POST, [
    'from_location' => 'required',
    'to_location' => 'required',
    'depart_date' => 'required',
    'vehicle_id' => 'required|numeric',
    'first_name' => 'required|min:2|max:255',
    'last_name' => 'required|min:2|max:255',
    'email' => 'required|email',
    'phone' => 'required|min:10',
    'total_price' => 'required|numeric',
]);

if (!empty($errors)) {
    set_old_values();
    foreach ($errors as $field => $error) {
        flash_error($error);
    }
    redirect($_SERVER['HTTP_REFERER'] ?? url('/your-ride'));
}

// Generate reference
$reference = 'ATH-' . strtoupper(bin2hex(random_bytes(4)));

// Insert booking
try {
    $booking_id = Database::insert('bookings', [
        'reference' => $reference,
        'from_location' => $_POST['from_location'],
        'to_location' => $_POST['to_location'],
        'depart_date' => $_POST['depart_date'],
        'depart_time' => $_POST['depart_time'] ?? null,
        'vehicle_id' => $_POST['vehicle_id'],
        'passenger_name' => $_POST['first_name'] . ' ' . $_POST['last_name'],
        'email' => $_POST['email'],
        'phone' => $_POST['phone'],
        'flight_number' => $_POST['flight_number'] ?? null,
        'note_to_driver' => $_POST['note_to_driver'] ?? null,
        'country_code' => $_POST['country_code'] ?? null,
        'total_price' => $_POST['total_price'],
        'status' => 'pending',
        'payment_status' => 'unpaid',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ]);

    // Subscribe to newsletter if opted in
    if (!empty($_POST['agree_promotions'])) {
        Database::insert('subscribers', [
            'email' => $_POST['email'],
            'name' => $_POST['first_name'] . ' ' . $_POST['last_name'],
            'active' => true,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        Database::insert('admin_notifications', [
            'type' => 'subscriber',
            'message' => 'New subscriber: ' . $_POST['email'],
            'read' => false,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    clear_old_values();
    redirect(url('/payment?reference=' . $reference));

} catch (Exception $e) {
    log_error('Booking store error: ' . $e->getMessage());
    flash_error('An error occurred. Please try again.');
    redirect($_SERVER['HTTP_REFERER'] ?? url('/your-ride'));
}
```

### Pattern 5: Authentication Handler

Example: `actions/admin-login.php`

```php
<?php
/**
 * Action: Admin Login
 *
 * POST /admin/login
 */

if (!verify_csrf()) {
    http_response_code(403);
    die('CSRF token mismatch');
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

$errors = validate($_POST, [
    'email' => 'required|email',
    'password' => 'required|min:6',
]);

if (!empty($errors)) {
    flash_error('Please fill in all fields correctly');
    redirect(url('/admin/login'));
}

// Find admin by email
$admin = Database::fetch('SELECT * FROM admins WHERE email = ?', [$email]);

if (!$admin || !password_verify($password, $admin['password'])) {
    flash_error('Invalid email or password');
    redirect(url('/admin/login'));
}

// Set session
session_regenerate_id(true);
$_SESSION['admin_id'] = $admin['id'];

flash_success('Logged in successfully');
redirect(url('/admin/stats'));
```

### Pattern 6: REST API Endpoint

Example: `api/contact-store.php` (already created)

```php
<?php
// Handle request
header('Content-Type: application/json');

// Get input
$input = json_decode(file_get_contents('php://input'), true);

// Validate
if (empty($input['email'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Email is required']);
    exit;
}

// Process
try {
    Database::insert('messages', ['email' => $input['email']]);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
```

## Creating Remaining Files

### Required Public Pages

1. **pages/booking/check-prices.php** - Vehicle selection + map
2. **pages/booking/transfer-details.php** - Passenger form + flight validation
3. **pages/booking/payment.php** - Stripe payment integration
4. **pages/booking/confirmation.php** - Booking confirmation display
5. **pages/booking/lookup.php** - Search booking by reference
6. **pages/legal/terms.php** - Terms of service (static)
7. **pages/legal/privacy-policy.php** - Privacy policy (static)
8. **pages/legal/privacy-statement.php** - Privacy statement (static)
9. **pages/legal/cookie-policy.php** - Cookie policy (static)

### Required Admin Pages

1. **admin/login.php** - Login form
2. **admin/dashboard.php** - Jobs list (similar to stats)
3. **admin/bookings.php** - Unpaid bookings list
4. **admin/booking-detail.php** - Edit single booking
5. **admin/fleet.php** - Vehicles list/edit
6. **admin/zones-map.php** - Coverage map (using Leaflet)
7. **admin/contact-messages.php** - Messages from contact form
8. **admin/promotions.php** - Send newsletters
9. **admin/subscribers.php** - Manage subscribers
10. **templates/admin/header.php** - Admin navigation
11. **templates/admin/footer.php** - Admin footer

### Required Action Files

1. **actions/booking-update.php** - Update booking (admin)
2. **actions/admin-logout.php** - Logout
3. **actions/mark-notification-read.php** - Mark notification read
4. **actions/mark-message-read.php** - Mark message read
5. **actions/send-promotion.php** - Send newsletter

## File Structure Template

Every file should follow this structure:

```php
<?php
/**
 * Page Title / Feature Description
 *
 * [Additional details like HTTP method, parameters]
 */

// 1. Include dependencies
// 2. Check authentication if needed
// 3. Get data from database
// 4. Validate if form submission
// 5. Include header/layout
// 6. Output HTML
// 7. Include footer/layout
```

## Common Modifications

### Add a New Database Column

```sql
-- Add to schema.sql
ALTER TABLE bookings ADD COLUMN special_requests TEXT AFTER note_to_driver;

-- Then migrate existing database:
ALTER TABLE bookings ADD COLUMN special_requests TEXT AFTER note_to_driver;
```

### Change Database Driver to SQLite

```php
// In config/config.php, change Database connection:
$dsn = 'sqlite:' . __DIR__ . '/../database.sqlite';
```

### Add Email Sending

```php
// Using PHPMailer (install via composer require phpmailer/phpmailer)
require_once 'vendor/autoload.php';

$mail = new \PHPMailer\PHPMailer\PHPMailer();
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'your-email@gmail.com';
$mail->Password = 'your-app-password';
$mail->SMTPSecure = 'tls';
$mail->Port = 587;

$mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
$mail->addAddress($booking['email']);
$mail->Subject = 'Booking Confirmation';
$mail->Body = 'Your booking reference: ' . $booking['reference'];

if (!$mail->send()) {
    log_error('Mail error: ' . $mail->ErrorInfo);
}
```

## Debugging Tips

```php
// Dump variable and die
dd($bookings);

// Log errors
log_error('Something went wrong: ' . $error);

// Enable all errors (add to public/index.php)
error_reporting(E_ALL);
ini_set('display_errors', '1');
```

## Performance Optimization

```php
// Add indexes to frequently queried columns (done in schema.sql)

// Cache results
$_SESSION['airports_cache'] = Database::fetchAll('SELECT * FROM airports');

// Use LIMIT in queries
$bookings = Database::fetchAll('SELECT * FROM bookings LIMIT 100');

// Close database connection explicitly (optional)
$pdo = null;
```

## Security Best Practices

✅ Always escape output: `<?= e($variable) ?>`  
✅ Always verify CSRF tokens: `verify_csrf()`  
✅ Always validate input: `validate($_POST, $rules)`  
✅ Use prepared statements (done automatically by Database class)  
✅ Hash passwords: `password_hash($password, PASSWORD_BCRYPT)`  
✅ Verify passwords: `password_verify($input, $hash)`  
✅ Use HTTPS in production  
✅ Never expose sensitive keys in code (use .env)

## Deployment Checklist

- [ ] Set `APP_DEBUG=false` in config.php
- [ ] Update database credentials
- [ ] Configure Stripe with production keys
- [ ] Set up SMTP email
- [ ] Configure web server (Apache/Nginx)
- [ ] Verify .htaccess or nginx rewrite rules
- [ ] Copy images/ directory from Laravel project
- [ ] Test all routes
- [ ] Set up SSL certificate
- [ ] Monitor error logs
- [ ] Set up regular backups

## Support Files Needed

Copy these from the Laravel project:

- `public/images/` - All images and logos
- `public/images/airports/` - Airport images
- `public/images/vehicles/` - Vehicle images
