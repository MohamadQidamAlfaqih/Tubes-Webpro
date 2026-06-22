<?php
header("Content-Type: application/json");
session_start();
include "../koneksi.php";

// cek login
if (!isset($_SESSION['id_user'])) {
    echo json_encode(["message" => "Unauthorized"]);
    exit;
}

$id_user = $_SESSION['id_user'];
$method = $_SERVER['REQUEST_METHOD'];


// =========================
// GET - ambil reminder user
// =========================
if ($method == "GET") {

    $query = mysqli_query($conn,
        "SELECT r.*, j.jenis_kegiatan
         FROM reminder r
         JOIN jadwal j ON r.id_jadwal = j.id_jadwal
         WHERE r.id_user='$id_user'
         ORDER BY r.waktu ASC"
    );

    $data = [];

    while ($row = mysqli_fetch_assoc($query)) {
        $data[] = $row;
    }

    echo json_encode($data);
}


// =========================
// POST - tambah reminder
// =========================
elseif ($method == "POST") {

    $data = json_decode(file_get_contents("php://input"), true);

    $id_jadwal = $data['id_jadwal'];
    $judul = $data['judul'];
    $waktu = $data['waktu'];
    $pesan = $data['pesan'];
    $status = $data['status'];

    $query = mysqli_query($conn,
        "INSERT INTO reminder
        (id_user, id_jadwal, judul, waktu, pesan, status)
        VALUES
        ('$id_user', '$id_jadwal', '$judul', '$waktu', '$pesan', '$status')"
    );

    if ($query) {
        echo json_encode(["message" => "Reminder berhasil ditambahkan"]);
    } else {
        echo json_encode(["message" => "Gagal menambahkan reminder"]);
    }
}


// =========================
// PUT - update reminder
// =========================
elseif ($method == "PUT") {

    $data = json_decode(file_get_contents("php://input"), true);

    $id_reminder = $data['id_reminder'];
    $id_jadwal = $data['id_jadwal'];
    $judul = $data['judul'];
    $waktu = $data['waktu'];
    $pesan = $data['pesan'];
    $status = $data['status'];

    $query = mysqli_query($conn,
        "UPDATE reminder SET
            id_jadwal='$id_jadwal',
            judul='$judul',
            waktu='$waktu',
            pesan='$pesan',
            status='$status'
         WHERE id_reminder='$id_reminder'
         AND id_user='$id_user'"
    );

    if ($query) {
        echo json_encode(["message" => "Reminder berhasil diupdate"]);
    } else {
        echo json_encode(["message" => "Gagal update reminder"]);
    }
}


// =========================
// DELETE - hapus reminder
// =========================
elseif ($method == "DELETE") {

    $data = json_decode(file_get_contents("php://input"), true);

    $id_reminder = $data['id_reminder'];

    $query = mysqli_query($conn,
        "DELETE FROM reminder
         WHERE id_reminder='$id_reminder'
         AND id_user='$id_user'"
    );

    if ($query) {
        echo json_encode(["message" => "Reminder berhasil dihapus"]);
    } else {
        echo json_encode(["message" => "Gagal hapus reminder"]);
    }
}
?>