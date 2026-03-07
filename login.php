<?php
require_once 'db.php';
if (session_status() === PHP_SESSION_NONE) session_start();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $identifier = trim($_POST['email'] ?? ''); // Use 'email' input value as generic identifier
  $pass     = $_POST['password'] ?? '';
  $role     = $_POST['role'] ?? '';

  if (!$identifier || !$pass || !$role) {
    $error = 'All fields are required.';
  } else {
    // 1. Determine table
    $table = match ($role) {
      'driver' => 'tbl_drivers',
      'operator' => 'tbl_station_operators',
      'admin' => 'tbl_admin',
      default => null,
    };

    if (!$table) {
        $error = 'Invalid role selected.';
    } else {
        // 2. Determine ID column and Identifier column
        $idcol = match ($role) {
          'driver' => 'driver_id',
          'operator' => 'operator_id',
          'admin' => 'admin_id',
        };
        
        // Use 'email' column for driver/operator, and assume 'username' for admin
        $identifier_col = $role === 'admin' ? 'username' : 'email';

        // 3. Prepare and execute query
        // The column name is now a variable, so we must build the query string carefully
        $stmt = $pdo->prepare("SELECT * FROM $table WHERE $identifier_col = ? LIMIT 1");
        $stmt->execute([$identifier]);
        $user = $stmt->fetch();

        if ($user && password_verify($pass, $user['password'])) {
          // Base session data
          $_SESSION['user'] = [
            'id'    => $user[$idcol],
            'name'  => $user['name'] ?? $user[$identifier_col], // Admin might not have a 'name' field, use identifier instead
            'role'  => $role,
            'email' => $user['email'] ?? $user[$identifier_col] // Store email or username in 'email' session key
          ];

          // Extra info for operators
          if ($role === 'operator' && isset($user['contact_number'])) {
            $_SESSION['user']['phone'] = $user['contact_number'];
          }

          // Redirect based on role
          header('Location: ' . (
            $role === 'driver'   ? 'driver.php'   : 
            ($role === 'operator'? 'station.php'  : 'admin.php')
          ));
          exit;
        } else {
          $error = 'Invalid credentials.';
        }
    }
  }
}
?>
<?php include 'partials/header.php'; ?>
<script>
function togglePassword(id, btn) {
  const input = document.getElementById(id);
  const icon = btn.querySelector("i");

  if (input.type === "password") {
    input.type = "text";
    if (icon) { icon.classList.replace("bi-eye", "bi-eye-slash"); }
  } else {
    input.type = "password";
    if (icon) { icon.classList.replace("bi-eye-slash", "bi-eye"); }
  }
}

// Update input type based on role selection
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.querySelector('select[name="role"]');
    const emailInput = document.querySelector('input[name="email"]');
    const emailLabel = document.querySelector('.mb-3 label[for="email"]'); // Assuming the label has a 'for' or is the immediate sibling

    roleSelect.addEventListener('change', function() {
        if (roleSelect.value === 'admin') {
            emailInput.type = 'text';
            emailInput.placeholder = 'Username';
            emailLabel.textContent = 'Username';
        } else {
            emailInput.type = 'email';
            emailInput.placeholder = 'Email';
            emailLabel.textContent = 'Email';
        }
    });
});
</script>

<div class="row justify-content-center">
  <div class="col-lg-5">
    <div class="card p-4 shadow-sm">
      <h2 class="mb-3">Login</h2>
      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <form method="post">
        <div class="mb-3">
          <label class="form-label">Role</label>
          <select class="form-select" name="role" required>
            <option value="">Select role</option>
            <option value="driver">Driver</option>
            <option value="operator">Station Operator</option>
            <option value="admin">Admin</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label" id="identifier-label">Email</label> 
          <input type="email" class="form-control" name="email" id="email-input" placeholder="Email" required> 
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <div class="input-group">
            <input class="form-control" type="password" id="password" name="password" placeholder="Password" required>
            <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password', this)">
              <i class="bi bi-eye"></i>
            </button>
          </div>
        </div>
        <button class="btn btn-brand text-white w-100">Login</button>
      </form>
      <div class="text-muted small mt-3">
        No account? 
        <a href="register_driver.php">Driver signup</a> / 
        <a href="register_station.php">Operator signup</a>
      </div>
    </div>
  </div>
</div>
<?php include 'partials/footer.php'; ?>