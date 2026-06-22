<?php
session_start();
include 'koneksi.php';

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] == true) {
    header("Location: admin_statistik.php");
    exit();
}

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM admin WHERE username='$username'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        if ($password == $row['password']) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['username'] = $row['username'];

            header("Location: admin_statistik.php");
            exit();
        }
    }

    $error = "Username atau Password salah!";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Hidup Sehat</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, Helvetica, sans-serif; }
        body { background-color: #9cd994; height: 100vh; display: flex; justify-content: center; align-items: center; padding: 20px; }
        .container { width: 880px; max-width: 100%; background: white; border-radius: 20px; overflow: hidden; display: flex; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15); }
        .kiri { width: 44%; background: #3b9e4a; padding: 50px 45px; color: white; display: flex; flex-direction: column; justify-content: center; }
        .kiri h1 { font-size: 56px; font-weight: bold; line-height: 1.1; letter-spacing: 0.5px; margin-bottom: 25px; }
        .kiri p { font-size: 16.5px; line-height: 1.6; opacity: 0.9; }
        .kanan { width: 56%; padding: 60px 55px; display: flex; flex-direction: column; justify-content: center; }
        .kanan h2 { font-size: 42px; color: #216934; font-weight: bold; margin-bottom: 5px; }
        .kanan h4 { margin-bottom: 35px; color: #555555; font-weight: normal; font-size: 15px; }
        label { display: block; margin-bottom: 10px; font-weight: bold; color: #555555; font-size: 14.5px; }
        input { width: 100%; padding: 14px 18px; margin-bottom: 25px; border: none; background: #f1f8e9; border-radius: 10px; font-size: 14.5px; color: #333333; }
        input::placeholder { color: #9cb29a; }
        input:focus { outline: 2px solid #3b9e4a; background: #edf6e5; }
        button { width: 100%; padding: 14px; background: #3b9e4a; border: none; color: white; font-size: 16px; font-weight: bold; border-radius: 10px; cursor: pointer; transition: background 0.2s ease; margin-top: 5px; }
        button:hover { background: #2f823c; }
        .error { background: #ffebee; padding: 12px; margin-bottom: 20px; border-radius: 8px; color: #c62828; text-align: center; font-size: 14px; }
        .footer { margin-top: 25px; text-align: center; color: #777777; font-size: 12.5px; }
        @media (max-width: 768px) {
            .container { flex-direction: column; }
            .kiri, .kanan { width: 100%; padding: 40px 30px; }
            body { height: auto; align-items: flex-start; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="kiri">
            <h1>HIDUP<br>SEHAT</h1>
            <p>Selamat datang di Website Olahraga Hidup Sehat, mulailah langkah kecil hari ini untuk menciptakan kehidupan yang lebih sehat dan berkualitas</p>
        </div>
        <div class="kanan">
            <h2>LOGIN</h2>
            <h4>Selamat Datang Admin</h4>
            <?php if (isset($error)): ?>
                <div class="error"><?= $error; ?></div>
            <?php endif; ?>
            <form method="POST">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Masukkan Username" required autocomplete="off">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Masukkan Password" required>
                <button type="submit" name="login">MASUK</button>
            </form>
            <div class="footer">© Kelompok 8 | Hidup Sehat</div>
        </div>
    </div>
</body>
</html>