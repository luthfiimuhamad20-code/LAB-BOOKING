<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');

if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$date = get('date');

if (!is_valid_date($date)) {
    http_response_code(422);
    echo json_encode(['error' => 'Tanggal tidak valid']);
    exit;
}

$bookings = q(
    "SELECT lab_id, TIME_FORMAT(start_time, '%H:%i') AS start_time, TIME_FORMAT(end_time, '%H:%i') AS end_time, status
     FROM bookings
     WHERE booking_date = ?
       AND status IN ('pending', 'approved')
     ORDER BY start_time ASC",
    [$date]
)->fetchAll();

echo json_encode([
    'date'     => $date,
    'bookings' => $bookings,
]);
