<?php
session_start();

// Otorisasi: Hanya admin yang sudah login yang bisa ganti password
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

require 'koneksi.php';

if (isset($_POST['update_kredensial'])) {
    $username_baru = $conn->real_escape_string($_POST['username_baru']);
    $password_baru = $_POST['password_baru'];
    
    // Mengamankan password baru dengan hash BCRYPT (Standar PHP modern)
    $password_hash = password_hash($password_baru, PASSWORD_BCRYPT);
    
    // Ambil username lama dari session untuk patokan WHERE klausul
    $username_lama = $_SESSION['username'];
    
    // Query untuk mengupdate username dan password sekaligus
    $sql = "UPDATE admin SET username = '$username_baru', password = '$password_hash' WHERE username = '$username_lama'";
    
    if ($conn->query($sql)) {
        // Perbarui data session dengan username yang baru
        $_SESSION['username'] = $username_baru;
        $success = "Username dan Password berhasil diperbarui!";
    } else {
        $error = "Gagal memperbarui data: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ganti Kredensial Admin</title>
</head>
<body style="font-family: Arial, sans-serif; margin: 50px;">

    <a href="index.php" style="text-decoration: none; color: #0275d8;">&larr; Kembali ke Dashboard</a>
    
    <div style="width: 400px; margin: 30px 0; border: 1px solid #ccc; padding: 20px; border-radius: 5px; background: #f9f9f9;">
        <h2>Pengaturan Akun Admin</h2>
        <p style="font-size: 14px; color: #666;">Silakan isi form di bawah ini untuk mengubah nama user dan kata sandi Anda.</p>
        <hr><br>
        
        <?php if(isset($success)): ?>
            <p style="color: green; font-weight: bold;"><?php echo $success; ?></p>
        <?php endif; ?>
        
        <?php if(isset($error)): ?>
            <p style="color: red; font-weight: bold;"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div style="margin-bottom: 15px;">
                <label>Username Baru:</label><br>
                <input type="text" name="username_baru" value="<?php echo htmlspecialchars($_SESSION['username']); ?>" style="width: 95%; padding: 8px; margin-top: 5px;" required>
            </div>
            
            <div style="margin-bottom: 20px;">
                <label>Password Baru:</label><br>
                <input type="password" name="password_baru" placeholder="Ketik password baru di sini" style="width: 95%; padding: 8px; margin-top: 5px;" required>
            </div>
            
            <button type="submit" name="update_kredensial" style="padding: 10px 15px; background-color: #0275d8; color: white; border: none; border-radius: 3px; cursor: pointer;">Simpan Perubahan</button>
        </form>
    </div>

</body>
</html>