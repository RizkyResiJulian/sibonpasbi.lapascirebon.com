<?php
session_start();

include "../config/koneksi.php";

if(!isset($_SESSION['login'])){
    header("Location: ../auth/login.php");
    exit;
}

if($_SESSION['role'] != "user"){
    echo "Akses ditolak";
    exit;
}

$user_id = $_SESSION['user_id'];

/* =========================
   AMBIL DATA FORM
========================= */

$keperluan = mysqli_real_escape_string($conn,$_POST['keperluan']);
$total     = (int)$_POST['total'];

$uraian    = $_POST['uraian'];
$subtotal  = $_POST['subtotal'];

/* =========================
   AMBIL DATA USER
========================= */

$userQuery = mysqli_query($conn,"
SELECT bidang
FROM users
WHERE id='$user_id'
");

$user = mysqli_fetch_assoc($userQuery);

$bidang = $user['bidang'];

/* =========================
   NOMOR PENGAJUAN
========================= */

$nomor = "BON-" . date('YmdHis');

/* =========================
   INSERT HEADER
========================= */

mysqli_query($conn,"
INSERT INTO permohonan_uang(
nomor_pengajuan,
tanggal,
user_id,
bidang,
total,
keperluan,
status
) VALUES (
'$nomor',
CURDATE(),
'$user_id',
'$bidang',
'$total',
'$keperluan',
'pending'
)
");

$pengajuan_id = mysqli_insert_id($conn);

/* =========================
   INSERT DETAIL
========================= */

for($i=0; $i<count($uraian); $i++){

    $u = mysqli_real_escape_string($conn,$uraian[$i]);
    $s = (int)$subtotal[$i];

    mysqli_query($conn,"
    INSERT INTO detail_pengajuan_uang(
    pengajuan_id,
    uraian,
    subtotal
    ) VALUES (
    '$pengajuan_id',
    '$u',
    '$s'
    )
    ");
}

/* =========================
   REDIRECT
========================= */

header("Location: riwayat_pengajuan.php");
exit;
?>