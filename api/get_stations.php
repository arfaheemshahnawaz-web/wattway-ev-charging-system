<?php
require_once '../db.php'; // Use PDO connection

$search = trim($_GET['q'] ?? '');
$plug = trim($_GET['plug'] ?? '');
$watt = intval($_GET['watt'] ?? 0);
$lat = floatval($_GET['lat'] ?? 0);
$lng = floatval($_GET['lng'] ?? 0);
$radius = 15; // km

$params = [];
$sql = "SELECT s.station_id, s.name, s.address, s.lat, s.lng, s.plug_type, s.watt_kw, s.approved, 
        a.available_slots,
        (6371 * acos(cos(radians(:lat)) * cos(radians(s.lat)) * cos(radians(s.lng) - radians(:lng)) + sin(radians(:lat)) * sin(radians(s.lat)))) AS distance
        FROM tbl_stations s
        LEFT JOIN tbl_availability a ON s.station_id = a.station_id
        WHERE s.approved = 1";

// Apply filters
if ($search) {
    $sql .= " AND (s.name LIKE :search OR s.address LIKE :search)";
    $params[':search'] = "%$search%";
}
if ($plug) {
    $sql .= " AND s.plug_type = :plug";
    $params[':plug'] = $plug;
}
if ($watt) {
    $sql .= " AND s.watt_kw >= :watt";
    $params[':watt'] = $watt;
}

// Haversine distance filter
if ($lat && $lng) {
    $params[':lat'] = $lat;
    $params[':lng'] = $lng;
    $sql .= " HAVING distance <= $radius";
}

$sql .= " ORDER BY distance ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
