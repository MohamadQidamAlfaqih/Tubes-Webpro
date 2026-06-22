<?php
session_start();
include 'koneksi.php';

// Cek login
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'];

$query = mysqli_query($conn,
    "DELETE FROM aktivitas
     WHERE id_aktivitas='$id'");

if ($query) {
    header("Location: aktivitas_index.php");
    exit;
} else {
    echo "Error: " . mysqli_error($conn);
}
?>