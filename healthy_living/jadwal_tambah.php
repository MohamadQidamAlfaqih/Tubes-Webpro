<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

if (isset($_POST['simpan'])) {

    $id_user = $_SESSION['id_user'];
    $hari = $_POST['hari'];
    $waktu = $_POST['waktu_mulai'];
    $kegiatan = $_POST['jenis_kegiatan'];

    mysqli_query($conn, "
        INSERT INTO jadwal (id_user, hari, waktu_mulai, jenis_kegiatan)
        VALUES ('$id_user','$hari','$waktu','$kegiatan')
    ");

    header("Location: jadwal_index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Jadwal</title>

    <style>
        body {
            font-family: Arial;
            background: linear-gradient(135deg, #74ebd5, #ACB6E5);
            margin: 0;
        }

        .container {
            width: 450px;
            margin: 60px auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        label {
            font-weight: bold;
            font-size: 14px;
        }

        select, input {
            width: 100%;
            padding: 10px;
            margin: 8px 0 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #4facfe;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background: #2196f3;
        }

        .back {
            display: block;
            text-align: center;
            margin-top: 10px;
            color: #333;
            text-decoration: none;
        }
    </style>
</head>

<body>

<div class="container">

<h2>➕ Tambah Jadwal</h2>

<form method="POST">

    <label>Hari</label>
    <select name="hari" required>
        <option>Senin</option>
        <option>Selasa</option>
        <option>Rabu</option>
        <option>Kamis</option>
        <option>Jumat</option>
        <option>Sabtu</option>
        <option>Minggu</option>
    </select>

    <label>Waktu Mulai</label>
    <input type="time" name="waktu_mulai" required>

    <label>Jenis Kegiatan</label>
    <input type="text" name="jenis_kegiatan" required>

    <button type="submit" name="simpan">💾 Simpan</button>

</form>

<a class="back" href="jadwal_index.php">⬅ Kembali</a>

</div>

</body>
</html>