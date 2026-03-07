<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../auth.php';
require_login('driver');
require_once '../db.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $station_id = intval($data['station_id'] ?? 0);
    $visit_date = $data['visit_date'] ?? '';
    $visit_time = $data['visit_time'] ?? '';
    $user_id = $_SESSION['user']['id'] ?? 0;

    if (!$station_id || !$visit_date || !$visit_time) {
        echo json_encode(['success' => false, 'message' => 'Invalid data sent']);
        exit;
    }

    // Check if slot is available
    $stmt = $pdo->prepare("
        SELECT availability_id 
        FROM tbl_availability 
        WHERE station_id=? AND is_available='Yes' AND slot_time=? AND visit_date=?
        LIMIT 1
    ");
    $stmt->execute([$station_id, $visit_time, $visit_date]);
    $slot = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$slot) {
        echo json_encode(['success' => false, 'message' => 'Selected slot is not available']);
        exit;
    }

    $availability_id = $slot['availability_id'];

    // Insert into bookings first to get booking_id
    $stmt = $pdo->prepare("
        INSERT INTO tbl_bookings (user_id, station_id, visit_date, visit_time, booking_time)
        VALUES (?, ?, ?, ?, NOW())
    ");
    $stmt->execute([$user_id, $station_id, $visit_date, $visit_time]);

    // Get the last inserted booking_id
    $booking_id = $pdo->lastInsertId();

    // Update tbl_availability with booking_id and mark as unavailable
    $stmt = $pdo->prepare("
        UPDATE tbl_availability 
        SET is_available='No', booking_id=?, updated_at=NOW()
        WHERE availability_id=?
    ");
    $stmt->execute([$booking_id, $availability_id]);

    echo json_encode(['success' => true, 'message' => 'Slot booked successfully!']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
