<?php
session_start();

if(!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>

<style>
body{
    margin:0;
    font-family:Arial;
    background:#f4f6f9;
}

.container{
    width:90%;
    margin:30px auto;
}

.header{
    background:#111827;
    color:white;
    padding:20px;
    border-radius:10px;
}

.grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:15px;
    margin-top:20px;
}

.card{
    background:white;
    padding:20px;
    border-radius:10px;
    text-align:center;
    box-shadow:0 2px 10px rgba(0,0,0,0.08);
}

.card a{
    text-decoration:none;
    font-weight:bold;
    color:#111;
}

.logout{
    display:inline-block;
    margin-top:20px;
    padding:10px 15px;
    background:red;
    color:white;
    text-decoration:none;
    border-radius:8px;
}
</style>

</head>

<body>

<div class="container">

<div class="header">
    <h2>👑 Admin Panel</h2>
    <p>Welcome <?= $_SESSION['nama']; ?></p>
</div>

<div class="grid">

    <!-- ADMIN KHUSUS -->
    <div class="card">
        <a href="/healthy_living/admin_user_index.php">👤 Manajemen User</a>
    </div>

    <div class="card">
        <a href="/healthy_living/admin_aktivitas_index.php">📊 Semua Aktivitas User</a>
    </div>

</div>

<a class="logout" href="/healthy_living/logout.php">Logout</a>

</div>

</body>
</html>