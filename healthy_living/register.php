<?php
include 'koneksi.php';

if(isset($_POST['register'])){

    $nama = $_POST['nama_pengguna'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $jk = $_POST['jenis_kelamin'];
    $tinggi = $_POST['tinggi_badan'];
    $berat = $_POST['berat_badan'];

    $api_key = bin2hex(random_bytes(16));

    mysqli_query($conn,
    "INSERT INTO users
    (nama_pengguna,email,password,jenis_kelamin,tinggi_badan,berat_badan,role,api_key)
    VALUES
    ('$nama','$email','$password','$jk','$tinggi','$berat','pengguna','$api_key')");

    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>

    <style>
        body {
            font-family: Arial;
            background: linear-gradient(135deg, #43e97b, #38f9d7);
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .card {
            background: white;
            padding: 25px;
            width: 400px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        }

        h2 {
            text-align: center;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        button {
            width: 100%;
            padding: 10px;
            background: #43e97b;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background: #2ecc71;
        }

        .link {
            text-align: center;
            margin-top: 10px;
        }

        .link a {
            color: #2ecc71;
            text-decoration: none;
        }
    </style>
</head>

<body>

<div class="card">

    <h2>📝 Register</h2>

    <form method="POST">

        <input type="text" name="nama_pengguna" placeholder="Nama" required>

        <input type="email" name="email" placeholder="Email" required>

        <input type="password" name="password" placeholder="Password" required>

        <select name="jenis_kelamin">
            <option value="L">Laki-laki</option>
            <option value="P">Perempuan</option>
        </select>

        <input type="number" name="tinggi_badan" placeholder="Tinggi Badan">

        <input type="number" name="berat_badan" placeholder="Berat Badan">

        <button type="submit" name="register">Register</button>

    </form>

    <div class="link">
        Sudah punya akun? <a href="login.php">Login</a>
    </div>

</div>

</body>
</html>