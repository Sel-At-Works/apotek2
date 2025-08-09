<?php
include 'config.php';

// Cek apakah sudah ada Super Admin
$superadmin_ada = false;
$cek_superadmin = mysqli_query($koneksi, "SELECT * FROM users WHERE role='superadmin'");
if (mysqli_num_rows($cek_superadmin) > 0) {
    $superadmin_ada = true;
}

$berhasil = false; // default tidak sukses

if (isset($_POST['simpan'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role     = $_POST['role'];
    $email     = mysqli_real_escape_string($koneksi, $_POST['email']);
    $status_login = 0;

    // Cek apakah username sudah digunakan
    $cek_username = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");
    if (mysqli_num_rows($cek_username) > 0) {
        echo "<script>alert('❌ Username sudah digunakan!'); window.location='tambah_user.php';</script>";
        exit;
    }

    // Cegah tambah superadmin kalau sudah ada
    if ($role == 'superadmin' && $superadmin_ada) {
        echo "<script>alert('⚠️ Super Admin hanya boleh 1!'); window.location='tambah_user.php';</script>";
        exit;
    }

    // Simpan user
    $simpan = mysqli_query($koneksi, "INSERT INTO users (username, password, role, email, status_login) 
                                      VALUES ('$username', '$password', '$role', '$email', '$status_login')");

    if ($simpan) {
        $berhasil = true;
    } else {
        echo "<script>alert('❌ Gagal menambahkan user. " . mysqli_error($koneksi) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah User</title>
    <style>
        * {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f2f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-container, .success-container {
            background-color: #fff;
            padding: 30px;
            width: 100%;
            max-width: 420px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease-in-out;
            text-align: center;
        }

        h2 {
            margin-bottom: 25px;
            color: #333;
        }

        input, select, button {
            width: 100%;
            padding: 12px 14px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.2s;
        }

        input:focus, select:focus {
            border-color: #007bff;
            outline: none;
        }

        button {
            background-color: #007bff;
            color: #fff;
            font-weight: bold;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease-in-out;
        }

        button:hover {
            background-color: #0056b3;
        }

        .note {
            font-size: 13px;
            color: #666;
            text-align: center;
            margin-top: 10px;
        }

        .success-icon {
            font-size: 40px;
            color: green;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

<?php if ($berhasil): ?>
    <div class="success-container">
        <div class="success-icon">✅</div>
        <h2>User berhasil ditambahkan</h2>
        <a href="login.php">
            <button>Login Sekarang</button>
        </a>
    </div>
<?php else: ?>
    <form class="form-container" method="POST">
        <h2>Tambah User Baru</h2>

        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="text" name="email" placeholder="email" required>

        <select name="role" required>
            <option value="">-- Pilih Role --</option>
            <option value="kasir">Kasir</option>
            <?php if (!$superadmin_ada): ?>
                <option value="superadmin">Super Admin</option>
            <?php endif; ?>
        </select>

        <button type="submit" name="simpan">Simpan User</button>
        <div class="note">Pastikan data yang diinputkan benar</div>
    </form>
<?php endif; ?>

</body>

</html>
