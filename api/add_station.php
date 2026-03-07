<?php
require_once '../auth.php';
require_login('operator');
require_once '../db.php';

$operator_id = $_SESSION['user']['id'] ?? 0;

$data = json_decode(file_get_contents('php://input'), true);

$station_name = trim($data['station_name'] ?? '');
$address = trim($data['address'] ?? '');
$plug_type = trim($data['plug_type'] ?? '');
$charging_speed = trim($data['charging_speed'] ?? '');
$pricing = floatval($data['pricing'] ?? 0);
$latitude = floatval($data['latitude'] ?? 0);
$longitude = floatval($data['longitude'] ?? 0);

// Simple validation
if(!$station_name || !$address || !$plug_type || !$charging_speed || !$pricing || !$latitude || !$longitude){
    echo json_encode(['success'=>false,'message'=>'All fields are required.']);
    exit;
}

// Default status is 'pending'
$stmt = $pdo->prepare("INSERT INTO tbl_stations (station_name,address,plug_type,charging_speed,pricing,latitude,longitude,added_by,status) VALUES (?,?,?,?,?,?,?,?,?)");
$success = $stmt->execute([$station_name,$address,$plug_type,$charging_speed,$pricing,$latitude,$longitude,$operator_id,'pending']);

if($success){
    echo json_encode(['success'=>true,'message'=>'Station added successfully! Awaiting admin approval.']);
}else{
    echo json_encode(['success'=>false,'message'=>'Failed to add station.']);
}
