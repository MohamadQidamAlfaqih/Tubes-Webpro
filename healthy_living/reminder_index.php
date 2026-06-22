<?php
session_start();
include 'koneksi.php';

if(!isset($_SESSION['id_user'])){
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

// Sapaan
$jam = (int)date('H');
if($jam < 11)      $sapaan = "Selamat Pagi";
elseif($jam < 15)  $sapaan = "Selamat Siang";
elseif($jam < 18)  $sapaan = "Selamat Sore";
else               $sapaan = "Selamat Malam";

// Query reminder
$query = mysqli_query($conn,
"SELECT r.*, j.jenis_kegiatan
 FROM reminder r
 JOIN jadwal j ON r.id_jadwal = j.id_jadwal
 WHERE r.id_user='$id_user'
 ORDER BY r.waktu ASC");

// Statistik
$qTotal = mysqli_query($conn,
    "SELECT COUNT(*) total_reminder FROM reminder WHERE id_user='$id_user'");
$stat = mysqli_fetch_assoc($qTotal);
?>

<!DOCTYPE html>
<html>
<head>
<title>Reminder</title>

<style>
:root{
    --green:#22c55e;
    --green-dark:#16a34a;
    --ink:#1f2430;
    --muted:#8a93a3;
    --line:#edeff2;
    --bg:#f6f7f9;
}

body{
    margin:0;
    font-family:Arial;
    background:var(--bg);
}

/* NAVBAR */
.navbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    background:#fff;
    padding:14px 28px;
    border-bottom:1px solid var(--line);
}

.nav-links{
    display:flex;
    gap:20px;
    font-size:13px;
}

.nav-links a{
    text-decoration:none;
    color:var(--muted);
}

.nav-links a.active{
    color:var(--green-dark);
    font-weight:bold;
}

.avatar{
    width:34px;
    height:34px;
    border-radius:50%;
    background:linear-gradient(135deg,#4facfe,#00f2fe);
    color:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
}

/* CONTAINER */
.container{
    max-width:1000px;
    margin:25px auto;
    padding:0 20px;
}

.panel{
    background:#fff;
    padding:20px;
    border-radius:10px;
    margin-bottom:20px;
}

/* STAT */
.stat{
    font-size:26px;
    color:var(--green-dark);
    font-weight:bold;
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
}

th{
    text-align:left;
    font-size:12px;
    color:var(--muted);
    border-bottom:1px solid var(--line);
    padding:10px;
}

td{
    padding:10px;
    border-bottom:1px solid var(--line);
    font-size:13px;
}

.status-aktif{
    color:green;
    font-weight:bold;
}

.status-nonaktif{
    color:red;
    font-weight:bold;
}

.edit{
    color:orange;
    font-weight:bold;
    margin-right:10px;
}

.hapus{
    color:red;
    font-weight:bold;
}

.btn{
    display:inline-block;
    background:var(--green);
    color:white;
    padding:10px 15px;
    border-radius:6px;
    text-decoration:none;
    font-size:13px;
}

.btn:hover{
    background:var(--green-dark);
}
</style>
</head>

<body>

<!-- NAVBAR -->
<div class="navbar">
    <div><b>HIDUP SEHAT</b></div>

    <div class="nav-links">
        <a href="dashboard_user.php">Dashboard</a>
        <a href="aktivitas_index.php">Activities</a>
        <a href="jadwal_index.php">Calendar</a>
        <a class="active" href="reminder_index.php">Reminders</a>
    </div>

    <div class="avatar"><?= strtoupper(substr($_SESSION['nama'],0,1)) ?></div>
</div>

<div class="container">

    <h3><?= $sapaan ?>, <?= $_SESSION['nama'] ?></h3>
    <p style="color:gray;">Kelola pengingat aktivitasmu di sini</p>

    <!-- STAT -->
    <div class="panel">
        <div>Total Reminder</div>
        <div class="stat"><?= $stat['total_reminder'] ?> Reminder</div>
    </div>

    <!-- BUTTON -->
    <a href="reminder_tambah.php" class="btn">+ Tambah Reminder</a>

    <!-- TABLE -->
    <div class="panel">
        <h4>Daftar Reminder</h4>

        <table>
            <tr>
                <th>Judul</th>
                <th>Jadwal</th>
                <th>Waktu</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>

            <?php while($row=mysqli_fetch_assoc($query)) { ?>
            <tr>
                <td><?= $row['judul'] ?></td>
                <td><?= $row['jenis_kegiatan'] ?></td>
                <td><?= $row['waktu'] ?></td>
                <td class="<?= $row['status']=='aktif'?'status-aktif':'status-nonaktif' ?>">
                    <?= $row['status'] ?>
                </td>
                <td>
                    <a class="edit" href="reminder_edit.php?id=<?= $row['id_reminder'] ?>">Edit</a>
                    <a class="hapus" href="reminder_hapus.php?id=<?= $row['id_reminder'] ?>">Hapus</a>
                </td>
            </tr>
            <?php } ?>

        </table>

    </div>

</div>

</body>
</html>