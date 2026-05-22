<?php
session_start();

if(!isset($_SESSION['login'])){
    header("Location: ../auth/login.php");
    exit;
}

if($_SESSION['role']!="operator"){
    echo "Akses ditolak";
    exit;
}

include "../config/koneksi.php";

/* CEK ID */
if(!isset($_GET['id'])){
    header("Location: kategori.php");
    exit;
}

$id = intval($_GET['id']);

/* CEK APAKAH KATEGORI DIGUNAKAN DI BARANG */
$cek = mysqli_query($conn,"
SELECT COUNT(*) as total 
FROM barang 
WHERE kategori_id = '$id'
");

$data = mysqli_fetch_assoc($cek);

if($data['total'] > 0){
    echo "<script>
        alert('Kategori tidak bisa dihapus karena masih digunakan oleh barang!');
        window.location='kategori.php';
    </script>";
    exit;
}

/* HAPUS DATA */
$hapus = mysqli_query($conn,"DELETE FROM kategori WHERE id='$id'");

if($hapus){
    echo "<script>
        alert('Kategori berhasil dihapus');
        window.location='kategori.php';
    </script>";
}else{
    echo "<script>
        alert('Gagal menghapus kategori');
        window.location='kategori.php';
    </script>";
}
?>