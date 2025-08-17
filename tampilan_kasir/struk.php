<?php
include '../config.php';

$id = $_GET['id'] ?? 0;
// $diskon = isset($_GET['diskon']) ? (int)$_GET['diskon'] : 0;
// $diskon = (int)$transaksi['diskon']; // pakai dari database

if ($id == 0) {
    die("ID transaksi tidak valid.");
}

/// Ambil data transaksi
$transaksi = $koneksi->query("SELECT * FROM transaksi WHERE id = $id")->fetch_assoc();
if (!$transaksi) {
    die("Transaksi tidak ditemukan.");
}

// --- Tambahkan ini setelah transaksi berhasil diambil ---
$diskon_transaksi = (float)($transaksi['diskon'] ?? 0);



// Ambil nama member
$nama_member = "-";
$no_hp = "";

if (!empty($transaksi['id_member'])) {
    $member = $koneksi->query("SELECT nama, no_hp FROM members WHERE id = " . (int)$transaksi['id_member'])->fetch_assoc();
    if ($member) {
        $nama_member = !empty($member['nama']) ? $member['nama'] : "-";
        $no_hp = $member['no_hp'] ?? '';
    }
}


// Ambil nomor HP member jika ada
$no_hp = "";
if (!empty($transaksi['id_member'])) {
    $member = $koneksi->query("SELECT no_hp FROM members WHERE id = " . $transaksi['id_member'])->fetch_assoc();
    if ($member && !empty($member['no_hp'])) {
        $no_hp = $member['no_hp'];
    }
}

// Kalau member tidak ada atau nomor kosong, ambil dari form POST
if (empty($no_hp) && !empty($_POST['no_hp'])) {
    $no_hp = $_POST['no_hp'];
}

// Ambil detail transaksi dan simpan ke array
$detail_transaksi = [];
$detail_result = $koneksi->query("
    SELECT td.*, p.nama_produk 
    FROM transaksi_detail td 
    JOIN produk p ON td.id_produk = p.id 
    WHERE td.id_transaksi = $id
");
while ($row = $detail_result->fetch_assoc()) {
    $detail_transaksi[] = $row;
}


// === Kirim WA lewat Fonnte jika tombol diklik ===
if (isset($_POST['kirim_wa'])) {
    $token = "qK2p9o1KxjuZcuteBnna"; // token Fonnte kamu
    $noHP = preg_replace('/[^0-9]/', '', $no_hp);
    if (substr($noHP, 0, 1) == "0") {
        $noHP = "62" . substr($noHP, 1);
    }

    $pesan = "*Struk Pembayaran Apotek Sehat*\n\n";
    $pesan .= "Tanggal: {$transaksi['tanggal']}\n";
    // $pesan .= "Admin: {$nama_admin}\n";
    $pesan .= "Member: {$nama_member}\n";


    foreach ($detail_transaksi as $item) {
        $nama = $item['nama_produk'];
        $jumlah = $item['jumlah'];
        $harga = number_format($item['harga_satuan'], 0, ',', '.');
        $subtotal = number_format($item['harga_satuan'] * $item['jumlah'], 0, ',', '.');
        $pesan .= "- {$nama} ({$jumlah} x Rp{$harga}) = Rp{$subtotal}\n";
    }

    $pesan .= "---------------------------------\n";
    $pesan .= "Total: Rp" . number_format($transaksi['total_harga'], 0, ',', '.') . "\n";
    $pesan .= "Diskon: Rp" . number_format($diskon_transaksi, 0, ',', '.') . "\n";
    $pesan .= "Dibayar: Rp" . number_format($transaksi['dibayar'], 0, ',', '.') . "\n";
    $pesan .= "Kembalian: Rp" . number_format($transaksi['kembalian'], 0, ',', '.') . "\n\n";
    $pesan .= "Terima kasih sudah berbelanja 🙏";

    // Kirim ke Fonnte
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.fonnte.com/send",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => array(
            "target" => $noHP,
            "message" => $pesan,
        ),
        CURLOPT_HTTPHEADER => array(
            "Authorization: $token"
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        echo "<script>alert('Gagal mengirim pesan: $err');</script>";
    } else {
        echo "<script>alert('Pesan berhasil dikirim via Fonnte!');</script>";
    }
}

?>
<!DOCTYPE html>
<html>

<head>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
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
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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

        th,
        td {
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

        <?php if (!empty($no_hp)) { ?>
            <p><strong>No. HP</strong> : <?= htmlspecialchars($no_hp) ?></p>
        <?php } ?>

        <p><strong>Tanggal</strong>: <?= $transaksi['tanggal'] ?></p>

        <?php if (!empty($nama_admin)) { ?>
            <p><strong>Admin</strong>: <?= htmlspecialchars($nama_admin) ?></p>
        <?php } ?>

        <p><strong>Member</strong>: <?= htmlspecialchars($nama_member) ?></p>

        <table>
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Qty</th>
                    <th style="text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($detail_transaksi as $row) { ?>
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
            <p><strong>Total</strong> : Rp<?= number_format($transaksi['total_harga'], 0, ',', '.') ?></p>
        <p><strong>Diskon</strong> : Rp<?= number_format($diskon_transaksi, 0, ',', '.') ?></p>

            <p><strong>Dibayar</strong> : Rp<?= number_format($transaksi['dibayar'], 0, ',', '.') ?></p>
            <p><strong>Kembalian</strong>: Rp<?= number_format($transaksi['kembalian'], 0, ',', '.') ?></p>
        </div>

        <div class="thankyou">
            <hr>
            <p>Terima Kasih 🙏</p>
            <p>Semoga Lekas Sembuh!</p>
        </div>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="downloadPDF()" style="padding: 10px 20px; background-color: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer;">Download PDF</button>

        <?php if (!empty($no_hp)) { ?>
            <form method="post" style="display:inline;">
                <input type="hidden" name="kirim_wa" value="1">
                <button type="submit" style="padding: 10px 20px; background-color: #25D366; color: white; border: none; border-radius: 5px; margin-left: 10px; cursor: pointer;">
                    Kirim ke WhatsApp
                </button>
            </form>
        <?php } ?>
    </div>


    <script>
        function downloadPDF() {
            const element = document.querySelector('.struk');
            const opt = {
                margin: 0.5,
                filename: 'struk-transaksi.pdf',
                image: {
                    type: 'jpeg',
                    quality: 0.98
                },
                html2canvas: {
                    scale: 2
                },
                jsPDF: {
                    unit: 'in',
                    format: 'a4',
                    orientation: 'portrait'
                }
            };

            html2pdf().set(opt).from(element).save();
        }
    </script>

</body>

</html>