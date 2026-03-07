<?php
require_once '../db.php';
require_once '../auth.php';
require_login('operator');

header('Content-Type: application/json');

// Read JSON input
$data = json_decode(file_get_contents('php://input'), true);

$availability_id = $data['availability_id'] ?? null;
$driver_id = $data['driver_id'] ?? null;
$otp = trim($data['otp'] ?? '');

if (!$availability_id || !$driver_id || !$otp) {
    echo json_encode(['success'=>false, 'message'=>'All fields are required']);
    exit;
}

// Optional: validate that driver exists
$stmt = $pdo->prepare("SELECT driver_id FROM tbl_drivers WHERE driver_id=?");
$stmt->execute([$driver_id]);
if (!$stmt->fetch()) {
    echo json_encode(['success'=>false, 'message'=>'Invalid driver']);
    exit;
}

// Update tbl_availability to assign driver and OTP
// Assuming tbl_availability has columns: availability_id, assigned_driver_id, otp, is_available
$stmt = $pdo->prepare("
    UPDATE tbl_availability 
    SET assigned_driver_id = ?, otp = ?, is_available = 'No', updated_by = ?
    WHERE availability_id = ?
");
$success = $stmt->execute([$driver_id, $otp, $_SESSION['user']['id'], $availability_id]);

echo json_encode([
    'success' => $success,
    'message' => $success ? 'Driver confirmed and slot assigned successfully' : 'Failed to assign driver'
]);
exit;
