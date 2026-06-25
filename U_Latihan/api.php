<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, X-API-KEY");

if (!file_exists("koneksi.php")) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "File koneksi.php tidak ditemukan! Periksa lokasi file Anda."]);
    exit();
}

require_once "koneksi.php";

if (!isset($conn) || !$conn) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Variabel koneksi database (\$conn) tidak tersedia atau gagal terhubung!"]);
    exit();
}

$api_key_valid = "LATIHAN2026";

$client_key = '';
if (isset($_SERVER['HTTP_X_API_KEY'])) {
    $client_key = $_SERVER['HTTP_X_API_KEY'];
} elseif (function_exists('getallheaders')) {
    $headers = getallheaders();
    if (isset($headers['X-API-KEY'])) {
        $client_key = $headers['X-API-KEY'];
    }
}

if ($client_key !== $api_key_valid) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "API Key Tidak Valid / Salah!"]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $query = mysqli_query($conn, "SELECT * FROM latihan_populer ORDER BY id ASC");
    $data = [];
    if ($query) {
        while ($row = mysqli_fetch_assoc($query)) {
            $data[] = $row;
        }
        echo json_encode(["status" => "success", "data" => $data]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal mengambil data: " . mysqli_error($conn)]);
    }
    exit();
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);
    
    if (isset($input['nama'], $input['repetisi'], $input['hari'])) {
        $nama = mysqli_real_escape_string($conn, $input['nama']);
        $repetisi = mysqli_real_escape_string($conn, $input['repetisi']);
        $hari = mysqli_real_escape_string($conn, $input['hari']);
        
        $sql = "INSERT INTO latihan_populer (nama_latihan, repetisi, hari) VALUES ('$nama', '$repetisi', '$hari')";
        if (mysqli_query($conn, $sql)) {
            echo json_encode(["status" => "success", "message" => "Data latihan berhasil disimpan!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Gagal menyimpan data: " . mysqli_error($conn)]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Data input tidak lengkap."]);
    }
    exit();
}

if ($method === 'PUT') {
    $input = json_decode(file_get_contents("php://input"), true);
    
    if (isset($input['id'], $input['nama'], $input['repetisi'], $input['hari'])) {
        $id = mysqli_real_escape_string($conn, $input['id']);
        $nama = mysqli_real_escape_string($conn, $input['nama']);
        $repetisi = mysqli_real_escape_string($conn, $input['repetisi']);
        $hari = mysqli_real_escape_string($conn, $input['hari']);
        
        $sql = "UPDATE latihan_populer SET nama_latihan='$nama', repetisi='$repetisi', hari='$hari' WHERE id='$id'";
        if (mysqli_query($conn, $sql)) {
            echo json_encode(["status" => "success", "message" => "Data latihan berhasil diubah!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Gagal mengubah data: " . mysqli_error($conn)]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Data update tidak lengkap."]);
    }
    exit();
}

if ($method === 'DELETE') {
    if (isset($_GET['id'])) {
        $id = mysqli_real_escape_string($conn, $_GET['id']);
        $sql = "DELETE FROM latihan_populer WHERE id='$id'";
        if (mysqli_query($conn, $sql)) {
            echo json_encode(["status" => "success", "message" => "Data latihan berhasil dihapus!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Gagal menghapus data: " . mysqli_error($conn)]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "ID data tidak ditemukan."]);
    }
    exit();
}
?>