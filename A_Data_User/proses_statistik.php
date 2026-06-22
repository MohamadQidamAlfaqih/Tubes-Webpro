<?php
session_start();
include "koneksi.php";

if (isset($_POST['aksi'])) {
    $aksi = $_POST['aksi'];

    if ($aksi === 'create') {
        $nama = mysqli_real_escape_string($conn, $_POST['nama_pengguna']);
        $bb   = mysqli_real_escape_string($conn, $_POST['berat_badan']);
        $tb   = mysqli_real_escape_string($conn, $_POST['tinggi_badan']);
        $target = mysqli_real_escape_string($conn, $_POST['status_target']);

        $sql = "INSERT INTO statistik_pengguna (nama_pengguna, berat_badan, tinggi_badan, status_target) VALUES ('$nama', '$bb', '$tb', '$target')";
        
        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Data statistik berhasil ditambahkan!'); window.location='admin_statistik.php';</script>";
        } else {
            echo "<script>alert('Gagal menambahkan data!'); window.location='admin_statistik.php';</script>";
        }
        exit();
    }

    if ($aksi === 'update') {
        $id   = mysqli_real_escape_string($conn, $_POST['id']);
        $nama = mysqli_real_escape_string($conn, $_POST['nama_pengguna']);
        $bb   = mysqli_real_escape_string($conn, $_POST['berat_badan']);
        $tb   = mysqli_real_escape_string($conn, $_POST['tinggi_badan']);
        $target = mysqli_real_escape_string($conn, $_POST['status_target']);

        $sql = "UPDATE statistik_pengguna SET nama_pengguna='$nama', berat_badan='$bb', tinggi_badan='$tb', status_target='$target' WHERE id='$id'";
        
        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Data statistik berhasil diubah!'); window.location='admin_statistik.php';</script>";
        } else {
            echo "<script>alert('Gagal mengubah data!'); window.location='admin_statistik.php';</script>";
        }
        exit();
    }

    if ($aksi === 'delete') {
        $id = mysqli_real_escape_string($conn, $_POST['id']);

        $sql = "DELETE FROM statistik_pengguna WHERE id='$id'";
        
        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Data statistik berhasil dihapus!'); window.location='admin_statistik.php';</script>";
        } else {
            echo "<script>alert('Gagal menghapus data!'); window.location='admin_statistik.php';</script>";
        }
        exit();
    }
} else {
    header("Location: admin_statistik.php");
    exit();
}
?>