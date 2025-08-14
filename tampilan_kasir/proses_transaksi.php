<?php
session_start();

$koneksi = new mysqli("localhost", "root", "", "apotek1");
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Ambil data dengan tipe yang tepat dari POST
$id_member    = !empty($_POST['id_member']) ? (int)$_POST['id_member'] : null;
$total_harga  = isset($_POST['total_harga']) ? (float)$_POST['total_harga'] : 0;
$diskon       = isset($_POST['diskon']) ? (float)$_POST['diskon'] : 0;
$poin_dipakai = isset($_POST['poin_dipakai']) ? (int)$_POST['poin_dipakai'] : 0;
$uang_dibayar = isset($_POST['uang_dibayar']) ? (float)$_POST['uang_dibayar'] : 0;

// // Debug: tampilkan data input
// echo "<pre>";
// echo "Input data:\n";
// echo "ID Member: $id_member\n";
// echo "Total Harga: $total_harga\n";
// echo "Diskon: $diskon\n";
// echo "Poin Dipakai: $poin_dipakai\n";
// echo "Uang Dibayar: $uang_dibayar\n";
// echo "</pre>";

// Hitung kembalian
// Hitung kembalian
$kembalian = $uang_dibayar - ($total_harga - $diskon);
if ($kembalian < 0) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Transaksi Gagal</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #fff3f3;
                margin: 0; padding: 0;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }
            .alert-box {
                background-color: #ffdddd;
                border-left: 6px solid #f44336;
                padding: 20px 30px;
                max-width: 400px;
                text-align: center;
                box-shadow: 0 0 15px rgba(244,67,54,0.4);
                border-radius: 8px;
            }
            .alert-box h1 {
                margin: 0 0 10px;
                color: #f44336;
                font-size: 1.8em;
            }
            .alert-box p {
                font-size: 1.2em;
                margin-bottom: 20px;
                color: #a94442;
            }
            .alert-box button {
                background-color: #f44336;
                border: none;
                color: white;
                padding: 10px 20px;
                font-size: 1em;
                border-radius: 5px;
                cursor: pointer;
                transition: background-color 0.3s ease;
            }
            .alert-box button:hover {
                background-color: #d32f2f;
            }
            .btn-kembali {
    display: inline-block;
    padding: 10px 20px;
    background-color: #f44336;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
    cursor: pointer;
}
.btn-kembali:hover {
    background-color: #d32f2f;
}

        </style>
    </head>
    <body>
        <div class="alert-box">
            <h1>⚠️ Transaksi Gagal</h1>
            <p>Uang dibayar kurang dari total yang harus dibayar.<br>
               Total Bayar: <strong>Rp <?= number_format($total_harga - $diskon, 0, ',', '.') ?></strong><br>
               Uang Dibayar: <strong>Rp <?= number_format($uang_dibayar, 0, ',', '.') ?></strong>
            </p>
            <a href="transaksi.php" class="btn-kembali">Kembali</a>
        </div>
    </body>
    </html>
    <?php
    exit;
}
echo "<p>Kembalian: $kembalian</p>";

// Update poin member jika ada
if ($id_member !== null) {
    $result = $koneksi->query("SELECT poin FROM members WHERE id = $id_member");
    if (!$result) {
        die("Error mengambil poin member: " . $koneksi->error);
    }
    $member = $result->fetch_assoc();
    $poin_sekarang = $member['poin'] ?? 0;

    $poin_baru = floor(($total_harga - $diskon) / 1000);
    $poin_akhir = $poin_sekarang - $poin_dipakai + $poin_baru;
    if ($poin_akhir < 0) $poin_akhir = 0;

    $update = $koneksi->query("UPDATE members SET poin = $poin_akhir WHERE id = $id_member");
    if (!$update) {
        die("Error update poin member: " . $koneksi->error);
    }
    echo "<p>Poin member diupdate: $poin_akhir</p>";
}

