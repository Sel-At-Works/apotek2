<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login Apotek</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-container {
            display: flex;
            width: 900px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .login-image {
            flex: 1;
            background-color: #e6f4ff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-image img {
            max-width: 100%;
            max-height: 400px;
            object-fit: contain;
        }

        .login-form {
            flex: 1;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo img {
            display: block;
            margin: 0 auto 10px;
        }

        .logo h2 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        input {
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
        }

        button {
            padding: 12px;
            background: #3399ff;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background: #2288ee;
        }

        .forgot-password {
            text-align: right;
            margin-top: 10px;
        }

        .forgot-password a {
            color: #3399ff;
            text-decoration: none;
            font-size: 14px;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }

        .error {
            color: red;
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-image">
            <img src="img/logo_apotek.jpg" alt="Ilustrasi Login">
        </div>
        <div class="login-form">
            <div class="logo">
                <!-- <img src="" alt="Logo Apotek" width="80"> -->
                <h2>Apotek Sehat</h2>
            </div>
            <form action="auth.php" method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login">Login</button>
            </form>
            <div class="action-links">
                <p class="signup">Belum punya akun? <a href="tambah_user.php">Daftar di sini</a></p>
                <p class="forgot"><a href="lupa_password.html">Lupa Password?</a></p>
            </div>
            <?php
            if (isset($_SESSION['error'])) {
                echo '<p class="error">' . $_SESSION['error'] . '</p>';
                unset($_SESSION['error']);
            }
            ?>

        </div>
    </div>
</body>

</html>