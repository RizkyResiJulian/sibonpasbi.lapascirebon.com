<?php
include "../config/koneksi.php";

$nomor = $_GET['nomor'];

/* ambil barang yang ada di pesanan */

$data = mysqli_query($conn,"
SELECT barang_id, jumlah 
FROM detail_pesanan 
WHERE nomor_pesanan='$nomor'
");

while($row = mysqli_fetch_assoc($data)){

$barang = $row['barang_id'];
$jumlah = $row['jumlah'];

/* kembalikan stok */

mysqli_query($conn,"
UPDATE barang 
SET stok = stok + $jumlah
WHERE id='$barang'
");

}

/* hapus pesanan */

mysqli_query($conn,"DELETE FROM detail_pesanan WHERE nomor_pesanan='$nomor'");
mysqli_query($conn,"DELETE FROM pesanan WHERE nomor_pesanan='$nomor'");

header("Location: pesanan.php");
?>