<?php
session_start();
include 'koneksi.php';

if(!isset($_SESSION['id_user'])){
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

// ---- Tambah aktivitas cepat dari form "Latihan Hari Ini" ----
if(isset($_POST['simpan'])){

    $jenis   = $_POST['jenis_olahraga'];
    $durasi  = $_POST['durasi'];
    $kalori  = $_POST['kalori'];
    $catatan = $_POST['catatan'];
    $tanggal = date('Y-m-d');
    $sumber  = 'Manual';

    mysqli_query($conn, "
        INSERT INTO aktivitas
        (id_user, jenis_olahraga, durasi, kalori, tanggal, catatan, sumber_data)
        VALUES
        ('$id_user', '$jenis', '$durasi', '$kalori', '$tanggal', '$catatan', '$sumber')
    ");

    header("Location: aktivitas_index.php");
    exit;
}

// ---- Statistik atas ----
$qTotal = mysqli_query($conn,
    "SELECT COUNT(*) total_sesi, COALESCE(SUM(durasi),0) total_durasi
     FROM aktivitas WHERE id_user='$id_user'");
$statTotal = mysqli_fetch_assoc($qTotal);

$qHariIni = mysqli_query($conn,
    "SELECT COALESCE(SUM(kalori),0) kalori_hari_ini, COALESCE(SUM(durasi),0) durasi_hari_ini
     FROM aktivitas WHERE id_user='$id_user' AND tanggal = CURDATE()");
$statHariIni = mysqli_fetch_assoc($qHariIni);

// ---- Daftar aktivitas terbaru (tabel bawah) ----
$qList = mysqli_query($conn,
    "SELECT * FROM aktivitas
     WHERE id_user='$id_user'
     ORDER BY tanggal DESC, id_aktivitas DESC
     LIMIT 6");

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
<title>Activities - User</title>

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
        margin-bottom: 18px;
    }

    /* ---------- Stat cards ---------- */
    .snapshot{
        display:grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 22px;
    }

    .snap-card{
        border-radius: 12px;
        padding: 18px;
        display:flex;
        align-items:center;
        gap: 14px;
    }

    .snap-card.a{ background:#fdeee0; }
    .snap-card.b{ background:#fdeaea; }
    .snap-card.c{ background:#e8f1fd; }

    .ring{
        width:46px; height:46px;
        border-radius:50%;
        display:flex; align-items:center; justify-content:center;
        font-weight:bold; font-size: 16px;
        background:#fff;
        flex-shrink:0;
    }

    .snap-card.a .ring{ border:4px solid #f5a35c; color:#c0701f; }
    .snap-card.b .ring{ border:4px solid #ef6f7a; color:#c23445; }
    .snap-card.c .ring{ border:4px solid #4facfe; color:#1f6fb8; }

    .snap-info .num{ font-size: 17px; font-weight:bold; }
    .snap-info .label{ font-size: 11px; color: var(--muted); }

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
        padding: 20px;
        box-shadow: 0 2px 10px rgba(20,20,30,0.04);
    }

    .panel h3{
        margin: 0 0 14px;
        font-size: 14px;
    }

    /* ---------- Exercise picker ---------- */
    .exercise-grid{
        display:grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 10px;
    }

    .ex-btn{
        border: 1px solid var(--line);
        background: #fafbfc;
        border-radius: 10px;
        padding: 12px 6px;
        text-align:center;
        font-size: 11px;
        color: var(--ink);
        cursor:pointer;
        transition: .15s;
    }

    .ex-btn .ico{ font-size: 18px; margin-bottom:6px; }

    .ex-btn.selected{
        border-color: var(--green);
        background: #eafbf1;
        color: var(--green-dark);
        font-weight:bold;
    }

    /* ---------- Burn card ---------- */
    .burn-card{
        background: linear-gradient(135deg, var(--green), var(--green-dark));
        color:#fff;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 14px;
    }

    .burn-card .label{
        font-size: 11px;
        opacity:.9;
        margin-bottom: 6px;
    }

    .burn-card .num{
        font-size: 30px;
        font-weight:bold;
    }

    .burn-card .num span{ font-size: 14px; font-weight:normal; }

    .tip-card{
        background: #eafbf1;
        border-radius: 12px;
        padding: 16px;
        font-size: 12px;
        color: #1f6f44;
        line-height:1.5;
    }

    .tip-card b{ display:block; margin-bottom:4px; }

    /* ---------- Latihan Hari Ini form ---------- */
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

    input[type=text], input[type=number]{
        width:100%;
        padding: 9px 10px;
        border: 1px solid var(--line);
        border-radius: 7px;
        font-size: 13px;
        outline:none;
    }

    input:focus{ border-color: var(--green); }

    input[type=range]{
        width:100%;
        accent-color: var(--green);
    }

    .duration-label{
        display:flex;
        justify-content:space-between;
        font-size: 11px;
        color: var(--muted);
        margin-bottom: 4px;
    }

    .range-marks{
        display:flex;
        justify-content:space-between;
        font-size: 10px;
        color: var(--muted);
        margin-top: 2px;
    }

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

    .detail-link{
        color: var(--green-dark);
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
        .snapshot{ grid-template-columns: 1fr; }
        .main-grid{ grid-template-columns: 1fr; }
        .exercise-grid{ grid-template-columns: repeat(3,1fr); }
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
        <a class="active" href="aktivitas_index.php">Activities</a>
        <a href="jadwal_index.php">Calendar</a>
        <a href="reminder_index.php">Reminders</a>
    </div>

    <div class="nav-right">
        <div class="bell">🔔</div>
        <a href="logout.php" class="avatar" title="Logout"><?= strtoupper(substr($_SESSION['nama'],0,1)) ?></a>
    </div>
</div>

<div class="container">

    <div class="page-title">Activities</div>

    <div class="snapshot">

        <div class="snap-card a">
            <div class="ring">🏃</div>
            <div class="snap-info">
                <div class="num"><?= (int)$statTotal['total_sesi'] ?></div>
                <div class="label">Total sesi latihan</div>
            </div>
        </div>

        <div class="snap-card b">
            <div class="ring">🔥</div>
            <div class="snap-info">
                <div class="num"><?= number_format($statHariIni['kalori_hari_ini']) ?> kal</div>
                <div class="label">Kalori hari ini</div>
            </div>
        </div>

        <div class="snap-card c">
            <div class="ring">⏱</div>
            <div class="snap-info">
                <div class="num"><?= (int)$statHariIni['durasi_hari_ini'] ?> min</div>
                <div class="label">Waktu aktif hari ini</div>
            </div>
        </div>

    </div>

    <div class="main-grid">

        <div>
            <div class="panel" style="margin-bottom:18px;">
                <h3>Select Exercise</h3>
                <div class="exercise-grid" id="exerciseGrid">
                    <div class="ex-btn" data-val="Running"><div class="ico">🏃</div>Running</div>
                    <div class="ex-btn" data-val="Cycling"><div class="ico">🚴</div>Cycling</div>
                    <div class="ex-btn" data-val="Yoga"><div class="ico">🧘</div>Yoga</div>
                    <div class="ex-btn" data-val="Gym"><div class="ico">🏋️</div>Gym</div>
                    <div class="ex-btn" data-val="Yoga Lanjutan"><div class="ico">🧘</div>Yoga</div>
                </div>
            </div>

            <div class="panel">
                <h3>Latihan Hari Ini</h3>

                <form method="POST" id="latihanForm">
                    <input type="hidden" name="jenis_olahraga" id="jenisOlahraga" value="Running" required>

                    <div class="form-row">
                        <div>
                            <label class="fl">Repetisi / Set (catatan)</label>
                            <input type="text" name="catatan" placeholder="Contoh: 10 reps / 3 set">
                        </div>
                        <div>
                            <label class="fl">Kalori</label>
                            <input type="number" name="kalori" placeholder="Contoh: 90 kalori" required>
                        </div>
                    </div>

                    <div class="duration-label">
                        <span>Durasi</span>
                        <span id="durasiLabel">45 min</span>
                    </div>
                    <input type="range" name="durasi" min="5" max="120" value="45" id="durasiRange">
                    <div class="range-marks"><span>5m</span><span>60m</span><span>120m</span></div>

                    <button type="submit" name="simpan" class="submit-btn">▷ Mulai Latihan</button>
                </form>
            </div>
        </div>

        <div>
            <div class="burn-card">
                <div class="label">ESTIMATED BURN CALORIES</div>
                <div class="num">450 <span>kcal</span></div>
            </div>

            <div class="tip-card">
                <b>Tips Latihan</b>
                Pastikan kamu cukup minum air sebelum dan setelah berolahraga agar tubuh tetap terhidrasi.
            </div>
        </div>

    </div>

    <div class="panel">
        <h3>Sesi Latihan Terbaru</h3>

        <?php if(count($list) > 0){ ?>
        <table>
            <tr>
                <th>Latihan</th>
                <th>Catatan / Set</th>
                <th>Durasi</th>
                <th>Kalori Terbakar</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
            <?php foreach($list as $row){ ?>
            <tr>
                <td><?= htmlspecialchars($row['jenis_olahraga']) ?></td>
                <td><?= htmlspecialchars($row['catatan']) ?: '-' ?></td>
                <td><?= (int)$row['durasi'] ?> menit</td>
                <td><?= (int)$row['kalori'] ?> kkal</td>
                <td><?= htmlspecialchars($row['tanggal']) ?></td>
                <td><a class="detail-link" href="aktivitas_edit.php?id=<?= $row['id_aktivitas'] ?>">Detail</a></td>
            </tr>
            <?php } ?>
        </table>
        <?php } else { ?>
            <div class="empty-note">Belum ada sesi latihan. Mulai latihan pertamamu di atas!</div>
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

<script>
    const exBtns = document.querySelectorAll('.ex-btn');
    const jenisInput = document.getElementById('jenisOlahraga');

    exBtns.forEach((btn, i) => {
        if(i === 1) btn.classList.add('selected'); // default: Cycling seperti contoh
        btn.addEventListener('click', () => {
            exBtns.forEach(b => b.classList.remove('selected'));
            btn.classList.add('selected');
            jenisInput.value = btn.dataset.val;
        });
    });
    jenisInput.value = 'Cycling';

    const durasiRange = document.getElementById('durasiRange');
    const durasiLabel = document.getElementById('durasiLabel');
    durasiRange.addEventListener('input', () => {
        durasiLabel.textContent = durasiRange.value + ' min';
    });
</script>

</body>
</html>