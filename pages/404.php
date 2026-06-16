<?php
/**
 * 404 Not Found Page
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="min-h-screen bg-gradient-to-br from-gray-900 to-gray-800 flex items-center justify-center px-4">
        <div class="text-center">
            <h1 class="text-9xl font-bold text-yellow-400 mb-4">404</h1>
            <p class="text-3xl font-bold text-white mb-2">Page Not Found</p>
            <p class="text-gray-400 mb-8">Sorry, the page you're looking for doesn't exist.</p>
            
            <div class="flex gap-4 justify-center flex-wrap">
                <a href="<?= url('/') ?>" class="bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-semibold px-8 py-3 rounded-lg transition">
                    <i class="fas fa-home mr-2"></i>Go Home
                </a>
                <a href="<?= url('/your-ride') ?>" class="bg-gray-700 hover:bg-gray-600 text-white font-semibold px-8 py-3 rounded-lg transition">
                    <i class="fas fa-taxi mr-2"></i>Book Transfer
                </a>
            </div>
        </div>
    </div>
</body>
</html>
