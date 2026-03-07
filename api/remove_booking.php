<?php
require_once '../db.php';
$data = json_decode(file_get_contents('php://input'), true);
$availability_id = intval($data['availability_id'] ?? 0);

$stmt = $pdo->prepare("UPDATE tbl_availability SET is_available='Yes', updated_by=NULL WHERE availability_id=?");
$stmt->execute([$availability_id]);

echo json_encode(['success'=>true,'message'=>'Booking removed, slot is now available']);
