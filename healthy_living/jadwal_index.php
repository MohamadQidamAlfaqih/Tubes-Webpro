<?php
session_start();
include 'koneksi.php';

if(!isset($_SESSION['id_user'])){
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

// ---- Tambah jadwal ----
if(isset($_POST['simpan'])){

    $hari     = $_POST['hari'];
    $waktu    = $_POST['waktu_mulai'];
    $kegiatan = $_POST['jenis_kegiatan'];

    mysqli_query($conn, "
        INSERT INTO jadwal (id_user, hari, waktu_mulai, jenis_kegiatan)
        VALUES ('$id_user','$hari','$waktu','$kegiatan')
    ");

    header("Location: jadwal_index.php");
    exit;
}

// ---- Sapaan berdasarkan jam ----
$jam = (int)date('H');
if($jam < 11)      $sapaan = "Selamat Pagi";
elseif($jam < 15)  $sapaan = "Selamat Siang";
elseif($jam < 18)  $sapaan = "Selamat Sore";
else               $sapaan = "Selamat Malam";

// ---- Statistik ----
$qTotal = mysqli_query($conn,
    "SELECT COUNT(*) total_kegiatan, COUNT(DISTINCT hari) hari_aktif
     FROM jadwal WHERE id_user='$id_user'");
$stat = mysqli_fetch_assoc($qTotal);

// ---- Daftar jadwal terbaru ----
$qList = mysqli_query($conn,
    "SELECT * FROM jadwal
     WHERE id_user='$id_user'
     ORDER BY FIELD(hari,'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'), waktu_mulai ASC
     LIMIT 8");

$list = [];
while($row = mysqli_fetch_assoc($qList)){
    $list[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Jadwal Kegiatan - User</title>

<style>
    :root{
        --green:#22c55e;
        --green-dark:#16a34a;
        --ink:#1f2430;
        --muted:#8a93a3;
        --line:#edeff2;
        --bg:#f6f7f9;
    }

    *{ box-sizing:border-box; }

    body{
        margin:0;
        font-family: Arial, Helvetica, sans-serif;
        background: var(--bg);
        color: var(--ink);
    }

    a{ text-decoration:none; color:inherit; }

    /* ---------- Navbar ---------- */
    .navbar{
        display:flex;
        align-items:center;
        justify-content:space-between;
        background:#fff;
        padding: 14px 28px;
        border-bottom: 1px solid var(--line);
    }

    .brand{
        display:flex;
        align-items:center;
        gap:8px;
        font-weight:bold;
        color: var(--green-dark);
        font-size: 15px;
    }

    .brand .dot{
        width:26px; height:26px;
        border-radius:7px;
        background: var(--green);
        display:flex; align-items:center; justify-content:center;
    }

    .nav-links{
        display:flex;
        gap: 26px;
        font-size: 13px;
        color: var(--muted);
    }

    .nav-links a.active{
        color: var(--green-dark);
        font-weight:bold;
        position:relative;
    }

    .nav-links a.active::after{
        content:"";
        position:absolute;
        left:0; right:0; bottom:-16px;
        height:2px;
        background: var(--green);
    }

    .nav-right{ display:flex; align-items:center; gap:16px; }

    .bell{
        width:34px; height:34px;
        border-radius:50%;
        background: var(--bg);
        display:flex; align-items:center; justify-content:center;
        color:#555; font-size:14px;
    }

    .avatar{
        width:34px; height:34px;
        border-radius:50%;
        background: linear-gradient(135deg,#4facfe,#00f2fe);
        color:#fff;
        display:flex; align-items:center; justify-content:center;
        font-weight:bold; font-size: 13px;
    }

    /* ---------- Container ---------- */
    .container{
        max-width: 1080px;
        margin: 26px auto;
        padding: 0 20px;
    }

    .page-title{
        font-size: 13px;
        color: var(--muted);
        margin-bottom: 4px;
    }

    .greeting{
        font-size: 19px;
        font-weight:bold;
        margin: 0 0 2px;
    }

    .greeting-sub{
        font-size: 12px;
        color: var(--muted);
        margin-bottom: 20px;
    }

    /* ---------- Main grid ---------- */
    .main-grid{
        display:grid;
        grid-template-columns: 1.4fr 1fr;
        gap: 18px;
        margin-bottom: 18px;
    }

    .panel{
        background:#fff;
        border-radius: 12px;
        padding: 22px;
        box-shadow: 0 2px 10px rgba(20,20,30,0.04);
    }

    .panel h3{ margin: 0 0 14px; font-size: 14px; }

    /* ---------- Headline stat ---------- */
    .stat-label{
        font-size: 11px;
        color: var(--muted);
        margin-bottom: 4px;
    }

    .stat-num{
        font-size: 30px;
        font-weight:bold;
        color: var(--green-dark);
        display:flex;
        align-items:center;
        gap: 8px;
        margin-bottom: 18px;
    }

    .mini-stats{
        display:grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
        border-top: 1px solid var(--line);
        padding-top: 16px;
    }

    .mini-stats .label{
        font-size: 10px;
        color: var(--muted);
        text-transform:uppercase;
        margin-bottom: 4px;
    }

    .mini-stats .val{
        font-size: 16px;
        font-weight:bold;
    }

    /* ---------- Motivation card ---------- */
    .motivation{
        position:relative;
        border-radius: 12px;
        overflow:hidden;
        min-height: 150px;
        padding: 16px;
        color:#fff;
        display:flex;
        flex-direction:column;
        justify-content:flex-end;
        background:
            linear-gradient(180deg, rgba(5,15,8,0.15), rgba(5,15,8,0.85)),
            url('https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?q=80&w=900&auto=format&fit=crop')
            center/cover no-repeat;
        margin-bottom: 14px;
    }

    .motivation .title{
        font-size: 15px;
        font-weight:bold;
        line-height:1.3;
    }

    .motivation .title span{ color: #4ade80; }

    .motivation-note{
        background: #eafbf1;
        border-radius: 12px;
        padding: 14px 16px;
        font-size: 12px;
        color: #1f6f44;
        line-height: 1.5;
    }

    .motivation-note b{ display:block; margin-bottom: 4px; }

    /* ---------- Form ---------- */
    .form-row{
        display:grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
        margin-bottom: 14px;
    }

    label.fl{
        display:block;
        font-size: 11px;
        color: var(--muted);
        margin-bottom: 6px;
    }

    select, input[type=text], input[type=time]{
        width:100%;
        padding: 9px 10px;
        border: 1px solid var(--line);
        border-radius: 7px;
        font-size: 13px;
        outline:none;
        background:#fff;
    }

    select:focus, input:focus{ border-color: var(--green); }

    .submit-btn{
        width:100%;
        padding: 12px;
        background: var(--green);
        color:#fff;
        border:none;
        border-radius: 8px;
        font-weight:bold;
        font-size: 13px;
        cursor:pointer;
        margin-top: 6px;
    }

    .submit-btn:hover{ background: var(--green-dark); }

    /* ---------- Table ---------- */
    table{ width:100%; border-collapse: collapse; }

    table th{
        text-align:left;
        font-size: 11px;
        color: var(--muted);
        padding: 8px 10px;
        border-bottom: 1px solid var(--line);
    }

    table td{
        font-size: 12px;
        padding: 10px;
        border-bottom: 1px solid var(--line);
    }

    table tr:last-child td{ border-bottom:none; }

    .day-badge{
        display:inline-block;
        background:#eafbf1;
        color: var(--green-dark);
        font-weight:bold;
        font-size: 11px;
        padding: 3px 10px;
        border-radius: 20px;
    }

    .detail-link{
        color: var(--green-dark);
        font-weight:bold;
        font-size: 12px;
        margin-right: 10px;
    }

    .hapus-link{
        color: #d9534f;
        font-weight:bold;
        font-size: 12px;
    }

    .empty-note{
        font-size: 12px;
        color: var(--muted);
        text-align:center;
        padding: 20px 0;
    }

    footer{
        margin-top: 30px;
        border-top: 1px solid var(--line);
        padding: 18px 28px;
        display:flex;
        align-items:center;
        justify-content:space-between;
        font-size: 11px;
        color: var(--muted);
    }

    footer .flinks a{ margin-left: 16px; }

    @media (max-width: 760px){
        .nav-links{ display:none; }
        .main-grid{ grid-template-columns: 1fr; }
        .form-row{ grid-template-columns: 1fr; }
        table{ display:block; overflow-x:auto; }
    }
</style>
</head>

<body>

<div class="navbar">
    <div class="brand">
        <span class="dot">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="13" cy="4" r="2"></circle>
                <path d="M4 17l4-2 2-4 3 2 2-3 3 1"></path>
                <path d="M10 11l-2 6 4 4"></path>
            </svg>
        </span>
        HIDUP SEHAT
    </div>

    <div class="nav-links">
        <a href="dashboard_user.php">Dashboard</a>
        <a href="aktivitas_index.php">Activities</a>
        <a href="latihan_index.php">Latihan</a>
        <a class="active" href="jadwal_index.php">Calendar</a>
        <a href="reminder_index.php">Reminders</a>
    </div>

    <div class="nav-right">
        <div class="bell">🔔</div>
        <a href="logout.php" class="avatar" title="Logout"><?= strtoupper(substr($_SESSION['nama'],0,1)) ?></a>
    </div>
</div>

<div class="container">

    <div class="page-title">Jadwal Kegiatan</div>
    <div class="greeting"><?= $sapaan ?>, <?= htmlspecialchars($_SESSION['nama']) ?></div>
    <div class="greeting-sub">Berikut adalah ringkasan jadwal latihan Anda hari ini.</div>

    <div class="main-grid">

        <div class="panel">
            <div class="stat-label">TOTAL KEGIATAN TERJADWAL</div>
            <div class="stat-num">📅 <?= (int)$stat['total_kegiatan'] ?> Kegiatan</div>

            <div class="mini-stats">
                <div>
                    <div class="label">Hari Aktif</div>
                    <div class="val"><?= (int)$stat['hari_aktif'] ?> Hari</div>
                </div>
                <div>
                    <div class="label">Total Jadwal Kegiatan</div>
                    <div class="val"><?= (int)$stat['total_kegiatan'] ?> Kegiatan</div>
                </div>
            </div>
        </div>

        <div>
            <div class="motivation">
                <div class="title">Mulailah hari dengan <span>semangat!</span></div>
            </div>
            <div class="motivation-note">
                <b>💡 Tips</b>
                Jadwalkan latihan di waktu yang sama setiap hari agar lebih mudah membentuk kebiasaan sehat.
            </div>
        </div>

    </div>

    <div class="panel" style="margin-bottom:18px;">
        <h3>Schedule &amp; Details</h3>

        <form method="POST">
            <div class="form-row">
                <div>
                    <label class="fl">Hari Kegiatan</label>
                    <select name="hari" required>
                        <option>Senin</option>
                        <option>Selasa</option>
                        <option>Rabu</option>
                        <option>Kamis</option>
                        <option>Jumat</option>
                        <option>Sabtu</option>
                        <option>Minggu</option>
                    </select>
                </div>
                <div>
                    <label class="fl">Waktu Mulai</label>
                    <input type="time" name="waktu_mulai" required>
                </div>
            </div>

            <div class="form-row">
                <div style="grid-column: 1 / -1;">
                    <label class="fl">Jenis Kegiatan</label>
                    <input type="text" name="jenis_kegiatan" placeholder="Contoh: Chest Day, Leg Day, Yoga" required>
                </div>
            </div>

            <button type="submit" name="simpan" class="submit-btn">Simpan Kegiatan</button>
        </form>
    </div>

    <div class="panel">
        <h3>Jadwal Terbaru</h3>

        <?php if(count($list) > 0){ ?>
        <table>
            <tr>
                <th>Hari</th>
                <th>Jenis Kegiatan</th>
                <th>Waktu Mulai</th>
                <th>Aksi</th>
            </tr>
            <?php foreach($list as $row){ ?>
            <tr>
                <td><span class="day-badge"><?= htmlspecialchars($row['hari']) ?></span></td>
                <td><?= htmlspecialchars($row['jenis_kegiatan']) ?></td>
                <td><?= htmlspecialchars($row['waktu_mulai']) ?></td>
                <td>
                    <a class="detail-link" href="jadwal_edit.php?id=<?= $row['id_jadwal'] ?>">Edit</a>
                    <a class="hapus-link" href="jadwal_hapus.php?id=<?= $row['id_jadwal'] ?>" onclick="return konfirmasiHapus(this, 'jadwal ini')">Hapus</a>
                </td>
            </tr>
            <?php } ?>
        </table>
        <?php } else { ?>
            <div class="empty-note">Belum ada jadwal kegiatan. Tambahkan jadwal pertamamu di atas!</div>
        <?php } ?>
    </div>

</div>

<footer>
    <div>HIDUP SEHAT</div>
    <div class="flinks">
        <a href="#">Privacy Policy</a>
        <a href="#">Terms of Service</a>
        <a href="#">Contact Us</a>
    </div>
</footer>


<!-- ===== MODAL KONFIRMASI HAPUS ===== -->
<div id="modalHapus" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
     background:rgba(0,0,0,0.4); z-index:999; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:12px; padding:30px; max-width:360px; width:90%; text-align:center; box-shadow:0 10px 30px rgba(0,0,0,0.2);">
        <div style="font-size:36px; margin-bottom:10px;">🗑️</div>
        <h3 style="margin:0 0 8px; color:#1f2430;">Hapus Data?</h3>
        <p id="pesanModal" style="color:#8a93a3; font-size:13px; margin:0 0 20px;">Yakin ingin menghapus data ini?</p>
        <div style="display:flex; gap:10px; justify-content:center;">
            <button onclick="tutupModal()" style="padding:10px 20px; border:1px solid #ddd; background:#fff; border-radius:8px; cursor:pointer; font-size:13px;">Batal</button>
            <a id="linkHapus" href="#" style="padding:10px 20px; background:#dc3545; color:#fff; border-radius:8px; font-size:13px; text-decoration:none;">Ya, Hapus</a>
        </div>
    </div>
</div>

<!-- ===== TOAST NOTIFIKASI ===== -->
<div id="toast" style="display:none; position:fixed; bottom:24px; right:24px; background:#22c55e; color:#fff;
     padding:12px 20px; border-radius:8px; font-size:13px; z-index:9999; box-shadow:0 4px 12px rgba(0,0,0,0.15);">
    ✅ Jadwal berhasil disimpan!
</div>

<script>
// ===== 1. VALIDASI FORM SEBELUM SUBMIT =====
// Mengecek apakah semua kolom form sudah diisi sebelum dikirim
document.querySelector('form').addEventListener('submit', function(e) {
    var hari     = document.querySelector('select[name="hari"]').value;
    var waktu    = document.querySelector('input[name="waktu_mulai"]').value;
    var kegiatan = document.querySelector('input[name="jenis_kegiatan"]').value.trim();

    // Jika ada yang kosong, tampilkan peringatan dan batalkan submit
    if (!hari || !waktu || !kegiatan) {
        e.preventDefault();
        tampilkanToast('⚠️ Semua kolom harus diisi!', '#f59e0b');
        return;
    }

    // Jika semua terisi, tampilkan toast sukses
    tampilkanToast('✅ Jadwal berhasil disimpan!', '#22c55e');
});

// ===== 2. KONFIRMASI HAPUS DENGAN MODAL =====
// Menampilkan modal konfirmasi sebelum menghapus jadwal
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

// ===== 4. FILTER JADWAL BERDASARKAN HARI =====
// Menambahkan tombol filter di atas tabel jadwal
var hariList = ['Semua', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
var filterDiv = document.createElement('div');
filterDiv.style.cssText = 'display:flex; gap:8px; flex-wrap:wrap; margin-bottom:14px;';

hariList.forEach(function(hari) {
    var btn = document.createElement('button');
    btn.textContent = hari;
    btn.dataset.hari = hari;
    btn.style.cssText = 'padding:5px 14px; border-radius:20px; border:1px solid #edeff2; background:#fff; font-size:12px; cursor:pointer;';

    // Tombol "Semua" aktif secara default
    if (hari === 'Semua') btn.style.background = '#22c55e', btn.style.color = '#fff', btn.style.border = '1px solid #22c55e';

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

        // Tampilkan/sembunyikan baris tabel sesuai hari yang dipilih
        var rows = document.querySelectorAll('table tr:not(:first-child)');
        rows.forEach(function(row) {
            var badge = row.querySelector('.day-badge');
            if (!badge) return;
            if (hari === 'Semua' || badge.textContent.trim() === hari) {
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
</script>

</body>
</html>
