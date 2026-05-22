<?php
session_start();

if(!isset($_SESSION['login'])){
    header("Location: ../auth/login.php");
    exit;
}

if($_SESSION['role'] != "bendahara"){
    echo "Akses ditolak";
    exit;
}

include "../config/koneksi.php";

$id = intval($_GET['id']);

mysqli_query($conn,"
UPDATE permohonan_uang
SET status='selesai'
WHERE id='$id'
");

header("Location: riwayat_pengajuan.php");
exit;
?>