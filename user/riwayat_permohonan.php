<?php
session_start();

// =======================
// CEK LOGIN
// =======================
if(!isset($_SESSION['login'])){
    header("Location: ../auth/login.php");
    exit;
}

// =======================
// CEK ROLE
// =======================
if($_SESSION['role'] != "user"){
    echo "Akses ditolak";
    exit;
}

include "../config/koneksi.php";

// =======================
// DATA USER LOGIN
// =======================
$nip  = $_SESSION['nip'];
$nama = $_SESSION['nama'];

// =======================
// STATISTIK
// =======================

$total_permohonan = mysqli_num_rows(mysqli_query($conn,"
    SELECT *
    FROM permohonan
    WHERE nip='$nip'
"));

$total_pending = mysqli_num_rows(mysqli_query($conn,"
    SELECT *
    FROM permohonan
    WHERE nip='$nip'
    AND verifikasi='pending'
"));

$total_diproses = mysqli_num_rows(mysqli_query($conn,"
    SELECT *
    FROM permohonan
    WHERE nip='$nip'
    AND status='diproses'
"));

$total_selesai = mysqli_num_rows(mysqli_query($conn,"
    SELECT *
    FROM permohonan
    WHERE nip='$nip'
    AND status='selesai'
"));

$total_ditolak = mysqli_num_rows(mysqli_query($conn,"
    SELECT *
    FROM permohonan
    WHERE nip='$nip'
    AND verifikasi='ditolak'
"));

// =======================
// AMBIL DATA PERMOHONAN
// =======================
$query = mysqli_query($conn,"
    SELECT *
    FROM permohonan
    WHERE nip='$nip'
    ORDER BY tanggal_pesan DESC
");

?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Riwayat Permohonan Barang</title>
<link rel="shortcut icon" href="../assets/img/logo1.ico">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<script src="https://unpkg.com/lucide@latest"></script>

<style>

:root{
--primary:#0f172a;
--accent:#d4af37;
--bg:#f1f5f9;
--text:#1e293b;
--gray:#64748b;
--danger:#ef4444;
--success:#22c55e;
--warning:#f59e0b;
--info:#3b82f6;
--shadow:0 10px 25px rgba(0,0,0,.08);
}

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:'Poppins',sans-serif;
}

body{
background:var(--bg);
display:flex;
min-height:100vh;
overflow-x:hidden;
}

/* SIDEBAR */

.sidebar{
width:260px;
background:var(--primary);
padding:25px;
color:white;
display:flex;
flex-direction:column;
}

.sidebar h2{
margin-bottom:35px;
font-size:22px;
color:var(--accent);
}

.sidebar a{
display:flex;
align-items:center;
gap:12px;
padding:13px 15px;
border-radius:14px;
text-decoration:none;
color:white;
margin-bottom:12px;
transition:.3s;
font-size:15px;
}

.sidebar a:hover,
.sidebar a.active{
background:rgba(255,255,255,.1);
}

/* MAIN */

.main{
flex:1;
padding:30px;
}

/* TOPBAR */

.topbar{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:30px;
flex-wrap:wrap;
gap:20px;
}

.topbar h2{
color:var(--primary);
font-size:28px;
}

.topbar p{
color:var(--gray);
margin-top:5px;
}

.user-info{
display:flex;
align-items:center;
gap:15px;
flex-wrap:wrap;
}

.badge{
background:var(--accent);
color:#0f172a;
padding:8px 14px;
border-radius:30px;
font-size:13px;
font-weight:600;
}

.logout{
background:var(--danger);
color:white;
padding:10px 18px;
border-radius:30px;
text-decoration:none;
font-size:14px;
font-weight:500;
display:flex;
align-items:center;
gap:8px;
}

/* STATS */

.stats{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
gap:18px;
margin-bottom:30px;
}

.stat-card{
background:white;
padding:20px;
border-radius:20px;
box-shadow:var(--shadow);
display:flex;
align-items:center;
justify-content:space-between;
transition:.3s;
}

.stat-card:hover{
transform:translateY(-4px);
}

.stat-left p{
font-size:13px;
color:var(--gray);
margin-bottom:6px;
}

.stat-left h3{
font-size:28px;
color:var(--primary);
}

.stat-icon{
width:55px;
height:55px;
border-radius:16px;
display:flex;
align-items:center;
justify-content:center;
background:rgba(212,175,55,.15);
color:var(--accent);
}

/* TABLE CARD */

.table-card{
background:white;
border-radius:24px;
padding:22px;
box-shadow:var(--shadow);
overflow:hidden;
}

/* HEADER */

.table-header{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:20px;
flex-wrap:wrap;
gap:15px;
}

.table-header h3{
font-size:22px;
color:var(--primary);
}

/* SEARCH */

.search-box{
position:relative;
}

.search-box input{
padding:12px 18px 12px 45px;
border:none;
border-radius:14px;
background:#f8fafc;
font-size:14px;
width:250px;
outline:none;
border:1px solid #e2e8f0;
}

.search-box i{
position:absolute;
left:15px;
top:12px;
color:#64748b;
}

/* TABLE */

.table-wrapper{
overflow-x:auto;
border:1px solid #e2e8f0;
border-radius:18px;
max-width:100%;
}

/* SCROLLBAR */

.table-wrapper::-webkit-scrollbar{
height:8px;
}

.table-wrapper::-webkit-scrollbar-thumb{
background:#cbd5e1;
border-radius:20px;
}

/* TABLE */

table{
width:100%;
border-collapse:collapse;
min-width:780px;
background:white;
}

/* HEADER TABLE */

thead{
background:#f8fafc;
}

th{
padding:13px 14px;
text-align:left;
font-size:12px;
font-weight:600;
color:var(--primary);
white-space:nowrap;
}

/* BODY TABLE */

td{
padding:13px 14px;
border-top:1px solid #e2e8f0;
font-size:12px;
color:var(--text);
vertical-align:middle;
}

/* HOVER */

tbody tr:hover{
background:#fafafa;
}

/* STATUS */

.status{
padding:5px 10px;
border-radius:999px;
font-size:11px;
font-weight:600;
display:inline-flex;
align-items:center;
gap:5px;
white-space:nowrap;
}

/* PROGRESS */

.progress-box{
display:flex;
flex-direction:column;
gap:5px;
max-width:180px;
}

.progress-item{
display:flex;
align-items:flex-start;
gap:6px;
font-size:11px;
line-height:1.5;
}

.progress-item i{
width:13px;
height:13px;
margin-top:1px;
flex-shrink:0;
}

.done{
color:var(--success);
font-weight:600;
}

.wait{
color:var(--warning);
font-weight:500;
}

.reject-text{
color:var(--danger);
font-weight:600;
}

/* BUTTON */

.btn-detail{
display:inline-flex;
align-items:center;
gap:5px;
padding:7px 10px;
border-radius:10px;
background:linear-gradient(135deg,#d4af37,#b8962e);
color:#0f172a;
text-decoration:none;
font-size:11px;
font-weight:600;
transition:.3s;
white-space:nowrap;
}

.btn-detail:hover{
transform:translateY(-2px);
}

/* MOBILE */

/* ================= MOBILE ================= */

@media(max-width:900px){

body{
flex-direction:column;
}

/* SIDEBAR */

.sidebar{
width:100%;
padding:18px 15px;
position:relative;
}

.sidebar h2{
font-size:20px;
margin-bottom:20px;
text-align:center;
}

.sidebar a{
padding:12px 14px;
font-size:14px;
border-radius:14px;
margin-bottom:10px;
}

/* MAIN */

.main{
padding:18px;
}

/* TOPBAR */

.topbar{
flex-direction:column;
align-items:flex-start;
gap:18px;
margin-bottom:25px;
}

.topbar h2{
font-size:24px;
line-height:1.3;
}

.topbar p{
font-size:14px;
}

.user-info{
width:100%;
flex-direction:column;
align-items:flex-start;
gap:12px;
}

.badge{
font-size:12px;
padding:7px 12px;
}

.logout{
width:100%;
justify-content:center;
padding:12px;
}

/* STATS */

.stats{
grid-template-columns:1fr;
gap:16px;
margin-bottom:28px;
}

.stat-card{
padding:20px;
border-radius:20px;
}

.stat-card h3{
font-size:26px;
}

.stat-card p{
font-size:13px;
}

.stat-icon{
width:52px;
height:52px;
border-radius:16px;
}

/* MENU GRID */

.grid{
grid-template-columns:1fr;
gap:18px;
}

.card{
padding:24px 20px;
border-radius:20px;
}

.card h4{
font-size:18px;
}

.card p{
font-size:14px;
line-height:1.7;
}

/* BUTTON */

.btn{
width:100%;
padding:12px;
}

/* ICON */

.icon{
width:62px;
height:62px;
border-radius:18px;
margin-bottom:16px;
}

/* PEMBATAS */

.divider{
margin:28px 0;
}

/* TEXT */

h3{
line-height:1.4;
}

}

</style>

</head>

<body>

<!-- SIDEBAR -->

<div class="sidebar">

<h2>SIBONPASBI</h2>

<a href="dashboard.php">
<i data-lucide="layout-dashboard"></i>
Dashboard
</a>

<a href="pengajuan_uang.php">
<i data-lucide="wallet"></i>
Pengajuan Uang
</a>

<a href="permohonan_barang.php">
<i data-lucide="package"></i>
Permohonan Barang
</a>

<a href="riwayat_pengajuan.php">
<i data-lucide="history"></i>
Riwayat Pengajuan Uang
</a>

<a href="riwayat_permohonan.php" class="active">
<i data-lucide="clipboard-list"></i>
Riwayat Permohonan Barang
</a>

<a href="ganti_password.php">
<i data-lucide="key-round"></i>
Ganti Password
</a>

</div>

<!-- MAIN -->

<div class="main">

<!-- TOPBAR -->

<div class="topbar">

<div>
<h2>Riwayat Permohonan Barang</h2>
<p>Monitoring seluruh progres permohonan barang</p>
</div>

<div class="user-info">

<div class="badge">
USER
</div>

<div>
Halo, <b><?= htmlspecialchars($nama); ?></b> 👋
</div>

<a href="../auth/logout.php" class="logout">
<i data-lucide="log-out"></i>
Logout
</a>

</div>

</div>

<!-- STATISTIK -->

<div class="stats">

<div class="stat-card">

<div class="stat-left">
<p>Total Permohonan</p>
<h3><?= $total_permohonan; ?></h3>
</div>

<div class="stat-icon">
<i data-lucide="clipboard-list"></i>
</div>

</div>

<div class="stat-card">

<div class="stat-left">
<p>Menunggu Verifikasi</p>
<h3><?= $total_pending; ?></h3>
</div>

<div class="stat-icon">
<i data-lucide="clock-3"></i>
</div>

</div>

<div class="stat-card">

<div class="stat-left">
<p>Sedang Diproses</p>
<h3><?= $total_diproses; ?></h3>
</div>

<div class="stat-icon">
<i data-lucide="loader"></i>
</div>

</div>

<div class="stat-card">

<div class="stat-left">
<p>Selesai</p>
<h3><?= $total_selesai; ?></h3>
</div>

<div class="stat-icon">
<i data-lucide="check-circle"></i>
</div>

</div>

<div class="stat-card">

<div class="stat-left">
<p>Ditolak</p>
<h3><?= $total_ditolak; ?></h3>
</div>

<div class="stat-icon">
<i data-lucide="x-circle"></i>
</div>

</div>

</div>

<!-- TABLE -->

<div class="table-card">

<div class="table-header">

<h3>Data Permohonan Barang</h3>

<div class="search-box">

<i data-lucide="search"></i>

<input 
type="text" 
id="searchInput"
placeholder="Cari permohonan...">

</div>

</div>

<?php if(mysqli_num_rows($query) > 0){ ?>

<div class="table-wrapper">

<table id="tableData">

<thead>

<tr>
<th>No</th>
<th>Nomor</th>
<th>Tanggal</th>
<th>Status</th>
<th>Verifikasi</th>
<th>Progress</th>
<th>Bukti</th>
</tr>

</thead>

<tbody>

<?php
$no = 1;

while($data = mysqli_fetch_assoc($query)){

$status = $data['status'];
$verif  = $data['verifikasi'];

?>

<tr>

<td><?= $no++; ?></td>

<td>
<b><?= htmlspecialchars($data['nomor_permohonan']); ?></b>
</td>

<td>
<?= date('d M Y H:i', strtotime($data['tanggal_pesan'])); ?>
</td>

<!-- STATUS -->

<td>

<?php

if($status == "pending"){
    echo "<span class='status pending'>Pending</span>";
}
elseif($status == "diproses"){
    echo "<span class='status diproses'>Diproses</span>";
}
elseif($status == "selesai"){
    echo "<span class='status selesai'>Selesai</span>";
}
else{
    echo "<span class='status batal'>Batal</span>";
}

?>

</td>

<!-- VERIF -->

<td>

<?php

if($verif == "pending"){
    echo "<span class='status verif-pending'>Pending</span>";
}
elseif($verif == "disetujui"){
    echo "<span class='status verif-setuju'>Disetujui</span>";
}
else{
    echo "<span class='status verif-tolak'>Ditolak</span>";
}

?>

</td>

<!-- PROGRESS -->

<td>

<div class="progress-box">

<?php if($verif == "pending"){ ?>

<div class="progress-item wait">
<i data-lucide="clock-3"></i>
Menunggu Kasubag Umum
</div>

<?php } elseif($verif == "ditolak"){ ?>

<div class="progress-item reject-text">
<i data-lucide="x-circle"></i>
Ditolak Kasubag Umum
</div>

<?php } else { ?>

<div class="progress-item done">
<i data-lucide="badge-check"></i>
Diverifikasi Kasubag Umum
</div>

<?php if($status == "diproses"){ ?>

<div class="progress-item wait">
<i data-lucide="truck"></i>
Barang Diproses
</div>

<?php } elseif($status == "selesai"){ ?>

<div class="progress-item done">
<i data-lucide="package-check"></i>
Barang Diserahkan
</div>

<?php } ?>

<?php } ?>

</div>

</td>

<!-- BUKTI -->

<td>

<?php if(!empty($data['bukti_penyerahan'])){ ?>

<a 
href="../uploads/bukti/<?= htmlspecialchars($data['bukti_penyerahan']); ?>"
target="_blank"
class="btn-detail">

<i data-lucide="image"></i>
Lihat

</a>

<?php } else { ?>

-

<?php } ?>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

<?php } else { ?>

<div class="empty">

<i data-lucide="inbox"></i>

<h3>Belum Ada Permohonan</h3>

<p>
Anda belum pernah membuat permohonan barang.
</p>

</div>

<?php } ?>

</div>

</div>

<script>

lucide.createIcons();

// SEARCH

document.getElementById('searchInput')
.addEventListener('keyup', function(){

let value = this.value.toLowerCase();

let rows = document.querySelectorAll('#tableData tbody tr');

rows.forEach(row=>{

let text = row.innerText.toLowerCase();

row.style.display = text.includes(value)
? ''
: 'none';

});

});

</script>

</body>
</html>