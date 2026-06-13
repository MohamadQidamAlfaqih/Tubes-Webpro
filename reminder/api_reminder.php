<?php
require_once "koneksi.php";

// Buat tabel reminder kalau belum ada
mysqli_query($koneksi, "CREATE TABLE IF NOT EXISTS reminder (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(100) NOT NULL,
    waktu TIME NOT NULL,
    jenis ENUM('Workout','Water Intake','Stretching','Sleep') NOT NULL,
    status ENUM('Aktif','Nonaktif') DEFAULT 'Aktif',
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$aksi = $_GET['aksi'] ?? '';

switch ($aksi) {

    // Ambil semua reminder
    case 'daftar':
        $hasil = mysqli_query($koneksi, "SELECT * FROM reminder ORDER BY waktu ASC");
        $list  = [];
        while ($r = mysqli_fetch_assoc($hasil)) $list[] = $r;
        kirimJSON(true, "OK", $list);
        break;

    // Simpan reminder baru
    case 'simpan':
        $body  = json_decode(file_get_contents("php://input"), true);
        $judul = $body['judul'];
        $waktu = $body['waktu'];
        $jenis = $body['jenis'];
        mysqli_query($koneksi, "INSERT INTO reminder (judul, waktu, jenis) VALUES ('$judul', '$waktu', '$jenis')");
        kirimJSON(true, "Reminder berhasil ditambahkan!");
        break;

    // Update reminder
    case 'update':
        $body  = json_decode(file_get_contents("php://input"), true);
        $id    = $body['id'];
        $judul = $body['judul'];
        $waktu = $body['waktu'];
        $jenis = $body['jenis'];
        mysqli_query($koneksi, "UPDATE reminder SET judul='$judul', waktu='$waktu', jenis='$jenis' WHERE id=$id");
        kirimJSON(true, "Reminder berhasil diubah!");
        break;

    // Toggle status aktif/nonaktif
    case 'toggle':
        $id = $_GET['id'];
        $r  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT status FROM reminder WHERE id=$id"));
        $baru = $r['status'] == 'Aktif' ? 'Nonaktif' : 'Aktif';
        mysqli_query($koneksi, "UPDATE reminder SET status='$baru' WHERE id=$id");
        kirimJSON(true, "Status diubah ke $baru");
        break;

    // Hapus reminder
    case 'hapus':
        $id = $_GET['id'];
        mysqli_query($koneksi, "DELETE FROM reminder WHERE id=$id");
        kirimJSON(true, "Reminder berhasil dihapus!");
        break;

    default:
        kirimJSON(false, "Aksi tidak dikenal");
        break;
}

function kirimJSON($sukses, $pesan, $data = null) {
    header("Content-Type: application/json");
    echo json_encode(['sukses' => $sukses, 'pesan' => $pesan, 'data' => $data], JSON_UNESCAPED_UNICODE);
    exit;
}