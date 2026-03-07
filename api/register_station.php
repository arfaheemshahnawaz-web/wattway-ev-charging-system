<?php
require_once '../auth.php'; require_login('operator'); require_once '../db.php';
$data = $_POST;
$stmt = $pdo->prepare('INSERT INTO tbl_stations (name,address,lat,lng,plug_type,watt_kw,approved,added_by,created_at) VALUES (?,?,?,?,?,?,0,?,NOW())');
try {
  $stmt->execute([$data['name'],$data['address'],$data['lat'],$data['lng'],$data['plug_type'],$data['watt_kw'], $_SESSION['user']['id']]);
  echo json_encode(['ok'=>true,'msg'=>'Station submitted. Awaiting admin approval.']);
} catch (Exception $e) {
  http_response_code(400);
  echo json_encode(['ok'=>false,'msg'=>'Failed: '.$e->getMessage()]);
}
