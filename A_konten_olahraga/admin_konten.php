<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include "koneksi.php";

$id_edit = "";
$judul_edit = "";
$kategori_edit = "";
$deskripsi_edit = "";
$tombol_aksi = "create";
$judul_form = "Tambah Konten Olahraga Baru";

if (isset($_GET['aksi']) && $_GET['aksi'] == 'edit') {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $ambil_data = mysqli_query($conn, "SELECT * FROM konten_olahraga WHERE id='$id'");
    $data = mysqli_fetch_assoc($ambil_data);

    if ($data) {
        $id_edit = $data['id'];
        $judul_edit = $data['judul'];
        $kategori_edit = $data['kategori'];
        $deskripsi_edit = $data['deskripsi'];
        $tombol_aksi = "update"; 
        $judul_form = "Edit Konten Olahraga";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin | Kelola Konten Olahraga</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; padding: 30px; display: flex; justify-content: center; }
        .container { background-color: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 100%; max-width: 650px; }
        h1, h3 { text-align: center; color: #333; }
        form { display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px; }
        input, select, textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { background-color: #008000; color: white; padding: 12px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .btn-batal { background-color: #6c757d; color: white; padding: 12px; border-radius: 4px; text-align: center; text-decoration: none; margin-top: 5px; display: block;}
        .btn-logout { background-color: #dc3545; color: white; padding: 8px 15px; border-radius: 4px; text-decoration: none; float: right; font-size: 14px;}
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; font-size: 14px; }
        th { background-color: #f9f9f9; text-align: center; }
        .clearfix { clear: both; }
    </style>
</head>
<body>
<div class="container">
    <a href="logout.php" class="btn-logout" onclick="return confirm('Yakin ingin logout?')">Logout</a>
    <div class="clearfix"></div>

    <h1>KELOLA KONTEN EDUKASI OLAHRAGA</h1>
    <p style="text-align: center;">Selamat datang, <strong><?php echo $_SESSION['username']; ?></strong></p>

    <form action="proses_admin.php" method="POST">
        <input type="hidden" name="aksi" value="<?php echo $tombol_aksi; ?>">
        <input type="hidden" name="id" value="<?php echo $id_edit; ?>">

        <h3><?php echo $judul_form; ?></h3>
        
        <label>Judul Konten</label>
        <input type="text" name="judul" value="<?php echo $judul_edit; ?>" required>
        
        <label>Kategori (Pilih opsi atau ketik manual baru)</label>
        <input type="text" name="kategori" list="pilihan_kategori" value="<?php echo $kategori_edit; ?>" placeholder="Klik untuk memilih atau ketik baru..." required>
        
        <datalist id="pilihan_kategori">
            <option value="Kardio">
            <option value="Kekuatan">
            <option value="Maraton">
        </datalist>

        <label>Deskripsi Konten</label>
        <textarea name="deskripsi" rows="3" required><?php echo $deskripsi_edit; ?></textarea>

        <button type="submit">Simpan Konten</button>
        
        <?php if ($tombol_aksi === 'update'): ?>
            <a href="admin_konten.php" class="btn-batal">Batal Edit</a>
        <?php endif; ?>
    </form>

    <hr>

    <table>
        <thead>
            <tr>
                <th style="width: 7%;">No</th>
                <th>Judul</th>
                <th>Kategori</th>
                <th>Deskripsi</th>
                <th style="width: 20%; text-align:center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = mysqli_query($conn, "SELECT * FROM konten_olahraga ORDER BY id ASC");
            $nomor = 1;
            if (mysqli_num_rows($query) > 0) {
                while($row = mysqli_fetch_assoc($query)) {
                ?>
                    <tr>
                        <td style="text-align:center;"><?php echo $nomor++; ?></td>
                        <td><?php echo $row['judul']; ?></td>
                        <td><?php echo $row['kategori']; ?></td>
                        <td><?php echo $row['deskripsi']; ?></td>
                        <td style="text-align:center;">
                            <a href="admin_konten.php?aksi=edit&id=<?php echo $row['id']; ?>">Edit</a> | 
                            
                            <form action="proses_admin.php" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus konten ini?')">
                                <input type="hidden" name="aksi" value="delete">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" style="background:none; color:red; padding:0; border:none; cursor:pointer; font-weight:normal;">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php 
                }
            } else {
                echo '<tr><td colspan="5" style="text-align:center;">Belum ada konten olahraga.</td></tr>';
            }
            ?>
        </tbody>
    </table>
</div>
</body>
</html>