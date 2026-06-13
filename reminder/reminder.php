<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Reminder - Hidup Sehat</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
        h1   { color: #1a1a1a; margin-bottom: 4px; }
        .sub { color: #888; font-size: 13px; margin-bottom: 20px; }

        /* STATISTIK */
        .statistik { display: flex; gap: 12px; margin-bottom: 20px; }
        .kotak { background: white; padding: 16px 20px; border-radius: 10px; flex: 1; text-align: center; box-shadow: 0 2px 6px rgba(0,0,0,0.07); }
        .kotak .angka { font-size: 28px; font-weight: bold; color: #2e7d32; }
        .kotak .label { font-size: 12px; color: #888; margin-top: 4px; }

        /* FORM */
        .form { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.07); }
        .form h2 { font-size: 15px; margin-bottom: 14px; color: #333; }
        .form input, .form select {
            padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px;
            font-size: 14px; margin-right: 8px; margin-bottom: 10px;
        }
        .form input[id="inp-judul"] { width: 220px; }
        .btn-simpan { background: #2e7d32; color: white; border: none; padding: 9px 20px; border-radius: 6px; cursor: pointer; }
        .btn-batal  { background: #888; color: white; border: none; padding: 9px 16px; border-radius: 6px; cursor: pointer; display: none; }

        /* TABEL */
        .tabel { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.07); }
        .tabel h2 { font-size: 15px; margin-bottom: 14px; color: #333; }
        table  { width: 100%; border-collapse: collapse; font-size: 14px; }
        th { background: #f5f5f5; padding: 10px; text-align: left; font-size: 12px; color: #888; }
        td { padding: 10px; border-bottom: 1px solid #f5f5f5; }

        /* BADGE */
        .badge { padding: 3px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .aktif    { background: #e8f5e9; color: #2e7d32; }
        .nonaktif { background: #f5f5f5; color: #888; }

        /* TOMBOL */
        .btn-toggle { background: #ff9800; color: white; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer; font-size: 12px; }
        .btn-edit   { background: #1565c0; color: white; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer; font-size: 12px; margin-left: 4px; }
        .btn-hapus  { background: #e53935; color: white; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer; font-size: 12px; margin-left: 4px; }

        /* NOTIFIKASI */
        .notif { padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; display: none; }
        .hijau { background: #e8f5e9; color: #2e7d32; }
        .biru  { background: #e3f2fd; color: #1565c0; }
        .merah { background: #ffebee; color: #e53935; }
    </style>
</head>
<body>

<h1>🔔 Reminder</h1>
<p class="sub">Jangan lewatkan aktivitas sehat Anda hari ini</p>

<!-- NOTIFIKASI -->
<div class="notif hijau" id="notif-hijau"></div>
<div class="notif biru"  id="notif-biru"></div>
<div class="notif merah" id="notif-merah"></div>

<!-- STATISTIK -->
<div class="statistik">
    <div class="kotak"><div class="angka" id="stat-total">0</div><div class="label">Total Reminder</div></div>
    <div class="kotak"><div class="angka" id="stat-aktif">0</div><div class="label">Aktif</div></div>
    <div class="kotak"><div class="angka" id="stat-nonaktif">0</div><div class="label">Nonaktif</div></div>
    <div class="kotak"><div class="angka" id="stat-persen">0%</div><div class="label">Adherence Rate</div></div>
</div>

<!-- FORM -->
<div class="form">
    <h2 id="judul-form">➕ Tambah Reminder</h2>
    <input type="hidden" id="inp-id">
    <input type="text"   id="inp-judul"  placeholder="Nama reminder (contoh: Minum Air)" style="width:220px">
    <input type="time"   id="inp-waktu"  value="08:00">
    <select id="inp-jenis">
        <option>Workout</option>
        <option>Water Intake</option>
        <option>Stretching</option>
        <option>Sleep</option>
    </select>
    <button class="btn-simpan" onclick="simpan()">Simpan</button>
    <button class="btn-batal"  id="btn-batal" onclick="batalEdit()">Batal</button>
</div>

<!-- TABEL -->
<div class="tabel">
    <h2>📋 Daftar Reminder</h2>
    <table>
        <thead>
            <tr>
                <th>Nama Reminder</th>
                <th>Waktu</th>
                <th>Jenis</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="isi-tabel">
            <tr><td colspan="5" style="text-align:center; color:#aaa">Memuat data...</td></tr>
        </tbody>
    </table>
</div>

<script>
// =============================================
// MUAT DATA saat halaman dibuka
// =============================================
window.onload = function() {
    muatData();
}

// Ambil semua reminder dari api_reminder.php
async function muatData() {
    const res  = await fetch('api_reminder.php?aksi=daftar');
    const json = await res.json();
    const tbody = document.getElementById('isi-tabel');

    if (!json.data || json.data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; color:#aaa">Belum ada reminder</td></tr>';
        hitungStat([]);
        return;
    }

    // Isi tabel
    tbody.innerHTML = json.data.map(r => `
        <tr>
            <td>${r.judul}</td>
            <td>${r.waktu.substring(0, 5)}</td>
            <td>${r.jenis}</td>
            <td><span class="badge ${r.status == 'Aktif' ? 'aktif' : 'nonaktif'}">${r.status}</span></td>
            <td>
                <button class="btn-toggle" onclick="toggle(${r.id})">${r.status == 'Aktif' ? '⏸ Nonaktifkan' : '▶ Aktifkan'}</button>
                <button class="btn-edit"   onclick="isiFormEdit(${r.id}, '${r.judul}', '${r.waktu.substring(0,5)}', '${r.jenis}')">✏️ Edit</button>
                <button class="btn-hapus"  onclick="hapus(${r.id})">🗑️ Hapus</button>
            </td>
        </tr>
    `).join('');

    hitungStat(json.data);
}

// Hitung statistik
function hitungStat(data) {
    const total   = data.length;
    const aktif   = data.filter(r => r.status == 'Aktif').length;
    const nonaktif = total - aktif;
    const persen  = total > 0 ? Math.round((aktif / total) * 100) : 0;

    document.getElementById('stat-total').textContent   = total;
    document.getElementById('stat-aktif').textContent   = aktif;
    document.getElementById('stat-nonaktif').textContent = nonaktif;
    document.getElementById('stat-persen').textContent  = persen + '%';
}

// =============================================
// SIMPAN atau UPDATE reminder
// =============================================
async function simpan() {
    const id    = document.getElementById('inp-id').value;
    const judul = document.getElementById('inp-judul').value;
    const waktu = document.getElementById('inp-waktu').value;
    const jenis = document.getElementById('inp-jenis').value;

    if (!judul) { alert('Nama reminder wajib diisi!'); return; }

    if (id) {
        // UPDATE
        await fetch('api_reminder.php?aksi=update', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, judul, waktu, jenis })
        });
        tampilNotif('biru', '✏️ Reminder berhasil diubah!');
        batalEdit();
    } else {
        // SIMPAN BARU
        await fetch('api_reminder.php?aksi=simpan', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ judul, waktu, jenis })
        });
        tampilNotif('hijau', '✅ Reminder berhasil ditambahkan!');
        document.getElementById('inp-judul').value = '';
    }

    muatData(); // Refresh tabel
}

// =============================================
// TOGGLE STATUS
// =============================================
async function toggle(id) {
    await fetch(`api_reminder.php?aksi=toggle&id=${id}`);
    muatData();
}

// =============================================
// HAPUS
// =============================================
async function hapus(id) {
    if (!confirm('Yakin hapus reminder ini?')) return;
    await fetch(`api_reminder.php?aksi=hapus&id=${id}`);
    tampilNotif('merah', '🗑️ Reminder berhasil dihapus!');
    muatData();
}

// =============================================
// ISI FORM untuk edit
// =============================================
function isiFormEdit(id, judul, waktu, jenis) {
    document.getElementById('inp-id').value    = id;
    document.getElementById('inp-judul').value = judul;
    document.getElementById('inp-waktu').value = waktu;
    document.getElementById('inp-jenis').value = jenis;
    document.getElementById('judul-form').textContent = '✏️ Edit Reminder';
    document.getElementById('btn-batal').style.display = 'inline-block';
    window.scrollTo(0, 0); // Scroll ke atas
}

// Batal edit, reset form
function batalEdit() {
    document.getElementById('inp-id').value    = '';
    document.getElementById('inp-judul').value = '';
    document.getElementById('inp-waktu').value = '08:00';
    document.getElementById('inp-jenis').value = 'Workout';
    document.getElementById('judul-form').textContent = '➕ Tambah Reminder';
    document.getElementById('btn-batal').style.display = 'none';
}

// Tampilkan notifikasi sementara
function tampilNotif(warna, pesan) {
    const el = document.getElementById('notif-' + warna);
    el.textContent  = pesan;
    el.style.display = 'block';
    setTimeout(() => el.style.display = 'none', 3000);
}
</script>

</body>
</html>