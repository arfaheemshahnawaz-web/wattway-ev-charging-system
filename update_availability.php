<?php
require_once 'auth.php';
require_login('operator');
require_once 'db.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$station_id = intval($data['station_id'] ?? 0);
$is_available = in_array($data['is_available'] ?? '', ['Yes','No']) ? $data['is_available'] : null;
$operator_id = $_SESSION['user']['id'] ?? 0;

if(!$station_id || !$is_available){
    echo json_encode(['success'=>false, 'error'=>'Invalid station_id or is_available']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT availability_id FROM tbl_availability WHERE station_id=?");
    $stmt->execute([$station_id]);
    $exists = $stmt->fetchColumn();

    if($exists){
        $stmt = $pdo->prepare("UPDATE tbl_availability SET is_available=?, updated_by=?, updated_at=NOW() WHERE station_id=?");
        $stmt->execute([$is_available, $operator_id, $station_id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO tbl_availability (station_id, is_available, updated_by, updated_at) VALUES (?,?,?,NOW())");
        $stmt->execute([$station_id, $is_available, $operator_id]);
    }

    echo json_encode(['success'=>true]);
} catch (PDOException $e) {
    echo json_encode(['success'=>false, 'error'=>$e->getMessage()]);
}
