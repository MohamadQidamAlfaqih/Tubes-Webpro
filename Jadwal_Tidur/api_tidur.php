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

    // GET DATA
    if ($_GET['action'] == "get") {
        $query = mysqli_query($conn, "SELECT * FROM jadwal_tidur");

        while ($data = mysqli_fetch_assoc($query)) {
            echo "<tr>";
            echo "<td>".$data['id']."</td>";
            echo "<td>".$data['hari']."</td>";
            echo "<td>".$data['jam_tidur']."</td>";
            echo "<td>".$data['jam_bangun']."</td>";
            echo "<td>".$data['kualitas_tidur']."</td>";
            echo "<td>";
            echo "<a href='jadwal_tidur.php?page=edit&id=".$data['id']."'>Edit</a> | ";
            echo "<a href='api_tidur.php?action=delete&id=".$data['id']."&api_key=LATIHAN2026' onclick='return confirm(\"Hapus jadwal ini?\")'>Hapus</a>";
            echo "</td>";
            echo "</tr>";
        }
    }

    // ADD DATA
    if ($_GET['action'] == "add") {
        $hari = $_POST['hari'];
        $jam_tidur = $_POST['jam_tidur'];
        $jam_bangun = $_POST['jam_bangun'];
        $kualitas = $_POST['kualitas'];

        mysqli_query($conn, "INSERT INTO jadwal_tidur VALUES(NULL, '$hari', '$jam_tidur', '$jam_bangun', '$kualitas')");

        header("Location:jadwal_tidur.php");
        exit();
    }

    // DELETE DATA
    if ($_GET['action'] == "delete") {
        $id = $_GET['id'];

        mysqli_query($conn, "DELETE FROM jadwal_tidur WHERE id = '$id'");

        header("Location:jadwal_tidur.php");
        exit();
    }

    // UPDATE DATA
    if ($_GET['action'] == "update") {
        $id = $_POST['id'];
        $hari = $_POST['hari'];
        $jam_tidur = $_POST['jam_tidur'];
        $jam_bangun = $_POST['jam_bangun'];
        $kualitas = $_POST['kualitas'];

        mysqli_query($conn, "UPDATE jadwal_tidur SET hari='$hari', jam_tidur='$jam_tidur', jam_bangun='$jam_bangun', kualitas_tidur='$kualitas' WHERE id='$id'");

        header("Location:jadwal_tidur.php");
        exit();
    }
}
?>