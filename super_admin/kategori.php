<?php
session_start();
$role = $_SESSION['role'] ?? 'kasir';
include_once 'sidebar_admin.php';

// Koneksi database
$conn = new mysqli("localhost", "root", "", "apotek1");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Fungsi cek duplikat nama kategori (untuk tambah)
function isNamaKategoriDuplicate($conn, $nama) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM kategori WHERE nama_kategori = ?");
    $stmt->bind_param("s", $nama);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count > 0;
}

// Fungsi cek duplikat nama kategori (untuk edit)
function isNamaKategoriDuplicateForEdit($conn, $nama, $id) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM kategori WHERE nama_kategori = ? AND id != ?");
    $stmt->bind_param("si", $nama, $id);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count > 0;
}

// Fungsi cek kategori yang masih dipakai produk
function isKategoriDipakaiProduk($conn, $id) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM produk WHERE id_kategori = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count > 0;
}

// Ambil ID tertinggi untuk next id
$result_id = $conn->query("SELECT MAX(id) AS max_id FROM kategori");
$row_id = $result_id->fetch_assoc();
$next_id = $row_id['max_id'] + 1;

// Proses tambah kategori
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id']) && isset($_FILES['gambar'])) {
    $id = $conn->real_escape_string($_POST['id']);
    $nama = $conn->real_escape_string($_POST['nama_kategori']);
    $keterangan = $conn->real_escape_string($_POST['keterangan']);

    // Cek duplikat nama kategori
    if (isNamaKategoriDuplicate($conn, $nama)) {
        $_SESSION['message'] = "Nama kategori sudah digunakan, silakan gunakan nama lain.";
        $_SESSION['msg_type'] = "error";
        header("Location: kategori.php");
        exit();
    }

    $upload_dir = realpath(__DIR__ . '/../uploads') . '/';
    $gambar_name = $_FILES['gambar']['name'];
    $gambar_tmp = $_FILES['gambar']['tmp_name'];
    $filename = time() . '_' . basename($gambar_name);
    $target_file = $upload_dir . $filename;

    if (!move_uploaded_file($gambar_tmp, $target_file)) {
        $_SESSION['message'] = "Gagal upload gambar.";
        $_SESSION['msg_type'] = "error";
        header("Location: kategori.php");
        exit();
    }

    $gambar_path_db = 'uploads/' . $filename;

    $stmt = $conn->prepare("INSERT INTO kategori (id, nama_kategori, keterangan, gambar) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $id, $nama, $keterangan, $gambar_path_db);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $_SESSION['message'] = "Kategori berhasil ditambahkan!";
        $_SESSION['msg_type'] = "success";
    } else {
        $_SESSION['message'] = "Gagal menambahkan kategori.";
        $_SESSION['msg_type'] = "error";
    }
    $stmt->close();

    header("Location: kategori.php");
    exit();
}

// Proses hapus kategori
if (isset($_GET['hapus'])) {
    $id = $conn->real_escape_string($_GET['hapus']);

    // Cek apakah kategori masih dipakai produk
    if (isKategoriDipakaiProduk($conn, $id)) {
        $_SESSION['message'] = "Kategori tidak bisa dihapus karena masih dipakai oleh produk.";
        $_SESSION['msg_type'] = "error";
    } else {
        $conn->query("DELETE FROM kategori WHERE id = '$id'");
        $_SESSION['message'] = "Kategori berhasil dihapus.";
        $_SESSION['msg_type'] = "success";
    }

    header("Location: kategori.php");
    exit();
}

