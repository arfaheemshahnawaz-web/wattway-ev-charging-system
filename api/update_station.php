<?php
require_once '../auth.php';
require_login('operator');
require_once '../db.php';

$operator_id = $_SESSION['user']['id'] ?? 0;
$data = json_decode(file_get_contents('php://input'), true);

$station_id = intval($data['station_id'] ?? 0);
if(!$station_id){
    echo json_encode(['success'=>false,'message'=>'Station ID is required.']);
    exit;
}

// Ensure operator owns the station
$stmt = $pdo->prepare("SELECT * FROM tbl_stations WHERE station_id=?");
$stmt->execute([$station_id]);
$station = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$station || $station['added_by'] != $operator_id){
    echo json_encode(['success'=>false,'message'=>'Unauthorized']);
    exit;
}

// Determine updated values (keep old if not provided)
$plug_type = isset($data['plug_type']) && $data['plug_type'] !== '' ? $data['plug_type'] : $station['plug_type'];
$charging_speed = isset($data['charging_speed']) && $data['charging_speed'] !== '' ? $data['charging_speed'] : $station['charging_speed'];
$pricing = isset($data['pricing']) && $data['pricing'] !== '' ? floatval($data['pricing']) : $station['pricing'];

// Update the station
$stmt = $pdo->prepare("UPDATE tbl_stations SET plug_type=?, charging_speed=?, pricing=? WHERE station_id=?");
$success = $stmt->execute([$plug_type, $charging_speed, $pricing, $station_id]);

if($success){
    echo json_encode(['success'=>true,'message'=>'Station updated successfully!']);
}else{
    echo json_encode(['success'=>false,'message'=>'Failed to update station.']);
}
