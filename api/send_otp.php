<?php
function send_demo_otp($email, $phone, $otp) {
    $subject = "EV Charging OTP";
    $message = "Your OTP for charging session verification is: $otp";
    @mail($email, $subject, $message);

    // Simulate SMS (store in a log)
    file_put_contents(__DIR__ . '/otp_log.txt', date('Y-m-d H:i:s') . " - Sent OTP $otp to $phone\n", FILE_APPEND);
}
?>
