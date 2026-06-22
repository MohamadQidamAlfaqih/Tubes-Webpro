<?php
session_start();
include 'koneksi.php';

if(!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit;
}

$id = $_GET['id'];

$data = mysqli_query($conn,"SELECT * FROM users WHERE id_user='$id'");
$row = mysqli_fetch_assoc($data);

if(isset($_POST['update'])){

    $nama = $_POST['nama_pengguna'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    mysqli_query($conn,"
        UPDATE users SET
        nama_pengguna='$nama',
        email='$email',
        role='$role'
        WHERE id_user='$id'
    ");

    header("Location: admin_user_index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit User</title>

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
    color:#007bff;
}
</style>

</head>

<body>

<div class="container">

<div class="card">

<h2>✏️ Edit User</h2>

<form method="POST">

    <input type="text" name="nama_pengguna" value="<?= $row['nama_pengguna'] ?>" required>

    <input type="email" name="email" value="<?= $row['email'] ?>" required>

    <select name="role">
        <option value="admin" <?= $row['role']=='admin'?'selected':'' ?>>Admin</option>
        <option value="pengguna" <?= $row['role']=='pengguna'?'selected':'' ?>>User</option>
    </select>

    <button type="submit" name="update">Update</button>

</form>

<a class="back" href="/healthy_living/admin_user_index.php">← Kembali</a>

</div>

</div>

</body>
</html>