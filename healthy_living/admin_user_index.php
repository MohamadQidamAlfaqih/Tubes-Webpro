<?php
session_start();
include 'koneksi.php';

if(!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit;
}

$query = mysqli_query($conn, "SELECT * FROM users ORDER BY id_user DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Data User</title>

    <style>
        body { font-family: Arial; background:#f2f4f8; }

        .container { width:90%; margin:30px auto; }

        table {
            width:100%;
            border-collapse: collapse;
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
            border-radius:5px;
            text-decoration:none;
            color:white;
        }

        .edit { background:orange; }
        .hapus { background:red; }
        .tambah { background:green; display:inline-block; margin-bottom:10px; }

        .dashboard {
            display:inline-block;
            margin-bottom:15px;
            padding:10px 15px;
            background:#007bff;
            color:white;
            border-radius:5px;
            text-decoration:none;
        }

        .header {
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:15px;
        }
    </style>
</head>

<body>

<div class="container">

<div class="header">

    <h2>👑 Admin - Manajemen User</h2>

    <!-- 🔥 BUTTON DASHBOARD -->
    <a class="dashboard" href="dashboard_admin.php">⬅ Dashboard</a>

</div>

<a class="tambah" href="admin_user_tambah.php">+ Tambah User</a>

<table>
<tr>
    <th>No</th>
    <th>Nama</th>
    <th>Email</th>
    <th>Role</th>
    <th>Aksi</th>
</tr>

<?php $no=1; while($row=mysqli_fetch_assoc($query)) { ?>
<tr>
    <td><?= $no++ ?></td>
    <td><?= $row['nama_pengguna'] ?></td>
    <td><?= $row['email'] ?></td>
    <td><?= $row['role'] ?></td>
    <td>
        <a class="edit" href="admin_user_edit.php?id=<?= $row['id_user'] ?>">Edit</a>
        <a class="hapus" href="admin_user_hapus.php?id=<?= $row['id_user'] ?>" onclick="return confirm('Hapus user ini?')">Hapus</a>
    </td>
</tr>
<?php } ?>

</table>

</div>

</body>
</html>