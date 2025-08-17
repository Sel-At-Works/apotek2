<?php
require '../vendor/autoload.php'; // Jika menggunakan Dompdf
use Dompdf\Dompdf;


$conn = new mysqli("localhost", "root", "", "apotek1");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

// ambil filter
$hari = $_GET['hari'] ?? '';
$bulan = $_GET['bulan'] ?? '';
$tahun = $_GET['tahun'] ?? '';

$where = [];
if ($hari) $where[] = "DAY(t.tanggal) = " . (int)$hari;
if ($bulan) $where[] = "MONTH(t.tanggal) = " . (int)$bulan;
if ($tahun) $where[] = "YEAR(t.tanggal) = " . (int)$tahun;
$where_clause = count($where) ? "WHERE " . implode(" AND ", $where) : "";

// query data transaksi
$query = "
    SELECT td.id_transaksi, t.tanggal, p.nama_produk, td.jumlah, td.harga_satuan,
           (td.jumlah * td.harga_satuan) AS total_harga
    FROM transaksi_detail td
    JOIN transaksi t ON td.id_transaksi = t.id
    JOIN produk p ON td.id_produk = p.id
    $where_clause
    ORDER BY t.tanggal DESC
";
$result = $conn->query($query);

$keuntunganQuery = "
    SELECT SUM((p.harga_jual - p.harga_beli) * td.jumlah) AS total_keuntungan
    FROM transaksi_detail td
    JOIN produk p ON td.id_produk = p.id
    JOIN transaksi t ON td.id_transaksi = t.id
    $where_clause
";
$keuntunganResult = $conn->query($keuntunganQuery);
$totalKeuntungan = $keuntunganResult->fetch_assoc()['total_keuntungan'] ?? 0;

ob_start();
?>
<h2 style="text-align:center;">Laporan Transaksi</h2>
<table border="1" cellspacing="0" cellpadding="5" width="100%">
    <tr>
        <th>ID Transaksi</th>
        <th>Tanggal</th>
        <th>Produk</th>
        <th>Jumlah</th>
        <th>Harga Satuan</th>
        <th>Total</th>
    </tr>
    <?php $totalAll = 0;
    while ($row = $result->fetch_assoc()):
        $totalAll += $row['total_harga']; ?>
        <tr>
            <td><?= $row['id_transaksi'] ?></td>
            <td><?= $row['tanggal'] ?></td>
            <td><?= $row['nama_produk'] ?></td>
            <td><?= $row['jumlah'] ?></td>
            <td>Rp<?= number_format($row['harga_satuan'], 0, ',', '.') ?></td>
            <td>Rp<?= number_format($row['total_harga'], 0, ',', '.') ?></td>
        </tr>
    <?php endwhile; ?>
    <tr>
        <td colspan="5" align="right"><b>Total Semua</b></td>
        <td><b>Rp<?= number_format($totalAll, 0, ',', '.') ?></b></td>
    </tr>
    <tr>
        <td colspan="5" align="right"><b>Total Keuntungan</b></td>
        <td><b>Rp<?= number_format($totalKeuntungan, 0, ',', '.') ?></b></td>
    </tr>
</table>
<?php
$html = ob_get_clean();

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("laporan-transaksi.pdf", ["Attachment" => true]);
