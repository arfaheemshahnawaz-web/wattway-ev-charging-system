<?php
require_once '../db.php';

$slotId = $_POST['availability_id'] ?? 0;

if (!$slotId) {
    echo json_encode(['success' => false, 'message' => 'Slot ID required']);
    exit;
}

$stmt = $pdo->prepare("DELETE FROM tbl_availability WHERE availability_id=?");
if ($stmt->execute([$slotId])) {
    echo json_encode(['success' => true, 'message' => 'Slot deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete slot']);
}
?>
