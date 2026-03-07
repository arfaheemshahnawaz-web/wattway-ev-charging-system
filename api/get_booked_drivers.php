<?php
require_once '../auth.php';
require_login('operator');
require_once '../db.php';

header('Content-Type: application/json');

$station_id = intval($_GET['station_id'] ?? 0);
if(!$station_id){
    echo json_encode([]);
    exit;
}

// Fetch drivers who booked slots for this station
$stmt = $pdo->prepare("
    SELECT d.name, b.visit_date, b.visit_time
    FROM tbl_bookings b
    JOIN tbl_users d ON b.driver_id = d.user_id
    WHERE b.station_id=?
    ORDER BY b.visit_date DESC, b.visit_time ASC
");
$stmt->execute([$station_id]);
$booked = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($booked);
