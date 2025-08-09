<?php
session_start();
$role = $_SESSION['role'] ?? null;
$username = $_SESSION['username'] ?? null;

include_once 'sidebar_kasir.php';

// Koneksi database
$koneksi = new mysqli("localhost", "root", "", "apotek1");
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Cek jika session kosong
if (!$username || $role !== 'kasir') {
    echo "<script>alert('Akses ditolak. Silakan login sebagai kasir.'); window.location.href = '../logout.php';</script>";
    exit;
}

// Ambil data user yang sedang login
$query = $koneksi->query("SELECT * FROM users WHERE username = '$username' AND role = 'kasir'");
$data = $query->fetch_assoc();

if (!$data) {
    echo "<script>alert('Data pengguna tidak ditemukan.'); window.location.href = '../logout.php';</script>";
    exit;
}

// Tangani form update profil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = $koneksi->real_escape_string($_POST['username']);
    $new_email = $koneksi->real_escape_string($_POST['email']);
    $id_user = $data['id'];

    // Validasi dasar
    if (empty($new_username) || empty($new_email)) {
        echo "<script>alert('Username dan Email tidak boleh kosong.');</script>";
    } else {
        $update = $koneksi->query("UPDATE users SET username='$new_username', email='$new_email' WHERE id='$id_user'");
        if ($update) {
            $_SESSION['username'] = $new_username; // Update session username
            echo "<script>alert('Profil berhasil diperbarui.'); window.location.href='profil.php';</script>";
            exit;
        } else {
            echo "<script>alert('Gagal memperbarui profil.');</script>";
        }
    }
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
      margin-left: 240px;
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
      align-items: center;
      font-size: 1rem;
    }

    .profil-item strong {
      width: 160px;
      color: #333;
    }

    .profil-item input {
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 6px;
      flex: 1;
    }

    .readonly {
      background-color: #f0f0f0;
      color: #666;
    }

    .simpan-button {
      text-align: center;
      margin-top: 2rem;
    }

    .simpan-button button {
      background-color: #1976d2;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 8px;
      font-size: 1rem;
      cursor: pointer;
    }

    .simpan-button button:hover {
      background-color: #125ea6;
    }

    .lupa-password {
      text-align: center;
      margin-top: 1.5rem;
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
    <h2 class="profil-title">Edit Profil Pengguna</h2>

    <div class="profil-image">
      <img src="../uploads/<?= htmlspecialchars($data['gambar']) ?>" alt="Foto Profil">
    </div>

    <form method="POST">
      <div class="profil-item"><strong>ID:</strong> <input type="text" class="readonly" value="<?= $data['id'] ?>" readonly></div>

      <div class="profil-item">
        <strong>Username:</strong>
        <input type="text" name="username" value="<?= htmlspecialchars($data['username']) ?>" required>
      </div>

      <div class="profil-item">
        <strong>Password:</strong>
        <input type="text" class="readonly" value="********" readonly>
      </div>

      <div class="profil-item">
        <strong>Role:</strong>
        <input type="text" class="readonly" value="<?= htmlspecialchars($data['role']) ?>" readonly>
      </div>

      <div class="profil-item">
        <strong>Email:</strong>
        <input type="email" name="email" value="<?= htmlspecialchars($data['email']) ?>" required>
      </div>

      <div class="profil-item">
        <strong>Status Login:</strong>
        <input type="text" class="readonly" value="<?= $data['status_login'] == 1 ? 'Online' : 'Offline' ?>" readonly>
      </div>

      <div class="simpan-button">
        <button type="submit">Simpan Perubahan</button>
      </div>
    </form>

    <div class="lupa-password">
      <a href="lupa_password.php?user=<?= urlencode($data['username']) ?>">Lupa Password?</a>
    </div>
  </div>
</div>

</body>
</html>
