<?php
session_start();
include "koneksi.php";

if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        header("Location: admin_aktivitas.php");
    } else {
        header("Location: aktivitas_fisik.php");
    }
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $query = "SELECT * FROM admin WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        
        $_SESSION['user_logged_in'] = true;
        $_SESSION['username'] = $row['username']; 
        $_SESSION['role'] = $row['role']; 
        
        if ($row['role'] === 'admin') {
            header("Location: admin_aktivitas.php");
        } else {
            header("Location: aktivitas_fisik.php");
        }
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Hidup Sehat</title>
    <style>
        body { background-color: #9bdba3; font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-card { display: flex; background: white; border-radius: 24px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.08); width: 920px; max-width: 95%; min-height: 500px; align-items: stretch;}
        .left-side { background-color: #349f4c; color: white; padding: 60px 45px; width: 42%; min-width: 340px; display: flex; flex-direction: column; justify-content: center; box-sizing: border-box; flex-shrink: 0;}
        .left-side h1 { margin: 0 0 25px 0; font-size: 64px; font-weight: bold; line-height: 1.05; letter-spacing: 0.5px;}
        .left-side p { margin: 0; font-size: 15px; line-height: 1.6; opacity: 0.9; }
        .right-side { padding: 50px 65px; width: 58%; display: flex; flex-direction: column; justify-content: center; box-sizing: border-box;}
        .right-side h2 { margin: 0; color: #14532d; font-size: 42px; font-weight: bold;}
        .right-side .subtitle { color: #555; font-size: 14px; margin: 6px 0 35px 0; }
        .form-group { margin-bottom: 22px; }
        .form-group label { display: block; font-size: 14px; font-weight: bold; margin-bottom: 10px; color: #333; }
        .form-group input { width: 100%; padding: 15px 18px; border: none; background-color: #edf4ed; border-radius: 12px; box-sizing: border-box; font-size: 14px; color: #333;}
        .form-group input::placeholder { color: #9cb59c; }
        .btn-submit { background-color: #349f4c; color: white; border: none; width: 100%; padding: 14px; border-radius: 12px; font-weight: bold; cursor: pointer; font-size: 15px; margin-top: 15px; letter-spacing: 0.5px;}
        .btn-submit:hover { background-color: #2a823d; }
        .footer-text { text-align: center; font-size: 12px; color: #777; margin-top: 35px; }
        .error-msg { color: #d93843; font-size: 13px; margin-bottom: 15px; font-weight: bold;}
    </style>
</head>
<body>
<div class="login-card">
    <div class="left-side">
        <h1>HIDUP<br>SEHAT</h1>
        <p>Selamat datang di Sistem Informasi Hidup Sehat. Mulailah langkah kecil hari ini untuk menciptakan kehidupan yang lebih sehat dan berkualitas.</p>
    </div>
    <div class="right-side">
        <h2>LOGIN</h2>
        <div class="subtitle">Selamat Datang Pengguna</div>
        
        <?php if(!empty($error)): ?>
            <div class="error-msg"><?= $error; ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Masukkan Username" required autocomplete="off">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Masukkan Password" required>
            </div>
            <button type="submit" class="btn-submit">MASUK</button>
        </form>
        <div class="footer-text">© Kelompok 8 | Hidup Sehat</div>
    </div>
</div>
</body>
</html>