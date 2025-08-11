<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $secret = 'MySuperSecretKey9871'; // ganti dengan key kamu sendiri
    $token = hash('sha256', $email . $secret);

 $baseUrl = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
$resetLink = $baseUrl . "/reset_password.php?email=" . urlencode($email) . "&token=" . $token;


    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'c6e92c53cc1737';
        $mail->Password   = '7afcf011e93e89';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 2525;

        $mail->setFrom('noreply@apotek1.com', 'Apotek 1');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Reset Password';
        $mail->Body    = "<p>Klik link berikut untuk reset password:</p>
                          <a href='$resetLink'>$resetLink</a>";

        $mail->send();
        echo "✅ Link reset dikirim ke email (cek Mailtrap)";
    } catch (Exception $e) {
        echo "❌ Error: {$mail->ErrorInfo}";
    }
}
?>
