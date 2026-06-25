<?php
session_start();

if (!isset($_SESSION['user_logged_in'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivitas Fisik</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; padding: 30px; }
        .container { max-width: 1000px; margin: auto; }
        .card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, .1); margin-bottom: 20px; }
        input { width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { border: none; padding: 10px 15px; cursor: pointer; border-radius: 5px; }
        .btn-simpan { background: #28a745; color: white; }
        .btn-edit { background: #ffc107; }
        .btn-hapus { background: #dc3545; color: white; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        th { background: #28a745; color: white; }
        .logout { background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; }
    </style>
</head>
<body>

    <div class="container">
        <div class="card">
            <h2 id="form-title">Tambah Aktivitas Fisik</h2>
            <input type="hidden" id="id">
            <input type="text" id="nama_aktivitas" placeholder="Nama Aktivitas">
            <input type="number" id="durasi" placeholder="Durasi (Menit)">
            <input type="number" id="kalori" placeholder="Kalori">
            <button class="btn-simpan" id="btn-submit" onclick="simpanData()">Simpan</button>
            <button style="display:none; background:#6c757d; color:white;" id="btn-batal" onclick="resetForm()">Batal</button>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Aktivitas</th>
                        <th>Durasi</th>
                        <th>Kalori</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="dataAktivitas"></tbody>
            </table>
        </div>

        <a href="logout.php" class="logout">Logout</a>
    </div>

    <script>
        loadData();

        function loadData() {
            fetch("api.php")
                .then(res => res.json())
                .then(response => {
                    if(response.status === "success") {
                        let html = '';
                        response.data.forEach((item, index) => {
                            html += `
                            <tr>
                                <td>${index+1}</td>
                                <td>${item.nama_aktivitas}</td>
                                <td>${item.durasi} Menit</td>
                                <td>${item.kalori} kcal</td>
                                <td>
                                    <button class="btn-edit" onclick="aksiEdit('${item.id}')">Edit</button>
                                    <button class="btn-hapus" onclick="hapusData('${item.id}')">Hapus</button>
                                </td>
                            </tr>`;
                        });
                        document.getElementById("dataAktivitas").innerHTML = html;
                    } else {
                        alert(response.message);
                    }
                })
                .catch(err => {
                    console.error("Gagal memuat data JSON:", err);
                });
        }

        function simpanData() {
            let formData = new FormData();
            let id = document.getElementById("id").value;

            formData.append("id", id);
            formData.append("nama_aktivitas", document.getElementById("nama_aktivitas").value);
            formData.append("durasi", document.getElementById("durasi").value);
            formData.append("kalori", document.getElementById("kalori").value);

            if (id == "") {
                formData.append("aksi", "create");
            } else {
                formData.append("aksi", "update");
            }

            fetch("api.php", {
                    method: "POST",
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    alert(data.message);
                    resetForm();
                    loadData();
                });
        }

        function aksiEdit(id) {
            fetch("api.php?id=" + id)
                .then(res => res.json())
                .then(response => {
                    if(response.status === "success") {
                        document.getElementById("id").value = response.data.id;
                        document.getElementById("nama_aktivitas").value = response.data.nama_aktivitas;
                        document.getElementById("durasi").value = response.data.durasi;
                        document.getElementById("kalori").value = response.data.kalori;
                        
                        document.getElementById("form-title").innerText = "Edit Aktivitas Fisik";
                        document.getElementById("btn-submit").innerText = "Perbarui";
                        document.getElementById("btn-batal").style.display = "inline-block";
                    } else {
                        alert(response.message);
                    }
                });
        }

        function hapusData(id) {
            if (confirm("Yakin hapus data?")) {
                let formData = new FormData();
                formData.append("aksi", "delete");
                formData.append("id", id);

                fetch("api.php", {
                        method: "POST",
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        alert(data.message);
                        loadData();
                    });
            }
        }

        function resetForm() {
            document.getElementById("id").value = "";
            document.getElementById("nama_aktivitas").value = "";
            document.getElementById("durasi").value = "";
            document.getElementById("kalori").value = "";
            
            document.getElementById("form-title").innerText = "Tambah Aktivitas Fisik";
            document.getElementById("btn-submit").innerText = "Simpan";
            document.getElementById("btn-batal").style.display = "none";
        }
    </script>
</body>
</html>