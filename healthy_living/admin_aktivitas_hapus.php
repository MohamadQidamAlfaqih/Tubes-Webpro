<?php
session_start();
include 'koneksi.php';

if(!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit;
}

$id = $_GET['id'];

mysqli_query($conn,"
DELETE FROM aktivitas WHERE id_aktivitas='$id'
");

header("Location: admin_aktivitas_index.php");
exit;
?>