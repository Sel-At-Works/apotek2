<?php
session_start();
$role = $_SESSION['role'] ?? 'kasir';
include_once 'sidebar_kasir.php';

$conn = new mysqli("localhost", "root", "", "apotek1");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Tambah produk
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama_produk'];
    $id_kategori = $_POST['id_kategori'];
    $harga_beli = $_POST['harga_beli'];
    $harga = $_POST['harga_jual'];
    $stok = $_POST['stok'];
    $kadaluarsa = $_POST['kadaluarsa'];

    $gambar = $_FILES['gambar']['name'];
    $tmp = $_FILES['gambar']['tmp_name'];
    $folder = '../uploads/' . $gambar;

    if (move_uploaded_file($tmp, $folder)) {
        $conn->query("INSERT INTO produk (nama_produk, id_kategori, harga_beli, harga_jual, stok, kadaluarsa, gambar) VALUES ('$nama', '$id_kategori', '$harga_beli','$harga', '$stok', '$kadaluarsa', '$gambar')");
        header("Location: produk.php");
        exit;
    }
}

if (isset($_POST['edit'])) {
    $id_edit = $_POST['id_edit'];
    $nama = $_POST['nama_produk'];
    $id_kategori = $_POST['id_kategori'];
    $harga = $_POST['harga_jual'];
    $harga_beli = $_POST['harga_beli'];
    $stok = $_POST['stok'];
    $kadaluarsa = $_POST['kadaluarsa'];

    // Periksa apakah ada gambar baru diupload
    if (!empty($_FILES['gambar_baru']['name'])) {
        $gambar_baru = $_FILES['gambar_baru']['name'];
        $tmp = $_FILES['gambar_baru']['tmp_name'];
        $folder = '../uploads/' . $gambar_baru;
        move_uploaded_file($tmp, $folder);

        // Update dengan gambar baru
       $conn->query("UPDATE produk SET 
    nama_produk='$nama',
    id_kategori='$id_kategori',
    harga_beli='$harga_beli',  -- ✅ Tambahkan baris ini
    harga_jual='$harga',
    stok='$stok',
    kadaluarsa='$kadaluarsa',
    gambar='$gambar_baru'
    WHERE id='$id_edit'");

    } else {
      $conn->query("UPDATE produk SET 
    nama_produk='$nama',
    id_kategori='$id_kategori',
    harga_beli='$harga_beli',  -- ✅ Tambahkan ini juga
    harga_jual='$harga',
    stok='$stok',
    kadaluarsa='$kadaluarsa'
    WHERE id='$id_edit'");
    header("Location: produk.php");
    exit;
    }
}


// Hapus produk
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $conn->query("DELETE FROM produk WHERE id = '$id'");
    header("Location: produk.php");
    exit;
}

$filter_kategori = $_GET['filter_kategori'] ?? '';

$tanggalHariIni = date('Y-m-d');

if ($filter_kategori !== '') {
    $produk = $conn->query("SELECT produk.*, kategori.nama_kategori 
                            FROM produk 
                            JOIN kategori ON produk.id_kategori = kategori.id 
                            WHERE produk.id_kategori = '$filter_kategori'
                              AND produk.kadaluarsa >= '$tanggalHariIni'
                            ORDER BY produk.id DESC");
} else {
    $produk = $conn->query("SELECT produk.*, kategori.nama_kategori 
                            FROM produk 
                            JOIN kategori ON produk.id_kategori = kategori.id 
                            WHERE produk.kadaluarsa >= '$tanggalHariIni'
                            ORDER BY produk.id DESC");
}


$kategoriList = $conn->query("SELECT * FROM kategori");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Produk</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f4f4;
      margin: 0;
      padding: 0;
    }
    .container {
      margin-left: 250px;
      padding: 20px;
    }
    h2 {
      margin-bottom: 20px;
    }
    .btn-tambah {
      background: #28a745;
      color: white;
      padding: 10px 15px;
      border: none;
      border-radius: 5px;
      margin-bottom: 20px;
      cursor: pointer;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    th, td {
      padding: 12px;
      border: 1px solid #ddd;
      text-align: center;
    }
    th {
      background-color: #f8f8f8;
    }
    .gambar-produk {
      max-width: 80px;
      max-height: 80px;
      object-fit: cover;
      border-radius: 5px;
    }
    .aksi a {
      padding: 6px 12px;
      color: white;
      border-radius: 5px;
      margin: 0 3px;
      text-decoration: none;
      font-size: 13px;
    }
    .aksi a.edit { background: #007bff; }
    .aksi a.hapus { background: #dc3545; }
    .modal {
      display: none;
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(0, 0, 0, 0.5);
      justify-content: center;
      align-items: center;
      z-index: 999;
    }
    .modal-content {
      background: white;
      padding: 20px;
      width: 90%;
      max-width: 400px;
      border-radius: 10px;
    }
    .modal-content input, .modal-content select, .modal-content button {
      width: 100%;
      padding: 10px;
      margin: 10px 0;
    }
    .btn-batal {
      background: #6c757d;
      color: white;
    }
    /* Gaya untuk form filter kategori */
form select#filter_kategori {
  padding: 10px 15px;
  border-radius: 8px;
  border: 1px solid #ccc;
  font-size: 14px;
  background-color: #fff;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
  transition: border 0.3s, box-shadow 0.3s;
}

form select#filter_kategori:focus {
  border-color: #007bff;
  outline: none;
  box-shadow: 0 0 5px rgba(0,123,255,0.5);
}

form label[for="filter_kategori"] {
  margin-right: 10px;
  font-size: 16px;
  color: #333;
}

.reset-filter {
  margin-left: 15px;
  color: #dc3545;
  font-weight: bold;
  text-decoration: none;
  transition: color 0.3s;
}

.reset-filter:hover {
  color: #a71d2a;
  text-decoration: underline;
}

  </style>
</head>
<body>
<div class="container">
  <input type="text" id="scan-input" placeholder="Scan produk di sini..." autofocus style="opacity:0; position:absolute;">
  <h2>Daftar Produk</h2>
  <button onclick="bukaScanner()" class="btn-tambah" style="background:#007bff;">📷 Scan Barcode</button>
  <!-- <input type="text" id="scan-input" placeholder="Scan produk di sini..." autofocus tabindex="1"> -->

<div id="scanner-modal" class="modal">
  <div class="modal-content">
    <h3>Scan Barcode Produk</h3>

    <!-- 👇 Input untuk scanner barcode fisik (USB) -->
    <input type="text" id="input-scan-alat" placeholder="Scan barcode di sini..." autofocus
      onkeypress="handleInputScanAlat(event)"
      style="opacity: 0; position: absolute;">

    <!-- 👇 Area kamera scanner (via Html5Qrcode) -->
    <div id="reader" style="width:100%;"></div>

    <button onclick="tutupScanner()" class="btn-batal">Tutup</button>
  </div>
</div>

<div style="margin-bottom: 20px;">
    <strong>Filter Kategori:</strong><br>
    <a href="produk.php" class="btn-filter <?= ($filter_kategori === '') ? 'active' : '' ?>">Semua</a>
    <?php
    $kategoriFilter = $conn->query("SELECT * FROM kategori");
    while ($kat = $kategoriFilter->fetch_assoc()) {
        $active = ($filter_kategori == $kat['id']) ? 'active' : '';
        echo "<a href='produk.php?filter_kategori={$kat['id']}' class='btn-filter $active'>{$kat['nama_kategori']}</a>";
    }
    ?>
</div>

<style>
.btn-filter {
    display: inline-block;
    padding: 8px 15px;
    margin: 5px 5px 0 0;
    background-color: #f1f1f1;
    color: #333;
    text-decoration: none;
    border-radius: 6px;
    transition: all 0.3s ease;
    font-size: 14px;
}
.btn-filter:hover {
    background-color: #007bff;
    color: white;
}
.btn-filter.active {
    background-color: #007bff;
    color: white;
    font-weight: bold;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}
</style>



  <div style="display: flex; flex-wrap: wrap; gap: 20px;">
    <?php while($row = $produk->fetch_assoc()): ?>
    <div style="background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 250px; padding: 15px;">
      <img src="../uploads/<?= htmlspecialchars($row['gambar']) ?>" alt="Produk" style="width: 100%; height: 160px; object-fit: cover; border-radius: 10px;">
      <h4 style="margin: 10px 0;"><?= htmlspecialchars($row['nama_produk']) ?></h4>
      <p><strong>Kategori:</strong> <?= htmlspecialchars($row['nama_kategori']) ?></p>
      <p><strong>Harga:</strong> Rp<?= number_format($row['harga_jual'], 0, ',', '.') ?></p>
      <p><strong>Stok:</strong> <?= $row['stok'] ?></p>
      <p><strong>Kadaluarsa:</strong> <?= $row['kadaluarsa'] ?></p>
  <form method="post" action="keranjang.php">
    <input type="hidden" name="id_produk" value="<?= $row['id'] ?>"> <!-- ID produk -->
    <button type="submit" name="masukkan" style="width: 100%; padding: 8px; background: #28a745; border: none; color: white; border-radius: 5px;">
        Masukkan ke Keranjang
    </button>
</form>
    </div>
    <?php endwhile; ?>
  </div>
</div>


<!-- Modal Tambah -->
<div id="modalTambah" class="modal">
  <form method="POST" enctype="multipart/form-data" class="modal-content">
    <h3>Tambah Produk</h3>
    <input type="text" name="nama_produk" placeholder="Nama Produk" required>
    <select name="id_kategori" required>
      <option value="">-- Pilih Kategori --</option>
      <?php while($kat = $kategoriList->fetch_assoc()): ?>
        <option value="<?= $kat['id'] ?>"><?= $kat['nama_kategori'] ?></option>
      <?php endwhile; ?>
    </select>
    <input type="number" name="harga_beli" placeholder="Harga Beli (modal)" required>
    <input type="number" name="harga_jual" placeholder="Harga Jual" required>
    <input type="number" name="stok" placeholder="Stok" required>
    <input type="date" name="kadaluarsa" required>
    <input type="file" name="gambar" accept="image/*" required>
    <button type="submit" name="tambah">Simpan</button>
    <button type="button" class="btn-batal" onclick="document.getElementById('modalTambah').style.display='none'">Batal</button>
  </form>
</div>

<!-- Modal Edit -->
<div id="modalEdit" class="modal">
 <form method="POST" enctype="multipart/form-data" class="modal-content">
    <h3>Edit Produk</h3>
    <input type="hidden" name="id_edit" id="edit_id">
    <input type="text" name="nama_produk" id="edit_nama" placeholder="Nama Produk" required>
    <select name="id_kategori" id="edit_kategori" required>
      <?php
      $kategori = $conn->query("SELECT * FROM kategori");
      while($kat = $kategori->fetch_assoc()) {
          echo '<option value="'.$kat['id'].'">'.$kat['nama_kategori'].'</option>';
      }
      ?>
    </select>
       <input type="number" name="harga_beli" id="edit_beli" placeholder="Harga_beli (modal)" required>
    <input type="number" name="harga_jual" id="edit_harga" placeholder="Harga Jual" required>
    <input type="number" name="stok" id="edit_stok" placeholder="Stok" required>
    <label>Ganti Gambar (opsional)</label>
<input type="file" name="gambar_baru" accept="image/*">
    <input type="date" name="kadaluarsa" id="edit_kadaluarsa" required>
    <button type="submit" name="edit">Simpan Perubahan</button>
    <button type="button" class="btn-batal" onclick="document.getElementById('modalEdit').style.display='none'">Batal</button>
  </form>
</div>

<script>
function bukaModalEdit(id, nama, kategori, harga_beli,harga, stok, kadaluarsa) {
  document.getElementById('edit_id').value = id;
  document.getElementById('edit_nama').value = nama;
   document.getElementById('edit_beli').value = harga_beli;
  document.getElementById('edit_harga').value = harga;
  document.getElementById('edit_stok').value = stok;
  document.getElementById('edit_kadaluarsa').value = kadaluarsa;

  const select = document.getElementById('edit_kategori');
  for (let i = 0; i < select.options.length; i++) {
    if (select.options[i].value == kategori) {
      select.selectedIndex = i;
      break;
    }
  }
  document.getElementById('modalEdit').style.display = 'flex';
}

window.onclick = function(event) {
  if (event.target.classList.contains('modal')) {
    event.target.style.display = "none";
  }
};
</script>
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
let scanner;

function bukaScanner() {
  Html5Qrcode.getCameras().then(cameras => {
    if (cameras.length > 0) {
      document.getElementById('scanner-modal').style.display = 'flex';
      document.getElementById("input-scan-alat").focus(); // Fokus input alat scan

      scanner = new Html5Qrcode("reader");
      scanner.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: 250 },
        (decodedText) => {
          scanner.stop().then(() => {
            playSuccessVoice("Produk berhasil dipindai");
            kirimKeKeranjang(decodedText);
            tutupScanner();
          });
        },
        (error) => {
          console.error("Scan error:", error);
        }
      );
    } else {
      alert("Tidak ada kamera, gunakan alat scanner.");
      document.getElementById("scanner-modal").style.display = 'flex';
      document.getElementById("input-scan-alat").focus(); // Fokus input alat scan
    }
  }).catch(err => {
    alert("Gagal mendeteksi kamera.");
    document.getElementById("scanner-modal").style.display = 'flex';
    document.getElementById("input-scan-alat").focus();
  });
}


function tutupScanner() {
  if (scanner) scanner.stop();
  document.getElementById('scanner-modal').style.display = 'none';
}

function kirimKeKeranjang(id_produk) {
  const form = document.createElement("form");
  form.method = "POST";
  form.action = "keranjang.php";

  const input = document.createElement("input");
  input.type = "hidden";
  input.name = "id_produk";
  input.value = id_produk;

  const tombol = document.createElement("input");
  tombol.type = "hidden";
  tombol.name = "masukkan";

  form.appendChild(input);
  form.appendChild(tombol);
  document.body.appendChild(form);
  form.submit();
}
</script>
<script>
document.getElementById('scan-input').addEventListener('keypress', function(e) {
  if (e.key === 'Enter') {
    e.preventDefault(); // Mencegah reload
    const kode = this.value.trim();
    if (kode) {
      kirimKeKeranjang(kode);
      this.value = ""; // Kosongkan input setelah scan
    }
}
});
</script>
<script>
function playSuccessVoice(text = "Scan berhasil") {
  const utterance = new SpeechSynthesisUtterance(text);
  utterance.lang = 'id-ID'; // Bahasa Indonesia
  speechSynthesis.speak(utterance);
}
</script>
<script>
window.onload = function() {
  document.getElementById('scan-input').focus();
};
</script>
<script>
function handleInputScanAlat(e) {
  if (e.key === 'Enter') {
    e.preventDefault();
    const kode = e.target.value.trim();
    if (kode) {
      kirimKeKeranjang(kode);
      e.target.value = ""; // kosongkan setelah scan
      tutupScanner(); // jika mau tutup otomatis
    }
  }
}
</script>

</body>
</html>