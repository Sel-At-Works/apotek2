<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = $_SESSION['role'] ?? null;
include '../config.php';

$username = $_SESSION['username'] ?? null;

$data_user = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username'"));
$foto_user = $data_user['gambar'] ?? 'default.jpg';


// Ambil total dari database
$totalMember = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM members"))['total'];
$totalKategori = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM kategori"))['total'];
$totalProduk = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM produk"))['total'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Dashboard Apotek</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f1f3f5;
            color: #333;
        }

        /* Sidebar */
        .sidebar {
            width: 240px;
            background-color: #1e88e5;
            height: 100vh;
            position: fixed;
            padding: 30px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .sidebar h2 {
            color: #fff;
            font-size: 22px;
            text-align: center;
            width: 100%;
            margin-bottom: 30px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
            width: 100%;
        }

        .sidebar ul li {
            width: 100%;
        }

        .sidebar ul li a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            font-weight: 500;
            transition: background-color 0.3s ease;
            width: 100%;
        }

        .sidebar ul li a:hover {
            background-color: #1565c0;
        }

        /* Konten Utama */
        .main-content {
            margin-left: 240px;
            padding: 40px;
            width: calc(100% - 240px);
        }

        .main-content h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .main-content p {
            font-size: 16px;
            color: #666;
        }

        .dashboard-boxes {
            display: flex;
            gap: 20px;
            margin-top: 30px;
        }

        .box {
            background-color: white;
            border-radius: 12px;
            padding: 20px;
            flex: 1;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .box h2 {
            font-size: 24px;
            margin: 0;
            color: #1e88e5;
        }

        .box p {
            margin-top: 10px;
            font-size: 14px;
            color: #777;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <a href="profil.php" style="text-decoration: none;">
   <img src="../uploads/<?= htmlspecialchars($foto_user) ?>" alt="Foto Profil" style="
    width: 120px;
    height: 120px;
    object-fit: cover;
    border-radius: 50%;
    border: 2px solid white;
    cursor: pointer;
" />

</a>

        <ul>
            <li><a href="kasir.php">🛒 Kasir</a></li>
            <li><a href="member.php">👥 Member</a></li>
            <li><a href="kategori.php">📦 Kategori</a></li>
            <li><a href="produk.php">💊 Produk</a></li>
            <li><a href="laporan.php">📊 Laporan</a></li>
            <!-- <li><a href="dashboard.php">Tampilan Awal</a></li> -->
            <?php if ($role === 'superadmin'): ?>
                <li><a href="superadmin.php">🛠️ Super Admin</a></li>
            <?php endif; ?>
             <li><a href="../logout.php"> []keluar</a></li>
        </ul>
    </div>
</body>

</html>
