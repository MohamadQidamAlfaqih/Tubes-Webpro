<?php
require_once "koneksi.php";

// HAPUS
if (isset($_GET['hapus'])) {
    mysqli_query($koneksi, "DELETE FROM sesi_latihan WHERE id = " . $_GET['hapus']);
    header("Location: jadwal.php?berhasil=hapus");
    exit;
}

// EDIT - ambil data lama
$data_edit = null;
if (isset($_GET['edit'])) {
    $data_edit = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM sesi_latihan WHERE id = " . $_GET['edit']));
}

// SIMPAN / UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal  = $_POST['tanggal'];
    $waktu    = $_POST['waktu'];
    $jenis    = $_POST['jenis_latihan'];
    $durasi   = $_POST['durasi_menit'];
    $kualitas = $_POST['kualitas'];

    if (!empty($_POST['id_edit'])) {
        mysqli_query($koneksi, "UPDATE sesi_latihan SET
            tanggal_latihan='$tanggal', waktu_mulai='$waktu',
            jenis_latihan='$jenis', durasi_menit='$durasi', repetisi='$kualitas'
            WHERE id=" . $_POST['id_edit']);
        header("Location: jadwal.php?berhasil=edit");
    } else {
        mysqli_query($koneksi, "INSERT INTO sesi_latihan
            (pengguna_id, tanggal_latihan, waktu_mulai, jenis_latihan, durasi_menit, repetisi, kalori_terbakar)
            VALUES (1, '$tanggal', '$waktu', '$jenis', '$durasi', '$kualitas', 0)");
        header("Location: jadwal.php?berhasil=tambah");
    }
    exit;
}

// Ambil semua sesi latihan
$latihan = mysqli_query($koneksi, "SELECT * FROM sesi_latihan WHERE pengguna_id = 1 ORDER BY tanggal_latihan DESC LIMIT 10");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jadwal Latihan</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
        h1   { color: #2e7d32; margin-bottom: 20px; }

        /* FORM */
        .form { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.07); }
        .form h2 { font-size: 15px; margin-bottom: 14px; color: #333; }
        .form select, .form input {
            padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px;
            font-size: 14px; margin-right: 8px; margin-bottom: 10px;
        }
        .btn-simpan { background: #2e7d32; color: white; border: none; padding: 9px 20px; border-radius: 6px; cursor: pointer; }
        .btn-batal  { background: #888;    color: white; border: none; padding: 9px 16px; border-radius: 6px; cursor: pointer; text-decoration: none; display: inline-block; }

        /* TABEL */
        .tabel { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.07); }
        .tabel h2 { font-size: 15px; margin-bottom: 14px; color: #333; }
        table  { width: 100%; border-collapse: collapse; font-size: 14px; }
        th { background: #f5f5f5; padding: 10px; text-align: left; font-size: 12px; color: #888; }
        td { padding: 10px; border-bottom: 1px solid #f5f5f5; }

        /* BADGE */
        .badge { padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .baik   { background: #e8f5e9; color: #2e7d32; }
        .sedang { background: #fff3e0; color: #e65100; }
        .tidak  { background: #ffebee; color: #e53935; }

        /* TOMBOL TABEL */
        .btn-edit  { background: #1565c0; color: white; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer; font-size: 12px; text-decoration: none; }
        .btn-hapus { background: #e53935; color: white; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer; font-size: 12px; margin-left: 4px; }

        /* NOTIFIKASI */
        .notif { padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; }
        .hijau { background: #e8f5e9; color: #2e7d32; }
        .biru  { background: #e3f2fd; color: #1565c0; }
        .merah { background: #ffebee; color: #e53935; }
    </style>
</head>
<body>

<h1>🌿 Jadwal Latihan</h1>

<!-- NOTIFIKASI -->
<?php if (isset($_GET['berhasil'])): ?>
    <?php if ($_GET['berhasil'] == 'tambah'): ?>
        <div class="notif hijau">✅ Latihan berhasil ditambahkan!</div>
    <?php elseif ($_GET['berhasil'] == 'edit'): ?>
        <div class="notif biru">✏️ Latihan berhasil diubah!</div>
    <?php elseif ($_GET['berhasil'] == 'hapus'): ?>
        <div class="notif merah">🗑️ Latihan berhasil dihapus!</div>
    <?php endif; ?>
<?php endif; ?>

<!-- FORM -->
<div class="form">
    <h2><?= $data_edit ? '✏️ Edit Latihan' : '➕ Tambah Latihan' ?></h2>
    <form method="POST">
        <?php if ($data_edit): ?>
            <input type="hidden" name="id_edit" value="<?= $data_edit['id'] ?>">
        <?php endif; ?>

        <input type="date" name="tanggal" required value="<?= $data_edit['tanggal_latihan'] ?? date('Y-m-d') ?>">
        <input type="time" name="waktu"   required value="<?= $data_edit ? substr($data_edit['waktu_mulai'], 0, 5) : '08:00' ?>">

        <select name="jenis_latihan">
            <?php foreach (['Running','Cycling','Yoga','Gym','Push Up','Sit Up'] as $p): ?>
                <option <?= ($data_edit && $data_edit['jenis_latihan'] == $p) ? 'selected' : '' ?>><?= $p ?></option>
            <?php endforeach; ?>
        </select>

        <input type="number" name="durasi_menit" placeholder="Durasi (menit)" min="1" required value="<?= $data_edit['durasi_menit'] ?? '' ?>">

        <select name="kualitas">
            <?php foreach (['Kualitas Baik','Kualitas Sedang','Tidak Baik'] as $k): ?>
                <option <?= ($data_edit && $data_edit['repetisi'] == $k) ? 'selected' : '' ?>><?= $k ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn-simpan"><?= $data_edit ? 'Simpan Perubahan' : 'Simpan' ?></button>
        <?php if ($data_edit): ?>
            <a href="jadwal.php" class="btn-batal">Batal</a>
        <?php endif; ?>
    </form>
</div>

<!-- TABEL -->
<div class="tabel">
    <h2>📋 Sesi Latihan Terbaru</h2>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Waktu</th>
                <th>Latihan</th>
                <th>Durasi</th>
                <th>Kualitas</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($b = mysqli_fetch_assoc($latihan)): ?>
            <tr>
                <td><?= date('d M Y', strtotime($b['tanggal_latihan'])) ?></td>
                <td><?= substr($b['waktu_mulai'], 0, 5) ?></td>
                <td><?= $b['jenis_latihan'] ?></td>
                <td><?= $b['durasi_menit'] ?> menit</td>
                <td>
                    <?php
                    $k = $b['repetisi'];
                    $kelas = $k == 'Kualitas Baik' ? 'baik' : ($k == 'Kualitas Sedang' ? 'sedang' : 'tidak');
                    ?>
                    <span class="badge <?= $kelas ?>"><?= $k ?></span>
                </td>
                <td>
                    <a href="jadwal.php?edit=<?= $b['id'] ?>" class="btn-edit">✏️ Edit</a>
                    <button class="btn-hapus" onclick="if(confirm('Yakin hapus?')) window.location='jadwal.php?hapus=<?= $b['id'] ?>'">🗑️ Hapus</button>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>