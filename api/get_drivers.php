<?php
require_once '../db.php';
require_once '../auth.php';
require_login('operator');

// Fetch all drivers (for assignment to any slot)
$stmt = $pdo->query("SELECT driver_id, name FROM tbl_drivers ORDER BY name");
$drivers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Add empty OTP field for consistency with front-end
foreach ($drivers as &$driver) {
    $driver['otp'] = '';
}

// Return JSON
header('Content-Type: application/json');
echo json_encode($drivers);
exit;
