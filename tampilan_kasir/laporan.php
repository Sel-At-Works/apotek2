<?php
session_start();
include_once 'sidebar_kasir.php';

$conn = new mysqli("localhost", "root", "", "apotek1");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

$filter_hari = $_GET['hari'] ?? '';
$filter_bulan = $_GET['bulan'] ?? '';
$filter_tahun = $_GET['tahun'] ?? '';

$where = [];
if (!empty($filter_hari)) $where[] = "DAY(t.tanggal) = " . (int)$filter_hari;
if (!empty($filter_bulan)) $where[] = "MONTH(t.tanggal) = " . (int)$filter_bulan;
if (!empty($filter_tahun)) $where[] = "YEAR(t.tanggal) = " . (int)$filter_tahun;
$where_clause = count($where) ? "WHERE " . implode(" AND ", $where) : "";

// Ambil transaksi detail dengan diskon
$query = "
    SELECT 
        td.id_transaksi,
        t.tanggal,
        t.diskon,
        t.dibayar,
        t.kembalian,
        td.id_produk,
        p.nama_produk AS nama_produk,
        td.jumlah,
        td.harga_satuan,
        (td.jumlah * td.harga_satuan) AS total_harga
    FROM transaksi_detail td
    JOIN transaksi t ON td.id_transaksi = t.id
    JOIN produk p ON td.id_produk = p.id
    $where_clause
    ORDER BY t.tanggal DESC
";

$limit = 10;
$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$offset = ($page - 1) * $limit;

// Hitung total baris untuk pagination
$countQuery = "
    SELECT COUNT(*) as total
    FROM transaksi_detail td
    JOIN transaksi t ON td.id_transaksi = t.id
    $where_clause
";
$countResult = $conn->query($countQuery);
$totalRows = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

// Tambahkan LIMIT
$query .= " LIMIT $limit OFFSET $offset";
$result = $conn->query($query);

