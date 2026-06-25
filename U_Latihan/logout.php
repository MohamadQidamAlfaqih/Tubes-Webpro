<?php
session_start();

// Hapus semua variabel data session yang terdaftar
session_unset();

// Hancurkan session yang berjalan pada server
session_destroy();

// Pindahkan kembali ke halaman login utama
header("Location: login.php");
exit();
?>