// Simpan transaksi utama
$stmt = $koneksi->prepare("INSERT INTO transaksi (id_member, total_harga, dibayar, kembalian, tanggal) VALUES (?, ?, ?, ?, NOW())");
if (!$stmt) {
    die("Prepare statement gagal: " . $koneksi->error);
}
$stmt->bind_param("iddd", $id_member, $total_harga, $uang_dibayar, $kembalian);

if (!$stmt->execute()) {
    die("Gagal menyimpan transaksi: " . $stmt->error);
}
$id_transaksi = $stmt->insert_id;
$stmt->close();

echo "<p>Transaksi disimpan dengan ID: $id_transaksi</p>";

// Simpan detail transaksi dan update stok produk
if (!empty($_SESSION['keranjang'])) {
    foreach ($_SESSION['keranjang'] as $id_produk => $jumlah) {
        $produk = $koneksi->query("SELECT harga_jual, stok FROM produk WHERE id = $id_produk")->fetch_assoc();
        if (!$produk) {
            echo "<p>Produk ID $id_produk tidak ditemukan, dilewati.</p>";
            continue;
        }

        $harga = $produk['harga_jual'];
        $stok_baru = $produk['stok'] - $jumlah;
        if ($stok_baru < 0) $stok_baru = 0;

        $insert_detail = $koneksi->query("INSERT INTO transaksi_detail (id_transaksi, id_produk, jumlah, harga_satuan) VALUES ($id_transaksi, $id_produk, $jumlah, $harga)");
        if (!$insert_detail) {
            die("Gagal simpan detail transaksi produk ID $id_produk: " . $koneksi->error);
        }

        // $update_stok = $koneksi->query("UPDATE produk SET stok = $stok_baru WHERE id = $id_produk");
        // if (!$update_stok) {
        //     die("Gagal update stok produk ID $id_produk: " . $koneksi->error);
        // }

        echo "<p>Produk ID $id_produk, jumlah $jumlah, stok baru $stok_baru disimpan.</p>";
    }
} else {
    echo "<p>Keranjang kosong, tidak ada produk disimpan.</p>";
}

unset($_SESSION['keranjang']);

// Kirim WA via Fonnte
$token = "qK2p9o1KxjuZcuteBnna"; // Ganti dengan Account Token dari Fonnte
// Ambil nomor HP member (jika ada)
$no_hp = null;
if ($id_member !== null) {
    $result = $koneksi->query("SELECT no_hp FROM members WHERE id = $id_member");
    if ($result) {
        $row = $result->fetch_assoc();
        $no_hp = $row['no_hp'] ?? null;
    }
}

if ($no_hp) {
    $target = preg_replace('/[^0-9]/', '', $no_hp);
    if (substr($target, 0, 1) == '0') {
        $target = '62' . substr($target, 1);
    }

    $tanggal = date('Y-m-d H:i:s');
    $pesan = "*Struk Pembayaran Apotek Sehat*\n\n".
             "Tanggal: {$tanggal}\n".
             "Total: Rp".number_format($total_harga,0,',','.')."\n".
             "Diskon: Rp".number_format($diskon,0,',','.')."\n".
             "Dibayar: Rp".number_format($uang_dibayar,0,',','.')."\n".
             "Kembalian: Rp".number_format($kembalian,0,',','.')."\n\n".
             "Terima kasih sudah berbelanja 🙏";

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://api.fonnte.com/send",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => [
            'target' => $target,
            'message' => $pesan
        ],
        CURLOPT_HTTPHEADER => [
            "Authorization: $token"
        ],
    ]);
    $response = curl_exec($curl);
    curl_close($curl);
}

// // Redirect ke struk.php agar langsung tampil struknya
// header("Location: struk.php?id=$id_transaksi&diskon=$diskon");
// exit();


// Redirect ke struk.php agar langsung tampil struknya
header("Location: struk.php?id=$id_transaksi&diskon=$diskon");
exit();