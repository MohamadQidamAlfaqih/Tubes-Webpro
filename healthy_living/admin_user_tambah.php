<?php
session_start();
include 'koneksi.php';

if(!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit;
}

if(isset($_POST['simpan'])){

    $nama = $_POST['nama_pengguna'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    mysqli_query($conn,"
        INSERT INTO users (nama_pengguna,email,password,role)
        VALUES ('$nama','$email','$password','$role')
    ");

    header("Location: admin_user_index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Tambah User</title>

<style>
body{
    font-family:Arial;
    background:#f4f6f9;
    margin:0;
}

.container{
    width:40%;
    margin:50px auto;
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

input, select{
    width:100%;
    padding:10px;
    margin:8px 0;
    border:1px solid #ddd;
    border-radius:6px;
}

button{
    width:100%;
    padding:10px;
    background:#28a745;
    color:white;
    border:none;
    border-radius:6px;
    cursor:pointer;
}

button:hover{
    background:#218838;
}

.back{
    display:block;
    text-align:center;
    margin-top:10px;
    text-decoration:none;
    color:#007bff;
}
</style>

</head>

<body>

<div class="container">

<div class="card">

<h2>➕ Tambah User</h2>

<form method="POST">

    <input type="text" name="nama_pengguna" placeholder="Nama" required>

    <input type="email" name="email" placeholder="Email" required>

    <input type="password" name="password" placeholder="Password" required>

    <select name="role">
        <option value="admin">Admin</option>
        <option value="pengguna">User</option>
    </select>

    <button type="submit" name="simpan">Simpan</button>

</form>

<a class="back" href="/healthy_living/admin_user_index.php">← Kembali</a>

</div>

</div>

</body>
</html>