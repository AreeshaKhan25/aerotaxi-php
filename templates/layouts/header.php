<?php
/**
 * Public site header/layout top
 * Ported from layouts/app.blade.php
 */
$_page_title = $__page_title ?? 'AeroTAXI - Airport Transfers';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($_page_title) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('css/app.css') ?>">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="icon" href="<?= base_url('images/favicon.ico') ?>">
    <?php if (!empty($__extra_head)) echo $__extra_head; ?>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-white min-h-screen flex flex-col">

    <!-- Navigation -->
    <nav class="bg-white border-b border-gray-100 sticky top-0 z-50" x-data="{ mobileMenu: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="<?= base_url('/') ?>" class="flex-shrink-0 flex items-center gap-0">
                    <img src="<?= base_url('images/logo.png') ?>" alt="AeroTAXI" class="h-8 sm:h-10 md:h-14 w-auto">
                    <img src="<?= base_url('images/logo2.png') ?>" alt="AeroTAXI" class="h-12 sm:h-14 md:h-20 w-auto ml-1 sm:ml-2">
                </a>
                <div class="hidden md:flex items-center gap-8">
                    <a href="<?= base_url('/') ?>" class="text-sm font-medium <?= is_current('/') ? 'text-gray-900' : 'text-gray-500 hover:text-gray-900' ?> transition">Home</a>
                    <a href="<?= base_url('coverage') ?>" class="text-sm font-medium <?= is_current('coverage') ? 'text-gray-900' : 'text-gray-500 hover:text-gray-900' ?> transition">Coverage</a>
                    <a href="<?= base_url('help') ?>" class="text-sm font-medium <?= is_current_prefix('help') ? 'text-gray-900' : 'text-gray-500 hover:text-gray-900' ?> transition">Help</a>
                    <a href="<?= base_url('booking/lookup') ?>" class="text-sm font-medium <?= is_current('booking/lookup') ? 'text-gray-900' : 'text-gray-500 hover:text-gray-900' ?> transition">My Booking</a>
                    <a href="<?= base_url('/') ?>" class="bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-semibold rounded-full px-6 py-2.5 text-sm transition-all hover:shadow-md active:scale-[0.98]">Book Now</a>
                </div>
                <button @click="mobileMenu = !mobileMenu" class="md:hidden text-gray-600 hover:text-gray-900">
                    <i class="fas" :class="mobileMenu ? 'fa-times' : 'fa-bars'" style="font-size:1.25rem"></i>
                </button>
            </div>
        </div>
        <!-- Mobile menu -->
        <div x-show="mobileMenu" x-cloak x-transition class="md:hidden border-t border-gray-100 px-4 py-3 space-y-2 bg-white">
            <a href="<?= base_url('/') ?>" class="block px-4 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Home</a>
            <a href="<?= base_url('coverage') ?>" class="block px-4 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Coverage</a>
            <a href="<?= base_url('help') ?>" class="block px-4 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Help</a>
            <a href="<?= base_url('booking/lookup') ?>" class="block px-4 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">My Booking</a>
            <a href="<?= base_url('/') ?>" class="block bg-yellow-400 text-gray-900 font-semibold rounded-full px-4 py-2.5 text-sm text-center mt-2">Book Now</a>
        </div>
    </nav>

    <!-- Main content -->
    <main class="flex-grow">
