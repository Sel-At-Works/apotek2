<?php
session_start();
include 'config.php'; // ganti sesuai file koneksi database-mu

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    // Cek data user di database
    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username' LIMIT 1");
    $data = mysqli_fetch_assoc($query);

    if ($data && password_verify($password, $data['password'])) {
        // Simpan ke session
         $_SESSION['id_user'] = $data['id']; 
        $_SESSION['login'] = true;
        $_SESSION['username'] = $data['username'];
        $_SESSION['role'] = $data['role'];
        $_SESSION['email'] = $data['email'];

        // Set status_login ke 1 (aktif)
mysqli_query($koneksi, "UPDATE users SET status_login = 1 WHERE id = " . $data['id']);


        // Arahkan sesuai role
        if ($data['role'] === 'superadmin') {
            header("Location: super_admin/dashboard.php");
        } elseif ($data['role'] === 'kasir') {
            header("Location: tampilan_kasir/dashboard.php");
        } else {
            $_SESSION['error'] = "Role tidak dikenali.";
            header("Location: login.php");
        }
        exit();
    } else {
        $_SESSION['error'] = "Username atau password salah!";
        header("Location: login.php");
        exit();
    }
} else {
    $_SESSION['error'] = "Akses tidak sah!";
    header("Location: login.php");
    exit();
}
