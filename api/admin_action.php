<?php
require_once '../auth.php'; require_login('admin'); require_once '../db.php';
$action = $_POST['action'] ?? '';
$id = intval($_POST['id'] ?? 0);
if (!$id) { http_response_code(400); echo json_encode(['ok'=>false,'msg'=>'Invalid ID']); exit; }
if ($action==='approve') {
  $stmt = $pdo->prepare('UPDATE tbl_stations SET approved = 1 WHERE station_id = ?');
  $stmt->execute([$id]);
  echo json_encode(['ok'=>true]);
} elseif ($action==='reject') {
  $stmt = $pdo->prepare('DELETE FROM tbl_stations WHERE station_id = ?');
  $stmt->execute([$id]);
  echo json_encode(['ok'=>true]);
} else {
  http_response_code(400); echo json_encode(['ok'=>false,'msg'=>'Invalid action']);
}
