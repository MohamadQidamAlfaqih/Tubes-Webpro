<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Jadwal Tidur</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
        }
        h1 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }
        h3 {
            margin-top: 0;
            color: #555;
            font-size: 16px;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        label {
            font-weight: bold;
            font-size: 14px;
            color: #444;
        }
        input[type="time"], select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }
        button[type="submit"] {
            background-color: #008000;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            margin-top: 8px;
        }
        button[type="submit"]:hover {
            background-color: #006400;
        }
        hr {
            margin: 25px 0;
            border: 0;
            border-top: 1px solid #eee;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
            font-size: 14px;
        }
        th {
            background-color: #f9f9f9;
            font-weight: bold;
        }
        .btn-batal {
            text-align: center;
            display: inline-block;
            margin-top: 5px;
            color: #555;
            text-decoration: none;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="container">

    <h1>JADWAL TIDUR SEHAT</h1>

    <?php
    $edit_data = null;
    if (isset($_GET['page']) && $_GET['page'] == 'edit' && isset($_GET['id'])) {
        include "koneksi.php";
        $id = $_GET['id'];
        $query = mysqli_query($conn, "SELECT * FROM jadwal_tidur WHERE id = '$id'");
        $edit_data = mysqli_fetch_assoc($query);
    }
    ?>

    <?php if ($edit_data): ?>
        <h3>Edit Jadwal Tidur</h3>
        <form action="api_tidur.php?action=update" method="POST">
            <input type="hidden" name="api_key" value="LATIHAN2026">
            <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">

            <label for="hari">Hari</label>
            <select id="hari" name="hari">
                <?php
                $hari_array = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                foreach ($hari_array as $h) {
                    $selected = ($edit_data['hari'] == $h) ? 'selected' : '';
                    echo "<option value='$h' $selected>$h</option>";
                }
                ?>
            </select>

            <label for="jam_tidur">Jam Tidur</label>
            <input type="time" id="jam_tidur" name="jam_tidur" value="<?php echo $edit_data['jam_tidur']; ?>" required>

            <label for="jam_bangun">Jam Bangun</label>
            <input type="time" id="jam_bangun" name="jam_bangun" value="<?php echo $edit_data['jam_bangun']; ?>" required>

            <label for="kualitas">Kualitas Tidur</label>
            <select id="kualitas" name="kualitas">
                <?php
                $kualitas_array = ['Sangat Baik', 'Baik', 'Cukup', 'Buruk'];
                foreach ($kualitas_array as $k) {
                    $selected = ($edit_data['kualitas_tidur'] == $k) ? 'selected' : '';
                    echo "<option value='$k' $selected>$k</option>";
                }
                ?>
            </select>

            <button type="submit">Update</button>
            <a href="jadwal_tidur.php" class="btn-batal">Batal</a>
        </form>
    <?php else: ?>
        <h3>Tambah Jadwal Tidur</h3>
        <form action="api_tidur.php?action=add" method="POST">
            <input type="hidden" name="api_key" value="LATIHAN2026">

            <label for="hari">Hari</label>
            <select id="hari" name="hari">
                <option>Senin</option>
                <option>Selasa</option>
                <option>Rabu</option>
                <option>Kamis</option>
                <option>Jumat</option>
                <option>Sabtu</option>
                <option>Minggu</option>
            </select>

            <label for="jam_tidur">Jam Tidur</label>
            <input type="time" id="jam_tidur" name="jam_tidur" required>

            <label for="jam_bangun">Jam Bangun</label>
            <input type="time" id="jam_bangun" name="jam_bangun" required>

            <label for="kualitas">Kualitas Tidur</label>
            <select id="kualitas" name="kualitas">
                <option>Sangat Baik</option>
                <option>Baik</option>
                <option>Cukup</option>
                <option>Buruk</option>
            </select>

            <button type="submit">Simpan</button>
        </form>
    <?php endif; ?>

    <hr>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Hari</th>
                <th>Jam Tidur</th>
                <th>Jam Bangun</th>
                <th>Kualitas</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $_GET['action'] = "get";
            $_REQUEST['api_key'] = "LATIHAN2026";
            include "api_tidur.php";
            ?>
        </tbody>
    </table>

</div>

</body>
</html>