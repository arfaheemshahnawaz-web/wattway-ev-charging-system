<?php
require_once '../auth.php'; require_login('admin'); require_once '../db.php';
$type = $_GET['type'] ?? '';
if ($type==='pending') {
  $stmt = $pdo->query('SELECT * FROM tbl_stations WHERE approved = 0 ORDER BY created_at DESC');
  echo json_encode($stmt->fetchAll());
} elseif ($type==='allstations') {
  $stmt = $pdo->query('SELECT * FROM tbl_stations ORDER BY created_at DESC');
  echo json_encode($stmt->fetchAll());
} elseif ($type==='drivers') {
  $stmt = $pdo->query('SELECT driver_id as id, name, email, created_at FROM tbl_drivers ORDER BY created_at DESC');
  echo json_encode($stmt->fetchAll());
} elseif ($type==='operators') {
  $stmt = $pdo->query('SELECT operator_id as id, name, email, created_at FROM tbl_station_operators ORDER BY created_at DESC');
  echo json_encode($stmt->fetchAll());
} else {
  echo json_encode([]);
}
