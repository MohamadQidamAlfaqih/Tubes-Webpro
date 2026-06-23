<?php
session_start();
include 'koneksi.php';

if(!isset($_SESSION['id_user'])){
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$id = $_GET['id'];

$query = mysqli_query($conn,
    "DELETE FROM reminder
     WHERE id_reminder='$id'
     AND id_user='$id_user'");

if($query){
    header("Location: reminder_index.php");
    exit;
} else {
    echo mysqli_error($conn);
}
?>