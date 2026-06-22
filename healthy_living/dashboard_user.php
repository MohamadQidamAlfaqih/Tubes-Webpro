<?php
session_start();
include 'koneksi.php';

if(!isset($_SESSION['id_user']) || $_SESSION['role'] != 'pengguna'){
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

// ---- Health Snapshot ----
$qAktivitasCount = mysqli_query($conn,
    "SELECT COUNT(*) total, COALESCE(SUM(durasi),0) total_durasi
     FROM aktivitas WHERE id_user='$id_user'");
$snapAktivitas = mysqli_fetch_assoc($qAktivitasCount);

$qKalori = mysqli_query($conn,
    "SELECT COALESCE(SUM(kalori),0) total_kalori
     FROM aktivitas WHERE id_user='$id_user'
     AND tanggal >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
$snapKalori = mysqli_fetch_assoc($qKalori);

$qReminder = mysqli_query($conn,
    "SELECT COUNT(*) total_aktif
     FROM reminder WHERE id_user='$id_user' AND status='aktif'");
$snapReminder = mysqli_fetch_assoc($qReminder);

// ---- Recent Activities (3 terbaru) ----
$qRecent = mysqli_query($conn,
    "SELECT * FROM aktivitas
     WHERE id_user='$id_user'
     ORDER BY tanggal DESC, id_aktivitas DESC
     LIMIT 3");

$recentList = [];
while($r = mysqli_fetch_assoc($qRecent)){
    $recentList[] = $r;
}

// ---- Data grafik (7 entri terakhir, urut tanggal naik) ----
$qChart = mysqli_query($conn,
    "SELECT tanggal, kalori FROM aktivitas
     WHERE id_user='$id_user'
     ORDER BY tanggal DESC, id_aktivitas DESC
     LIMIT 7");

$chartData = [];
while($c = mysqli_fetch_assoc($qChart)){
    $chartData[] = (float)$c['kalori'];
}
$chartData = array_reverse($chartData);
if(count($chartData) == 0){
    $chartData = [0,0,0,0,0,0,0];
}

// bangun titik polyline untuk mini chart
$chartW = 600; $chartH = 110; $pad = 10;
$maxVal = max($chartData); if($maxVal <= 0) $maxVal = 1;
$n = count($chartData);
$points = [];
foreach($chartData as $i => $val){
    $x = $pad + ($i * (($chartW - 2*$pad) / max(1, $n - 1)));
    $y = $chartH - $pad - (($val / $maxVal) * ($chartH - 2*$pad));
    $points[] = round($x,1).",".round($y,1);
}
$polyline = implode(" ", $points);
$areaPath = "M".$points[0]." L".implode(" L", $points)." L".$chartW.",".$chartH." L0,".$chartH." Z";
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard - User</title>

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

    .nav-right{
        display:flex;
        align-items:center;
        gap:16px;
    }

    .bell{
        width:34px; height:34px;
        border-radius:50%;
        background: var(--bg);
        display:flex; align-items:center; justify-content:center;
        color:#555;
        font-size:14px;
    }

    .avatar{
        width:34px; height:34px;
        border-radius:50%;
        background: linear-gradient(135deg,#4facfe,#00f2fe);
        color:#fff;
        display:flex; align-items:center; justify-content:center;
        font-weight:bold;
        font-size: 13px;
    }

    /* ---------- Container ---------- */
    .container{
        max-width: 1080px;
        margin: 26px auto;
        padding: 0 20px;
    }

    /* ---------- Hero ---------- */
    .hero{
        position:relative;
        border-radius: 14px;
        overflow:hidden;
        min-height: 220px;
        display:flex;
        align-items:center;
        padding: 30px 36px;
        color:#fff;
        background:
            linear-gradient(100deg, rgba(10,15,10,0.78) 10%, rgba(10,15,10,0.25) 60%, rgba(10,15,10,0.05) 100%),
            url('https://images.unsplash.com/photo-1517836357463-d25dfeac3438?q=80&w=1400&auto=format&fit=crop')
            center/cover no-repeat;
    }

    .hero h1{
        font-size: 26px;
        margin: 0 0 10px;
        max-width: 420px;
    }

    .hero p{
        font-size: 13px;
        color: #e4e7ea;
        max-width: 380px;
        margin: 0 0 18px;
        line-height: 1.5;
    }

    .hero .btn{
        display:inline-block;
        background: var(--green);
        color:#fff;
        font-size: 13px;
        font-weight:bold;
        padding: 10px 18px;
        border-radius: 6px;
    }

    .hero .btn:hover{ background: var(--green-dark); }

    /* ---------- Search ---------- */
    .search{
        margin: 22px 0;
        position:relative;
    }

    .search input{
        width:100%;
        padding: 12px 14px 12px 38px;
        border-radius: 8px;
        border: 1px solid var(--line);
        background:#fff;
        font-size: 13px;
        outline:none;
    }

    .search input:focus{ border-color: var(--green); }

    .search .icon{
        position:absolute;
        left: 13px; top:50%;
        transform: translateY(-50%);
        color: var(--muted);
        font-size: 13px;
    }

    /* ---------- Section heading ---------- */
    .section-head{
        display:flex;
        align-items:center;
        justify-content:space-between;
        margin-bottom: 12px;
    }

    .section-head h3{
        font-size: 15px;
        margin:0;
    }

    .section-head .view-link{
        font-size: 12px;
        color: var(--green-dark);
        font-weight:bold;
    }

    /* ---------- Health Snapshot cards ---------- */
    .snapshot{
        display:grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 26px;
    }

    .snap-card{
        border-radius: 12px;
        padding: 18px;
        display:flex;
        align-items:center;
        gap: 14px;
    }

    .snap-card.steps{ background:#fdeee0; }
    .snap-card.calorie{ background:#fdeaea; }
    .snap-card.active{ background:#e8f1fd; }

    .ring{
        width:54px; height:54px;
        border-radius:50%;
        display:flex;
        align-items:center;
        justify-content:center;
        font-weight:bold;
        font-size: 11px;
        background:#fff;
        flex-shrink:0;
    }

    .snap-card.steps .ring{ border:4px solid #f5a35c; color:#c0701f; }
    .snap-card.calorie .ring{ border:4px solid #ef6f7a; color:#c23445; }
    .snap-card.active .ring{ border:4px solid #4facfe; color:#1f6fb8; }

    .snap-info .num{
        font-size: 17px;
        font-weight:bold;
    }

    .snap-info .label{
        font-size: 11px;
        color: var(--muted);
    }

    /* ---------- Bottom grid ---------- */
    .bottom-grid{
        display:grid;
        grid-template-columns: 1.3fr 1fr;
        gap: 18px;
    }

    .panel{
        background:#fff;
        border-radius: 12px;
        padding: 18px;
        box-shadow: 0 2px 10px rgba(20,20,30,0.04);
    }

    .badge{
        font-size: 10px;
        background:#eafbf1;
        color: var(--green-dark);
        padding: 3px 9px;
        border-radius: 20px;
        font-weight:bold;
    }

    .chart-wrap svg{ width:100%; height:auto; display:block; }

    .chart-days{
        display:flex;
        justify-content:space-between;
        font-size: 10px;
        color: var(--muted);
        margin-top: 6px;
        padding: 0 4px;
    }

    /* ---------- Activity list ---------- */
    .activity-row{
        display:flex;
        align-items:center;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid var(--line);
    }

    .activity-row:last-child{ border-bottom:none; }

    .act-icon{
        width:34px; height:34px;
        border-radius:8px;
        background:#eef2ff;
        color:#4f6df5;
        display:flex; align-items:center; justify-content:center;
        font-size:14px;
        flex-shrink:0;
    }

    .act-title{
        font-size: 13px;
        font-weight:bold;
    }

    .act-sub{
        font-size: 11px;
        color: var(--muted);
    }

    .act-value{
        margin-left:auto;
        font-size: 12px;
        font-weight:bold;
        color: var(--green-dark);
    }

    .empty-note{
        font-size: 12px;
        color: var(--muted);
        text-align:center;
        padding: 16px 0;
    }

    /* ---------- Footer ---------- */
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
        .bottom-grid{ grid-template-columns: 1fr; }
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
        <a class="active" href="dashboard_user.php">Dashboard</a>
        <a href="aktivitas_index.php">Activities</a>
        <a href="jadwal_index.php">Calendar</a>
        <a href="reminder_index.php">Reminders</a>
    </div>

    <div class="nav-right">
        <div class="bell">🔔</div>
        <a href="logout.php" class="avatar" title="Logout"><?= strtoupper(substr($_SESSION['nama'],0,1)) ?></a>
    </div>
</div>

<div class="container">

    <div class="hero">
        <div>
            <h1>Your Daily Wellness Journey Starts Here!</h1>
            <p>Track your progress, monitor your vitals, and achieve your health goals with a personalized wellness experience designed for you.</p>
            <a class="btn" href="aktivitas_tambah.php">Start New Activity</a>
        </div>
    </div>

    <div class="search">
        <span class="icon">🔍</span>
        <input type="text" placeholder="Search activities, goals, etc.">
    </div>

    <div class="section-head">
        <h3>Health Snapshot</h3>
    </div>

    <div class="snapshot">

        <div class="snap-card steps">
            <div class="ring"><?= (int)$snapAktivitas['total'] ?></div>
            <div class="snap-info">
                <div class="num"><?= (int)$snapAktivitas['total_durasi'] ?> min</div>
                <div class="label">Total durasi aktivitas</div>
            </div>
        </div>

        <div class="snap-card calorie">
            <div class="ring"><?= number_format($snapKalori['total_kalori']) ?></div>
            <div class="snap-info">
                <div class="num"><?= number_format($snapKalori['total_kalori']) ?> kal</div>
                <div class="label">Kalori 7 hari terakhir</div>
            </div>
        </div>

        <div class="snap-card active">
            <div class="ring"><?= (int)$snapReminder['total_aktif'] ?></div>
            <div class="snap-info">
                <div class="num"><?= (int)$snapReminder['total_aktif'] ?> aktif</div>
                <div class="label">Reminder aktif</div>
            </div>
        </div>

    </div>

    <div class="bottom-grid">

        <div class="panel">
            <div class="section-head">
                <h3>Recent Activities</h3>
                <span class="badge"><?= count($recentList) ?> entri</span>
            </div>

            <div class="chart-wrap">
                <svg viewBox="0 0 <?= $chartW ?> <?= $chartH ?>" preserveAspectRatio="none">
                    <path d="<?= $areaPath ?>" fill="#dcf7e4"></path>
                    <polyline points="<?= $polyline ?>" fill="none" stroke="#22c55e" stroke-width="3"></polyline>
                </svg>
                <div class="chart-days">
                    <?php foreach($chartData as $v){ echo "<span>•</span>"; } ?>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="section-head">
                <h3>Aktivitas Terbaru</h3>
                <a class="view-link" href="aktivitas_index.php">VIEW DETAILS</a>
            </div>

            <?php if(count($recentList) > 0){ ?>
                <?php foreach($recentList as $row){ ?>
                <div class="activity-row">
                    <div class="act-icon">🏃</div>
                    <div>
                        <div class="act-title"><?= htmlspecialchars($row['jenis_olahraga']) ?></div>
                        <div class="act-sub"><?= htmlspecialchars($row['tanggal']) ?> • <?= (int)$row['durasi'] ?> min</div>
                    </div>
                    <div class="act-value">+<?= (int)$row['kalori'] ?> kal</div>
                </div>
                <?php } ?>
            <?php } else { ?>
                <div class="empty-note">Belum ada aktivitas. Yuk mulai catat aktivitas pertamamu!</div>
            <?php } ?>

        </div>

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

</body>
</html>