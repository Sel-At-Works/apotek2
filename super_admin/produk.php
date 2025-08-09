<?php
session_start();
$role = $_SESSION['role'] ?? 'kasir';
include_once 'sidebar_admin.php';

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

$produk = $conn->query("SELECT produk.*, kategori.nama_kategori FROM produk JOIN kategori ON produk.id_kategori = kategori.id ORDER BY produk.id DESC");
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
  </style>
</head>
<body>
<div class="container">
  <h2>Data Produk</h2>
  <button class="btn-tambah" onclick="document.getElementById('modalTambah').style.display='flex'">+ Tambah Produk</button>
  <table>
    <tr>
      <th>ID</th>
      <th>Nama Produk</th>
      <th>Kategori</th>
      <th>Harga Beli(modal)</th>
      <th>Harga Jual</th>
      <th>Stok</th>
      <th>Kadaluarsa</th>
      <th>Gambar</th>
      <th>Barcode</th>
      <th>Aksi</th>
    </tr>
    <?php while($row = $produk->fetch_assoc()): ?>
    <tr>
      <td><?= $row['id'] ?></td>
      <td><?= htmlspecialchars($row['nama_produk']) ?></td>
      <td><?= htmlspecialchars($row['nama_kategori']) ?></td>
      <td>Rp<?= number_format($row['harga_beli'], 0, ',', '.') ?></td>
      <td>Rp<?= number_format($row['harga_jual'], 0, ',', '.') ?></td>
      <td><?= $row['stok'] ?></td>
      <td><?= $row['kadaluarsa'] ?></td>
      <td><img class="gambar-produk" src="../uploads/<?= htmlspecialchars($row['gambar']) ?>" alt="Gambar"></td>
      <td>
  <img src="https://barcode.tec-it.com/barcode.ashx?data=<?= $row['id'] ?>&code=Code128&dpi=96" alt="Barcode">
</td>

      <td class="aksi">
        <a href="#" class="edit"
  onclick="bukaModalEdit(
    '<?= $row['id'] ?>',
    '<?= htmlspecialchars($row['nama_produk'], ENT_QUOTES) ?>',
    '<?= $row['id_kategori'] ?>',
    '<?= $row['harga_beli'] ?>',
    '<?= $row['harga_jual'] ?>',
    '<?= $row['stok'] ?>',
    '<?= $row['kadaluarsa'] ?>'
  )">Edit</a>

        <a href="?hapus=<?= $row['id'] ?>" class="hapus" onclick="return confirm('Yakin ingin menghapus produk ini?')">Hapus</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
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
</body>
</html>