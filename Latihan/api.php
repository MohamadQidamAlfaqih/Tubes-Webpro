<?php

include "koneksi.php";

$api_key = "LATIHAN2026";

if (!isset($_REQUEST['api_key'])) {
    die("API KEY tidak ditemukan");
}

if ($_REQUEST['api_key'] != $api_key) {
    die("API KEY salah");
}

if (isset($_GET['action'])) {

    // 1. GET DATA (Tampil di Tabel)
    if ($_GET['action'] == "get") {
        $query = mysqli_query($conn, "SELECT * FROM latihan_populer");

        while ($data = mysqli_fetch_assoc($query)) {
            echo "<tr>";
            echo "<td>".$data['id']."</td>";
            echo "<td>".$data['nama_latihan']."</td>";
            echo "<td>".$data['repetisi']."</td>";
            echo "<td>".$data['hari']."</td>";
            
            // Menambahkan tombol Edit dan Delete
            echo "<td>";
            echo "<a href='index.php?page=edit&id=".$data['id']."'>Edit</a> | ";
            echo "<a href='api.php?action=delete&id=".$data['id']."&api_key=LATIHAN2026' onclick='return confirm(\"Yakin ingin menghapus?\")'>Hapus</a>";
            echo "</td>";
            
            echo "</tr>";
        }
    }

    // 2. ADD DATA (Tambah)
    if ($_GET['action'] == "add") {
        $nama = $_POST['nama'];
        $repetisi = $_POST['repetisi'];
        $hari = $_POST['hari'];

        mysqli_query($conn, "INSERT INTO latihan_populer VALUES(NULL, '$nama', '$repetisi', '$hari')");

        header("Location:index.php");
        exit();
    }

    // 3. DELETE DATA (Hapus)
    if ($_GET['action'] == "delete") {
        $id = $_GET['id'];

        mysqli_query($conn, "DELETE FROM latihan_populer WHERE id = '$id'");

        header("Location:index.php");
        exit();
    }

    // 4. UPDATE DATA (Edit)
    if ($_GET['action'] == "update") {
        $id = $_POST['id'];
        $nama = $_POST['nama'];
        $repetisi = $_POST['repetisi'];
        $hari = $_POST['hari'];

        mysqli_query($conn, "UPDATE latihan_populer SET nama_latihan='$nama', repetisi='$repetisi', hari='$hari' WHERE id='$id'");

        header("Location:index.php");
        exit();
    }
}
?>