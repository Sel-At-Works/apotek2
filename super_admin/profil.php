<?php
session_start();
$role = $_SESSION['role'] ?? 'kasir';
$username = $_SESSION['username'] ?? null;

include_once 'sidebar_admin.php';

// Koneksi database
$conn = new mysqli("localhost", "root", "", "apotek1");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$query = $conn->query("SELECT * FROM users WHERE username = '$username'");
$data = $query->fetch_assoc();

if ($data['role'] !== 'superadmin') {
    echo "<script>alert('Halaman ini hanya untuk Superadmin.'); window.location.href = '../logout.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Profil User</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      background: #f4f6f8;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    .container {
      margin-left: 240px; /* untuk sidebar */
      padding: 3rem;
      flex-grow: 1;
    }

    .profil-card {
      background: #ffffff;
      border-radius: 12px;
      padding: 2rem;
      max-width: 800px;
      margin: auto;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    }

    .profil-title {
      text-align: center;
      font-size: 1.8rem;
      color: #1976d2;
      margin-bottom: 1.5rem;
    }

    .profil-image {
      display: flex;
      justify-content: center;
      margin-bottom: 1.5rem;
    }

    .profil-image img {
      width: 130px;
      height: 130px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #1976d2;
    }

    .profil-item {
      margin: 12px 0;
      display: flex;
      font-size: 1rem;
    }

    .profil-item strong {
      width: 160px;
      color: #333;
    }

    .lupa-password {
      text-align: center;
      margin-top: 2rem;
    }

    .lupa-password a {
      text-decoration: none;
      background-color: #f44336;
      color: white;
      padding: 10px 20px;
      border-radius: 8px;
      transition: 0.3s;
    }

    .lupa-password a:hover {
      background-color: #d32f2f;
    }
  </style>
</head>
<body>

<div class="container">
  <div class="profil-card">
    <h2 class="profil-title">Data Pengguna (Sedang Login)</h2>

    <div class="profil-image">
      <img src="../uploads/<?= htmlspecialchars($data['gambar']) ?>" alt="Foto Profil">
    </div>

    <div class="profil-item"><strong>ID:</strong> <?= $data['id'] ?></div>
    <div class="profil-item"><strong>Username:</strong> <?= htmlspecialchars($data['username']) ?></div>
    <div class="profil-item"><strong>Password:</strong> ********</div>
    <div class="profil-item"><strong>Role:</strong> <?= htmlspecialchars($data['role']) ?></div>
    <div class="profil-item"><strong>Email:</strong> <?= htmlspecialchars($data['email']) ?></div>
    <div class="profil-item"><strong>Status Login:</strong> <?= $data['status_login'] == 1 ? 'Online' : 'Offline' ?></div>

    <div class="lupa-password">
      <a href="lupa_password.php?user=<?= urlencode($data['username']) ?>">Lupa Password?</a>
    </div>
  </div>
</div>

</body>
</html>
