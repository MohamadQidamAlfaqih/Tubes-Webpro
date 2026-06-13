<?php
$host     = "localhost";  
$username = "root";      
$password = "";            
$database = "hidup_sehat"; 


$koneksi = mysqli_connect($host, $username, $password, $database);

if (!$koneksi) {
    die("❌ Koneksi database gagal! Error: " . mysqli_connect_error());
}

mysqli_set_charset($koneksi, "utf8");

