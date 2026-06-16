<?php
/**
 * API: Airport Search
 * 
 * GET /api/airports/search?q=london
 * 
 * Returns JSON array of airports matching the search query
 */

header('Content-Type: application/json');

$query = trim($_GET['q'] ?? '');

if ($query !== '') {
    // Search airports by name or code
    $results = Database::fetchAll(
        'SELECT id, code, name, city FROM airports WHERE name LIKE ? OR code LIKE ? OR city LIKE ? ORDER BY sort_order LIMIT 10',
        ["%$query%", "%$query%", "%$query%"]
    );
} else {
    // Return default 10 airports
    $results = Database::fetchAll('SELECT id, code, name, city FROM airports ORDER BY sort_order LIMIT 10');
}

echo json_encode($results->all());
?>
