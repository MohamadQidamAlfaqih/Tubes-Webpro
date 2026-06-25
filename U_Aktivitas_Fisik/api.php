<?php
session_start();
include "koneksi.php";

header("Content-Type: application/json");

if (!isset($_SESSION['user_logged_in'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Silakan login terlebih dahulu"
    ]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "GET") {

    if (isset($_GET['id'])) {
        $id = mysqli_real_escape_string($conn, $_GET['id']);
        $query = mysqli_query($conn, "SELECT * FROM aktivitas_fisik WHERE id = '$id'");
        
        if (mysqli_num_rows($query) > 0) {
            $row = mysqli_fetch_assoc($query);
            echo json_encode([
                "status" => "success",
                "data" => $row
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Data aktivitas tidak ditemukan"
            ]);
        }
        exit();
    }

    $data = [];
    $query = mysqli_query($conn, "SELECT * FROM aktivitas_fisik ORDER BY id ASC");

    if ($query) {
        while ($row = mysqli_fetch_assoc($query)) {
            $data[] = $row;
        }
    }

    echo json_encode([
        "status" => "success",
        "data" => $data
    ]);
    exit();
}

if ($method == "POST") {

    $aksi = $_POST['aksi'] ?? '';

    if ($aksi == "create") {

        $nama = mysqli_real_escape_string($conn, $_POST['nama_aktivitas']);
        $durasi = mysqli_real_escape_string($conn, $_POST['durasi']);
        $kalori = mysqli_real_escape_string($conn, $_POST['kalori']);

        $sql = "INSERT INTO aktivitas_fisik (nama_aktivitas, durasi, kalori) VALUES ('$nama', '$durasi', '$kalori')";

        if (mysqli_query($conn, $sql)) {
            echo json_encode([
                "status" => "success",
                "message" => "Data berhasil ditambahkan"
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => mysqli_error($conn)
            ]);
        }
        exit();
    }

    if ($aksi == "update") {

        $id = mysqli_real_escape_string($conn, $_POST['id']);
        $nama = mysqli_real_escape_string($conn, $_POST['nama_aktivitas']);
        $durasi = mysqli_real_escape_string($conn, $_POST['durasi']);
        $kalori = mysqli_real_escape_string($conn, $_POST['kalori']);

        $sql = "UPDATE aktivitas_fisik SET nama_aktivitas='$nama', durasi='$durasi', kalori='$kalori' WHERE id='$id'";

        if (mysqli_query($conn, $sql)) {
            echo json_encode([
                "status" => "success",
                "message" => "Data berhasil diubah"
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => mysqli_error($conn)
            ]);
        }
        exit();
    }

    if ($aksi == "delete") {

        $id = mysqli_real_escape_string($conn, $_POST['id']);
        $sql = "DELETE FROM aktivitas_fisik WHERE id='$id'";

        if (mysqli_query($conn, $sql)) {
            echo json_encode([
                "status" => "success",
                "message" => "Data berhasil dihapus"
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => mysqli_error($conn)
            ]);
        }
        exit();
    }
}
?>