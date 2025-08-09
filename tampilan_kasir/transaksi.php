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
        /* Reset dan font */
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f9fafb;
            margin: 0; padding: 20px;
            color: #555;
        }

        .container {
            background: #ffffff;
            max-width: 700px;
            margin: 30px auto;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(150, 150, 150, 0.1);
            padding: 30px 40px;
            border: 1px solid #e2e8f0;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #4a5568; /* abu gelap kalem */
            font-weight: 600;
            text-transform: capitalize;
            letter-spacing: 1px;
        }

        /* Table styling */
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
            margin-bottom: 25px;
        }
        th, td {
            text-align: left;
            padding: 14px 20px;
            font-size: 0.95rem;
        }
        th {
            background: #e2e8f0; /* abu soft */
            color: #4a5568;
            font-weight: 600;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        tbody tr {
            background: #f7fafc; /* putih kebiruan */
            border-radius: 8px;
            transition: background 0.25s ease;
        }
        tbody tr:hover {
            background: #e2e8f0; /* hover soft */
        }
        td:last-child {
            font-weight: 600;
            color: #3182ce; /* biru kalem */
        }

        /* Info list */
        .info-list {
            margin-bottom: 25px;
            padding: 20px;
            background: #edf2f7;
            border-radius: 10px;
            color: #4a5568;
            font-size: 1rem;
            line-height: 1.5;
            border: 1px solid #cbd5e0;
        }
        .info-list p {
            margin: 10px 0;
            font-weight: 600;
        }
        .info-list strong {
            color: #2b6cb0;
        }

        /* Form styling */
        form {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 15px;
            justify-content: center;
        }

        label {
            flex-basis: 100%;
            font-weight: 600;
            margin-bottom: 6px;
            color: #4a5568;
        }

        input[type="text"],
        input[type="number"] {
            flex-grow: 1;
            padding: 12px 15px;
            border: 1.5px solid #a0aec0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
            color: #2d3748;
            background: #f7fafc;
        }
        input[type="text"]:focus,
        input[type="number"]:focus {
            outline: none;
            border-color: #63b3ed; /* biru soft */
            box-shadow: 0 0 8px #bee3f8;
            background: #fff;
        }

        button {
            background: #63b3ed;
            color: white;
            font-weight: 700;
            border: none;
            padding: 12px 28px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1rem;
            transition: background 0.3s ease;
            flex-shrink: 0;
            box-shadow: 0 4px 8px rgba(99, 179, 237, 0.4);
        }
        button:hover {
            background: #4299e1;
            box-shadow: 0 6px 12px rgba(66, 153, 225, 0.6);
        }

        hr {
            margin: 30px 0;
            border: none;
            border-top: 1px solid #cbd5e0;
        }

        /* Error message */
        .error {
            background: #fed7d7;
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            color: #9b2c2c;
            font-weight: 600;
            box-shadow: inset 2px 2px 6px #fca5a5;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .container {
                padding: 20px;
                margin: 15px;
            }
            form {
                flex-direction: column;
                gap: 10px;
            }
            input[type="text"], input[type="number"], button {
                flex-basis: 100%;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Proses Transaksi</h2>

    <?php if (!empty($error_msg)): ?>
        <div class="error"><?= htmlspecialchars($error_msg) ?></div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Nama Produk</th>
                <th>Jumlah</th>
                <th>Harga</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
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
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" style="text-align:right;">Total</th>
                <th>Rp<?= number_format($grand_total, 0, ',', '.') ?></th>
            </tr>
        </tfoot>
    </table>

    <form method="POST">
        <label for="no_telp">Nomor Telepon Member</label>
        <input type="text" id="no_telp" name="no_telp" placeholder="Masukkan nomor telepon member" value="<?= htmlspecialchars($_POST['no_telp'] ?? '') ?>">
        <input type="hidden" name="total_harga" value="<?= $grand_total ?>">
        <button type="submit" name="cek_member">Cek Member / Lanjut Tanpa Member</button>
    </form>

    <hr>

    <div class="info-list">
        <p><strong>Nama Member:</strong> <?= htmlspecialchars($nama_member) ?></p>
        <p><strong>Status:</strong> <?= ucfirst(htmlspecialchars($status)) ?></p>
        <p><strong>Poin:</strong> <?= number_format($poin_member) ?></p>
        <p><strong>Diskon dari Poin:</strong> Rp<?= number_format($diskon, 0, ',', '.') ?></p>
        <p><strong>Total Bayar:</strong> Rp<?= number_format($grand_total - $diskon, 0, ',', '.') ?></p>
    </div>

    <?php if (isset($_POST['cek_member']) && empty($error_msg)): ?>
    <form method="POST" action="proses_transaksi.php">
        <input type="hidden" name="id_member" value="<?= htmlspecialchars($id_member) ?>">
        <input type="hidden" name="total_harga" value="<?= $grand_total ?>">
        <input type="hidden" name="diskon" value="<?= $diskon ?>">
        <input type="hidden" name="poin_dipakai" value="<?= $poin_dipakai ?>">

        <label for="uang_dibayar">Uang Dibayar</label>
        <input type="number" id="uang_dibayar" name="uang_dibayar" min="0" required placeholder="Masukkan jumlah uang yang dibayar">

        <button type="submit" name="bayar">Selesaikan Transaksi</button>
    </form>
    <?php endif; ?>
</div>
</body>
</html>
