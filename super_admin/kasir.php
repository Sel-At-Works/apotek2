<?php
session_start();
$role = $_SESSION['role'] ?? 'kasir';
include_once 'sidebar_admin.php';

// Koneksi database
$conn = new mysqli("localhost", "root", "", "apotek1");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
 // Hitung ulang superadmin setelah POST
function cek_superadmin($conn) {
    $res = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'superadmin'");
    return $res->fetch_assoc()['total'] > 0;
}

$superadminExists = cek_superadmin($conn);

//

if (isset($_POST['tambah'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role'];
    $email = $_POST['email'];


    // Cek email saja (karena username boleh duplikat)
   $cek_duplikat_email = $conn->query("SELECT * FROM users WHERE email = '$email'");
if ($cek_duplikat_email->num_rows > 0) {
    echo "<script>alert('Email sudah digunakan oleh user lain!'); window.location='kasir.php';</script>";
    exit;
}

    // Upload gambar
   $fotoName = '';
if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
    $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $fotoName = time() . '_' . uniqid() . '.' . $ext;
    $uploadDir = '../uploads/'; // Sesuaikan: folder uploads ada DI LUAR super_admin
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    move_uploaded_file($_FILES['foto']['tmp_name'], $uploadDir . $fotoName);
}


    // Simpan ke database (tambahkan kolom gambar)
    $conn->query("INSERT INTO users (username, password, role, email, gambar, status_login) 
                  VALUES ('$username', '$password', '$role', '$email', '$fotoName', 0)");

    header("Location: kasir.php");
    exit;
}


// Hapus user (dengan proteksi superadmin)
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];

    // Cek role dan ID user yang ingin dihapus
    $result = $conn->query("SELECT role FROM users WHERE id = $id");
    $data = $result->fetch_assoc();

    // Cegah hapus superadmin
    if ($data['role'] == 'superadmin') {
        echo "<script>alert('Super Admin tidak boleh dihapus!'); window.location='kasir.php';</script>";
        exit;
    }

    // Cegah kasir menghapus dirinya sendiri
   // Cegah hanya jika KASIR menghapus DIRINYA SENDIRI
if ($_SESSION['role'] === 'kasir' && $_SESSION['id_user'] == $id) {
    echo "<script>alert('Anda tidak bisa menghapus akun Anda sendiri!'); window.location='kasir.php';</script>";
    exit;
}

// Cek apakah user sedang login
$cek_login = $conn->query("SELECT status_login FROM users WHERE id = $id");
$login_data = $cek_login->fetch_assoc();
if ($login_data['status_login'] == 1) {
    echo "<script>alert('User ini sedang login dan tidak bisa dihapus!'); window.location='kasir.php';</script>";
    exit;
}



    // Lanjut hapus jika aman
    $conn->query("DELETE FROM users WHERE id = $id");
    header("Location: kasir.php");
    exit;
}


if (isset($_POST['edit'])) {
    $id = $_POST['id_edit'];

    // ✅ Cegah kasir mengedit user lain (boleh edit dirinya sendiri saja)
    if ($_SESSION['role'] === 'kasir' && $_SESSION['id_user'] != $id) {
        echo "<script>alert('Anda tidak dapat mengedit user lain!'); window.location='kasir.php';</script>";
        exit;
    }

    // ✅ Cegah user non-superadmin mengedit superadmin
    if ($_SESSION['role'] !== 'superadmin') {
        $cek_target = $conn->query("SELECT role FROM users WHERE id = $id");
        $target = $cek_target->fetch_assoc();
        if ($target['role'] === 'superadmin') {
            echo "<script>alert('Anda tidak memiliki hak untuk mengedit Super Admin!'); window.location='kasir.php';</script>";
            exit;
        }
    }

    // lanjut update
    $username = $_POST['username'];
    $role = $_POST['role'];
    $email = $_POST['email'];

    // Validasi duplikat email, upload foto, update ke DB, dst...



    // (lanjut proses upload gambar, password, update, dll)
    $fotoName = '';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $fotoName = time() . '_' . uniqid() . '.' . $ext;
        $uploadDir = '../uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        move_uploaded_file($_FILES['foto']['tmp_name'], $uploadDir . $fotoName);
    }

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $query = "UPDATE users SET username='$username', password='$password', role='$role', email='$email'";
    } else {
        $query = "UPDATE users SET username='$username', role='$role', email='$email'";
    }

    if ($fotoName != '') {
        $query .= ", gambar='$fotoName'";
    }

    $query .= " WHERE id=$id";
    $conn->query($query);

    header("Location: kasir.php");
    exit;
}


