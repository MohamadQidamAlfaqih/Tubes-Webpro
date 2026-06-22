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

body{
    font-family:Arial, sans-serif;
    background:#f4f6f9;
    padding:30px;
}

.container{
    max-width:1000px;
    margin:auto;
}

.card{
    background:white;
    padding:20px;
    border-radius:10px;
    box-shadow:0 2px 10px rgba(0,0,0,.1);
    margin-bottom:20px;
}

input{
    width:100%;
    padding:10px;
    margin-bottom:10px;
    border:1px solid #ddd;
    border-radius:5px;
}

button{
    border:none;
    padding:10px 15px;
    cursor:pointer;
    border-radius:5px;
}

.btn-simpan{
    background:#28a745;
    color:white;
}

.btn-edit{
    background:#ffc107;
}

.btn-hapus{
    background:#dc3545;
    color:white;
}

table{
    width:100%;
    border-collapse:collapse;
}

th,td{
    border:1px solid #ddd;
    padding:10px;
    text-align:center;
}

th{
    background:#28a745;
    color:white;
}

.logout{
    background:#dc3545;
    color:white;
    padding:10px 20px;
    text-decoration:none;
    border-radius:5px;
}

</style>

</head>
<body>

<div class="container">

<div class="card">

<h2>Aktivitas Fisik</h2>

<input type="hidden" id="id">

<input type="text"
id="nama_aktivitas"
placeholder="Nama Aktivitas">

<input type="number"
id="durasi"
placeholder="Durasi (Menit)">

<input type="number"
id="kalori"
placeholder="Kalori">

<button class="btn-simpan"
onclick="simpanData()">
Simpan
</button>

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

<tbody id="dataAktivitas">

</tbody>

</table>

</div>

<a href="logout.php" class="logout">
Logout
</a>

</div>

<script>

loadData();

function loadData(){

fetch("api.php")
.then(res=>res.json())
.then(data=>{

let html='';

data.forEach((item,index)=>{

html += `
<tr>

<td>${index+1}</td>

<td>${item.nama_aktivitas}</td>

<td>${item.durasi}</td>

<td>${item.kalori}</td>

<td>

<button
class="btn-edit"
onclick="editData(
'${item.id}',
'${item.nama_aktivitas}',
'${item.durasi}',
'${item.kalori}'
)">
Edit
</button>

<button
class="btn-hapus"
onclick="hapusData('${item.id}')">
Hapus
</button>

</td>

</tr>
`;

});

document.getElementById("dataAktivitas").innerHTML = html;

});

}

function simpanData(){

let formData = new FormData();

let id = document.getElementById("id").value;

formData.append("id",id);

formData.append(
"nama_aktivitas",
document.getElementById("nama_aktivitas").value
);

formData.append(
"durasi",
document.getElementById("durasi").value
);

formData.append(
"kalori",
document.getElementById("kalori").value
);

if(id==""){
formData.append("aksi","create");
}else{
formData.append("aksi","update");
}

fetch("api.php",{
method:"POST",
body:formData
})
.then(res=>res.json())
.then(data=>{

alert(data.message);

resetForm();

loadData();

});

}

function editData(id,nama,durasi,kalori){

document.getElementById("id").value=id;
document.getElementById("nama_aktivitas").value=nama;
document.getElementById("durasi").value=durasi;
document.getElementById("kalori").value=kalori;

}

function hapusData(id){

if(confirm("Yakin hapus data?")){

let formData = new FormData();

formData.append("aksi","delete");
formData.append("id",id);

fetch("api.php",{
method:"POST",
body:formData
})
.then(res=>res.json())
.then(data=>{

alert(data.message);

loadData();

});

}

}

function resetForm(){

document.getElementById("id").value="";
document.getElementById("nama_aktivitas").value="";
document.getElementById("durasi").value="";
document.getElementById("kalori").value="";

}

</script>

</body>
</html>