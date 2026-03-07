<?php
require_once '../db.php';
header('Content-Type: application/json');

$station_id = intval($_POST['station_id'] ?? 0);
$slot_time = $_POST['slot_time'] ?? '';
$visit_date = $_POST['visit_date'] ?? '';

if(!$station_id || !$slot_time || !$visit_date){
    echo json_encode(['success'=>false,'message'=>'Invalid data']);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO tbl_availability (station_id, slot_time, visit_date, is_available) VALUES (?,?,?, 'Yes')");
$stmt->execute([$station_id, $slot_time, $visit_date]);

echo json_encode(['success'=>true,'message'=>'Slot added successfully']);
