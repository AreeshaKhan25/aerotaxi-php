<?php
// Admin page - requires authentication
// Ported from: admin/fleet.blade.php
$__page_title = 'Fleet';

$vehicles = Database::fetchAll('SELECT * FROM vehicles ORDER BY sort_order');
?>
<?php require BASE_PATH . '/templates/admin/header.php'; ?>
<div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Fleet</h1>
        <span class="text-sm text-gray-500"><?= e($vehicles->count()) ?> vehicles</span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($vehicles as $vehicle): ?>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden hover:shadow-md transition">
            
            <div class="h-48 bg-gray-50 flex items-center justify-center p-8">
                <img src="<?= e(url($vehicle->image)) ?>" alt="<?= e($vehicle->name) ?>" class="max-h-full max-w-full object-contain">
            </div>

            <div class="p-6">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-lg font-bold text-gray-900"><?= e($vehicle->name) ?></h3>
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Active</span>
                </div>

                <?php if ($vehicle->car_model): ?>
                <p class="text-sm text-gray-500 mb-4"><?= e($vehicle->car_model) ?></p>
                <?php endif; ?>

                <div class="flex items-center gap-4 mb-4">
                    <div class="flex items-center gap-1.5 text-sm text-gray-600">
                        <i class="fa-solid fa-users text-gray-400"></i>
                        <span><?= e($vehicle->passengers) ?></span>
                    </div>
                    <div class="flex items-center gap-1.5 text-sm text-gray-600">
                        <i class="fa-solid fa-suitcase-rolling text-gray-400"></i>
                        <span><?= e($vehicle->suitcases) ?></span>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-4 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Min price</span>
                        <span class="font-semibold text-gray-900">&pound;<?= e(number_format($vehicle->price, 2)) ?></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Short rate/mi</span>
                        <span class="font-medium text-gray-700">&pound;<?= e(number_format($vehicle->short_per_mile, 2)) ?></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Long rate/mi</span>
                        <span class="font-medium text-gray-700">&pound;<?= e(number_format($vehicle->long_per_mile, 2)) ?></span>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>


<?php require BASE_PATH . '/templates/admin/footer.php'; ?>