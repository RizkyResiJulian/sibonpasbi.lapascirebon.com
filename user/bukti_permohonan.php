<?php
include "../config/koneksi.php";

$kode = $_GET['kode'] ?? '';

if(empty($kode)){
    die("Kode tidak ditemukan");
}

// =======================
// AMBIL DATA PERMOHONAN
// =======================
$stmt = mysqli_prepare($conn,"
    SELECT * FROM permohonan 
    WHERE nomor_permohonan = ?
");

mysqli_stmt_bind_param($stmt,"s",$kode);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$permohonan = mysqli_fetch_assoc($result);

if(!$permohonan){
    die("Data tidak ditemukan");
}

// =======================
// AMBIL DETAIL BARANG
// =======================
$stmt = mysqli_prepare($conn,"
    SELECT b.nama_barang, d.jumlah 
    FROM detail_permohonan d
    JOIN barang b ON d.barang_id = b.id
    WHERE d.permohonan_id = ?
");

mysqli_stmt_bind_param($stmt,"i",$permohonan['id']);
mysqli_stmt_execute($stmt);
$detail = mysqli_stmt_get_result($stmt);

// status badge
$status = strtolower($permohonan['status'] ?? 'pending');

$badge = "pending";
if($status == "selesai"){
    $badge = "selesai";
}elseif($status == "diproses"){
    $badge = "diproses";
}elseif($status == "ditolak"){
    $badge = "ditolak";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Bukti Permohonan</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<script src="https://unpkg.com/lucide@latest"></script>

<style>

:root{
--primary:#0f172a;
--secondary:#1e293b;
--accent:#d4af37;
--bg:#eef2f7;
--card:#ffffff;
--text:#0f172a;
--shadow:0 10px 30px rgba(0,0,0,0.1);
--radius:20px;
}

*{
margin:0;
padding:0;
box-sizing:border-box;
}

body{
font-family:'Poppins',sans-serif;
background:linear-gradient(135deg,#e2e8f0,#f8fafc);
padding:30px;
color:var(--text);
}

/* CARD */

.container{
max-width:900px;
margin:auto;
background:var(--card);
border-radius:var(--radius);
overflow:hidden;
box-shadow:var(--shadow);
animation:fade .5s ease;
}

@keyframes fade{
from{
opacity:0;
transform:translateY(20px);
}
to{
opacity:1;
transform:translateY(0);
}
}

/* HEADER */

.header{
background:linear-gradient(135deg,#0f172a,#1e293b);
padding:30px;
color:white;
position:relative;
overflow:hidden;
}

.header::after{
content:'';
position:absolute;
width:200px;
height:200px;
background:rgba(255,255,255,0.05);
border-radius:50%;
top:-80px;
right:-60px;
}

.logo{
display:flex;
align-items:center;
gap:12px;
margin-bottom:15px;
}

.logo-icon{
width:55px;
height:55px;
background:rgba(255,255,255,0.1);
border-radius:15px;
display:flex;
align-items:center;
justify-content:center;
}

.logo-icon i{
color:var(--accent);
width:28px;
height:28px;
}

.header h1{
font-size:28px;
font-weight:700;
}

.header p{
opacity:0.8;
margin-top:5px;
}

/* STATUS */

.status{
display:inline-flex;
align-items:center;
gap:6px;
padding:8px 15px;
border-radius:50px;
font-size:13px;
font-weight:600;
margin-top:18px;
}

.pending{
background:#fef3c7;
color:#92400e;
}

.diproses{
background:#dbeafe;
color:#1d4ed8;
}

.selesai{
background:#dcfce7;
color:#166534;
}

.ditolak{
background:#fee2e2;
color:#991b1b;
}

/* CONTENT */

.content{
padding:30px;
}

/* GRID */

.grid{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
gap:18px;
margin-bottom:30px;
}

.info-card{
background:#f8fafc;
padding:18px;
border-radius:15px;
border:1px solid #e5e7eb;
transition:.3s;
}

.info-card:hover{
transform:translateY(-3px);
box-shadow:0 6px 18px rgba(0,0,0,0.08);
}

.info-label{
font-size:13px;
color:#64748b;
margin-bottom:5px;
}

.info-value{
font-weight:600;
font-size:15px;
}

/* TABLE */

.table-box{
margin-top:20px;
border-radius:15px;
overflow:hidden;
border:1px solid #e5e7eb;
}

table{
width:100%;
border-collapse:collapse;
}

th{
background:var(--primary);
color:white;
padding:15px;
font-size:14px;
}

td{
padding:14px;
border-bottom:1px solid #eee;
text-align:center;
font-size:14px;
}

tr:hover{
background:#f8fafc;
}

/* FOOTER */

.footer{
margin-top:40px;
display:flex;
justify-content:space-between;
align-items:center;
flex-wrap:wrap;
gap:20px;
}

.qr{
display:flex;
align-items:center;
gap:15px;
background:#f8fafc;
padding:15px;
border-radius:15px;
border:1px solid #e5e7eb;
}

.qr-box{
width:70px;
height:70px;
background:#e2e8f0;
border-radius:10px;
display:flex;
align-items:center;
justify-content:center;
}

.qr-box i{
width:35px;
height:35px;
color:#475569;
}

.signature{
text-align:center;
}

.signature .line{
margin-top:60px;
border-top:2px solid #333;
width:220px;
margin-left:auto;
}

/* BUTTONS */

.actions{
display:flex;
justify-content:space-between;
gap:15px;
margin-top:35px;
}

.btn{
flex:1;
padding:14px;
border:none;
border-radius:14px;
font-size:15px;
font-weight:600;
cursor:pointer;
transition:.3s;
display:flex;
align-items:center;
justify-content:center;
gap:8px;
}

.btn-back{
background:#e2e8f0;
color:#0f172a;
}

.btn-back:hover{
background:#cbd5e1;
}

.btn-print{
background:linear-gradient(135deg,#16a34a,#15803d);
color:white;
}

.btn-print:hover{
transform:translateY(-2px);
box-shadow:0 10px 20px rgba(22,163,74,0.25);
}

/* PRINT */

@media print{

body{
background:white;
padding:0;
}

.actions{
display:none;
}

.container{
box-shadow:none;
}

}

</style>

</head>

<body>

<div class="container">

<!-- HEADER -->

<div class="header">

<div class="logo">

<div class="logo-icon">
<i data-lucide="package-check"></i>
</div>

<div>
<h1>Bukti Permohonan Barang</h1>
<p>Sistem Informasi Bon Lapas Kesambi</p>
</div>

</div>

<div class="status <?= $badge ?>">
<i data-lucide="shield-check"></i>
<?= strtoupper(htmlspecialchars($permohonan['status'])) ?>
</div>

</div>

<!-- CONTENT -->

<div class="content">

<div class="grid">

<div class="info-card">
<div class="info-label">Nomor Permohonan</div>
<div class="info-value">
<?= htmlspecialchars($permohonan['nomor_permohonan']) ?>
</div>
</div>

<div class="info-card">
<div class="info-label">Nama Pemohon</div>
<div class="info-value">
<?= htmlspecialchars($permohonan['nama']) ?>
</div>
</div>

<div class="info-card">
<div class="info-label">NIP</div>
<div class="info-value">
<?= htmlspecialchars($permohonan['nip']) ?>
</div>
</div>

<div class="info-card">
<div class="info-label">Bidang</div>
<div class="info-value">
<?= htmlspecialchars($permohonan['bidang']) ?>
</div>
</div>

<div class="info-card">
<div class="info-label">Tanggal</div>
<div class="info-value">
<?= htmlspecialchars($permohonan['tanggal_pesan']) ?>
</div>
</div>

</div>

<!-- TABLE -->

<div class="table-box">

<table>

<tr>
<th width="80">No</th>
<th>Nama Barang</th>
<th width="150">Jumlah</th>
</tr>

<?php 
$no = 1;
while($row = mysqli_fetch_assoc($detail)){ 
?>

<tr>
<td><?= $no++ ?></td>
<td><?= htmlspecialchars($row['nama_barang']) ?></td>
<td><?= htmlspecialchars($row['jumlah']) ?></td>
</tr>

<?php } ?>

</table>

</div>

<!-- FOOTER -->

<div class="footer">

<div class="qr">

<div class="qr-box">
<i data-lucide="qr-code"></i>
</div>

<div>
<b>Dokumen Digital</b><br>
<small>
Kode: <?= htmlspecialchars($permohonan['nomor_permohonan']) ?>
</small>
</div>

</div>

<div class="signature">

<p>Cirebon, <?= date("d-m-Y") ?></p>

<div class="line"></div>

<p style="margin-top:8px;">
Sub Bagian Umum
</p>

</div>

</div>

<!-- BUTTON -->

<div class="actions">

<button class="btn btn-back" onclick="window.location.href='permohonan_barang.php'">
<i data-lucide="arrow-left"></i>
Kembali
</button>

<button class="btn btn-print" onclick="window.print()">
<i data-lucide="printer"></i>
Cetak Bukti
</button>

</div>

</div>

</div>

<script>
lucide.createIcons();
</script>

</body>
</html>