// Proses update kategori
if (isset($_POST['edit_id'])) {
    $id = $conn->real_escape_string($_POST['edit_id']);
    $nama = $conn->real_escape_string($_POST['edit_nama_kategori']);
    $keterangan = $conn->real_escape_string($_POST['edit_keterangan']);

    // Cek duplikat nama kategori untuk edit
    if (isNamaKategoriDuplicateForEdit($conn, $nama, $id)) {
        $_SESSION['message'] = "Nama kategori sudah digunakan oleh kategori lain, silakan gunakan nama lain.";
        $_SESSION['msg_type'] = "error";
        header("Location: kategori.php");
        exit();
    }

    if (!empty($_FILES['edit_gambar']['name'])) {
        $upload_dir = realpath(__DIR__ . '/../uploads') . '/';
        $gambar_name = $_FILES['edit_gambar']['name'];
        $gambar_tmp = $_FILES['edit_gambar']['tmp_name'];
        $filename = time() . '_' . basename($gambar_name);
        $target_file = $upload_dir . $filename;

        if (!move_uploaded_file($gambar_tmp, $target_file)) {
            $_SESSION['message'] = "Gagal upload gambar.";
            $_SESSION['msg_type'] = "error";
            header("Location: kategori.php");
            exit();
        }

        $gambar_path_db = 'uploads/' . $filename;
        $conn->query("UPDATE kategori SET nama_kategori='$nama', keterangan='$keterangan', gambar='$gambar_path_db' WHERE id='$id'");
    } else {
        $conn->query("UPDATE kategori SET nama_kategori='$nama', keterangan='$keterangan' WHERE id='$id'");
    }

    $_SESSION['message'] = "Kategori berhasil diperbarui.";
    $_SESSION['msg_type'] = "success";
    header("Location: kategori.php");
    exit();
}

// Ambil data kategori
$result = $conn->query("SELECT * FROM kategori ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Kategori</title>
  <style>
    /* CSS tetap sama seperti kode sebelumnya */
    body {
      font-family: 'Inter', sans-serif;
      background: #f9fafb;
      margin: 0;
      display: flex;
      min-height: 100vh;
    }

    .container {
      margin-left: 240px;
      padding: 2rem;
      flex-grow: 1;
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
    }

    .btn-tambah {
      background-color: #3b82f6;
      color: white;
      padding: 0.6rem 1rem;
      border: none;
      border-radius: 10px;
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      text-align: left;
      padding: 0.75rem;
      border-bottom: 1px solid #e5e7eb;
    }

    th {
      background-color: #f3f4f6;
    }

    .modal {
      display: none;
      position: fixed;
      z-index: 10;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background-color: rgba(0,0,0,0.5);
      justify-content: center;
      align-items: center;
    }

    .modal-content {
      background: #fff;
      padding: 2rem;
      border-radius: 10px;
      width: 400px;
      position: relative;
    }

    .form-group {
      margin-bottom: 1rem;
    }

    .form-group label {
      display: block;
      margin-bottom: 0.3rem;
    }

    .form-group input,
    .form-group textarea {
      width: 100%;
      padding: 0.5rem;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    .btn-simpan {
      background-color: #10b981;
      color: white;
      padding: 0.5rem 1rem;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }

    .close-btn {
      position: absolute;
      top: 10px;
      right: 15px;
      font-weight: bold;
      cursor: pointer;
    }

    .btn-hapus {
      background-color: #ef4444;
      color: white;
      padding: 0.4rem 0.8rem;
      border: none;
      border-radius: 8px;
      text-decoration: none;
      font-weight: 600;
      cursor: pointer;
    }

    /* Pesan alert */
    .alert {
      padding: 10px;
      margin-bottom: 15px;
      border-radius: 6px;
    }
    .alert-success {
      color: #065f46;
      background-color: #d1fae5;
      border: 1px solid #10b981;
    }
    .alert-error {
      color: #991b1b;
      background-color: #fee2e2;
      border: 1px solid #ef4444;
    }
  </style>
</head>
<body>

<div class="container">

  <!-- Tampilkan pesan sukses / error -->
  <?php if (isset($_SESSION['message'])): ?>
    <div class="alert <?= $_SESSION['msg_type'] === 'success' ? 'alert-success' : 'alert-error' ?>">
      <?= htmlspecialchars($_SESSION['message']) ?>
    </div>
    <?php 
      unset($_SESSION['message']); 
      unset($_SESSION['msg_type']);
    ?>
  <?php endif; ?>

  <div class="header">
    <h2>Data Kategori</h2>
    <button class="btn-tambah" onclick="openModal()">+ Tambah</button>
  </div>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Nama Kategori</th>
        <th>Gambar</th>
        <th>Keterangan</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['id']) ?></td>
          <td><?= htmlspecialchars($row['nama_kategori']) ?></td>
          <td><img src="../<?= htmlspecialchars($row['gambar']) ?>" alt="Gambar" style="width:60px; border-radius: 8px;"></td>
          <td><?= htmlspecialchars($row['keterangan']) ?></td>
          <td>
            <button class="btn-tambah" onclick="editModal(
              '<?= $row['id'] ?>',
              '<?= htmlspecialchars($row['nama_kategori'], ENT_QUOTES) ?>',
              '<?= htmlspecialchars($row['keterangan'], ENT_QUOTES) ?>',
              '<?= isset($row['gambar']) ? htmlspecialchars($row['gambar'], ENT_QUOTES) : '' ?>'
            )">Edit</button>

            <a href="?hapus=<?= $row['id'] ?>" class="btn-hapus" onclick="return confirm('Yakin ingin menghapus kategori ini?')">Hapus</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<!-- Modal Tambah -->
