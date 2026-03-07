<?php
require_once 'auth.php';
require_login('admin');
require_once 'db.php';

// --- 1. HANDLE APPROVAL/REJECTION ACTION ---
$action_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['station_id'])) {
    $station_id = $_POST['station_id'];
    $new_status = $_POST['action'] === 'approve' ? 'active' : 'rejected';
    
    $stmt = $pdo->prepare("UPDATE tbl_stations SET status = ? WHERE station_id = ?");
    if ($stmt->execute([$new_status, $station_id])) {
        $action_msg = "✅ Station #{$station_id} status updated to " . strtoupper($new_status) . ".";
    } else {
        $action_msg = "❌ Error updating station #{$station_id} status.";
    }
}

// --- 2. DATA FETCHING ---

// a. Pending Stations
$pending_stmt = $pdo->prepare("
    SELECT s.*, o.name AS operator_name 
    FROM tbl_stations s
    JOIN tbl_station_operators o ON s.added_by = o.operator_id
    WHERE s.status = 'pending'
    ORDER BY s.created_at ASC
");
$pending_stmt->execute();
$pending_stations = $pending_stmt->fetchAll(PDO::FETCH_ASSOC);

// b. All Stations
$all_stmt = $pdo->prepare("
    SELECT station_name, plug_type, charging_speed, status 
    FROM tbl_stations 
    ORDER BY station_name ASC
");
$all_stmt->execute();
$all_stations = $all_stmt->fetchAll(PDO::FETCH_ASSOC);

// c. Drivers
$drivers_stmt = $pdo->prepare("
    SELECT name, email, created_at 
    FROM tbl_drivers 
    ORDER BY created_at DESC
");
$drivers_stmt->execute();
$drivers = $drivers_stmt->fetchAll(PDO::FETCH_ASSOC);

// d. Operators
$operators_stmt = $pdo->prepare("
    SELECT name, email, contact_number
    FROM tbl_station_operators 
    ORDER BY operator_id DESC
");
$operators_stmt->execute();
$operators = $operators_stmt->fetchAll(PDO::FETCH_ASSOC);

// Helper for status badges
function get_status_badge($status) {
    $status_data = match ($status) {
        'active' => ['class' => 'badge bg-success', 'color' => '#FFF'],
        'inactive' => ['class' => 'badge bg-secondary', 'color' => '#FFF'],
        'rejected' => ['class' => 'badge bg-danger', 'color' => '#FFF'],
        default => ['class' => 'badge bg-warning', 'color' => '#000'],
    };
    $text = htmlspecialchars(ucfirst($status));
    return "<span class=\"{$status_data['class']}\" style=\"color: {$status_data['color']} !important;\">{$text}</span>";
}
?>

<?php include 'partials/header.php'; ?>

<!-- Hero Section -->
<section class="hero-section admin position-relative">
  <div class="hero-overlay d-flex align-items-center justify-content-center">
    <div class="container text-center text-white">
      <h1 class="hero-title fw-bold">⚡ Admin Dashboard</h1>
      <p class="lead">Manage Stations, Drivers & Operators with ease</p>
    </div>
  </div>
  <div class="floating-circles">
    <span></span><span></span><span></span><span></span><span></span>
  </div>
</section>

<div class="container my-5">
  <?php if ($action_msg): ?>
    <div class="alert alert-info text-center"><?= $action_msg ?></div>
  <?php endif; ?>

  <div class="row g-4">
    <!-- Pending Stations -->
    <div class="col-lg-6">
      <div class="card glass p-3">
        <h5 class="card-title text-warning mb-3">⏳ Pending Stations (<?= count($pending_stations) ?>)</h5>
        <div class="table-responsive glass-table">
          <?php if (empty($pending_stations)): ?>
            <p class="text-muted text-center py-3">🎉 No new stations are pending review.</p>
          <?php else: ?>
            <table class="table table-hover table-sm align-middle">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Plug / Speed</th>
                  <th>Added By</th>
                  <th class="text-center">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($pending_stations as $station): ?>
                  <tr>
                    <td>
                      <strong><?= htmlspecialchars($station['station_name']) ?></strong>
                      <div class="small text-muted"><?= htmlspecialchars($station['address']) ?></div>
                    </td>
                    <td><?= htmlspecialchars($station['plug_type']) ?> / <?= htmlspecialchars($station['charging_speed']) ?></td>
                    <td><?= htmlspecialchars($station['operator_name']) ?></td>
                    <td class="text-center">
                      <form method="POST" class="d-inline-flex gap-2">
                        <input type="hidden" name="station_id" value="<?= $station['station_id'] ?>">
                        <button type="submit" name="action" value="approve" class="btn-admin btn-sm" title="Approve station">
                          <i class="bi bi-check-lg"></i>
                        </button>
                        <button type="submit" name="action" value="reject" class="btn-admin btn-sm bg-danger" title="Reject station">
                          <i class="bi bi-x-lg"></i>
                        </button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- All Stations -->
    <div class="col-lg-6">
      <div class="card glass p-3">
        <h5 class="card-title mb-3">🌎 All Stations (Overview)</h5>
        <div class="table-responsive glass-table" style="max-height: 400px;">
          <?php if (empty($all_stations)): ?>
            <p class="text-muted text-center py-3">No stations have been added yet.</p>
          <?php else: ?>
            <table class="table table-striped table-sm align-middle">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Type</th>
                  <th>Speed</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($all_stations as $station): ?>
                  <tr>
                    <td><?= htmlspecialchars($station['station_name']) ?></td>
                    <td><?= htmlspecialchars($station['plug_type']) ?></td>
                    <td><?= htmlspecialchars($station['charging_speed']) ?></td>
                    <td><?= get_status_badge($station['status']) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <hr class="my-5">

  <div class="row g-4">
    <!-- Drivers -->
    <div class="col-lg-6">
      <div class="card glass p-3">
        <h5 class="card-title mb-3">🚗 Drivers (<?= count($drivers) ?> Registered)</h5>
        <div class="table-responsive glass-table" style="max-height: 400px;">
          <?php if (empty($drivers)): ?>
            <p class="text-muted text-center py-3">No drivers registered yet.</p>
          <?php else: ?>
            <table class="table table-striped table-sm align-middle">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Joined</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($drivers as $driver): ?>
                  <tr>
                    <td><?= htmlspecialchars($driver['name']) ?></td>
                    <td><?= htmlspecialchars($driver['email']) ?></td>
                    <td><?= date('Y-m-d', strtotime($driver['created_at'])) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Station Operators -->
    <div class="col-lg-6">
      <div class="card glass p-3">
        <h5 class="card-title mb-3">👨‍💼 Station Operators (<?= count($operators) ?> Registered)</h5>
        <div class="table-responsive glass-table" style="max-height: 400px;">
          <?php if (empty($operators)): ?>
            <p class="text-muted text-center py-3">No station operators registered yet.</p>
          <?php else: ?>
            <table class="table table-striped table-sm align-middle">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Contact</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($operators as $operator): ?>
                  <tr>
                    <td><?= htmlspecialchars($operator['name']) ?></td>
                    <td><?= htmlspecialchars($operator['email']) ?></td>
                    <td><?= htmlspecialchars($operator['contact_number']) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.btn-admin.bg-danger').forEach(button => {
    button.addEventListener('click', e => {
      if (!confirm('Are you sure you want to REJECT this station?')) {
        e.preventDefault();
      }
    });
  });
});
</script>

<?php include 'partials/footer.php'; ?>
