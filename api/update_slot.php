<?php
require_once '../db.php';
header('Content-Type: application/json');

$availability_id = intval($_POST['availability_id'] ?? 0);
$slot_time = $_POST['slot_time'] ?? '';
$visit_date = $_POST['visit_date'] ?? '';

if(!$availability_id || !$slot_time || !$visit_date){
    echo json_encode(['success'=>false,'message'=>'Invalid data']);
    exit;
}

$stmt = $pdo->prepare("UPDATE tbl_availability SET slot_time=?, visit_date=? WHERE availability_id=?");
$stmt->execute([$slot_time, $visit_date, $availability_id]);

echo json_encode(['success'=>true,'message'=>'Slot updated successfully']);
