<?php
session_start();
include 'koneksi.php';

if(!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit;
}

$query = mysqli_query($conn,"
SELECT a.*, u.nama_pengguna
FROM aktivitas a
JOIN users u ON a.id_user = u.id_user
ORDER BY a.tanggal DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Aktivitas</title>

    <style>
        body { font-family: Arial; background:#f2f4f8; }
        .container { width:95%; margin:30px auto; }

        table {
            width:100%;
            border-collapse:collapse;
            background:white;
        }

        th {
            background:#333;
            color:white;
            padding:10px;
        }

        td {
            padding:10px;
            border:1px solid #ddd;
            text-align:center;
        }

        a {
            padding:5px 10px;
            color:white;
            text-decoration:none;
            border-radius:5px;
        }

        .edit { background:orange; }
        .hapus { background:red; }

        .btn-dashboard {
            display:inline-block;
            margin-bottom:15px;
            padding:8px 12px;
            background:#007bff;
            color:white;
            text-decoration:none;
            border-radius:5px;
        }
    </style>
</head>

<body>

<div class="container">

<h2>📊 Admin - Aktivitas Semua User</h2>

<!-- 🔥 FIX DASHBOARD (TIDAK ERROR LAGI) -->
<?php if($_SESSION['role'] == 'admin'){ ?>
    <a class="btn-dashboard" href="dashboard_admin.php">⬅ Dashboard</a>
<?php } else { ?>
    <a class="btn-dashboard" href="dashboard_user.php">⬅ Dashboard</a>
<?php } ?>

<br><br>

<table>
<tr>
    <th>No</th>
    <th>User</th>
    <th>Olahraga</th>
    <th>Durasi</th>
    <th>Kalori</th>
    <th>Tanggal</th>
    <th>Catatan</th>
    <th>Sumber</th>
    <th>Aksi</th>
</tr>

<?php $no=1; while($row=mysqli_fetch_assoc($query)) { ?>

<tr>
    <td><?= $no++ ?></td>
    <td><?= $row['nama_pengguna'] ?></td>
    <td><?= $row['jenis_olahraga'] ?></td>
    <td><?= $row['durasi'] ?></td>
    <td><?= $row['kalori'] ?></td>
    <td><?= $row['tanggal'] ?></td>
    <td><?= $row['catatan'] ?></td>
    <td><?= $row['sumber_data'] ?></td>

    <td>
        <a class="edit" href="admin_aktivitas_edit.php?id=<?= $row['id_aktivitas'] ?>">Edit</a>
        <a class="hapus" href="admin_aktivitas_hapus.php?id=<?= $row['id_aktivitas'] ?>" onclick="return confirm('Hapus data?')">Hapus</a>
    </td>
</tr>

<?php } ?>

</table>

</div>

</body>
</html>