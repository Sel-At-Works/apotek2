<?php
$koneksi = new mysqli("localhost", "root", "", "apotek1");
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

$id = $_GET['id'] ?? 0;
$diskon = isset($_GET['diskon']) ? (int)$_GET['diskon'] : 0;

if ($id == 0) {
    die("ID transaksi tidak valid.");
}

// Ambil data transaksi
$transaksi = $koneksi->query("SELECT * FROM transaksi WHERE id = $id")->fetch_assoc();
if (!$transaksi) {
    die("Transaksi tidak ditemukan.");
}

// Ambil nomor HP member jika ada
$no_hp = "-";
if (!empty($transaksi['id_member'])) {
    $member = $koneksi->query("SELECT no_hp FROM members WHERE id = ".$transaksi['id_member'])->fetch_assoc();
    if ($member) {
        $no_hp = $member['no_hp'];
    }
}

// Ambil detail transaksi
$detail = $koneksi->query("
    SELECT td.*, p.nama_produk 
    FROM transaksi_detail td 
    JOIN produk p ON td.id_produk = p.id 
    WHERE td.id_transaksi = $id
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Struk Transaksi</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            background-color: #f9f9f9;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 30px;
        }
        .struk {
            width: 320px;
            background: #fff;
            padding: 15px 20px;
            border: 1px dashed #333;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .center {
            text-align: center;
            margin-bottom: 10px;
        }
        .center h3 {
            margin: 0;
            font-size: 18px;
        }
        .center p {
            margin: 0;
            font-size: 12px;
        }
        hr {
            border: none;
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
        table {
            width: 100%;
            font-size: 12px;
            border-collapse: collapse;
        }
        th, td {
            padding: 6px 2px;
            text-align: left;
        }
        th {
            border-bottom: 1px solid #000;
        }
        .totals {
            font-size: 13px;
            margin-top: 10px;
        }
        .totals p {
            margin: 2px 0;
        }
        .thankyou {
            text-align: center;
            margin-top: 15px;
            font-size: 13px;
            font-weight: bold;
        }
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="struk">
        <div class="center">
            <h3>Apotek Sehat</h3>
            <p>Jl. Pantai Indah Utara 3, Kapuk Muara, Kec. Penjaringan, Kota Jkt Utara</p>
            <p>Telp: 021 5880911</p>
        </div>

        <hr>

        <p><strong>No. HP</strong> : <?= htmlspecialchars($no_hp) ?></p>
        <p><strong>Tanggal</strong>: <?= $transaksi['tanggal'] ?></p>

        <table>
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Qty</th>
                    <th style="text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $detail->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nama_produk']) ?></td>
                        <td><?= $row['jumlah'] ?></td>
                        <td style="text-align: right;">Rp<?= number_format($row['harga_satuan'] * $row['jumlah'], 0, ',', '.') ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="totals">
            <hr>
            <p><strong>Total</strong>    : Rp<?= number_format($transaksi['total_harga'], 0, ',', '.') ?></p>
            <p><strong>Diskon</strong>   : Rp<?= number_format($diskon, 0, ',', '.') ?></p>
            <p><strong>Dibayar</strong>  : Rp<?= number_format($transaksi['dibayar'], 0, ',', '.') ?></p>
            <p><strong>Kembalian</strong>: Rp<?= number_format($transaksi['kembalian'], 0, ',', '.') ?></p>
        </div>

        <div class="thankyou">
            <hr>
            <p>Terima Kasih 🙏</p>
            <p>Semoga Lekas Sembuh!</p>
        </div>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background-color: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer;">Download PDF</button>

        <button onclick="kirimWA()" style="padding: 10px 20px; background-color: #25D366; color: white; border: none; border-radius: 5px; margin-left: 10px; cursor: pointer;">Kirim ke WhatsApp</button>
    </div>

    <script>
    function kirimWA() {
        const noHP = "<?= preg_replace('/[^0-9]/', '', $no_hp) ?>";
        const total = "Rp<?= number_format($transaksi['total_harga'], 0, ',', '.') ?>";
        const diskon = "Rp<?= number_format($diskon, 0, ',', '.') ?>";
        const dibayar = "Rp<?= number_format($transaksi['dibayar'], 0, ',', '.') ?>";
        const kembalian = "Rp<?= number_format($transaksi['kembalian'], 0, ',', '.') ?>";
        const tanggal = "<?= $transaksi['tanggal'] ?>";

        let pesan = `*Struk Pembayaran Apotek Sehat*\n\nTanggal: ${tanggal}\nTotal: ${total}\nDiskon: ${diskon}\nDibayar: ${dibayar}\nKembalian: ${kembalian}\n\nTerima kasih sudah berbelanja 🙏`;

        // Format nomor WA
        let waNomor = noHP.startsWith("0") ? "62" + noHP.substring(1) : noHP;
        let waLink = `https://wa.me/${waNomor}?text=${encodeURIComponent(pesan)}`;

        window.open(waLink, '_blank');
    }
    </script>
</body>
</html>
