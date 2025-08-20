<?php
session_start();
// include 'sidebar_kasir.php';

$conn = new mysqli("localhost", "root", "", "apotek1");
if ($conn->connect_error) {
  die("Koneksi gagal: " . $conn->connect_error);
}

// // Cek apakah timer keranjang sudah habis
// // Inisialisasi timer keranjang
// if (!isset($_SESSION['keranjang_timer']) || !is_int($_SESSION['keranjang_timer'])) {
//     $_SESSION['keranjang_timer'] = time();
// }

// Cek apakah timer keranjang sudah habis
if (isset($_SESSION['keranjang_timer']) && is_int($_SESSION['keranjang_timer'])) {
    if (time() - $_SESSION['keranjang_timer'] > 60) { // 60 detik
        // kembalikan stok semua produk
        if(isset($_SESSION['keranjang']) && is_array($_SESSION['keranjang'])) {
            foreach ($_SESSION['keranjang'] as $id_produk => $jumlah_dihapus) {
                $conn->query("UPDATE produk SET stok = stok + $jumlah_dihapus WHERE id = '$id_produk'");
            }
        }
        // kosongkan keranjang
        $_SESSION['keranjang'] = [];
        unset($_SESSION['keranjang_timer']);
    }
}





// // Hapus produk dari keranjang jika sudah lebih dari 1 menit
// foreach ($_SESSION['keranjang_timer'] as $id_produk => $waktu_masuk) {
//     if (time() - $waktu_masuk > 60) { // 60 detik = 1 menit
//         $jumlah_dihapus = $_SESSION['keranjang'][$id_produk] ?? 0;
//         unset($_SESSION['keranjang'][$id_produk]);
//         unset($_SESSION['keranjang_timer']);
//         // Kembalikan stok
//         $conn->query("UPDATE produk SET stok = stok + $jumlah_dihapus WHERE id = '$id_produk'");
//     }
// }

// Masukkan produk ke keranjang
if (isset($_POST['masukkan'])) {
  $id_produk = $_POST['id_produk'];
  $produk = $conn->query("SELECT stok FROM produk WHERE id = '$id_produk'")->fetch_assoc();

  if ($produk && $produk['stok'] > 0) {
    // Kurangi stok di database
    $conn->query("UPDATE produk SET stok = stok - 1 WHERE id = '$id_produk'");

    if (isset($_SESSION['keranjang'][$id_produk])) {
      $_SESSION['keranjang'][$id_produk]++;
    } else {
      $_SESSION['keranjang'][$id_produk] = 1;
    }
     // Set waktu masuk untuk timer
        $_SESSION['keranjang_timer'] = time();
  } else {
    echo "<script>alert('Stok produk tidak mencukupi!'); window.location.href='keranjang.php';</script>";
    exit;
  }
  header("Location: keranjang.php");
  exit;
}


// Tambah jumlah
if (isset($_POST['tambah'])) {
  $id = $_POST['id_produk'];
  $produk = $conn->query("SELECT stok FROM produk WHERE id = '$id'")->fetch_assoc();

  if ($produk && $produk['stok'] > 0) {
    $_SESSION['keranjang'][$id]++;
     $_SESSION['keranjang_timer']= time(); // Reset timer saat ditambah
    $conn->query("UPDATE produk SET stok = stok - 1 WHERE id = '$id'");
  } else {
    echo "<script>alert('Stok produk tidak mencukupi!'); window.location.href='keranjang.php';</script>";
    exit;
  }
  header("Location: keranjang.php");
  exit;
}

// Kurangi jumlah
if (isset($_POST['kurang'])) {
    $id = $_POST['id_produk'];
    if (isset($_SESSION['keranjang'][$id])) {
        $_SESSION['keranjang'][$id]--;
        $conn->query("UPDATE produk SET stok = stok + 1 WHERE id = '$id'");

        if ($_SESSION['keranjang'][$id] <= 0) {
            unset($_SESSION['keranjang'][$id]);
            if (empty($_SESSION['keranjang'])) {
                unset($_SESSION['keranjang_timer']);
            }
        } else {
            $_SESSION['keranjang_timer'] = time(); // Reset timer untuk seluruh keranjang
        }
    }
    header("Location: keranjang.php");
    exit;
}

