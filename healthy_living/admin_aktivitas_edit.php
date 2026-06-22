<?php
session_start();
include 'koneksi.php';

if(!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit;
}

$id = $_GET['id'];

$data = mysqli_query($conn,"SELECT * FROM aktivitas WHERE id_aktivitas='$id'");
$row = mysqli_fetch_assoc($data);

if(isset($_POST['update'])){

    $jenis = $_POST['jenis_olahraga'];
    $durasi = $_POST['durasi'];
    $kalori = $_POST['kalori'];
    $tanggal = $_POST['tanggal'];
    $catatan = $_POST['catatan'];
    $sumber = $_POST['sumber_data'];

    mysqli_query($conn,"
        UPDATE aktivitas SET
        jenis_olahraga='$jenis',
        durasi='$durasi',
        kalori='$kalori',
        tanggal='$tanggal',
        catatan='$catatan',
        sumber_data='$sumber'
        WHERE id_aktivitas='$id'
    ");

    header("Location: admin_aktivitas_index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Aktivitas Admin</title>

<style>
body{
    font-family:Arial;
    background:#f4f6f9;
    margin:0;
}

.container{
    width:45%;
    margin:40px auto;
}

.card{
    background:white;
    padding:25px;
    border-radius:10px;
    box-shadow:0 2px 10px rgba(0,0,0,0.1);
}

h2{
    text-align:center;
}

input, textarea{
    width:100%;
    padding:10px;
    margin:8px 0;
    border:1px solid #ddd;
    border-radius:6px;
}

button{
    width:100%;
    padding:10px;
    background:#007bff;
    color:white;
    border:none;
    border-radius:6px;
    cursor:pointer;
}

button:hover{
    background:#0056b3;
}

.back{
    display:block;
    text-align:center;
    margin-top:10px;
    text-decoration:none;
}
</style>

</head>

<body>

<div class="container">

<div class="card">

<h2>✏️ Edit Aktivitas (Admin)</h2>

<form method="POST">

    <input type="text" name="jenis_olahraga" value="<?= $row['jenis_olahraga'] ?>" required>

    <input type="number" name="durasi" value="<?= $row['durasi'] ?>" required>

    <input type="number" name="kalori" value="<?= $row['kalori'] ?>" required>

    <input type="date" name="tanggal" value="<?= $row['tanggal'] ?>" required>

    <textarea name="catatan"><?= $row['catatan'] ?></textarea>

    <input type="text" name="sumber_data" value="<?= $row['sumber_data'] ?>">

    <button type="submit" name="update">Update</button>

</form>

<a class="back" href="admin_aktivitas_index.php">← Kembali</a>

</div>

</div>

</body>
</html>