<?php
require_once '../db.php';
require_once 'send_otp.php';
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');

try {
    $booking_id = intval($_POST['booking_id'] ?? 0);
    $method = $_POST['method'] ?? '';

    if (!$booking_id || !$method) {
        throw new Exception('Invalid request');
    }

    // Fetch booking + driver info
    $stmt = $pdo->prepare("
        SELECT b.*, d.email, d.phone, s.station_name, s.pricing
        FROM tbl_bookings b
        LEFT JOIN tbl_drivers d ON b.user_id = d.driver_id
        LEFT JOIN tbl_stations s ON b.station_id = s.station_id
        WHERE b.booking_id = ?
    ");
    $stmt->execute([$booking_id]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$booking) {
        throw new Exception('Booking not found');
    }

    // Generate OTP
    $otp = rand(1000, 9999);

    // Update booking: mark as Paid, save payment method and OTP
    $stmt = $pdo->prepare("
        UPDATE tbl_bookings 
        SET otp=?, payment_status='Paid', payment_method=?
        WHERE booking_id=?
    ");
    $stmt->execute([$otp, $method, $booking_id]);

    // Send OTP via email/SMS simulation
    send_demo_otp($booking['email'], $booking['phone'], $otp);

    echo json_encode([
        'success' => true,
        'message' => "Payment successful! OTP: $otp. Take this OTP to the station operator."
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
