# AeroTAXI Classical PHP Setup Guide

Follow these steps to set up and run the AeroTAXI classical PHP project (`aerotaxi-php`) on a new PC from scratch.

---

## Prerequisites

Before starting, ensure your system has the following installed:
1. **PHP 8.1+**
2. **Composer** (Dependency manager for PHP)
3. **MySQL / MariaDB** (via XAMPP, Laragon, or standalone)
4. **Apache** (usually included in XAMPP) or PHP's built-in server

---

## Step-by-Step Setup

### Step 1: Copy / Clone the Repository Files
Copy the `aerotaxi-php` project folder to your local machine (e.g., `C:\xampp\htdocs\aerotaxi-php` or any directory of your choice).

### Step 2: Configure Environment Variables
1. Navigate to the root directory of the project.
2. Duplicate the `.env.example` file and rename it to `.env`:
   ```bash
   cp .env.example .env
   ```
3. Open the `.env` file in a text editor and configure your variables:
   - **Database Details**:
     ```env
     DB_HOST=localhost
     DB_PORT=3306
     DB_NAME=aerotaxiphp
     DB_USER=root
     DB_PASS=
     ```
   - **Stripe API Keys** (required for checkout):
     ```env
     STRIPE_KEY=pk_test_...
     STRIPE_SECRET=sk_test_...
     ```
   - **Tawk.to Live Chat Account ID** (required for support chat bubble):
     ```env
     TAWK_PROPERTY_ID=69cdfcdcbe444a1c3a7ffc6f/1jl6a7g5l
     ```
   - **SMTP Email Configuration** (required for booking and contact notifications):
     ```env
     MAIL_MAILER=smtp
     MAIL_HOST=smtp.gmail.com
     MAIL_PORT=587
     MAIL_USERNAME=supportaerotaxi@gmail.com
     MAIL_PASSWORD="your-gmail-app-password"
     MAIL_ENCRYPTION=tls
     MAIL_FROM_ADDRESS="supportaerotaxi@gmail.com"
     MAIL_FROM_NAME="Aero Taxi"
     ```

### Step 3: Install PHP Dependencies (Composer)
Open your terminal in the project root directory and install dependencies:
```bash
composer install
```
*Note: This generates the `vendor/` folder, downloading Stripe PHP SDK and PHPMailer, and registers the autoloader.*

### Step 4: Import Database Schema & Seed Data
1. Open your database management tool (such as **phpMyAdmin** at `http://localhost/phpmyadmin`).
2. Create a new database named **`aerotaxiphp`** with `utf8mb4_general_ci` collation.
3. Import the setup files located in the `setup` folder:
   - **First**, import [setup/schema.sql](file:///d:/006/web/aerotaxi-php/setup/schema.sql) (creates tables).
   - **Second**, import [setup/seed.sql](file:///d:/006/web/aerotaxi-php/setup/seed.sql) (inserts initial records like vehicles and admin accounts).

*Alternatively, you can run these commands via CLI:*
```bash
mysql -u root -p -e "CREATE DATABASE aerotaxiphp;"
mysql -u root -p aerotaxiphp < setup/schema.sql
mysql -u root -p aerotaxiphp < setup/seed.sql
```

### Step 5: Copy Images and Assets
Ensure that vehicle and logo images are present inside the public directory. If they are missing, copy them from the original Laravel version of the repository:
- Copy the contents of `aerotaxi/public/images/` to `aerotaxi-php/public/images/`.
- Verify the following logo assets are present:
  - `public/images/logo.png`
  - `public/images/logo2.png`

### Step 6: Start Server & Run

#### Option A: Apache / XAMPP Subdirectory Setup (Recommended)
1. Move the `aerotaxi-php` folder inside your `htdocs` directory (e.g. `C:\xampp\htdocs\aerotaxi-php`).
2. Start the Apache and MySQL modules in your XAMPP Control Panel.
3. Open your browser and navigate to:
   ```
   http://localhost/aerotaxi-php/public/
   ```

#### Option B: PHP Built-in Server
1. Open your terminal in the `public` subdirectory of the project:
   ```bash
   cd public
   ```
2. Start the built-in PHP development server:
   ```bash
   php -S localhost:8000
   ```
3. Open your browser and navigate to:
   ```
   http://localhost:8000
   ```

---

## Default Admin Credentials
To log in to the admin panel (`/admin/login`):
- **Email**: `admin@aerotaxi.com`
- **Password**: `admin123`
