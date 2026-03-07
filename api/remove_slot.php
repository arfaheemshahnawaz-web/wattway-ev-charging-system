<?php
require_once '../db.php';
require_once '../auth.php';
require_login('operator');

header('Content-Type: application/json');

$availability_id = $_POST['availability_id'] ?? null;
if (!$availability_id) {
    echo json_encode(['success' => false, 'message' => 'Slot ID required']);
    exit;
}

try {
    // Start transaction to keep data consistent
    $pdo->beginTransaction();

    // Get booking_id linked to this availability
    $stmt1 = $pdo->prepare("
        SELECT booking_id 
        FROM tbl_availability 
        WHERE availability_id = ?
        LIMIT 1
    ");
    $stmt1->execute([$availability_id]);
    $slot = $stmt1->fetch(PDO::FETCH_ASSOC);

    if ($slot && !empty($slot['booking_id'])) {
        // Delete the related booking
        $stmt2 = $pdo->prepare("DELETE FROM tbl_bookings WHERE booking_id = ?");
        $stmt2->execute([$slot['booking_id']]);
    }

    // Delete the slot itself
    $stmt3 = $pdo->prepare("DELETE FROM tbl_availability WHERE availability_id = ?");
    $stmt3->execute([$availability_id]);

    // Commit transaction
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Slot and related booking deleted successfully'
    ]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
