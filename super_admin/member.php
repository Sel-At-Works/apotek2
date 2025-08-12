<?php
session_start();
include_once 'sidebar_admin.php';

// Koneksi database
$conn = new mysqli("localhost", "root", "", "apotek1");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Tambah member
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama'];
    $no_hp = $_POST['no_hp'];
    $poin = $_POST['poin'];
    $status = $_POST['status'];

    $cek = $conn->query("SELECT * FROM members WHERE no_hp = '$no_hp'");
    if ($cek->num_rows > 0) {
        echo "<script>alert('Nomor HP sudah digunakan!'); window.location='member.php';</script>";
        exit;
    }

    $conn->query("INSERT INTO members (nama, no_hp, poin, status) VALUES ('$nama', '$no_hp', '$poin', '$status')");
    header("Location: member.php");
    exit;
}


// Nonaktifkan member (tidak menghapus dari database)
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $conn->query("UPDATE members SET status = 'nonaktif' WHERE id = $id");
    header("Location: member.php");
    exit;
}



// Edit member
if (isset($_POST['edit'])) {
    $id = $_POST['id_edit'];
    $nama = $_POST['nama'];
    $no_hp = $_POST['no_hp'];
    $poin = $_POST['poin'];
    $status = $_POST['status'];

    $cek = $conn->query("SELECT * FROM members WHERE no_hp = '$no_hp' AND id != $id");
if ($cek->num_rows > 0) {
    echo "<script>alert('Nomor HP sudah digunakan oleh member lain!'); window.location='member.php';</script>";
    exit;
}

    $conn->query("UPDATE members SET nama='$nama', no_hp='$no_hp', poin='$poin', status='$status' WHERE id=$id");
    header("Location: member.php");
    exit;
}

// Ambil data member
$members = $conn->query("SELECT * FROM members ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Member</title>
    <style>
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
        h2 { text-align: center; margin-bottom: 20px; }
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
            background: white;
            border-collapse: collapse;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #007bff;
            color: white;
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
        .btn-batal { background: #6c757d; color: white; }
    </style>
</head>
<body>
<div class="container">
    <h2>Manajemen Member</h2>
    <!-- <button class="btn-tambah" onclick="document.getElementById('modalTambah').style.display='flex'">+ Tambah Member</button> -->

    <table>
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>No HP</th>
            <th>Poin</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        <?php while ($row = $members->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['nama']) ?></td>
            <td><?= htmlspecialchars($row['no_hp']) ?></td>
            <td><?= $row['poin'] ?></td>
            <td><?= $row['status'] ?></td>
            <td class="aksi">
                <a href="#" class="edit" onclick="bukaModalEdit('<?= $row['id'] ?>','<?= htmlspecialchars($row['nama'], ENT_QUOTES) ?>','<?= htmlspecialchars($row['no_hp'], ENT_QUOTES) ?>','<?= $row['poin'] ?>','<?= $row['status'] ?>')">Edit</a>
                <a href="?hapus=<?= $row['id'] ?>" class="hapus" onclick="return confirm('Yakin ingin menghapus member ini?')">Hapus</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<!-- Modal Tambah -->
<div id="modalTambah" class="modal">
  <form method="POST" class="modal-content">
    <h3>Tambah Member</h3>
    <input type="text" name="nama" placeholder="Nama Lengkap" required>
    <input type="text" name="no_hp" placeholder="No HP" required>
    <input type="number" name="poin" placeholder="Poin" value="0" required>
    <select name="status" required>
      <option value="aktif">Aktif</option>
      <option value="nonaktif">Non Aktif</option>
    </select>
    <button type="submit" name="tambah">Simpan</button>
    <button type="button" class="btn-batal" onclick="document.getElementById('modalTambah').style.display='none'">Batal</button>
  </form>
</div>

<!-- Modal Edit -->
<div id="modalEdit" class="modal">
  <form method="POST" class="modal-content">
    <h3>Edit Member</h3>
    <input type="hidden" name="id_edit" id="edit_id">
    <input type="text" name="nama" id="edit_nama" required>
    <input type="text" name="no_hp" id="edit_no_hp" required>
    <input type="number" name="poin" id="edit_poin" required>
    <select name="status" id="edit_status" required>
        <option value="aktif">Aktif</option>
        <option value="nonaktif">Non Aktif</option>
    </select>
    <button type="submit" name="edit">Simpan Perubahan</button>
    <button type="button" class="btn-batal" onclick="document.getElementById('modalEdit').style.display='none'">Batal</button>
  </form>
</div>

<script>
function bukaModalEdit(id, nama, no_hp, poin, status) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_nama').value = nama;
    document.getElementById('edit_no_hp').value = no_hp;
    document.getElementById('edit_poin').value = poin;
    document.getElementById('edit_status').value = status;
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