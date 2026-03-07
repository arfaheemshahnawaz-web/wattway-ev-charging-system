<?php
require_once '../db.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$booking_id = intval($_GET['booking_id'] ?? 0);
if (!$booking_id) die("Invalid booking ID");

// Fetch booking info + station pricing + driver email/phone
$stmt = $pdo->prepare("
    SELECT b.*, s.station_name, s.pricing, d.email, d.phone
    FROM tbl_bookings b
    LEFT JOIN tbl_stations s ON b.station_id = s.station_id
    LEFT JOIN tbl_drivers d ON b.user_id = d.driver_id
    WHERE b.booking_id = ?
");
$stmt->execute([$booking_id]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) die("Booking not found");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Payment - EV Charging</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="card shadow p-4 mx-auto" style="max-width:500px;">
    <h4 class="text-center mb-3">Payment for Booking #<?php echo $booking_id; ?></h4>
    <p><strong>Station:</strong> <?php echo htmlspecialchars($booking['station_name']); ?></p>
    <p><strong>Amount:</strong> ₹<?php echo htmlspecialchars($booking['pricing']); ?></p>

    <h6 class="mt-4">Select Payment Method:</h6>
    <form id="paymentForm">
      <div class="form-check">
        <input class="form-check-input" type="radio" name="method" value="Cash" required>
        <label class="form-check-label">Cash</label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="radio" name="method" value="UPI" required>
        <label class="form-check-label">UPI (PhonePe / GPay)</label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="radio" name="method" value="Card" required>
        <label class="form-check-label">Card (Credit/Debit)</label>
      </div>
      <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">
      <button type="submit" class="btn btn-primary mt-3 w-100">Pay Now</button>
    </form>

    <div id="statusMsg" class="mt-3 text-center fw-bold"></div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$('#paymentForm').on('submit', function(e) {
    e.preventDefault();
    $('#statusMsg').html('Processing payment...');
    
    $.ajax({
        url: 'update_payment.php',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(data) {
            if(data.success){
                $('#statusMsg').html('<span class="text-success">'+data.message+'</span>');
            } else {
                $('#statusMsg').html('<span class="text-danger">'+data.message+'</span>');
            }
        },
        error: function(xhr, status, err){
            console.error(xhr.responseText);
            $('#statusMsg').html('<span class="text-danger">Server error: '+err+'</span>');
        }
    });
});
</script>
</body>
</html>
