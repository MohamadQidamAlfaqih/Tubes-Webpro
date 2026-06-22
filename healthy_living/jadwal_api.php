<?php
header("Content-Type: application/json");
session_start();
include "../koneksi.php";

// pastikan user login
if (!isset($_SESSION['id_user'])) {
    echo json_encode(["message" => "Unauthorized"]);
    exit;
}

$id_user = $_SESSION['id_user'];

$method = $_SERVER['REQUEST_METHOD'];


// =======================
// GET - ambil data jadwal user
// =======================
if ($method == "GET") {

    $query = mysqli_query($conn,
        "SELECT * FROM jadwal WHERE id_user='$id_user' ORDER BY hari ASC"
    );

    $data = [];

    while ($row = mysqli_fetch_assoc($query)) {
        $data[] = $row;
    }

    echo json_encode($data);
}


// =======================
// POST - tambah jadwal
// =======================
elseif ($method == "POST") {

    $data = json_decode(file_get_contents("php://input"), true);

    $hari = $data['hari'];
    $waktu = $data['waktu_mulai'];
    $kegiatan = $data['jenis_kegiatan'];

    $query = mysqli_query($conn,
        "INSERT INTO jadwal (id_user, hari, waktu_mulai, jenis_kegiatan)
         VALUES ('$id_user', '$hari', '$waktu', '$kegiatan')"
    );

    if ($query) {
        echo json_encode(["message" => "Jadwal berhasil ditambahkan"]);
    } else {
        echo json_encode(["message" => "Gagal menambah data"]);
    }
}


// =======================
// PUT - update jadwal
// =======================
elseif ($method == "PUT") {

    $data = json_decode(file_get_contents("php://input"), true);

    $id = $data['id_jadwal'];
    $hari = $data['hari'];
    $waktu = $data['waktu_mulai'];
    $kegiatan = $data['jenis_kegiatan'];

    $query = mysqli_query($conn,
        "UPDATE jadwal SET
            hari='$hari',
            waktu_mulai='$waktu',
            jenis_kegiatan='$kegiatan'
         WHERE id_jadwal='$id' AND id_user='$id_user'"
    );

    if ($query) {
        echo json_encode(["message" => "Jadwal berhasil diupdate"]);
    } else {
        echo json_encode(["message" => "Gagal update"]);
    }
}


// =======================
// DELETE - hapus jadwal
// =======================
elseif ($method == "DELETE") {

    $data = json_decode(file_get_contents("php://input"), true);

    $id = $data['id_jadwal'];

    $query = mysqli_query($conn,
        "DELETE FROM jadwal WHERE id_jadwal='$id' AND id_user='$id_user'"
    );

    if ($query) {
        echo json_encode(["message" => "Jadwal berhasil dihapus"]);
    } else {
        echo json_encode(["message" => "Gagal hapus"]);
    }
}
?>