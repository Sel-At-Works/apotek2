<?php
session_start();
include 'config.php'; // koneksi database

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    // Ambil data user
    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username' LIMIT 1");
    $data = mysqli_fetch_assoc($query);

    if ($data) {
        // ✅ Tambahan pengecekan status login
        if ($data['status_login'] == 1) {
            $_SESSION['error'] = "Akun ini sedang digunakan di perangkat lain!";
            header("Location: login.php");
            exit();
        }

        // Cek password
        if (password_verify($password, $data['password'])) {
            // Simpan session
            $_SESSION['id_user'] = $data['id'];
            $_SESSION['login'] = true;
            $_SESSION['username'] = $data['username'];
            $_SESSION['role'] = $data['role'];
            $_SESSION['email'] = $data['email'];

            // Update status login
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
            $_SESSION['error'] = "Password salah!";
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Username tidak ditemukan!";
        header("Location: login.php");
        exit();
    }
} else {
    $_SESSION['error'] = "Akses tidak sah!";
    header("Location: login.php");
    exit();
}
