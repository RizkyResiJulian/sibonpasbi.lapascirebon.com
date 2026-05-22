<?php
session_start();

if(!isset($_SESSION['login'])){
    header("Location: ../auth/login.php");
    exit;
}

if($_SESSION['role'] != 'user'){
    echo "Akses ditolak";
    exit;
}

include "../config/koneksi.php";

$user_id = $_SESSION['user_id'];

$id = (int)$_GET['id'];

/* =========================
   CEK DATA
========================= */

$cek = mysqli_query($conn,"
SELECT *
FROM permohonan_uang
WHERE id='$id'
AND user_id='$user_id'
AND status='pending'
");

if(mysqli_num_rows($cek) < 1){

    echo "Pengajuan tidak ditemukan atau tidak bisa dihapus";
    exit;

}

/* =========================
   HAPUS DETAIL
========================= */

mysqli_query($conn,"
DELETE FROM detail_pengajuan_uang
WHERE pengajuan_id='$id'
");

/* =========================
   HAPUS HEADER
========================= */

mysqli_query($conn,"
DELETE FROM permohonan_uang
WHERE id='$id'
");

/* =========================
   REDIRECT
========================= */

header("Location: riwayat_pengajuan.php");
exit;
?>