<div id="modalTambah" class="modal">
  <div class="modal-content">
    <span class="close-btn" onclick="closeModal()">&times;</span>
    <h3>Tambah Kategori</h3>
    <form method="POST" enctype="multipart/form-data">
      <div class="form-group">
        <label>ID Kategori</label>
        <input type="text" name="id" value="<?= $next_id ?>" readonly>
      </div>
      <div class="form-group">
        <label>Nama Kategori</label>
        <input type="text" name="nama_kategori" required>
      </div>
      <div class="form-group">
        <label>Keterangan</label>
        <textarea name="keterangan" rows="3" required></textarea>
      </div>
      <div class="form-group">
        <label>Gambar</label>
        <input type="file" name="gambar" accept="image/*" required>
      </div>
      <button type="submit" class="btn-simpan">Simpan</button>
    </form>
  </div>
</div>

<!-- Modal Edit -->
<div id="modalEdit" class="modal">
  <div class="modal-content">
    <span class="close-btn" onclick="closeModalEdit()">&times;</span>
    <h3>Edit Kategori</h3>
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="edit_id" id="edit_id">
      <input type="hidden" name="edit_gambar_lama" id="edit_gambar_lama">

      <div class="form-group">
        <label>Nama Kategori</label>
        <input type="text" name="edit_nama_kategori" id="edit_nama_kategori" required>
      </div>

      <div class="form-group">
        <label>Keterangan</label>
        <textarea name="edit_keterangan" id="edit_keterangan" rows="3" required></textarea>
      </div>

      <div class="form-group">
        <label>Ganti Gambar (Opsional)</label>
        <input type="file" name="edit_gambar" accept="image/*">
      </div>

      <button type="submit" class="btn-simpan">Simpan Perubahan</button>
    </form>
  </div>
</div>

<script>
  function openModal() {
    document.getElementById('modalTambah').style.display = 'flex';
  }

  function closeModal() {
    document.getElementById('modalTambah').style.display = 'none';
  }

  function editModal(id, nama, keterangan, gambar) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_nama_kategori').value = nama;
    document.getElementById('edit_keterangan').value = keterangan;
    document.getElementById('edit_gambar_lama').value = gambar;
    document.getElementById('modalEdit').style.display = 'flex';
  }

  function closeModalEdit() {
    document.getElementById('modalEdit').style.display = 'none';
  }

  window.addEventListener("click", function(event) {
    if (event.target === document.getElementById('modalTambah')) {
      closeModal();
    }
    if (event.target === document.getElementById('modalEdit')) {
      closeModalEdit();
    }
  });
</script>

</body>
</html>
