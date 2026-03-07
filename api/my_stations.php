<?php
require_once '../auth.php'; require_login('operator'); require_once '../db.php';
$stmt = $pdo->prepare('SELECT * FROM tbl_stations WHERE added_by = ? ORDER BY created_at DESC');
$stmt->execute([$_SESSION['user']['id']]);
echo json_encode($stmt->fetchAll());
