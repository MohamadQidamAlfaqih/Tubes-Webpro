<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include "koneksi.php";

$id_edit = "";
$nama_pengguna = "";
$berat_badan = "";
$tinggi_badan = "";
$status_target = ""; 
$tombol_aksi = "create";
$judul_form = "Tambah Statistik Pengguna Baru";

if (isset($_GET['aksi']) && $_GET['aksi'] == 'edit') {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $ambil_data = mysqli_query($conn, "SELECT * FROM statistik_pengguna WHERE id='$id'");
    $data = mysqli_fetch_assoc($ambil_data);

    if ($data) {
        $id_edit = $data['id'];
        $nama_pengguna = $data['nama_pengguna'];
        $berat_badan = $data['berat_badan'];
        $tinggi_badan = $data['tinggi_badan'];
        $status_target = $data['status_target'];
        $tombol_aksi = "update";
        $judul_form = "Edit Statistik Pengguna";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Statistik Pengguna</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 40px 20px; display: flex; justify-content: center; }
        .container { background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); width: 100%; max-width: 700px; }
        h1 { text-align: center; font-size: 24px; margin-bottom: 5px; color: #333; }
        h3 { margin-top: 0; color: #555; font-size: 16px; }
        form { display: flex; flex-direction: column; gap: 12px; margin-bottom: 25px; }
        label { font-weight: bold; font-size: 14px; color: #444; }
        input[type="text"], input[type="number"], select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; font-size: 14px; }
        button { background-color: #008000; color: white; padding: 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; font-weight: bold; }
        button:hover { background-color: #006400; }
        .btn-batal { background-color: #6c757d; color: white; padding: 12px; border: none; border-radius: 4px; text-align: center; text-decoration: none; margin-top: 5px; font-size: 14px; font-weight: bold; display: block; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; font-size: 14px; }
        th { background-color: #f9f9f9; font-weight: bold; text-align: center; }
        .btn-aksi { padding: 4px 8px; text-decoration: none; border-radius: 4px; font-size: 12px; color: white; display: inline-block; }
        .btn-edit { background-color: #ffc107; color: black; margin-right: 5px; }
        .btn-hapus { background-color: #dc3545; color: white; border: none; padding: 5px 9px; cursor: pointer; border-radius: 4px; font-size: 12px; }
        .logout-box { text-align: center; margin-bottom: 20px; font-size: 14px; }
        .btn-logout { color: #dc3545; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <h1>ADMIN<br>STATISTIK KESEHATAN PENGGUNA</h1>
    
    <div class="logout-box">
        Selamat datang, <strong><?php echo $_SESSION['username']; ?></strong> | 
        <a href="logout.php" class="btn-logout" onclick="return confirm('Yakin ingin keluar?')">Logout</a>
    </div>

    <h3><?php echo $judul_form; ?></h3>
    
    <form action="proses_statistik.php" method="POST">
        <input type="hidden" name="aksi" value="<?php echo $tombol_aksi; ?>">
        <input type="hidden" name="id" value="<?php echo $id_edit; ?>">

        <label for="nama_pengguna">Nama Pengguna</label>
        <input type="text" name="nama_pengguna" value="<?php echo $nama_pengguna; ?>" required placeholder="Contoh: Budi Santoso">

        <label for="berat_badan">Berat Badan (Kg)</label>
        <input type="number" name="berat_badan" value="<?php echo $berat_badan; ?>" required placeholder="Contoh: 65">

        <label for="tinggi_badan">Tinggi Badan (Cm)</label>
        <input type="number" name="tinggi_badan" value="<?php echo $tinggi_badan; ?>" required placeholder="Contoh: 170">

        <label for="status_target">Target Kesehatan</label>
        <input type="text" name="status_target" id="status_target" list="target_list" value="<?php echo $status_target; ?>" required placeholder="Pilih atau ketik target baru secara manual...">
        
        <datalist id="target_list">
            <option value="Menurunkan Berat Badan">
            <option value="Menaikkan Massa Otot">
            <option value="Menjaga Pola Hidup">
        </datalist>

        <button type="submit">Simpan Data Statistik</button>
        
        <?php if ($tombol_aksi === 'update'): ?>
            <a href="admin_statistik.php" class="btn-batal">Batal Edit</a>
        <?php endif; ?>
    </form>

    <hr>

    <table>
        <thead>
            <tr>
                <th style="width: 7%;">No</th>
                <th>Nama Pengguna</th>
                <th style="width: 15%; text-align:center;">BB</th>
                <th style="width: 15%; text-align:center;">TB</th>
                <th>Target</th>
                <th style="width: 20%; text-align:center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = mysqli_query($conn, "SELECT * FROM statistik_pengguna ORDER BY id ASC");
            $nomor = 1;
            
            if (mysqli_num_rows($query) > 0) {
                while ($row = mysqli_fetch_assoc($query)) {
                    ?>
                    <tr>
                        <td style="text-align:center;"><?php echo $nomor++; ?></td>
                        <td><?php echo $row['nama_pengguna']; ?></td>
                        <td style="text-align:center;"><?php echo $row['berat_badan']; ?> Kg</td>
                        <td style="text-align:center;"><?php echo $row['tinggi_badan']; ?> Cm</td>
                        <td><?php echo $row['status_target']; ?></td>
                        <td style="text-align:center;">
                            <a href="admin_statistik.php?aksi=edit&id=<?php echo $row['id']; ?>" class="btn-aksi btn-edit">Edit</a>
                            
                            <form action="proses_statistik.php" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus statistik pengguna ini?')">
                                <input type="hidden" name="aksi" value="delete">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn-hapus">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                echo '<tr><td colspan="6" style="text-align:center;">Belum ada data statistik pengguna.</td></tr>';
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>