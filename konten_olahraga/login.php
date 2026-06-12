<?php
session_start();
require 'koneksi.php';

// Jika session admin sudah aktif, langsung alihkan ke index.php (Kelola Konten)
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: index.php");
    exit();
}

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    $sql = "SELECT * FROM admin WHERE username = '$username'";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // JALUR PINTAS: Langsung cocokkan teks biasa tanpa enkripsi hash
        if ($password === $row['password'] || password_verify($password, $row['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['username'] = $row['username'];
            
            // Alihkan langsung ke halaman utama kelola konten olahraga
            header("Location: index.php");
            exit();
        }
    }
    $error = "Username atau password salah!";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin - Konten Olahraga</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f9;">
    <div style="width: 300px; margin: 100px auto; border: 1px solid #ccc; padding: 20px; border-radius: 5px; background-color: #fff; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <h2 style="text-align: center; margin-bottom: 20px;">Login Admin</h2>
        
        <?php if(isset($error)): ?>
            <p style="color:red; text-align: center; font-size: 14px; font-weight: bold;"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div style="margin-bottom: 15px;">
                <label>Username:</label><br>
                <input type="text" name="username" style="width: 93%; padding: 8px; margin-top: 5px;" required autocomplete="off">
            </div>
            <div style="margin-bottom: 20px;">
                <label>Password:</label><br>
                <input type="password" name="password" style="width: 93%; padding: 8px; margin-top: 5px;" required>
            </div>
            <button type="submit" name="login" style="width: 100%; padding: 10px; background-color: #4CAF50; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 16px;">Masuk</button>
        </form>
    </div>
</body>
</html>