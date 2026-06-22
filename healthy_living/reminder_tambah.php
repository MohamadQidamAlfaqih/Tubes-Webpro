<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

$jadwal = mysqli_query($conn, "SELECT * FROM jadwal WHERE id_user='$id_user'");

if (isset($_POST['simpan'])) {

    $id_jadwal = $_POST['id_jadwal'];
    $judul = $_POST['judul'];
    $waktu = $_POST['waktu'];
    $pesan = $_POST['pesan'];
    $status = $_POST['status'];

    mysqli_query($conn, "
        INSERT INTO reminder
        (id_user,id_jadwal,judul,waktu,pesan,status)
        VALUES
        ('$id_user','$id_jadwal','$judul','$waktu','$pesan','$status')
    ");

    header("Location: reminder_index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Reminder</title>

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

        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            outline: none;
        }

        input:focus, textarea:focus, select:focus {
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

        .back:hover {
            color: #000;
        }
    </style>
</head>

<body>

<div class="container">

<h2>🔔 Tambah Reminder</h2>

<form method="POST">

    <label>Judul</label>
    <input type="text" name="judul" required>

    <label>Jadwal</label>
    <select name="id_jadwal" required>
        <?php while($j = mysqli_fetch_assoc($jadwal)) { ?>
            <option value="<?= $j['id_jadwal'] ?>">
                <?= $j['jenis_kegiatan'] ?>
            </option>
        <?php } ?>
    </select>

    <label>Waktu</label>
    <input type="datetime-local" name="waktu" required>

    <label>Pesan</label>
    <textarea name="pesan" placeholder="Tulis pesan reminder..."></textarea>

    <label>Status</label>
    <select name="status">
        <option value="aktif">Aktif</option>
        <option value="nonaktif">Nonaktif</option>
    </select>

    <button type="submit" name="simpan">💾 Simpan Reminder</button>

</form>

<a class="back" href="reminder_index.php">⬅ Kembali</a>

</div>

</body>
</html>