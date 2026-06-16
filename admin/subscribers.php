<?php
// Admin page - requires authentication
// Ported from: admin/subscribers.blade.php
$__page_title = 'Subscribers';

$subscribers = Database::fetchAll('SELECT * FROM subscribers ORDER BY created_at DESC');
?>
<?php require BASE_PATH . '/templates/admin/header.php'; ?>
<div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-800">Newsletter Subscribers</h2>
            <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full font-medium"><?= e($subscribers->count()) ?> total</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Subscribed Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if (!empty($subscribers)): foreach ($subscribers as $index => $subscriber): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-gray-500"><?= e($index + 1) ?></td>
                            <td class="px-6 py-4">
                                <a href="mailto:<?= e($subscriber->email) ?>" class="text-yellow-600 hover:text-yellow-700 font-medium"><?= e($subscriber->email) ?></a>
                            </td>
                            <td class="px-6 py-4 text-gray-900"><?= e($subscriber->name ?? '--') ?></td>
                            <td class="px-6 py-4 text-gray-600"><?= e(format_date($subscriber->created_at, 'd M Y \a\t H:i')) ?></td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-users text-3xl mb-3 block text-gray-300"></i>
                                No subscribers yet.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php require BASE_PATH . '/templates/admin/footer.php'; ?>