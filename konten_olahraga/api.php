<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type, X-API-KEY");

require 'koneksi.php';

// Konfigurasi API Key yang valid
$api_key_valid = "RAHASIA_KONTEN_OLAHRAGA_123";
$headers = getallheaders();

// Validasi keberadaan dan kesesuaian API Key
if (!isset($headers['X-API-KEY']) || $headers['X-API-KEY'] !== $api_key_valid) {
    http_response_code(401);
    echo json_encode([
        "status" => "error", 
        "message" => "Unauthorized: API Key tidak valid atau tidak disertakan."
    ]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

// =======================================================
// METHOD 1: GET (Mengambil Semua Data Konten Olahraga)
// =======================================================
if ($method === 'GET') {
    $sql = "SELECT * FROM konten_olahraga ORDER BY tanggal_dibuat DESC";
    $result = $conn->query($sql);
    $data = [];
    
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    
    http_response_code(200);
    echo json_encode([
        "status" => "success", 
        "total_data" => count($data),
        "data" => $data
    ]);
    exit();
}

// =======================================================
// METHOD 2: POST (Menambahkan Konten Olahraga Baru)
// =======================================================
if ($method === 'POST') {
    // Membaca data JSON yang dikirimkan melalui body request
    $input = json_decode(file_get_contents("php://input"), true);
    
    if (!empty($input['judul']) && !empty($input['kategori']) && !empty($input['deskripsi'])) {
        $judul = $conn->real_escape_string($input['judul']);
        $kategori = $conn->real_escape_string($input['kategori']);
        $deskripsi = $conn->real_escape_string($input['deskripsi']);
        
        $sql = "INSERT INTO konten_olahraga (judul, kategori, deskripsi) VALUES ('$judul', '$kategori', '$deskripsi')";
        
        if ($conn->query($sql)) {
            http_response_code(201);
            echo json_encode([
                "status" => "success", 
                "message" => "Konten olahraga berhasil ditambahkan via API."
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "status" => "error", 
                "message" => "Gagal menyimpan data ke database."
            ]);
        }
    } else {
        http_response_code(400);
        echo json_encode([
            "status" => "error", 
            "message" => "Data tidak lengkap. Pastikan 'judul', 'kategori', dan 'deskripsi' terisi."
        ]);
    }
    exit();
}

// Jika method request bukan GET atau POST
http_response_code(405);
echo json_encode([
    "status" => "error", 
    "message" => "Method HTTP tidak diizinkan."
]);
?>