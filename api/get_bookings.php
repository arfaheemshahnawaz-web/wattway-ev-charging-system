<?php
require_once '../db.php';
$station_id = $_GET['station_id'] ?? 0;

$stmt = $pdo->prepare("
    SELECT b.visit_date, b.visit_time, d.name as driver_name
    FROM tbl_bookings b
    JOIN tbl_users d ON b.driver_id=d.user_id
    WHERE b.station_id = ?
");
$stmt->execute([$station_id]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
