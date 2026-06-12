<?php

$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "db_hidup_sehat"
);

if(!$conn){
    die("Koneksi gagal");
}

?>