<?php
include 'koneksi.php';

header("Content-Type: application/json");

// Ambil header API KEY
$headers = getallheaders();

if (!isset($headers['API-KEY'])) {
    http_response_code(401);
    echo json_encode([
        "status" => false,
        "message" => "API Key diperlukan"
    ]);
    exit;
}

$api_key = $headers['API-KEY'];

// Validasi API Key
$user = mysqli_query($conn,
    "SELECT * FROM users WHERE api_key='$api_key'");

if (mysqli_num_rows($user) == 0) {
    http_response_code(401);
    echo json_encode([
        "status" => false,
        "message" => "API Key tidak valid"
    ]);
    exit;
}

$dataUser = mysqli_fetch_assoc($user);
$id_user = $dataUser['id_user'];

$method = $_SERVER['REQUEST_METHOD'];

switch($method){

    // ================= GET =================
    case 'GET':

        $query = mysqli_query($conn,
            "SELECT * FROM aktivitas
             WHERE id_user='$id_user'");

        $data = [];

        while($row = mysqli_fetch_assoc($query)){
            $data[] = $row;
        }

        echo json_encode([
            "status" => true,
            "data" => $data
        ]);
        break;


    // ================= POST =================
    case 'POST':

        $input = json_decode(file_get_contents("php://input"), true);

        $jenis = $input['jenis_olahraga'];
        $durasi = $input['durasi'];
        $kalori = $input['kalori'];
        $tanggal = $input['tanggal'];
        $catatan = $input['catatan'];
        $sumber = $input['sumber_data'];

        mysqli_query($conn,
            "INSERT INTO aktivitas
            (id_user, jenis_olahraga, durasi,
             kalori, tanggal, catatan, sumber_data)

            VALUES
            ('$id_user','$jenis','$durasi',
             '$kalori','$tanggal','$catatan','$sumber')");

        echo json_encode([
            "status" => true,
            "message" => "Data berhasil ditambahkan"
        ]);

        break;


    // ================= PUT =================
    case 'PUT':

        $input = json_decode(file_get_contents("php://input"), true);

        $id = $input['id_aktivitas'];

        mysqli_query($conn,
            "UPDATE aktivitas SET
            jenis_olahraga='".$input['jenis_olahraga']."',
            durasi='".$input['durasi']."',
            kalori='".$input['kalori']."',
            tanggal='".$input['tanggal']."',
            catatan='".$input['catatan']."',
            sumber_data='".$input['sumber_data']."'

            WHERE id_aktivitas='$id'
            AND id_user='$id_user'");

        echo json_encode([
            "status" => true,
            "message" => "Data berhasil diupdate"
        ]);

        break;


    // ================= DELETE =================
    case 'DELETE':

        $input = json_decode(file_get_contents("php://input"), true);

        $id = $input['id_aktivitas'];

        mysqli_query($conn,
            "DELETE FROM aktivitas
             WHERE id_aktivitas='$id'
             AND id_user='$id_user'");

        echo json_encode([
            "status" => true,
            "message" => "Data berhasil dihapus"
        ]);

        break;


    default:

        http_response_code(405);

        echo json_encode([
            "status" => false,
            "message" => "Method tidak diizinkan"
        ]);
}
?>