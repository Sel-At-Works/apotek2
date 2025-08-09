<?php
session_start();
$role = $_SESSION['role'] ?? 'kasir';
include_once 'sidebar_admin.php';

// ✅ Koneksi database
$conn = new mysqli("localhost", "root", "", "apotek1");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
