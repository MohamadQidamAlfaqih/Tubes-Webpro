<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'];

$data = mysqli_query($conn, "
    SELECT * FROM aktivitas
    WHERE id_aktivitas='$id'
");

$row = mysqli_fetch_assoc($data);

if (!$row) {
    echo "Data tidak ditemukan!";
    exit;
}

if (isset($_POST['update'])) {

    $jenis = $_POST['jenis_olahraga'];
    $durasi = $_POST['durasi'];
    $kalori = $_POST['kalori'];
    $tanggal = $_POST['tanggal'];
    $catatan = $_POST['catatan'];
    $sumber = $_POST['sumber_data'];

    mysqli_query($conn, "
        UPDATE aktivitas SET
        jenis_olahraga='$jenis',
        durasi='$durasi',
        kalori='$kalori',
        tanggal='$tanggal',
        catatan='$catatan',
        sumber_data='$sumber'
        WHERE id_aktivitas='$id'
    ");

    header("Location: aktivitas_index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Aktivitas</title>

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
    </style>
</head>

<body>

<div class="container">

<h2>✏️ Edit Aktivitas</h2>

<form method="POST">

    <label>Jenis Olahraga</label>
    <input type="text" name="jenis_olahraga" value="<?= $row['jenis_olahraga'] ?>" required>

    <label>Durasi (menit)</label>
    <input type="number" name="durasi" value="<?= $row['durasi'] ?>" required>

    <label>Kalori</label>
    <input type="number" name="kalori" value="<?= $row['kalori'] ?>" required>

    <label>Tanggal</label>
    <input type="date" name="tanggal" value="<?= $row['tanggal'] ?>" required>

    <label>Catatan</label>
    <textarea name="catatan"><?= $row['catatan'] ?></textarea>

    <label>Sumber Data</label>
    <input type="text" name="sumber_data" value="<?= $row['sumber_data'] ?>">

    <button type="submit" name="update">💾 Update Aktivitas</button>

</form>

<a class="back" href="aktivitas_index.php">⬅ Kembali</a>

</div>

</body>
</html>