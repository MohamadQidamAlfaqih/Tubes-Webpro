<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include "koneksi.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'];

    if ($aksi === 'create') {
        $judul = mysqli_real_escape_string($conn, $_POST['judul']);
        $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
        $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);

        $sql = "INSERT INTO konten_olahraga (judul, kategori, deskripsi) VALUES ('$judul', '$kategori', '$deskripsi')";
        mysqli_query($conn, $sql);
        
        header("Location: admin_konten.php");
        exit();
    }

    if ($aksi === 'update') {
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        $judul = mysqli_real_escape_string($conn, $_POST['judul']);
        $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
        $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);

        $sql = "UPDATE konten_olahraga SET judul='$judul', kategori='$kategori', deskripsi='$deskripsi' WHERE id='$id'";
        mysqli_query($conn, $sql);

        header("Location: admin_konten.php");
        exit();
    }

    if ($aksi === 'delete') {
        $id = mysqli_real_escape_string($conn, $_POST['id']);

        $sql = "DELETE FROM konten_olahraga WHERE id='$id'";
        mysqli_query($conn, $sql);

        header("Location: admin_konten.php");
        exit();
    }
}

header("Location: admin_konten.php");
exit();
?>