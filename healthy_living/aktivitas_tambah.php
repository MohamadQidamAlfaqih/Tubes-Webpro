<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

if (isset($_POST['simpan'])) {

    $id_user = $_SESSION['id_user'];
    $jenis = $_POST['jenis_olahraga'];
    $durasi = $_POST['durasi'];
    $kalori = $_POST['kalori'];
    $tanggal = $_POST['tanggal'];
    $catatan = $_POST['catatan'];
    $sumber = $_POST['sumber_data'];

    mysqli_query($conn, "
        INSERT INTO aktivitas
        (id_user, jenis_olahraga, durasi, kalori, tanggal, catatan, sumber_data)
        VALUES
        ('$id_user', '$jenis', '$durasi', '$kalori', '$tanggal', '$catatan', '$sumber')
    ");

    header("Location: aktivitas_index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Aktivitas</title>

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
            margin-bottom: 20px;
            color: #333;
        }

        label {
            font-size: 14px;
            font-weight: bold;
            color: #444;
        }

        input, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            outline: none;
        }

        input:focus, textarea:focus {
            border-color: #4facfe;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #4facfe;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.2s;
        }

        button:hover {
            background: #2196f3;
        }

        .back {
            display: block;
            text-align: center;
            margin-top: 15px;
            text-decoration: none;
            color: #333;
        }

        .back:hover {
            color: #000;
        }
    </style>
</head>

<body>

<div class="container">

    <h2>🏃 Tambah Aktivitas</h2>

    <form method="POST">

        <label>Jenis Olahraga</label>
        <input type="text" name="jenis_olahraga" placeholder="Contoh: Lari, Push up" required>

        <label>Durasi (menit)</label>
        <input type="number" name="durasi" placeholder="Contoh: 30" required>

        <label>Kalori</label>
        <input type="number" name="kalori" placeholder="Contoh: 200" required>

        <label>Tanggal</label>
        <input type="date" name="tanggal" required>

        <label>Catatan</label>
        <textarea name="catatan" placeholder="Opsional..."></textarea>

        <label>Sumber Data</label>
        <input type="text" name="sumber_data" placeholder="Manual / Smartwatch">

        <button type="submit" name="simpan">💾 Simpan Aktivitas</button>

    </form>

    <a class="back" href="aktivitas_index.php">⬅ Kembali</a>

</div>

</body>
</html>