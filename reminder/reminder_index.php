<?php
session_start();
include 'koneksi.php';

if(!isset($_SESSION['id_user'])){
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

// Sapaan
$jam = (int)date('H');
if($jam < 11)      $sapaan = "Selamat Pagi";
elseif($jam < 15)  $sapaan = "Selamat Siang";
elseif($jam < 18)  $sapaan = "Selamat Sore";
else               $sapaan = "Selamat Malam";

// Query reminder
$query = mysqli_query($conn,
"SELECT r.*, j.jenis_kegiatan
 FROM reminder r
 JOIN jadwal j ON r.id_jadwal = j.id_jadwal
 WHERE r.id_user='$id_user'
 ORDER BY r.waktu ASC");

// Statistik
$qTotal = mysqli_query($conn,
    "SELECT COUNT(*) total_reminder FROM reminder WHERE id_user='$id_user'");
$stat = mysqli_fetch_assoc($qTotal);
?>

<!DOCTYPE html>
<html>
<head>
<title>Reminder</title>

<style>
:root{
    --green:#22c55e;
    --green-dark:#16a34a;
    --ink:#1f2430;
    --muted:#8a93a3;
    --line:#edeff2;
    --bg:#f6f7f9;
}

body{
    margin:0;
    font-family:Arial;
    background:var(--bg);
}

/* NAVBAR */
.navbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    background:#fff;
    padding:14px 28px;
    border-bottom:1px solid var(--line);
}

.nav-links{
    display:flex;
    gap:20px;
    font-size:13px;
}

.nav-links a{
    text-decoration:none;
    color:var(--muted);
}

.nav-links a.active{
    color:var(--green-dark);
    font-weight:bold;
}

.avatar{
    width:34px;
    height:34px;
    border-radius:50%;
    background:linear-gradient(135deg,#4facfe,#00f2fe);
    color:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
}

/* CONTAINER */
.container{
    max-width:1000px;
    margin:25px auto;
    padding:0 20px;
}

.panel{
    background:#fff;
    padding:20px;
    border-radius:10px;
    margin-bottom:20px;
}

/* STAT */
.stat{
    font-size:26px;
    color:var(--green-dark);
    font-weight:bold;
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
}

th{
    text-align:left;
    font-size:12px;
    color:var(--muted);
    border-bottom:1px solid var(--line);
    padding:10px;
}

td{
    padding:10px;
    border-bottom:1px solid var(--line);
    font-size:13px;
}

.status-aktif{
    color:green;
    font-weight:bold;
}

.status-nonaktif{
    color:red;
    font-weight:bold;
}

.edit{
    color:orange;
    font-weight:bold;
    margin-right:10px;
}

.hapus{
    color:red;
    font-weight:bold;
}

.btn{
    display:inline-block;
    background:var(--green);
    color:white;
    padding:10px 15px;
    border-radius:6px;
    text-decoration:none;
    font-size:13px;
}

.btn:hover{
    background:var(--green-dark);
}
</style>
</head>

<body>

<!-- NAVBAR -->
<div class="navbar">
    <div><b>HIDUP SEHAT</b></div>

    <div class="nav-links">
        <a href="dashboard_user.php">Dashboard</a>
        <a href="aktivitas_index.php">Activities</a>
        <a href="latihan_index.php">Latihan</a>
        <a href="jadwal_index.php">Calendar</a>
        <a class="active" href="reminder_index.php">Reminders</a>
    </div>

    <div class="avatar"><?= strtoupper(substr($_SESSION['nama'],0,1)) ?></div>
</div>

<div class="container">

    <h3><?= $sapaan ?>, <?= $_SESSION['nama'] ?></h3>
    <p style="color:gray;">Kelola pengingat aktivitasmu di sini</p>

    <!-- STAT -->
    <div class="panel">
        <div>Total Reminder</div>
        <div class="stat"><?= $stat['total_reminder'] ?> Reminder</div>
    </div>

    <!-- BUTTON -->
    <a href="reminder_tambah.php" class="btn">+ Tambah Reminder</a>

    <!-- TABLE -->
    <div class="panel">
        <h4>Daftar Reminder</h4>

        <table>
            <tr>
                <th>Judul</th>
                <th>Jadwal</th>
                <th>Waktu</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>

            <?php while($row=mysqli_fetch_assoc($query)) { ?>
            <tr>
                <td><?= $row['judul'] ?></td>
                <td><?= $row['jenis_kegiatan'] ?></td>
                <td><?= $row['waktu'] ?></td>
                <td class="<?= $row['status']=='aktif'?'status-aktif':'status-nonaktif' ?>">
                    <?= $row['status'] ?>
                </td>
                <td>
                    <a class="edit" href="reminder_edit.php?id=<?= $row['id_reminder'] ?>">Edit</a>
                    <a class="hapus" href="reminder_hapus.php?id=<?= $row['id_reminder'] ?>" onclick="return konfirmasiHapus(this, 'reminder ini')">Hapus</a>
                </td>
            </tr>
            <?php } ?>

        </table>

    </div>

</div>


<!-- ===== MODAL KONFIRMASI HAPUS ===== -->
<div id="modalHapus" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
     background:rgba(0,0,0,0.4); z-index:999; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:12px; padding:30px; max-width:360px; width:90%; text-align:center; box-shadow:0 10px 30px rgba(0,0,0,0.2);">
        <div style="font-size:36px; margin-bottom:10px;">🗑️</div>
        <h3 style="margin:0 0 8px; color:#1f2430;">Hapus Reminder?</h3>
        <p id="pesanModal" style="color:#8a93a3; font-size:13px; margin:0 0 20px;">Yakin ingin menghapus reminder ini?</p>
        <div style="display:flex; gap:10px; justify-content:center;">
            <button onclick="tutupModal()" style="padding:10px 20px; border:1px solid #ddd; background:#fff; border-radius:8px; cursor:pointer; font-size:13px;">Batal</button>
            <a id="linkHapus" href="#" style="padding:10px 20px; background:#dc3545; color:#fff; border-radius:8px; font-size:13px; text-decoration:none;">Ya, Hapus</a>
        </div>
    </div>
</div>

<!-- ===== TOAST NOTIFIKASI ===== -->
<div id="toast" style="display:none; position:fixed; bottom:24px; right:24px; background:#22c55e; color:#fff;
     padding:12px 20px; border-radius:8px; font-size:13px; z-index:9999; box-shadow:0 4px 12px rgba(0,0,0,0.15);">
</div>

<script>
// ===== 1. KONFIRMASI HAPUS DENGAN MODAL =====
// Menampilkan modal konfirmasi sebelum menghapus reminder
function konfirmasiHapus(elLink, namaData) {
    document.getElementById('pesanModal').textContent = 'Yakin ingin menghapus ' + namaData + '?';
    document.getElementById('linkHapus').href = elLink.href;
    document.getElementById('modalHapus').style.display = 'flex';
    return false; // mencegah link langsung terbuka
}

// Menutup modal jika tombol Batal diklik
function tutupModal() {
    document.getElementById('modalHapus').style.display = 'none';
}

// Menutup modal jika klik di luar kotak modal
document.getElementById('modalHapus').addEventListener('click', function(e) {
    if (e.target === this) tutupModal();
});

// ===== 2. FILTER REMINDER BERDASARKAN STATUS =====
// Menambahkan tombol filter Semua / Aktif / Nonaktif di atas tabel
var statusList = ['Semua', 'aktif', 'nonaktif'];
var labelBtn   = { 'Semua': 'Semua', 'aktif': '🟢 Aktif', 'nonaktif': '🔴 Nonaktif' };

var filterDiv = document.createElement('div');
filterDiv.style.cssText = 'display:flex; gap:8px; flex-wrap:wrap; margin-bottom:14px;';

statusList.forEach(function(status) {
    var btn = document.createElement('button');
    btn.textContent = labelBtn[status];
    btn.dataset.status = status;
    btn.style.cssText = 'padding:5px 14px; border-radius:20px; border:1px solid #edeff2; background:#fff; font-size:12px; cursor:pointer;';

    // Tombol "Semua" aktif secara default
    if (status === 'Semua') btn.style.background = '#22c55e', btn.style.color = '#fff', btn.style.border = '1px solid #22c55e';

    btn.addEventListener('click', function() {
        // Reset semua tombol ke style default
        filterDiv.querySelectorAll('button').forEach(function(b) {
            b.style.background = '#fff';
            b.style.color = '#000';
            b.style.border = '1px solid #edeff2';
        });
        // Tandai tombol yang diklik sebagai aktif
        btn.style.background = '#22c55e';
        btn.style.color = '#fff';
        btn.style.border = '1px solid #22c55e';

        // Tampilkan/sembunyikan baris tabel sesuai status yang dipilih
        var rows = document.querySelectorAll('table tr:not(:first-child)');
        rows.forEach(function(row) {
            var statusCell = row.querySelector('td:nth-child(4)');
            if (!statusCell) return;
            if (status === 'Semua' || statusCell.textContent.trim() === status) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    filterDiv.appendChild(btn);
});

// Sisipkan tombol filter tepat di atas tabel
var tabel = document.querySelector('table');
if (tabel) tabel.parentNode.insertBefore(filterDiv, tabel);

// ===== 3. TOAST NOTIFIKASI =====
// Menampilkan pesan notifikasi kecil di pojok kanan bawah
function tampilkanToast(pesan, warna) {
    var toast = document.getElementById('toast');
    toast.textContent = pesan;
    toast.style.background = warna;
    toast.style.display = 'block';

    // Toast otomatis hilang setelah 3 detik
    setTimeout(function() {
        toast.style.display = 'none';
    }, 3000);
}

// ===== 4. HIGHLIGHT BARIS REMINDER AKTIF =====
// Memberi warna latar hijau muda pada reminder yang statusnya aktif
document.querySelectorAll('table tr:not(:first-child)').forEach(function(row) {
    var statusCell = row.querySelector('td:nth-child(4)');
    if (statusCell && statusCell.textContent.trim() === 'aktif') {
        row.style.background = '#f0fdf4'; // hijau sangat muda
    }
});
</script>

</body>
</html>