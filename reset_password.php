<?php
$secret = 'MySuperSecretKey9871';
$email = $_GET['email'] ?? '';
$token = $_GET['token'] ?? '';

// Cek token
if (hash('sha256', $email . $secret) !== $token) {
    die("Token tidak valid!");
}

// Pesan sukses/gagal
$message = "";
$message_class = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($new_password) || empty($confirm_password)) {
        $message = "⚠️ Password tidak boleh kosong!";
        $message_class = "error";
    } elseif ($new_password !== $confirm_password) {
        $message = "⚠️ Konfirmasi password tidak cocok!";
        $message_class = "error";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $conn = new mysqli("localhost", "root", "", "apotek1");
        if ($conn->connect_error) {
            die("Koneksi gagal: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("UPDATE users SET password=? WHERE email=?");
        $stmt->bind_param("ss", $hashed_password, $email);

        if ($stmt->execute()) {
            $message = "✅ Password berhasil direset!";
            $message_class = "success";
        } else {
            $message = "❌ Gagal reset password: " . $conn->error;
            $message_class = "error";
        }

        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Reset Password</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: #f4f7fa;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }
    .reset-container {
        background: white;
        padding: 25px 30px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        width: 320px;
        text-align: center;
    }
    h2 {
        margin-bottom: 15px;
        color: #333;
    }
    label {
        display: block;
        margin-bottom: 8px;
        text-align: left;
        font-weight: bold;
        font-size: 14px;
        color: #555;
    }
    input[type="password"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 8px;
        outline: none;
        font-size: 14px;
        margin-bottom: 15px;
        transition: 0.3s;
    }
    input[type="password"]:focus {
        border-color: #4a90e2;
        box-shadow: 0 0 5px rgba(74,144,226,0.5);
    }
    button {
        width: 100%;
        padding: 10px;
        border: none;
        color: white;
        font-size: 14px;
        font-weight: bold;
        border-radius: 8px;
        cursor: pointer;
        transition: background 0.3s ease;
        margin-bottom: 10px;
    }
    button[type="submit"] {
        background: #4a90e2;
    }
    button[type="submit"]:hover {
        background: #357abd;
    }
    .login-btn {
        background: #28a745;
        text-decoration: none;
        display: inline-block;
        text-align: center;
    }
    .login-btn:hover {
        background: #1f8b39;
    }
    .message {
        margin-bottom: 10px;
        padding: 8px;
        border-radius: 6px;
        font-size: 13px;
    }
    .success {
        background: #d4edda;
        color: #155724;
    }
    .error {
        background: #f8d7da;
        color: #721c24;
    }
</style>
</head>
<body>

<div class="reset-container">
    <h2>🔑 Reset Password</h2>

    <?php if (!empty($message)): ?>
        <div class="message <?= $message_class; ?>"><?= $message; ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Password Baru:</label>
        <input type="password" name="password" placeholder="Masukkan password baru" required>

        <label>Konfirmasi Password:</label>
        <input type="password" name="confirm_password" placeholder="Ulangi password baru" required>

        <button type="submit">Reset Password</button>
    </form>

    <a href="login.php">
        <button type="button" class="login-btn">🔐 Kembali ke Login</button>
    </a>
</div>

</body>
</html>
