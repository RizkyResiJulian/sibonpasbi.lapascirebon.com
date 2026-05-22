<?php
session_start();
include "../config/koneksi.php";

// =======================
// AMBIL USER LOGIN
// =======================
$user_id = $_SESSION['user_id'] ?? 0;
$user = null;

if($user_id){
    $stmtUser = mysqli_prepare($conn,"SELECT nama, nip FROM users WHERE id=?");

    if(!$stmtUser){
        die("Query user error: ".mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmtUser,"i",$user_id);
    mysqli_stmt_execute($stmtUser);
    $resultUser = mysqli_stmt_get_result($stmtUser);
    $user = mysqli_fetch_assoc($resultUser);
}

// =======================
// AMBIL ID
// =======================
$id = $_GET['id'] ?? 0;

if(empty($id)){
    die("ID tidak ditemukan");
}

// =======================
// DATA PERMOHONAN
// =======================
$stmt = mysqli_prepare($conn,"
    SELECT * FROM permohonan 
    WHERE id = ?
");

if(!$stmt){
    die("Query permohonan error: ".mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt,"i",$id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$permohonan = mysqli_fetch_assoc($result);

if(!$permohonan){
    die("Data tidak ditemukan");
}

// =======================
// DETAIL BARANG
// =======================
$stmt = mysqli_prepare($conn,"
    SELECT b.nama_barang, d.jumlah 
    FROM detail_permohonan d
    JOIN barang b ON d.barang_id = b.id
    WHERE d.permohonan_id = ?
");

if(!$stmt){
    die("Query detail error: ".mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt,"i",$id);
mysqli_stmt_execute($stmt);
$detail = mysqli_stmt_get_result($stmt);

// HITUNG JUMLAH BARIS
$total_rows = max(1, mysqli_num_rows($detail));
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Bon Barang</title>

<style>
body{
    font-family: Arial, sans-serif;
    background:white;
}

.container{
    width:800px;
    margin:auto;
    padding:20px;
}

/* HEADER */
.header{
    text-align:center;
    border-bottom:2px solid black;
    padding-bottom:10px;
    margin-bottom:20px;
}

.header img{
    position:absolute;
    left:90px;
    top:30px;
    width:90px;
}

.judul{
    text-align:center;
    font-weight:bold;
    margin:20px 0;
}

/* INFO */
.info{
    margin-bottom:15px;
}

.info table{
    border:none;
}

.info td{
    padding:3px 5px;
}

/* TABEL */
table{
    width:100%;
    border-collapse:collapse;
}

th, td{
    border:1px solid black;
    padding:6px;
    text-align:center;
    font-size:14px;
}

/* GAMBAR */
.img-bukti{
    width:180px;
    height:150px;
    object-fit:cover;
    border:1px solid #000;
    border-radius:6px;
}

/* TTD */
.ttd{
    margin-top:40px;
    display:flex;
    justify-content:space-between;
}

.ttd-box{
    width:40%;
    text-align:center;
    font-size:14px;
}

.ttd-bawah{
    margin-top:40px;
    text-align:center;
}

.ttd-bawah img{
    height:80px;
    margin:10px 0;
}

/* PRINT */
@media print{
    button{
        display:none;
    }
}
</style>
</head>

<body>

<div class="container">

<!-- HEADER -->
<div class="header">
    <img src="../assets/img/logo1.png">
    <div>
        <b>KEMENTERIAN IMIGRASI DAN PEMASYARAKATAN RI</b><br>
        DIREKTORAT JENDERAL PEMASYARAKATAN<br>
        KANTOR WILAYAH JAWA BARAT<br>
        <b>LEMBAGA PEMASYARAKATAN CIREBON</b><br>
        Jalan Kesambi No 38, Kesambi, Kota Cirebon, Jawa Barat<br>
        Telepon (0231)204522 Faksimile (0231)202322<br>
        Laman: https://lapascirebon.kemenkumham.go.id, Pos-el: lp.cirebon@kemenkumham.go.id
    </div>
</div>

<div class="judul">
    PERMINTAAN BON BARANG PERSEDIAAN
</div>

<!-- INFO -->
<div class="info">
<table>
<tr>
<td width="150">Nama Pemohon</td>
<td width="10">:</td>
<td><?= htmlspecialchars($permohonan['nama']) ?></td>
</tr>

<tr>
<td>Bidang</td>
<td>:</td>
<td><?= htmlspecialchars($permohonan['bidang']) ?></td>
</tr>

<tr>
<td>Tanggal</td>
<td>:</td>
<td><?= htmlspecialchars($permohonan['tanggal_pesan']) ?></td>
</tr>
</table>
</div>

<!-- TABEL -->
<table>
<tr>
<th width="50">No</th>
<th>Nama Barang</th>
<th width="100">Jumlah</th>
<th width="220">Bukti Penyerahan</th>
</tr>

<?php 
$no = 1;
$first = true;

while($row = mysqli_fetch_assoc($detail)){
?>
<tr>
<td><?= $no++ ?></td>
<td><?= htmlspecialchars($row['nama_barang']) ?></td>
<td><?= htmlspecialchars($row['jumlah']) ?></td>

<?php if($first){ ?>
<td rowspan="<?= $total_rows ?>" style="vertical-align:top; text-align:center; padding-top:10px;">

<?php if(!empty($permohonan['bukti_penyerahan'])){ ?>
    <img src="../uploads/bukti/<?= $permohonan['bukti_penyerahan']; ?>" class="img-bukti">
<?php } else { ?>
    -
<?php } ?>

</td>
<?php $first = false; } ?>

</tr>
<?php } ?>

</table>

<!-- TTD -->
<div class="ttd">
    <div class="ttd-box">
        Staf Umum /<br>Yang Menyerahkan<br><br><br>
        <b><?= htmlspecialchars($user['nama'] ?? '-') ?></b><br>
        NIP. <?= htmlspecialchars($user['nip'] ?? '-') ?>
    </div>

    <div class="ttd-box">
        Pemohon /<br>Yang Menerima<br><br><br>
        <b><?= htmlspecialchars($permohonan['nama']) ?></b><br>
        NIP. <?= htmlspecialchars($permohonan['nip']) ?>
    </div>
</div>

<div class="ttd-bawah">
    <p>Mengetahui,<br>Kasubbag Umum</p>
    <img src="../uploads/ttd_dummy.png"><br>
    <b>Endang Hendaryati, S.H., M.Si.</b><br>
    NIP. 197305251999032001
</div>

<br>

<button onclick="window.print()">Cetak</button>

</div>

</body>
</html>