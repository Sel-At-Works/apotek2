<?php
session_start();
include 'config.php';

$username = $_SESSION['username'] ?? null;
if ($username) {
    // ✅ Set status_login ke 0 (logout)
    mysqli_query($koneksi, "UPDATE users SET status_login = 0 WHERE username = '$username'");
}

session_destroy();
header("Location: login.php");
exit;
