<?php
session_start();
include_once 'sidebar_kasir.php';

$conn = new mysqli("localhost", "root", "", "apotek1");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$filter_hari = $_GET['hari'] ?? '';
$filter_bulan = $_GET['bulan'] ?? '';
$filter_tahun = $_GET['tahun'] ?? '';

$where = [];
if (!empty($filter_hari)) {
    $where[] = "DAY(t.tanggal) = " . (int)$filter_hari;
}
if (!empty($filter_bulan)) {
    $where[] = "MONTH(t.tanggal) = " . (int)$filter_bulan;
}
if (!empty($filter_tahun)) {
    $where[] = "YEAR(t.tanggal) = " . (int)$filter_tahun;
}
$where_clause = count($where) ? "WHERE " . implode(" AND ", $where) : "";

$query = "
    SELECT 
        td.id_transaksi,
        t.tanggal,
        t.id_user,
          u.username AS nama_user, 
        t.id_member,
           m.nama AS nama_member,  
        td.id_produk,
        p.nama_produk AS nama_produk,
        td.jumlah,
        td.harga_satuan,
        (td.jumlah * td.harga_satuan) AS total_harga
    FROM transaksi_detail td
    JOIN transaksi t ON td.id_transaksi = t.id
    JOIN produk p ON td.id_produk = p.id
    LEFT JOIN users u ON t.id_user = u.id 
    LEFT JOIN members m ON t.id_member = m.id  
    $where_clause
    ORDER BY t.tanggal DESC
";

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);
$offset = ($page - 1) * $limit;

$countQuery = "
    SELECT COUNT(*) as total 
    FROM transaksi_detail td
    JOIN transaksi t ON td.id_transaksi = t.id
    JOIN produk p ON td.id_produk = p.id
    $where_clause
";
$countResult = $conn->query($countQuery);
$totalRows = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

// Tambahkan LIMIT dan OFFSET ke query utama
$query .= " LIMIT $limit OFFSET $offset";




$result = $conn->query($query);

// === Query total keuntungan ===
$keuntunganQuery = "
    SELECT 
        SUM((p.harga_jual - p.harga_beli) * td.jumlah) AS total_keuntungan
    FROM transaksi_detail td
    JOIN transaksi t ON td.id_transaksi = t.id
    JOIN produk p ON td.id_produk = p.id
    $where_clause
";
$keuntunganResult = $conn->query($keuntunganQuery);
$totalKeuntungan = $keuntunganResult->fetch_assoc()['total_keuntungan'] ?? 0;

?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi</title>

    <style>
        .pagination {
    text-align: center;
    margin-top: 30px;
}

.pagination a {
    display: inline-block;
    padding: 6px 12px;
    margin: 0 3px;
    background-color: #ffffff;
    color: #007bff;
    border: 1px solid #007bff;
    border-radius: 4px;
    text-decoration: none;
    font-size: 14px;
    transition: background-color 0.3s, color 0.3s;
}

.pagination a:hover {
    background-color: #007bff;
    color: #ffffff;
}

.pagination a.active {
    background-color: #007bff;
    color: #ffffff;
    font-weight: bold;
    pointer-events: none;
    border: 1px solid #007bff;
}

    </style>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f1f3f5;
            margin: 0;
        }

        .container {
            margin-left: 240px; /* Sesuaikan jika sidebar berbeda */
            padding: 2rem;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        form {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 25px;
        }

        form label {
            font-weight: 600;
        }

        form select, form button {
            padding: 10px 14px;
            font-size: 14px;
            border-radius: 6px;
            border: 1px solid #ccc;
            background-color: #fff;
        }

        form button {
            background-color: #007bff;
            color: white;
            border: none;
            transition: background 0.3s;
        }

        form button:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }

        table thead {
            background-color: #007bff;
            color: #fff;
        }

        table th, table td {
            padding: 12px 10px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }

        table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        table tbody tr:hover {
            background-color: #e9ecef;
        }

        .no-data {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #888;
        }

        @media screen and (max-width: 768px) {
            .container {
                margin-left: 0;
                padding: 1rem;
            }

            table {
                font-size: 13px;
            }

            form {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Laporan Transaksi</h2>

    <form method="GET">
        <label for="hari">Hari:</label>
        <select name="hari" id="hari">
            <option value="">Semua</option>
            <?php for ($i = 1; $i <= 31; $i++): ?>
                <option value="<?= $i ?>" <?= ($filter_hari == $i) ? 'selected' : '' ?>><?= $i ?></option>
            <?php endfor; ?>
        </select>

        <label for="bulan">Bulan:</label>
        <select name="bulan" id="bulan">
            <option value="">Semua</option>
            <?php
            $bulan_nama = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
            foreach ($bulan_nama as $num => $nama):
            ?>
                <option value="<?= $num ?>" <?= ($filter_bulan == $num) ? 'selected' : '' ?>><?= $nama ?></option>
            <?php endforeach; ?>
        </select>

        <label for="tahun">Tahun:</label>
        <select name="tahun" id="tahun">
            <option value="">Semua</option>
            <?php
            $tahun_ini = date('Y');
            for ($t = $tahun_ini; $t >= $tahun_ini - 5; $t--):
            ?>
                <option value="<?= $t ?>" <?= ($filter_tahun == $t) ? 'selected' : '' ?>><?= $t ?></option>
            <?php endfor; ?>
        </select>

        <button type="submit">Tampilkan</button>
        
          <!-- Tombol Unduh di sini -->
    <a href="unduh_laporan.php?<?= http_build_query($_GET) ?>" 
   style="margin-left: 20px; padding: 10px 15px; background:#28a745; color:#fff; text-decoration:none; border-radius:5px;">
   Unduh Laporan (Excel)
</a>

    </form>

   <table>
    <thead>
        <tr>
            <th>ID Transaksi</th>
            <th>Tanggal</th>
            <!-- <th>Nama Kasir</th> -->
            <th>Nama Member</th>
            <th>ID Produk</th>
            <th>Nama Produk</th>
            <th>Jumlah</th>
            <th>Harga Satuan</th>
            <th>Total Harga</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id_transaksi'] ?></td>
                    <td><?= $row['tanggal'] ?></td>
                    <!-- <td><?= htmlspecialchars($row['nama_user'] ?? '-') ?></td> -->
<td><?= $row['nama_member'] ?? '-' ?></td>

                    <td><?= $row['id_produk'] ?></td>
                    <td><?= $row['nama_produk'] ?></td>
                    <td><?= $row['jumlah'] ?></td>
                    <td>Rp<?= number_format($row['harga_satuan'], 0, ',', '.') ?></td>
                    <td>Rp<?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="9" class="no-data">Tidak ada data transaksi detail.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<br>
<table>
    <tr style="background-color:#28a745;color:white;font-weight:bold;">
        <td style="text-align:center;">Total Keuntungan: 
            Rp<?= number_format($totalKeuntungan, 0, ',', '.') ?>
        </td>
    </tr>
</table>
<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">&laquo; Prev</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>

    <?php if ($page < $totalPages): ?>
        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">Next &raquo;</a>
    <?php endif; ?>
</div>





</div>

</body>
</html>
