<?php
session_start();
include 'koneksi.php';

if($_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit;
}

$id = $_GET['id'];

mysqli_query($conn, "DELETE FROM users WHERE id_user='$id'");

header("Location: admin_user_index.php");
exit;
?>