// Hitung total keuntungan
$keuntunganQuery = "
    SELECT SUM((p.harga_jual - p.harga_beli) * td.jumlah) AS total_keuntungan
    FROM transaksi_detail td
    JOIN produk p ON td.id_produk = p.id
    JOIN transaksi t ON td.id_transaksi = t.id
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
body { font-family: 'Segoe UI', sans-serif; background:#f1f3f5; margin:0; }
.container { margin-left: 240px; padding:2rem; }
h2 { text-align:center; color:#333; margin-bottom:20px; }
form { display:flex; gap:15px; justify-content:center; flex-wrap:wrap; margin-bottom:20px; }
form label { font-weight:600; }
form select, form button { padding:8px 12px; border-radius:6px; border:1px solid #ccc; }
form button { background:#007bff; color:#fff; border:none; cursor:pointer; }
form button:hover { background:#0056b3; }
table { width:100%; border-collapse:collapse; background:#fff; border-radius:6px; overflow:hidden; box-shadow:0 2px 6px rgba(0,0,0,0.05); margin-bottom:15px; }
table thead { background:#007bff; color:#fff; }
table th, table td { padding:10px; text-align:center; border-bottom:1px solid #eee; }
table tbody tr:nth-child(even){background:#f8f9fa;}
table tbody tr:hover{background:#e9ecef;}
.total-row { font-weight:bold; background:#f1f1f1; }
.total-keuntungan { 
    font-weight: bold; 
    background: #28a745 !important; 
    color: #fff !important; 
}
.total-keuntungan td {
    background: #28a745 !important;
    color: #fff !important;
}
.pagination { text-align:center; margin-top:15px; }
.pagination a { display:inline-block; padding:6px 12px; margin:0 3px; background:#fff; color:#007bff; border:1px solid #007bff; border-radius:4px; text-decoration:none; }
.pagination a.active { background:#007bff; color:#fff; pointer-events:none; font-weight:bold; }
.pagination a:hover { background:#007bff; color:#fff; }
.no-data { text-align:center; font-style:italic; color:#888; }
</style>
</head>
<body>
<div class="container" id="laporanTransaksi">
<h2>Laporan Transaksi</h2>

<form method="GET">
    <label>Hari:</label>
    <select name="hari"><option value="">Semua</option>
    <?php for($i=1;$i<=31;$i++): ?>
        <option value="<?= $i ?>" <?= ($filter_hari==$i)?'selected':'' ?>><?= $i ?></option>
    <?php endfor; ?>
    </select>

    <label>Bulan:</label>
    <select name="bulan"><option value="">Semua</option>
    <?php
    $bulan_nama = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
    foreach($bulan_nama as $num=>$nama):
    ?>
        <option value="<?= $num ?>" <?= ($filter_bulan==$num)?'selected':'' ?>><?= $nama ?></option>
    <?php endforeach; ?>
    </select>

    <label>Tahun:</label>
    <select name="tahun"><option value="">Semua</option>
    <?php for($t=date('Y');$t>=date('Y')-5;$t--): ?>
        <option value="<?= $t ?>" <?= ($filter_tahun==$t)?'selected':'' ?>><?= $t ?></option>
    <?php endfor; ?>
    </select>

    <button type="submit">Tampilkan</button>
</form>

<table>
<thead>
<tr>
<th>ID Transaksi</th>
<th>Tanggal</th>
<th>ID Produk</th>
<th>Nama Produk</th>
<th>Jumlah</th>
<th>Harga Satuan</th>
<th>Total Harga</th>
<th>Aksi</th>
</tr>
</thead>
<tbody>
<?php 
$totalAll = 0;
if($result && $result->num_rows>0):
    while($row=$result->fetch_assoc()):
        $totalAll += $row['total_harga'];
?>
<tr>
<td><?= $row['id_transaksi'] ?></td>
<td><?= $row['tanggal'] ?></td>
<td><?= $row['id_produk'] ?></td>
<td><?= $row['nama_produk'] ?></td>
<td><?= $row['jumlah'] ?></td>
<td>Rp<?= number_format($row['harga_satuan'],0,',','.') ?></td>
<td>Rp<?= number_format($row['total_harga'],0,',','.') ?></td>
<td>
<a href="download_struk.php?id=<?= $row['id_transaksi'] ?>&diskon=<?= (float)($row['diskon']??0) ?>" target="_blank"
 style="padding:5px 8px; background:#17a2b8;color:#fff;text-decoration:none;border-radius:4px;">Download Struk</a>
</td>
</tr>
<?php endwhile; ?>
<tr class="total-row">
<td colspan="6">Total Harga Semua</td>
<td colspan="2">Rp<?= number_format($totalAll,0,',','.') ?></td>
</tr>
<tr class="total-keuntungan">
<td colspan="6">Total Keuntungan</td>
<td colspan="2">Rp<?= number_format($totalKeuntungan,0,',','.') ?></td>
</tr>
<?php else: ?>
<tr><td colspan="8" class="no-data">Tidak ada data transaksi.</td></tr>
<?php endif; ?>
</tbody>
</table>

<!-- Tombol download PDF -->
<div style="text-align:center; margin-top:15px;">
    <a href="cetak_laporan.php?hari=<?= $filter_hari ?>&bulan=<?= $filter_bulan ?>&tahun=<?= $filter_tahun ?>" 
       target="_blank"
       style="padding:8px 16px; background:#28a745; color:#fff; text-decoration:none; border-radius:6px;">
       Download PDF (Server)
    </a>
</div>


<!-- Script html2pdf (taruh di bawah sebelum </body>) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
function downloadPDF(){
    const element = document.getElementById('laporanTransaksi');
    html2pdf().set({
        margin: 0.5,
        filename: 'laporan-transaksi.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
    }).from(element).save();
}
</script>

<div class="pagination">
<?php if($page>1): ?>
<a href="?<?= http_build_query(array_merge($_GET,['page'=>$page-1])) ?>">&laquo; Prev</a>
<?php endif; ?>
<?php for($i=1;$i<=$totalPages;$i++): ?>
<a href="?<?= http_build_query(array_merge($_GET,['page'=>$i])) ?>" class="<?= ($i==$page)?'active':'' ?>"><?= $i ?></a>
<?php endfor; ?>
<?php if($page<$totalPages): ?>
<a href="?<?= http_build_query(array_merge($_GET,['page'=>$page+1])) ?>">Next &raquo;</a>
<?php endif; ?>
</div>

</div>
</body>
</html>
