<?php
require_once '../db.php';
require_once '../auth.php';
require_login('operator');

$data = json_decode(file_get_contents('php://input'), true);
$availability_id = intval($data['availability_id'] ?? 0);
$driver_id = intval($data['driver_id'] ?? 0);
$otp = trim($data['otp'] ?? '');

if (!$availability_id || !$driver_id || !$otp) {
    echo json_encode(['success'=>false,'message'=>'Invalid data']);
    exit;
}

// Update availability with assigned driver and OTP
$stmt = $pdo->prepare("
    UPDATE tbl_availability
    SET assigned_driver_id=?, otp=?, is_available='No', updated_by=?, updated_at=NOW()
    WHERE availability_id=?
");
$res = $stmt->execute([$driver_id, $otp, $_SESSION['user']['id'], $availability_id]);

echo json_encode(['success'=>$res, 'message'=>$res ? 'Driver assigned successfully' : 'Failed to assign driver']);
