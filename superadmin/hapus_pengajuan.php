<?php
session_start();

if(!isset($_SESSION['login'])){
    header("Location: ../auth/login.php");
    exit;
}

if($_SESSION['role'] != "superadmin"){
    echo "Akses ditolak";
    exit;
}

include "../config/koneksi.php";

// =======================
// VALIDASI ID
// =======================

if(!isset($_GET['id'])){
    header("Location: riwayat_pengajuan.php");
    exit;
}

$id = intval($_GET['id']);

// =======================
// CEK DATA PENGAJUAN
// =======================

$cek = mysqli_query($conn,"
SELECT *
FROM permohonan_uang
WHERE id='$id'
");

if(mysqli_num_rows($cek) < 1){
    echo "
    <script>
    alert('Data pengajuan tidak ditemukan!');
    window.location='riwayat_pengajuan.php';
    </script>
    ";
    exit;
}

$data = mysqli_fetch_assoc($cek);

// =======================
// HAPUS FILE LPJ JIKA ADA
// =======================

if(!empty($data['lpj_file'])){

    $path = "../uploads/lpj/" . $data['lpj_file'];

    if(file_exists($path)){
        unlink($path);
    }
}

// =======================
// HAPUS DETAIL PENGAJUAN
// =======================

mysqli_query($conn,"
DELETE FROM detail_pengajuan_uang
WHERE pengajuan_id='$id'
");

// =======================
// HAPUS LOG APPROVAL
// =======================

mysqli_query($conn,"
DELETE FROM log_approval_uang
WHERE pengajuan_id='$id'
");

// =======================
// HAPUS DATA UTAMA
// =======================

$hapus = mysqli_query($conn,"
DELETE FROM permohonan_uang
WHERE id='$id'
");

// =======================
// RESPONSE
// =======================

if($hapus){

    echo "
    <script>
    alert('Pengajuan berhasil dihapus!');
    window.location='riwayat_pengajuan.php';
    </script>
    ";

}else{

    echo "
    <script>
    alert('Gagal menghapus pengajuan!');
    window.location='riwayat_pengajuan.php';
    </script>
    ";
}
?>