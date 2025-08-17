<?php
// unduh_laporan.php

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
        u.username AS nama_user, 
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

$result = $conn->query($query);

// ======= letakkan kode ini di sini =======
$totalHargaAll = 0;
$rows = []; // inisialisasi array kosong
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $totalHargaAll += $row['total_harga'];
        $rows[] = $row; // simpan data sementara
    }
}

$keuntunganQuery = "
    SELECT SUM((p.harga_jual - p.harga_beli) * td.jumlah) AS total_keuntungan
    FROM transaksi_detail td
    JOIN transaksi t ON td.id_transaksi = t.id
    JOIN produk p ON td.id_produk = p.id
    $where_clause
";
$keuntunganResult = $conn->query($keuntunganQuery);
$totalKeuntungan = $keuntunganResult->fetch_assoc()['total_keuntungan'] ?? 0;
// ========================================

// Set header agar browser download file excel
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=laporan_transaksi.xls");
header("Pragma: no-cache");
header("Expires: 0");

// Mulai output tabel Excel
echo "<table border='1'>";
echo "<thead>";
echo "<tr>";
echo "<th>ID Transaksi</th>";
echo "<th>Tanggal</th>";
echo "<th>Nama Member</th>";
echo "<th>ID Produk</th>";
echo "<th>Nama Produk</th>";
echo "<th>Jumlah</th>";
echo "<th>Harga Satuan</th>";
echo "<th>Total Harga</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";

// Tampilkan semua baris
foreach ($rows as $row) {
    echo "<tr>";
    echo "<td>" . $row['id_transaksi'] . "</td>";
    echo "<td>" . $row['tanggal'] . "</td>";
    echo "<td>" . htmlspecialchars($row['nama_member'] ?? '-') . "</td>";
    echo "<td>" . $row['id_produk'] . "</td>";
    echo "<td>" . $row['nama_produk'] . "</td>";
    echo "<td>" . $row['jumlah'] . "</td>";
    echo "<td>Rp" . number_format($row['harga_satuan'],0,',','.') . "</td>";
    echo "<td>Rp" . number_format($row['total_harga'],0,',','.') . "</td>";
    echo "</tr>";
}

// Tambahkan baris Total Harga Semua
echo "<tr style='font-weight:bold; background-color:#e9ecef;'>
        <td colspan='7' style='text-align:right;'>Total Harga Semua:</td>
        <td>Rp" . number_format($totalHargaAll,0,',','.') . "</td>
      </tr>";

// Tambahkan baris Total Keuntungan
echo "<tr style='font-weight:bold; background-color:#28a745; color:white;'>
        <td colspan='7' style='text-align:right;'>Total Keuntungan:</td>
        <td>Rp" . number_format($totalKeuntungan,0,',','.') . "</td>
      </tr>";

echo "</tbody></table>";
