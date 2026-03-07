<?php
require_once '../db.php';

$station_id = $_GET['station_id'] ?? 0;

// Fetch all slots for this station
$stmt = $pdo->prepare("
    SELECT 
        a.availability_id,
        a.visit_date,
        a.slot_time,
        a.is_available,
        a.assigned_driver_id,
        a.otp AS slot_otp,
        d.name AS driver_name,
        b.booking_id,
        b.user_id AS booked_user_id,
        b.otp AS booking_otp
    FROM tbl_availability a
    LEFT JOIN tbl_drivers d ON a.assigned_driver_id = d.driver_id
    LEFT JOIN tbl_bookings b 
        ON a.station_id = b.station_id 
        AND a.visit_date = b.visit_date 
        AND a.slot_time = b.visit_time
    WHERE a.station_id = ?
    ORDER BY a.visit_date, a.slot_time
");
$stmt->execute([$station_id]);
$slots = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($slots);
