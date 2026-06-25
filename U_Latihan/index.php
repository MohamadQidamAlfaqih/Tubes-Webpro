<?php
session_start();

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengguna | Rekomendasi Latihan</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 40px 20px; display: flex; justify-content: center; }
        .container { background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); width: 100%; max-width: 600px; }
        h1 { text-align: center; font-size: 24px; margin-bottom: 5px; color: #333; }
        h3 { margin-top: 0; color: #555; font-size: 16px; }
        form { display: flex; flex-direction: column; gap: 12px; }
        label { font-weight: bold; font-size: 14px; color: #444; }
        input[type="text"], input[type="number"], select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; font-size: 14px; }
        button[type="submit"] { background-color: #008000; color: white; padding: 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; font-weight: bold; margin-top: 8px; }
        button[type="submit"]:hover { background-color: #006400; }
        .btn-batal { background-color: #6c757d; color: white; padding: 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; font-weight: bold; text-align: center; text-decoration: none; margin-top: 5px; }
        hr { margin: 25px 0; border: 0; border-top: 1px solid #eee; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; font-size: 14px; }
        th { background-color: #f9f9f9; font-weight: bold; }
        .btn-aksi { padding: 4px 8px; text-decoration: none; border-radius: 4px; font-size: 12px; color: white; cursor: pointer; border: none; }
        .btn-edit { background-color: #ffc107; color: black; margin-right: 5px; }
        .btn-hapus { background-color: #dc3545; }
        
        .logout-box { text-align: center; margin-bottom: 25px; font-size: 14.5px; color: #555; background-color: #f8f9fa; padding: 10px; border-radius: 6px; border: 1px solid #e9ecef; }
        .btn-logout { color: #dc3545; text-decoration: none; font-weight: bold; margin-left: 5px; }
        .btn-logout:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="container">
    <h1> HIDUP SEHAT <br>LATIHAN POPULER </h1>
    
    <div class="logout-box">
        Selamat datang, <strong><?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Pengguna'; ?></strong> (<?= isset($_SESSION['role']) ? htmlspecialchars($_SESSION['role']) : 'user'; ?>) | 
        <a href="logout.php" class="btn-logout" onclick="return confirm('Yakin ingin keluar dari akun?')">Logout</a>
    </div>

    <h3 id="judulForm">Tambah Data Latihan</h3>
    
    <form id="formLatihan">
        <input type="hidden" id="id_latihan" value="">

        <label for="nama">Nama Latihan</label>
        <input type="text" id="nama" required placeholder="Contoh: Push Up">

        <label for="repetisi">Repetisi</label>
        <input type="number" id="repetisi" required placeholder="Contoh: 15">

        <label for="hari">Hari</label>
        <select id="hari">
            <option>Senin</option>
            <option>Selasa</option>
            <option>Rabu</option>
            <option>Kamis</option>
            <option>Jumat</option>
            <option>Sabtu</option>
            <option>Minggu</option>
        </select>

        <button type="submit" id="btnSimpan">Simpan</button>
        <button type="button" id="btnBatal" class="btn-batal" style="display: none;" onclick="resetForm()">Batal Edit</button>
    </form>

    <hr>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Latihan</th>
                <th>Repetisi</th>
                <th>Hari</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="tabelData"></tbody>
    </table>
</div>

<script>
    const URL_API = 'api.php';
    const KEY_API = 'LATIHAN2026';
    let semuaDataLatihan = [];

    window.onload = tampilkanData;

    function tampilkanData() {
        fetch(URL_API, {
            method: 'GET',
            headers: { 'X-API-KEY': KEY_API }
        })
        .then(response => response.json())
        .then(hasil => {
            const tbody = document.getElementById('tabelData');
            tbody.innerHTML = ''; 

            if (hasil.status === 'success' && hasil.data.length > 0) {
                semuaDataLatihan = hasil.data; 

                hasil.data.forEach((item, index) => {
                    tbody.innerHTML += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${item.nama_latihan}</td>
                            <td>${item.repetisi} X</td>
                            <td>${item.hari}</td>
                            <td>
                                <button class="btn-aksi btn-edit" onclick="pindahkanKeForm(${item.id})">Edit</button>
                                <button class="btn-aksi btn-hapus" onclick="hapusData(${item.id})">Hapus</button>
                            </td>
                        </tr>
                    `;
                });
            } else {
                tbody.innerHTML = `<tr><td colspan="5">Belum ada data latihan.</td></tr>`;
            }
        })
        .catch(err => console.error("Gagal memuat data API:", err));
    }

    document.getElementById('formLatihan').addEventListener('submit', function(e) {
        e.preventDefault(); 

        const id = document.getElementById('id_latihan').value;
        const nama = document.getElementById('nama').value;
        const repetisi = document.getElementById('repetisi').value;
        const hari = document.getElementById('hari').value;

        const dataPaket = { id: id, nama: nama, repetisi: repetisi, hari: hari };
        let methodHTTP = (id === '') ? 'POST' : 'PUT';

        fetch(URL_API, {
            method: methodHTTP,
            headers: { 
                'X-API-KEY': KEY_API,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(dataPaket)
        })
        .then(response => response.json())
        .then(hasil => {
            alert(hasil.message);
            resetForm();      
            tampilkanData();  
        });
    });

    function pindahkanKeForm(id) {
        const dataTerpilih = semuaDataLatihan.find(item => item.id == id);

        if (dataTerpilih) {
            document.getElementById('id_latihan').value = dataTerpilih.id;
            document.getElementById('nama').value = dataTerpilih.nama_latihan;
            document.getElementById('repetisi').value = dataTerpilih.repetisi;
            document.getElementById('hari').value = dataTerpilih.hari;

            document.getElementById('judulForm').innerText = 'Edit Data Latihan';
            document.getElementById('btnSimpan').innerText = 'Perbarui Data';
            document.getElementById('btnBatal').style.display = 'block'; 
        }
    }

    function hapusData(id) {
        if (confirm('Yakin ingin menghapus data latihan ini?')) {
            fetch(URL_API + '?id=' + id, {
                method: 'DELETE',
                headers: { 'X-API-KEY': KEY_API }
            })
            .then(response => response.json())
            .then(hasil => {
                alert(hasil.message);
                tampilkanData(); 
            });
        }
    }

    function resetForm() {
        document.getElementById('id_latihan').value = '';
        document.getElementById('formLatihan').reset();
        
        document.getElementById('judulForm').innerText = 'Tambah Data Latihan';
        document.getElementById('btnSimpan').innerText = 'Simpan';
        document.getElementById('btnBatal').style.display = 'none'; 
    }
</script>
</body>
</html>