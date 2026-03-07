<?php
require_once 'db.php';
$msg = $err = '';
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $name = trim($_POST['name']??''); 
  $email = trim($_POST['email']??'');
  $pass = $_POST['password']??'';
  $cpass = $_POST['cpassword']??''; 
  if($pass !== $cpass) $err = 'Passwords do not match.';
  $phone = trim($_POST['phone']??'');
  if (!$name || !$email || !$pass || !$cpass || !$phone ) $err='All fields required.';
  else {
    $stmt = $pdo->prepare('INSERT INTO tbl_drivers (name,email,password,created_at) VALUES (?,?,?,NOW())');
    try {
      $stmt->execute([$name,$email,password_hash($pass, PASSWORD_BCRYPT)]);
      $msg = 'Driver registered. You can now login.';
    } catch (Exception $e) { $err='Email already exists.'; }
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
    if (icon) {
      icon.classList.remove("bi-eye");
      icon.classList.add("bi-eye-slash");
    }
  } else {
    input.type = "password";
    if (icon) {
      icon.classList.remove("bi-eye-slash");
      icon.classList.add("bi-eye");
    }
  }
}

function checkPasswordMatch() {
  const pass = document.getElementById("password").value;
  const cpass = document.getElementById("cpassword").value;
  const msg = document.getElementById("passwordHelp");

  if (!cpass) {
    msg.textContent = "";
    return;
  }

  if (pass === cpass) {
    msg.textContent = "Passwords match ✔";
    msg.className = "password-match text-success";
  } else {
    msg.textContent = "Passwords do not match ✘";
    msg.className = "password-match text-danger";
  }
}
</script>
<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="card p-4">
      <h2 class="mb-3">Driver Signup</h2>
      <?php if($msg): ?><div class="alert alert-success"><?=$msg?></div><?php endif; ?>
      <?php if($err): ?><div class="alert alert-danger"><?=$err?></div><?php endif; ?>
      <form method="post">
        <div class="row g-3">
          <div class="col-md-12"><input class="form-control" name="name" placeholder="Full name" required></div>
          <div class="col-md-6"><input class="form-control" type="email" name="email" placeholder="Email" required></div>
          <div class="col-md-6"><input class="form-control" type="number" name="phone" placeholder="Phone number" required></div>
          <div class="col-md-6">
            <div class="input-group">
              <input class="form-control" type="password" id="password" name="password" placeholder="Password" required onkeyup="checkPasswordMatch()">
              <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password', this)">
                <i class="bi bi-eye"></i>
              </button>
            </div>
          </div>

          <!-- Confirm Password -->
          <div class="col-md-6">
            <div class="input-group">
              <input class="form-control" type="password" id="cpassword" name="cpassword" placeholder="Confirm Password" required onkeyup="checkPasswordMatch()">
              <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('cpassword', this)">
                <i class="bi bi-eye"></i>
              </button>
            </div>
            <div id="passwordHelp" class="password-match"></div>
          </div>
        </div>
        <button class="btn btn-brand text-white mt-3 w-100">Create account</button>
      </form>
    </div>
  </div>
</div>
<?php include 'partials/footer.php'; ?>