// Ambil semua user
$users = $conn->query("SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen User</title>
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

        h2 {
            text-align: center;
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

        .btn-batal {
            background: #6c757d;
            color: white;
        }
   a.disabled, span.disabled {
    pointer-events: none;
    background-color: #ccc !important;
    color: #666 !important;
    cursor: not-allowed !important;
    text-decoration: none;
}


    </style>
</head>
<body>

<div class="container">
    <h2>Manajemen User</h2>
    <button class="btn-tambah" onclick="document.getElementById('modalTambah').style.display='flex'">+ Tambah User</button>

    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Role</th>
            <th>email</th>
            <th>gambar</th>
            <th>Status Login</th>
            <th>Aksi</th>
        </tr>
<?php while ($row = $users->fetch_assoc()): ?>
<?php
    $isLoginKasir = $row['role'] === 'kasir' && $row['status_login'] == 1;
?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= htmlspecialchars($row['username']) ?></td>
    <td><?= $row['role'] ?></td>
    <td><?= htmlspecialchars($row['email']) ?></td>
    <td>
        <img src="../uploads/<?= htmlspecialchars($row['gambar']) ?>" alt="Foto" style="width: 40px; height: 40px; border-radius: 50%;">
    </td>
    <td><?= ($row['status_login'] == 1) ? 'Aktif' : 'Non Aktif' ?></td>
    <td class="aksi">
        <?php if ($row['role'] !== 'superadmin'): ?>
            <?php if ($isLoginKasir): ?>
                <a href="javascript:void(0);" class="edit disabled" title="Tidak bisa edit user yang sedang login">Edit</a>
                <a href="javascript:void(0);" class="hapus disabled" title="Tidak bisa hapus user yang sedang login">Hapus</a>
            <?php else: ?>
                <a href="#" class="edit" onclick="bukaModalEdit('<?= $row['id'] ?>','<?= htmlspecialchars($row['username'], ENT_QUOTES) ?>','<?= $row['role'] ?>','<?= htmlspecialchars($row['email'], ENT_QUOTES) ?>')">Edit</a>

                <?php if ($_SESSION['role'] !== 'kasir'): ?>
                    <a href="?hapus=<?= $row['id'] ?>" class="hapus" onclick="return confirm('Yakin ingin menghapus user ini?')">Hapus</a>
                <?php else: ?>
                    <a href="javascript:void(0);" class="hapus disabled" title="Kasir tidak diizinkan menghapus">Hapus</a>
                <?php endif; ?>
            <?php endif; ?>
        <?php else: ?>
            <span style="color: gray;">(Terkunci)</span>
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>

    </table>
</div>

<!-- Modal Tambah -->
<!-- Modal Tambah -->
<div id="modalTambah" class="modal">
  <form method="POST" class="modal-content" enctype="multipart/form-data">
    <h3>Tambah User</h3>

    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>

    <select name="role" required>
      <option value="">-- Pilih Role --</option>
      <option value="kasir">Kasir</option>
      <?php if (!$superadminExists): ?>
          <option value="superadmin">Super Admin</option>
      <?php endif; ?>
    </select>

    <input type="text" name="email" placeholder="email" required>
    <input type="file" name="foto" accept="image/*"> <!-- ✅ harus di dalam form -->

    <button type="submit" name="tambah">Simpan</button>
    <button type="button" class="btn-batal" onclick="document.getElementById('modalTambah').style.display='none'">Batal</button>
  </form>
</div>

    
    
    <!-- ✅ Input Upload Foto
    <input type="file" name="foto" accept="image/*">

    <button type="submit" name="tambah">Simpan</button>
    <button type="button" class="btn-batal" onclick="document.getElementById('modalTambah').style.display='none'">Batal</button>
  </form>
</div> -->

<!-- 
    <input type="text" name="nama" placeholder="Nama Lengkap" required>
    <button type="submit" name="tambah">Simpan</button>
    <button type="button" class="btn-batal" onclick="document.getElementById('modalTambah').style.display='none'">Batal</button>
  </form>
</div> -->

<!-- Modal Edit -->
<div id="modalEdit" class="modal">
  <form method="POST" class="modal-content" enctype="multipart/form-data">
    <h3>Edit User</h3>
    <input type="hidden" name="id_edit" id="edit_id">
    <input type="text" name="username" id="edit_username" required>
    <input type="text" name="email" id="edit_email" required>
    <input type="password" name="password" placeholder="Password Baru (opsional)">
    <input type="file" name="foto" accept="image/*">
   <select name="role" id="edit_role" required>
    <option value="kasir">Kasir</option>
    <?php if (!$superadminExists): ?>
        <option value="superadmin">Super Admin</option>
    <?php endif; ?>
</select>

    <button type="submit" name="edit">Simpan Perubahan</button>
    <button type="button" class="btn-batal" onclick="document.getElementById('modalEdit').style.display='none'">Batal</button>
  </form>
</div>

<script>
function bukaModalEdit(id, username, role, email) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_username').value = username;
    document.getElementById('edit_role').value = role;
    document.getElementById('edit_email').value = email;
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
