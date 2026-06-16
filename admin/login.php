<?php
// Ported from: admin/auth/login.blade.php
ensure_session();
if (admin_logged_in()) {
    redirect(base_url('admin'));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - AeroTAXI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        <div class="bg-gray-800 rounded-2xl shadow-2xl p-8">
            <div class="text-center mb-8">
                <img src="<?= base_url('images/logo.png') ?>" alt="AeroTAXI" class="h-14 mx-auto mb-3">
                <p class="text-gray-400 text-sm">Admin Panel</p>
            </div>

            <!-- Error Messages -->
            <?php $errs = get_errors(); if (!empty($errs)): ?>
                <div class="bg-red-500/10 border border-red-500/50 rounded-lg p-4 mb-6">
                    <?php foreach ($errs as $error): ?>
                        <p class="text-red-400 text-sm"><?= e($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="POST" action="<?= base_url('admin/login') ?>">
                <?= csrf_field() ?>

                <div class="mb-5">
                    <label for="email" class="block text-sm font-medium text-gray-300 mb-2">Email Address</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" id="email" name="email" value="<?= e(old('email')) ?>" required autofocus
                            class="w-full bg-gray-700 text-white border border-gray-600 rounded-lg pl-10 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent placeholder-gray-500"
                            placeholder="admin@aerotaxi.com">
                    </div>
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-300 mb-2">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" id="password" name="password" required
                            class="w-full bg-gray-700 text-white border border-gray-600 rounded-lg pl-10 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent placeholder-gray-500"
                            placeholder="Enter your password">
                    </div>
                </div>

                <div class="flex items-center mb-6">
                    <input type="checkbox" id="remember" name="remember"
                        class="w-4 h-4 rounded border-gray-600 bg-gray-700 text-yellow-400 focus:ring-yellow-400 focus:ring-offset-gray-800">
                    <label for="remember" class="ml-2 text-sm text-gray-400">Remember me</label>
                </div>

                <button type="submit"
                    class="w-full bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-bold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-sign-in-alt"></i>
                    Sign In
                </button>
            </form>
        </div>

        <p class="text-center text-gray-600 text-sm mt-6">&copy; <?= e(date('Y')) ?> AeroTAXI. All rights reserved.</p>
    </div>
</body>
</html>

