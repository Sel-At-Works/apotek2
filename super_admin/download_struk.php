<?php
require '../vendor/autoload.php'; // Jika menggunakan Dompdf
use Dompdf\Dompdf;
use Dompdf\Options;

$conn = new mysqli("localhost", "root", "", "apotek1");
if ($conn->connect_error) die("Koneksi gagal: ".$conn->connect_error);

$id = $_GET['id'] ?? 0;
if ($id == 0) die("ID transaksi tidak valid.");

// Ambil data transaksi
$transaksi = $conn->query("SELECT * FROM transaksi WHERE id=$id")->fetch_assoc();
if (!$transaksi) die("Transaksi tidak ditemukan.");

// Ambil diskon langsung dari database
$diskon = (float)$transaksi['diskon'];

// Ambil detail transaksi
$detail_transaksi = [];
$detail_result = $conn->query("
    SELECT td.*, p.nama_produk 
    FROM transaksi_detail td 
    JOIN produk p ON td.id_produk = p.id 
    WHERE td.id_transaksi = $id
");
while ($row = $detail_result->fetch_assoc()) $detail_transaksi[] = $row;

// Hitung total sebelum diskon dari detail
$total_before_diskon = array_sum(array_map(fn($item)=>$item['harga_satuan']*$item['jumlah'], $detail_transaksi));

// Total setelah diskon
$total_after_diskon = max($total_before_diskon - $diskon, 0);


// Ambil nama admin
$nama_admin = "";
if (!empty($transaksi['id_user'])) {
    $admin = $conn->query("SELECT nama FROM users WHERE id=".(int)$transaksi['id_user'])->fetch_assoc();
    $nama_admin = $admin['nama'] ?? '';
}

// Ambil member
$nama_member = "-";
$no_hp = "";
$poin_member = 0;
if (!empty($transaksi['id_member'])) {
    $member = $conn->query("SELECT nama, no_hp, poin FROM members WHERE id=".(int)$transaksi['id_member'])->fetch_assoc();
    if ($member) {
        $nama_member = $member['nama'] ?? "-";
        $no_hp = $member['no_hp'] ?? "";
        // $poin_member = (int)($member['poin'] ?? 0);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Struk Transaksi</title>
    <style>
        body { font-family: 'Courier New', monospace; background:#f9f9f9; display:flex; justify-content:center; padding:30px;}
        .struk { width:320px; background:#fff; padding:15px 20px; border:1px dashed #333; }
        .center { text-align:center; margin-bottom:10px;}
        .center h3 { margin:0; font-size:18px; }
        .center p { margin:0; font-size:12px; }
        hr { border:none; border-top:1px dashed #000; margin:10px 0; }
        table { width:100%; font-size:12px; border-collapse:collapse; }
        th, td { padding:6px 2px; text-align:left; }
        th { border-bottom:1px solid #000; }
        .totals { font-size:13px; margin-top:10px; }
        .totals p { margin:2px 0; }
        .thankyou { text-align:center; margin-top:15px; font-size:13px; font-weight:bold; }
        @media print { .no-print { display:none !important; } }
    </style>
</head>
<body>
<div class="container">
    <div class="struk">
        <!-- isi struk -->
        <div class="center">
            <h3>Apotek Sehat</h3>
            <p>Jl. Pantai Indah Utara 3, Kapuk Muara, Kec. Penjaringan, Kota Jkt Utara</p>
            <p>Telp: 021 5880911</p>
        </div>
        <hr>
        <?php if(!empty($no_hp)) echo "<p><strong>No. HP</strong>: ".htmlspecialchars($no_hp)."</p>"; ?>
        <p><strong>Tanggal</strong>: <?= $transaksi['tanggal'] ?></p>
        <?php if(!empty($nama_admin)) echo "<p><strong>Admin</strong>: ".htmlspecialchars($nama_admin)."</p>"; ?>
       <p><strong>Member</strong>: <?= htmlspecialchars($nama_member) ?></p>


        <table>
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Qty</th>
                    <th style="text-align:right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($detail_transaksi as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['nama_produk']) ?></td>
                    <td><?= $row['jumlah'] ?></td>
                    <td style="text-align:right;">Rp<?= number_format($row['harga_satuan']*$row['jumlah'],0,',','.') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <div class="totals">
            <hr>
            <p><strong>Total Sebelum Diskon</strong>: Rp<?= number_format($total_before_diskon,0,',','.') ?></p>
            <p><strong>Diskon</strong>: Rp<?= number_format($transaksi['diskon'], 0, ',', '.') ?></p>
            <p><strong>Dibayar</strong>: Rp<?= number_format($transaksi['dibayar'],0,',','.') ?></p>
            <p><strong>Kembalian</strong>: Rp<?= number_format($transaksi['kembalian'],0,',','.') ?></p>
        </div>

        <div class="thankyou">
            <hr>
            <p>Terima Kasih 🙏</p>
            <p>Semoga Lekas Sembuh!</p>
        </div>
    </div>

    <!-- Tombol di bawah struk -->
    <!-- Tombol di bawah struk -->
<div class="no-print" style="margin-top:20px; text-align:center;">
    <button onclick="downloadPDF()" 
        style="padding:8px 16px; 
               background:#3498db; 
               color:#fff; 
               border:none; 
               border-radius:5px; 
               cursor:pointer; 
               font-size:14px;">
        Download PDF
    </button>
</div>
</div>
</body>

</html>
<!-- Tambahkan script html2pdf -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
function downloadPDF(){
    const element = document.querySelector('.struk');
    html2pdf().set({
        margin:0.3, 
        filename:'struk-transaksi.pdf', 
        image:{type:'jpeg',quality:0.98}, 
        html2canvas:{scale:2}, 
        jsPDF:{unit:'in', format:'a5', orientation:'portrait'} // kecil pakai A5
    }).from(element).save();
}
</script>
<style>
      body { 
        font-family: 'Courier New', monospace; 
        background:#f9f9f9; 
        display:flex; 
        justify-content:center; 
        padding:30px;
    }
    .container {
        display: flex;
        flex-direction: column; /* Susun ke bawah */
        align-items: center;    /* Biar center */
    }
    .struk { 
        width:320px; 
        background:#fff; 
        padding:15px 20px; 
        border:1px dashed #333; 
    }
</style>
