<?php
require_once '../db.php';
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $station_id = $data['station_id'] ?? null;
    $feedback = trim($data['feedback'] ?? '');
    $rating = $data['rating'] ?? null;  // new rating field
    $driver_id = $_SESSION['user']['id'] ?? null;  // driver ID from session

    if (!$station_id || !$feedback || !$rating) {
        echo json_encode(['success' => false, 'message' => 'Missing station ID, feedback, or rating.']);
        exit;
    }

    $stmt = $pdo->prepare(
        "INSERT INTO tbl_feedback (station_id, driver_id, feedback_text, rating) VALUES (?, ?, ?, ?)"
    );
    $stmt->execute([$station_id, $driver_id, $feedback, $rating]);

    echo json_encode(['success' => true, 'message' => 'Feedback saved successfully.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: '.$e->getMessage()]);
}
