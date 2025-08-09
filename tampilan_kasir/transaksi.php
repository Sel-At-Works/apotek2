<?php
session_start();
$conn = new mysqli("localhost", "root", "", "apotek1");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$grand_total = 0;
$diskon = 0;
$poin_member = 0;
$id_member = null;
$nama_member = "-";
$status = "nonaktif";
$poin_dipakai = 0;
$error_msg = "";

if (!empty($_SESSION['keranjang'])) {
    foreach ($_SESSION['keranjang'] as $id_produk => $qty) {
        $produk = $conn->query("SELECT * FROM produk WHERE id = '$id_produk'")->fetch_assoc();
        if ($produk) {
            $grand_total += $produk['harga_jual'] * $qty;
        }
    }
}

if (isset($_POST['cek_member'])) {
    $no_telp = trim($conn->real_escape_string($_POST['no_telp']));
    $total_harga = (int) $_POST['total_harga'];

    if ($no_telp === "") {
        // Bukan member
        $id_member = null;
        $nama_member = "-";
        $status = "nonaktif";
        $poin_member = 0;
        $diskon = 0;
        $poin_dipakai = 0;
    } else {
        // Cek di database
        $member = $conn->query("SELECT * FROM members WHERE no_hp = '$no_telp'")->fetch_assoc();
        if ($member) {
            $id_member = $member['id'];
            $nama_member = $member['nama'];
            $status = strtolower($member['status']);
            $poin_member = $member['poin'];

            if ($status === 'aktif') {
                if ($poin_member >= 10) {
                    $diskon = $poin_member * 100;
                    if ($diskon > $total_harga) {
                        $diskon = $total_harga;
                    }
                    $poin_dipakai = $diskon / 100;
                } else {
                    $diskon = 0;
                    $poin_dipakai = 0;
                }
            } else {
                $error_msg = "⚠️ Member ditemukan tapi status tidak aktif!";
            }
        } else {
            $error_msg = "❌ Nomor telepon tidak terdaftar sebagai member!";
        }
    }
}


if (isset($_POST['bayar'])) {
    $uang_dibayar = (int) $_POST['uang_dibayar'];
    $total_bayar = $grand_total - $_POST['diskon'];

    if ($uang_dibayar < $total_bayar) {
        $error_msg = "❌ Uang dibayar kurang! Total bayar: Rp" . number_format($total_bayar, 0, ',', '.');
    } else {
        header("Location: proses_transaksi.php?" . http_build_query($_POST));
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Transaksi</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; padding: 20px; }
        .container { background: #fff; padding: 20px; border-radius: 10px; max-width: 600px; margin: auto; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #eee; }
        .error { background: #ffcccc; padding: 10px; margin-bottom: 10px; border-radius: 5px; color: red; }
        .success { background: #ccffcc; padding: 10px; margin-bottom: 10px; border-radius: 5px; color: green; }
    </style>
</head>
<body>
<div class="container">
    <h2>Proses Transaksi</h2>

    <?php if (!empty($error_msg)): ?>
        <div class="error"><?= $error_msg ?></div>
    <?php endif; ?>

    <table>
        <tr>
            <th>Nama Produk</th>
            <th>Jumlah</th>
            <th>Harga</th>
            <th>Subtotal</th>
        </tr>
        <?php
        if (!empty($_SESSION['keranjang'])) {
            foreach ($_SESSION['keranjang'] as $id_produk => $qty) {
                $produk = $conn->query("SELECT * FROM produk WHERE id = '$id_produk'")->fetch_assoc();
                if ($produk) {
                    $subtotal = $produk['harga_jual'] * $qty;
                    echo "<tr>
                            <td>".htmlspecialchars($produk['nama_produk'])."</td>
                            <td>{$qty}</td>
                            <td>Rp".number_format($produk['harga_jual'], 0, ',', '.')."</td>
                            <td>Rp".number_format($subtotal, 0, ',', '.')."</td>
                          </tr>";
                }
            }
        }
        ?>
        <tr>
            <th colspan="3" style="text-align:right;">Total</th>
            <th>Rp<?= number_format($grand_total, 0, ',', '.') ?></th>
        </tr>
    </table>

    <form method="POST">
       <label>Nomor Telepon Member</label>
<input type="text" name="no_telp" value="<?= $_POST['no_telp'] ?? '' ?>">
<input type="hidden" name="total_harga" value="<?= $grand_total ?>">
<button type="submit" name="cek_member">Cek Member / Lanjut Tanpa Member</button>

    </form>

    <hr>
    <p><strong>Nama Member:</strong> <?= $nama_member ?></p>
    <p><strong>Status:</strong> <?= ucfirst($status) ?></p>
    <p><strong>Poin:</strong> <?= $poin_member ?></p>
    <p><strong>Diskon dari Poin:</strong> Rp<?= number_format($diskon, 0, ',', '.') ?></p>
    <p><strong>Total Bayar:</strong> Rp<?= number_format($grand_total - $diskon, 0, ',', '.') ?></p>

    <?php if (isset($_POST['cek_member']) && empty($error_msg)): ?>
   <form method="POST" action="proses_transaksi.php">
        <input type="hidden" name="id_member" value="<?= $id_member ?>">
        <input type="hidden" name="total_harga" value="<?= $grand_total ?>">
        <input type="hidden" name="diskon" value="<?= $diskon ?>">
        <input type="hidden" name="poin_dipakai" value="<?= $poin_dipakai ?>">
        <label>Uang Dibayar</label>
        <input type="number" name="uang_dibayar" required>
        <button type="submit" name="bayar">Selesaikan Transaksi</button>
    </form>
    <?php endif; ?>
</div>
</body>
</html>
