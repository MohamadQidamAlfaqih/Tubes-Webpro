<?php
session_start();
include 'koneksi.php';

if(!isset($_SESSION['id_user'])){
    header("Location: login.php");
    exit;
}

$id = $_GET['id'];

$query = mysqli_query($conn,
    "DELETE FROM jadwal
     WHERE id_jadwal='$id'");

if($query){
    header("Location: jadwal_index.php");
    exit;
} else {
    echo mysqli_error($conn);
}
?>