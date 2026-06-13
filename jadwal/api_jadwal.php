<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once "koneksi.php";

$aksi = $_GET['aksi'] ?? '';

switch ($aksi) {

    // Ambil statistik pengguna
    case 'statistik':
        $id   = $_GET['pengguna_id'] ?? 1;
        $stmt = mysqli_prepare($koneksi, "SELECT * FROM pengguna WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        kirimJSON(true, "OK", $data);
        break;

    // Ambil daftar latihan
    case 'daftar_latihan':
        $id   = $_GET['pengguna_id'] ?? 1;
        $stmt = mysqli_prepare($koneksi, "SELECT * FROM sesi_latihan WHERE pengguna_id = ? ORDER BY dibuat_pada DESC LIMIT 10");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $hasil = mysqli_stmt_get_result($stmt);
        $list  = [];
        while ($baris = mysqli_fetch_assoc($hasil)) $list[] = $baris;
        kirimJSON(true, "OK", $list);
        break;

    // Hitung estimasi kalori
    case 'hitung_kalori':
        $olahraga = $_GET['olahraga'] ?? 'Running';
        $durasi   = (int)($_GET['durasi'] ?? 30);
        $rate     = ['Running'=>10, 'Cycling'=>8, 'Yoga'=>4, 'Gym'=>7][$olahraga] ?? 6;
        kirimJSON(true, "OK", ['estimasi_kalori' => $rate * $durasi]);
        break;

    // Simpan latihan baru
    case 'simpan_latihan':
        $body = json_decode(file_get_contents("php://input"), true);
        $stmt = mysqli_prepare($koneksi,
            "INSERT INTO sesi_latihan (pengguna_id, jenis_latihan, repetisi, durasi_menit, kalori_terbakar, tanggal_latihan, waktu_mulai)
             VALUES (?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "issiiis",
            $body['pengguna_id'], $body['jenis_latihan'], $body['repetisi'],
            $body['durasi_menit'], $body['kalori_terbakar'], $body['tanggal_latihan'], $body['waktu_mulai']);
        mysqli_stmt_execute($stmt)
            ? kirimJSON(true, "Latihan berhasil disimpan!")
            : kirimJSON(false, "Gagal menyimpan");
        break;

    // Hapus latihan
    case 'hapus_latihan':
        $id   = (int)($_GET['id'] ?? 0);
        $stmt = mysqli_prepare($koneksi, "DELETE FROM sesi_latihan WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0
            ? kirimJSON(true, "Latihan berhasil dihapus")
            : kirimJSON(false, "Data tidak ditemukan");
        break;

    // Ambil ringkasan fase latihan (untuk jadwal.php)
    case 'fase_latihan':
        $id   = $_GET['pengguna_id'] ?? 1;
        $stmt = mysqli_prepare($koneksi,
            "SELECT jenis_latihan, SUM(durasi_menit) as total
             FROM sesi_latihan WHERE pengguna_id = ?
             GROUP BY jenis_latihan ORDER BY total DESC");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $hasil = mysqli_stmt_get_result($stmt);
        $list  = [];
        while ($baris = mysqli_fetch_assoc($hasil)) $list[] = $baris;
        kirimJSON(true, "OK", $list);
        break;

    default:
        kirimJSON(false, "Aksi tidak dikenal: $aksi");
        break;
}

// Fungsi kirim respon JSON
function kirimJSON($sukses, $pesan, $data = null) {
    echo json_encode([
        'sukses' => $sukses,
        'pesan'  => $pesan,
        'data'   => $data
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}