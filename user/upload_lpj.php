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

$id = (int) $_POST['id'];

// AMBIL DATA PENGAJUAN
$stmt = mysqli_prepare($conn,"
    SELECT file_lpj,status
    FROM permohonan_uang
    WHERE id=?
    AND user_id=?
");

mysqli_stmt_bind_param($stmt,"ii",$id,$user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

if(!$data){
    die("Data tidak ditemukan");
}

// VALIDASI STATUS
if(
    $data['status'] != 'dicairkan' &&
    $data['status'] != 'lpj' &&
    $data['status'] != 'revisi'
){
    die("Status tidak valid");
}

// VALIDASI FILE
if(!isset($_FILES['lpj']) || $_FILES['lpj']['error'] != 0){
    die("File gagal diupload");
}

$file = $_FILES['lpj'];

$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

$allowed = [
    'pdf',
    'jpg',
    'jpeg',
    'png',
    'doc',
    'docx',
    'xls',
    'xlsx'
];

if(!in_array($ext,$allowed)){
    die("Format file tidak diizinkan");
}

// VALIDASI MIME TYPE
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime  = finfo_file($finfo, $file['tmp_name']);

$allowedMime = [

    'application/pdf',

    'image/jpeg',
    'image/png',

    'application/msword',

    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',

    'application/vnd.ms-excel',

    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',

    // tambahan agar hosting lebih fleksibel
    'application/octet-stream',
    'application/zip'
];

if(!in_array($mime,$allowedMime)){
    die("Tipe file tidak valid");
}

// BATAS 5MB
if($file['size'] > 5 * 1024 * 1024){
    die("Ukuran file terlalu besar");
}

// FOLDER
$folder = "../uploads/lpj/";

if(!is_dir($folder)){
    mkdir($folder,0777,true);
}

// HAPUS FILE LAMA
if(!empty($data['file_lpj'])){

    $oldFile = $folder . $data['file_lpj'];

    if(file_exists($oldFile)){
        unlink($oldFile);
    }

}

// NAMA FILE BARU
$namaFile = "LPJ_" . time() . "_" . rand(1000,9999) . "." . $ext;

$path = $folder . $namaFile;

// UPLOAD
if(move_uploaded_file($file['tmp_name'],$path)){

    $update = mysqli_prepare($conn,"
        UPDATE permohonan_uang
        SET
            file_lpj=?,
            status='lpj'
        WHERE id=?
    ");

    mysqli_stmt_bind_param($update,"si",$namaFile,$id);
    mysqli_stmt_execute($update);

    header("Location: riwayat_pengajuan.php");
    exit;

}else{

    die("Upload gagal");

}
?>