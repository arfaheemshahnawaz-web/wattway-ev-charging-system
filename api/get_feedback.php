<?php
require_once '../db.php';
header('Content-Type: application/json');

$station_id = $_GET['station_id'] ?? 0;

$stmt = $pdo->prepare("
    SELECT f.feedback_text, f.rating, f.created_at, d.name AS user_name
    FROM tbl_feedback f
    LEFT JOIN tbl_drivers d ON f.driver_id = d.driver_id
    WHERE f.station_id = ?
    ORDER BY f.created_at DESC
");
$stmt->execute([$station_id]);
$feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($feedbacks);
