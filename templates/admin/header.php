<?php
/**
 * Admin layout header
 */
ensure_session();
require_admin();

$admin = admin_user();
$unreadCount = Database::countRows('admin_notifications', '`read` = 0');
$recentNotifs = Database::fetchAll('SELECT * FROM admin_notifications ORDER BY created_at DESC LIMIT 15');

$current_path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '';
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$base = rtrim($scriptDir, '/');

// Active nav helper
function is_admin_nav_active(array $paths) {
    $current_path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '';
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    $base = rtrim($scriptDir, '/');

    foreach ($paths as $path) {
        $check = rtrim($base . '/' . ltrim($path, '/'), '/');
        if ($current_path === $check) {
            return true;
        }
        if ($path !== '' && str_starts_with($current_path, $check . '/')) {
            return true;
        }
    }
    return false;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($__page_title ?? 'Dashboard') ?> - AeroTAXI Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
    <?php if (!empty($__extra_head)) echo $__extra_head; ?>
</head>
<body class="bg-gray-100 min-h-screen" x-data="{ mobileOpen: false }">

    <nav class="bg-[#1a2e4a] sticky top-0 z-50 shadow-lg">
        <div class="px-4 sm:px-6">
            <div class="flex items-center justify-between h-16">
                <a href="<?= base_url('admin') ?>" class="flex items-center gap-3 flex-shrink-0">
                    <img src="<?= base_url('images/logo.png') ?>" alt="AeroTAXI" class="h-9">
                    <span class="text-white font-bold text-lg">Admin</span>
                </a>

                <div class="hidden md:flex items-center gap-2">
                    <?php
                    $nav = [
                        ['paths' => ['admin', 'admin/subscribers'], 'url' => base_url('admin'), 'label' => 'Dashboard', 'icon' => 'fa-chart-pie'],
                        ['paths' => ['admin/jobs', 'admin/bookings/'], 'url' => base_url('admin/jobs'), 'label' => 'Jobs', 'icon' => 'fa-briefcase'],
                        ['paths' => ['admin/bookings'], 'url' => base_url('admin/bookings'), 'label' => 'Unpaid Bookings', 'icon' => 'fa-clock'],
                        ['paths' => ['admin/fleet'], 'url' => base_url('admin/fleet'), 'label' => 'Fleet', 'icon' => 'fa-car-side'],
                        ['paths' => ['admin/zones-map'], 'url' => base_url('admin/zones-map'), 'label' => 'Zones Map', 'icon' => 'fa-map-location-dot'],
                        ['paths' => ['admin/contact-messages'], 'url' => base_url('admin/contact-messages'), 'label' => 'Messages', 'icon' => 'fa-envelope'],
                        ['paths' => ['admin/promotions'], 'url' => base_url('admin/promotions'), 'label' => 'Promotions', 'icon' => 'fa-bullhorn'],
                    ];
                    ?>
                    <?php foreach ($nav as $item): ?>
                        <?php $active = is_admin_nav_active($item['paths']); ?>
                        <a href="<?= $item['url'] ?>"
                           class="px-4 py-2 rounded-lg text-sm font-medium transition-all flex items-center gap-2 <?= $active ? 'bg-blue-600 text-white shadow' : 'text-blue-200 hover:bg-white/10 hover:text-white' ?>">
                            <i class="fa-solid <?= $item['icon'] ?>"></i> <?= $item['label'] ?>
                        </a>
                    <?php endforeach; ?>
                </div>

                <div class="flex items-center gap-4">
                    <span class="hidden sm:block text-sm text-blue-200"><i class="fa-solid fa-user-circle mr-1"></i> <?= e($admin->name ?? 'Admin') ?></span>

                    <div class="relative" x-data="{ notifOpen: false }">
                        <button @click="notifOpen = !notifOpen" class="relative text-blue-200 hover:text-white transition">
                            <i class="fa-solid fa-bell text-lg"></i>
                            <?php if ($unreadCount > 0): ?>
                                <span class="absolute -top-1.5 -right-1.5 bg-red-500 text-white text-[10px] font-bold rounded-full h-4 w-4 flex items-center justify-center"><?= $unreadCount > 9 ? '9+' : $unreadCount ?></span>
                            <?php endif; ?>
                        </button>

                        <div x-show="notifOpen" x-cloak @click.outside="notifOpen = false"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-1"
                             class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-gray-200 z-50">
                            <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                                <h3 class="text-sm font-semibold text-gray-800">Notifications</h3>
                                <?php if ($unreadCount > 0): ?>
                                    <form method="POST" action="<?= base_url('admin/notifications/mark-read') ?>" class="inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Mark all read</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                            <div class="max-h-72 overflow-y-auto divide-y divide-gray-100">
                                <?php if (empty($recentNotifs)): ?>
                                    <div class="px-4 py-8 text-center text-gray-400 text-sm">
                                        No notifications yet.
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($recentNotifs as $notif): ?>
                                        <div class="px-4 py-3 text-sm <?= !$notif->read ? 'bg-blue-50/60' : '' ?> hover:bg-gray-50">
                                            <div class="flex items-start gap-2">
                                                <?php if ($notif->type === 'booking'): ?>
                                                    <i class="fa-solid fa-briefcase text-blue-500 mt-0.5"></i>
                                                <?php elseif ($notif->type === 'contact_message'): ?>
                                                    <i class="fa-solid fa-envelope text-green-500 mt-0.5"></i>
                                                <?php elseif ($notif->type === 'subscriber'): ?>
                                                    <i class="fa-solid fa-user-plus text-purple-500 mt-0.5"></i>
                                                <?php else: ?>
                                                    <i class="fa-solid fa-bell text-gray-400 mt-0.5"></i>
                                                <?php endif; ?>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-gray-800 leading-snug"><?= e($notif->message) ?></p>
                                                    <p class="text-xs text-gray-400 mt-1"><?= time_ago($notif->created_at) ?></p>
                                                </div>
                                                <?php if (!$notif->read): ?>
                                                    <span class="h-2 w-2 rounded-full bg-blue-500 mt-1.5 flex-shrink-0"></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="<?= base_url('admin/logout') ?>">
                        <?= csrf_field() ?>
                        <button type="submit" class="text-blue-300 hover:text-red-400 transition text-sm"><i class="fas fa-sign-out-alt text-lg"></i></button>
                    </form>
                    <button @click="mobileOpen = !mobileOpen" class="md:hidden text-blue-200 hover:text-white text-xl">
                        <i class="fas" :class="mobileOpen ? 'fa-times' : 'fa-bars'"></i>
                    </button>
                </div>
            </div>
        </div>

        <div x-show="mobileOpen" x-cloak x-transition class="md:hidden border-t border-white/10 px-4 py-3 space-y-1">
            <?php foreach ($nav as $item): ?>
                <?php $active = is_admin_nav_active($item['paths']); ?>
                <a href="<?= $item['url'] ?>"
                   class="block px-4 py-3 rounded-lg text-base font-medium <?= $active ? 'bg-blue-600 text-white' : 'text-blue-200 hover:bg-white/10' ?>">
                    <i class="fa-solid <?= $item['icon'] ?> mr-2"></i> <?= $item['label'] ?>
                </a>
            <?php endforeach; ?>
        </div>
    </nav>

    <main class="p-4 sm:p-6">
