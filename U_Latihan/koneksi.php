<?php

$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "db_olahraga" // PERBAIKAN: Ubah db_hidup_sehat menjadi db_olahraga
);

if(!$conn){
    die("Koneksi gagal");
}

?>