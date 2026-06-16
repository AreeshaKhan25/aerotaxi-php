<?php
/**
 * API: Get Admin Notifications
 * GET /admin/notifications
 */

ensure_session();
if (!admin_logged_in()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$notifications = Database::fetchAll("SELECT * FROM admin_notifications ORDER BY created_at DESC LIMIT 50");

// Convert collection to array
$notifArray = [];
foreach ($notifications as $n) {
    $notifArray[] = [
        'id' => (int)$n->id,
        'type' => $n->type,
        'message' => $n->message,
        'data' => json_decode($n->data, true),
        'read' => (bool)$n->read,
        'created_at' => $n->created_at,
        'updated_at' => $n->updated_at,
    ];
}

header('Content-Type: application/json');
echo json_encode($notifArray);
exit;
