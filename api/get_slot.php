<?php
require_once '../db.php';
require_once '../auth.php';

if (!is_logged_in() || ($_SESSION['user']['role'] ?? '') !== 'driver') {
    http_response_code(403);
    echo json_encode([]);
    exit;
}

$station_id = intval($_GET['station_id'] ?? 0);
$date = $_GET['date'] ?? date('Y-m-d');

if (!$station_id) {
    echo json_encode([]);
    exit;
}

// Fetch slots for the station and date
$stmt = $pdo->prepare("
    SELECT a.availability_id, a.slot_time, a.is_available, d.name AS driver_name
    FROM tbl_availability a
    LEFT JOIN tbl_drivers d ON a.updated_by = d.driver_id
    WHERE a.station_id = ? AND a.visit_date = ?
    ORDER BY a.slot_time ASC
");
$stmt->execute([$station_id, $date]);

$slots = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($slots);