// Hapus produk dari keranjang
if (isset($_POST['hapus'])) {
    $id = $_POST['id_produk'];
    $jumlah_dihapus = $_SESSION['keranjang'][$id] ?? 0;
    unset($_SESSION['keranjang'][$id]);

    // Jika keranjang kosong, hapus timer
    if (empty($_SESSION['keranjang'])) {
        unset($_SESSION['keranjang_timer']);
    }

    // Kembalikan stok
    $conn->query("UPDATE produk SET stok = stok + $jumlah_dihapus WHERE id = '$id'");
    header("Location: keranjang.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Keranjang Belanja</title>
  <style>
    body {
      font-family: Arial;
      padding: 20px;
      background: #f4f4f4;
    }

    h2 {
      margin-bottom: 20px;
    }

    .keranjang-container {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 20px;
    }

    .card {
      background: #fff;
      padding: 15px;
      border-radius: 10px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .card img {
      width: 100%;
      height: 150px;
      object-fit: cover;
      border-radius: 5px;
      margin-bottom: 10px;
    }

    .card h3 {
      font-size: 18px;
      margin: 0 0 10px;
      text-align: center;
    }

    .card p {
      margin: 5px 0;
    }

    .card form {
      margin-top: 10px;
      display: flex;
      gap: 5px;
      flex-wrap: wrap;
      justify-content: center;
    }

    .card button {
      padding: 6px 12px;
      border: none;
      border-radius: 5px;
      background-color: #3498db;
      color: white;
      cursor: pointer;
    }

    .card button:hover {
      background-color: #2980b9;
    }

    .card button[name="hapus"] {
      background-color: #e74c3c;
    }

    .card button[name="hapus"]:hover {
      background-color: #c0392b;
    }

    .total {
      margin-top: 30px;
      font-size: 20px;
      font-weight: bold;
    }
  </style>
</head>

<body>
  <!-- ✅ Input untuk scanner
<input type="text" id="scan-input" placeholder="Scan produk di sini..." autofocus tabindex="1"> -->

  <a href="produk.php" style="
  display: inline-block;
  margin-bottom: 20px;
  padding: 10px 20px;
  background-color: #2ecc71;
  color: white;
  text-decoration: none;
  border-radius: 5px;
  font-weight: bold;
">← Kembali ke Produk</a>

  <h2>Isi Keranjang</h2>
  <?php if (isset($_SESSION['keranjang_timer']) && !empty($_SESSION['keranjang'])): ?>
<div style="margin: 20px 0 40px 0; width: 100%; max-width: 400px;">
  <div style="background: #ddd; border-radius: 10px; overflow: hidden; height: 25px;">
    <div id="progress-bar" style="height: 100%; background-color: #27ae60; width: 100%; text-align: center; color: white; line-height: 25px; font-weight: bold;">
      60s
    </div>
  </div>
</div>

<script>
  let waktu = <?= 60 - (time() - $_SESSION['keranjang_timer']) ?>;
  const bar = document.getElementById("progress-bar");

  const countdown = setInterval(() => {
    if (waktu <= 0) {
      clearInterval(countdown);
      bar.innerHTML = "⏰ Waktu habis!";
      bar.style.backgroundColor = "#e74c3c";
      setTimeout(() => location.reload(), 1000);
    } else {
      bar.innerHTML = waktu + " detik";
      if (waktu <= 10) {
        bar.style.backgroundColor = "#e74c3c";
      } else if (waktu <= 30) {
        bar.style.backgroundColor = "#f1c40f";
      }
      bar.style.width = (waktu / 60 * 100) + "%";
      waktu--;
    }
  }, 1000);
</script>
<?php endif; ?>


  <div class="keranjang-container">
<?php
$total = 0;
$sisa_waktu = null;
if (isset($_SESSION['keranjang_timer'])) {
    $sisa_waktu = 60 - (time() - $_SESSION['keranjang_timer']);
    if ($sisa_waktu < 0) $sisa_waktu = 0;
}

// Cek dulu apakah keranjang ada dan tidak kosong
if (!empty($_SESSION['keranjang'])):
    foreach ($_SESSION['keranjang'] as $id_produk => $qty):
        $produk = $conn->query("SELECT * FROM produk WHERE id = '$id_produk'")->fetch_assoc();
        $subtotal = $produk['harga_jual'] * $qty;
        $total += $subtotal;
?>
    <div class="card">
        <img src="../uploads/<?= htmlspecialchars($produk['gambar']) ?>" alt="<?= htmlspecialchars($produk['nama_produk']) ?>">
        <h3><?= htmlspecialchars($produk['nama_produk']) ?></h3>
        <p>Jumlah: <?= $qty ?></p>
        <p>Harga: Rp<?= number_format($produk['harga_jual'], 0, ',', '.') ?></p>
        <p>Subtotal: Rp<?= number_format($subtotal, 0, ',', '.') ?></p>

        <form method="post">
          <input type="hidden" name="id_produk" value="<?= $id_produk ?>">
          <button type="submit" name="tambah">+</button>
          <button type="submit" name="kurang">-</button>
          <button type="submit" name="hapus">Hapus</button>
        </form>
    </div>
<?php
    endforeach;
else:
    echo "<p>Keranjang kosong.</p>";
endif;
?>


  <!-- TOTAL & TOMBOL TRANSAKSI TENGAH BAWAH (AGAK NAIK) -->
  <div style="
  position: fixed;
  bottom: 40px; /* Tidak terlalu bawah */
  left: 0;
  width: 100%;
  text-align: center;
  z-index: 999;
  pointer-events: none;
">
    <div style="display: inline-block; pointer-events: auto;">
      <p style="font-size: 18px; margin: 0 0 10px 0; color: #000;">
        Total: <strong>Rp<?= number_format($total, 0, ',', '.') ?></strong>
      </p>
      <form action="transaksi.php" method="POST">
        <input type="hidden" name="no_telp" value="<?= $_SESSION['no_hp'] ?? '0000000000' ?>">
        <input type="hidden" name="total_harga" value="<?= $total ?>">
        <input type="hidden" name="uang_dibayar" value="<?= $total ?>">
        <input type="hidden" name="kembalian" value="0">
        <button type="submit" style="padding: 12px 24px; background-color: #27ae60; color: white; border: none; font-weight: bold; border-radius: 5px;">
          Lanjut ke Transaksi
        </button>
      </form>
    </div>
  </div>
  </div>
  <script>
    // Fokuskan input ketika halaman dimuat
    window.onload = function() {
      document.getElementById('scan-input').focus();
    };

    // Proses jika user tekan Enter saat scan barcode
    document.getElementById('scan-input').addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        const kode = this.value.trim();
        if (kode) {
          // Kirim produk ke keranjang lewat URL
          window.location.href = 'tambah_ke_keranjang.php?kode=' + encodeURIComponent(kode);
          this.value = ""; // Reset input
        }
      }
    });

    // Fokus ke input saat halaman dibuka
    window.onload = function() {
      document.getElementById('scan-input').focus();
    };

    // Tangkap input saat user scan dan tekan ENTER
    document.getElementById('scan-input').addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        const kode = this.value.trim();
        if (kode !== '') {
          // Arahkan ke file pemroses keranjang
          window.location.href = `tambah_ke_keranjang.php?kode=${encodeURIComponent(kode)}`;
        }
        this.value = ''; // Kosongkan input
      }
    });

    // Fokus ulang jika user klik tempat lain
    document.addEventListener('click', function() {
      document.getElementById('scan-input').focus();
    });
  </script>
</body>

</html>