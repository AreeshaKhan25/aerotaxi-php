<?php
// Admin page - requires authentication
// Ported from: admin/contact-messages.blade.php
$__page_title = 'Contact Messages';

$messages = Database::fetchAll('SELECT * FROM contact_messages ORDER BY created_at DESC');
?>
<?php require BASE_PATH . '/templates/admin/header.php'; ?>
<div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-800">Contact Messages</h2>
            <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full font-medium"><?= e($messages->count()) ?> total</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if (!empty($messages)): foreach ($messages as $msg): ?>
                        <tr class="hover:bg-gray-50 <?= e(!$msg->read ? 'bg-blue-50/50' : '') ?>" x-data="{ expanded: false }">
                            <td class="px-6 py-4 text-gray-600 whitespace-nowrap"><?= e(format_date($msg->created_at, 'd M Y H:i')) ?></td>
                            <td class="px-6 py-4 text-gray-900 font-medium"><?= e($msg->name) ?></td>
                            <td class="px-6 py-4">
                                <a href="mailto:<?= e($msg->email) ?>" class="text-yellow-600 hover:text-yellow-700"><?= e($msg->email) ?></a>
                            </td>
                            <td class="px-6 py-4 text-gray-700"><?= e($msg->subject ?? '--') ?></td>
                            <td class="px-6 py-4 text-gray-600 max-w-xs">
                                <div x-show="!expanded" class="truncate max-w-xs"><?= e(str_limit($msg->message, 60)) ?></div>
                                <div x-show="expanded" x-cloak class="whitespace-pre-wrap"><?= e($msg->message) ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <?php if ($msg->read): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Read</span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">Unread</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button @click="expanded = !expanded" class="text-blue-600 hover:text-blue-800 text-xs font-medium mr-2">
                                    <span x-text="expanded ? 'Collapse' : 'View'"></span>
                                </button>
                                <?php if (!$msg->read): ?>
                                    <form method="POST" action="<?= e(route('admin.contact-messages.mark-read', $msg->id)) ?>" class="inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="text-green-600 hover:text-green-800 text-xs font-medium">Mark Read</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-envelope text-3xl mb-3 block text-gray-300"></i>
                                No contact messages yet.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php require BASE_PATH . '/templates/admin/footer.php'; ?>