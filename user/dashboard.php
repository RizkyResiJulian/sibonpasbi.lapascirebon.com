<?php
session_start();

if(!isset($_SESSION['login'])){
    header("Location: ../auth/login.php");
    exit;
}

if($_SESSION['role'] != "user"){
    echo "Akses ditolak";
    exit;
}

include "../config/koneksi.php";

/* TOTAL PENGAJUAN UANG */

$user_id = $_SESSION['user_id'];

$total_pengajuan = mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM permohonan_uang
WHERE user_id='$user_id'
"));

/* =========================
   STATUS PENGAJUAN UANG
========================= */

/* DIPROSES
   pending + approve_ppk + approve_kalapas
*/

$diproses = mysqli_num_rows(mysqli_query($conn,"
SELECT *
FROM permohonan_uang
WHERE user_id='$user_id'
AND status IN (
    'pending',
    'approve_ppk',
    'approve_kalapas'
)
"));

/* SUDAH DICAIRKAN */

$dicairkan = mysqli_num_rows(mysqli_query($conn,"
SELECT *
FROM permohonan_uang
WHERE user_id='$user_id'
AND status='dicairkan'
"));

/* REVISI LPJ */

$revisi = mysqli_num_rows(mysqli_query($conn,"
SELECT *
FROM permohonan_uang
WHERE user_id='$user_id'
AND status='revisi'
"));

/* SELESAI */

$selesai = mysqli_num_rows(mysqli_query($conn,"
SELECT *
FROM permohonan_uang
WHERE user_id='$user_id'
AND status='selesai'
"));

/* DITOLAK */

$ditolak = mysqli_num_rows(mysqli_query($conn,"
SELECT *
FROM permohonan_uang
WHERE user_id='$user_id'
AND status='ditolak'
"));

/* TOTAL PERMOHONAN BARANG */

$user_data = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT nip FROM users
WHERE id='$user_id'
"));

$nip = $user_data['nip'];

$total_barang = mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM permohonan
WHERE nip='$nip'
"));

/* TOTAL PERMOHONAN BARANG */

$total_barang = mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM permohonan
WHERE nip='$nip'
"));

$barang_pending = mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM permohonan
WHERE nip='$nip'
AND status='pending'
"));

$barang_diproses = mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM permohonan
WHERE nip='$nip'
AND status='diproses'
"));

$barang_selesai = mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM permohonan
WHERE nip='$nip'
AND status='selesai'
"));

$barang_batal = mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM permohonan
WHERE nip='$nip'
AND status='batal'
"));
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Dashboard User</title>
<link rel="shortcut icon" href="../assets/img/logo1.ico">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<script src="https://unpkg.com/lucide@latest"></script>

<style>

:root{
--primary:#0f172a;
--accent:#d4af37;
--bg:#f1f5f9;
--text:#1e293b;
--gray:#64748b;
--white:#ffffff;
--danger:#ef4444;
--success:#22c55e;
}

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:Poppins;
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
grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
gap:20px;
margin-bottom:35px;
}

.stat-card{
background:white;
padding:25px;
border-radius:22px;
box-shadow:0 10px 25px rgba(0,0,0,.08);
display:flex;
justify-content:space-between;
align-items:center;
transition:.3s;
}

.stat-card:hover{
transform:translateY(-5px);
}

.stat-card h3{
font-size:30px;
margin-top:8px;
color:var(--primary);
}

.stat-card p{
color:var(--gray);
font-size:14px;
}

.stat-icon{
width:60px;
height:60px;
border-radius:18px;
display:flex;
align-items:center;
justify-content:center;
background:rgba(212,175,55,.15);
color:var(--accent);
}

/* MENU */

.grid{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
gap:25px;
}

.card{
background:white;
padding:30px;
border-radius:22px;
text-align:center;
box-shadow:0 10px 25px rgba(0,0,0,.08);
transition:.3s;
}

.card:hover{
transform:translateY(-6px);
}

.icon{
width:70px;
height:70px;
margin:auto;
background:var(--primary);
color:var(--accent);
display:flex;
align-items:center;
justify-content:center;
border-radius:20px;
margin-bottom:18px;
}

.card h4{
margin-bottom:10px;
font-size:18px;
color:var(--primary);
}

.card p{
font-size:14px;
color:var(--gray);
line-height:1.7;
margin-bottom:20px;
}

.btn{
display:inline-flex;
align-items:center;
justify-content:center;
gap:8px;
padding:10px 20px;
background:linear-gradient(135deg,#d4af37,#b8962e);
color:#0f172a;
text-decoration:none;
border-radius:30px;
font-weight:600;
transition:.3s;
}

.btn:hover{
transform:translateY(-3px);
}

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
display:grid;
grid-template-columns:repeat(2,1fr);
gap:12px;
margin-bottom:22px;
}

.stat-card{
padding:14px;
border-radius:18px;
min-height:95px;
}

.stat-card h3{
font-size:20px;
margin-top:4px;
}

.stat-card p{
font-size:11px;
line-height:1.3;
}

.stat-icon{
width:42px;
height:42px;
border-radius:14px;
flex-shrink:0;
}

.stat-icon i{
width:18px;
height:18px;
}

/* BIAR FULL WIDTH KALO GANJIL */

.stats .stat-card:last-child:nth-child(odd){
grid-column:1 / -1;
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

.divider{
height:2px;
background:linear-gradient(to right, transparent, #d4af37, transparent);
margin:35px 0;
border-radius:999px;
}

/* ================= MOBILE SIDEBAR ================= */

.menu-toggle{
display:none;
position:fixed;
top:15px;
left:15px;
z-index:2001;

width:48px;
height:48px;

border:none;
border-radius:14px;

background:var(--primary);
color:white;

align-items:center;
justify-content:center;

cursor:pointer;

box-shadow:0 10px 25px rgba(0,0,0,.15);
}

.sidebar-overlay{
display:none;
position:fixed;
inset:0;
background:rgba(0,0,0,.45);
z-index:1998;
opacity:0;
transition:.3s;
}

/* ================= MOBILE ================= */

@media(max-width:900px){

body{
flex-direction:column;
}

/* BUTTON MENU */

.menu-toggle{
display:flex;
}

/* SIDEBAR */

.sidebar{
position:fixed;
top:0;
left:-280px;

width:260px;
height:100vh;

z-index:2000;

transition:.35s ease;
overflow-y:auto;

padding-top:25px;
}

/* ACTIVE */

.sidebar.active{
left:0;
}

.sidebar-overlay.active{
display:block;
opacity:1;
}

/* MAIN */

.main{
width:100%;
padding:85px 18px 20px;
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

/* LOGOUT */

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
transition:.3s;
}

.logout:hover{
transform:translateY(-2px);
}

/* ================= MOBILE ================= */

@media(max-width:900px){

/* LOGOUT FLOATING */

.logout{
position:fixed;
top:15px;
right:15px;

z-index:2001;

width:48px;
height:48px;

padding:0;

border-radius:14px;

justify-content:center;

font-size:0;

box-shadow:0 10px 25px rgba(0,0,0,.15);
}

.logout i{
width:20px;
height:20px;
margin:0;
}

/* USER INFO */

.user-info{
width:100%;
flex-direction:column;
align-items:flex-start;
gap:12px;
}

/* MAIN */

.main{
padding:85px 18px 20px;
}

}

/* CARD */

.grid{
grid-template-columns:1fr;
gap:18px;
}

.card{
padding:24px 20px;
border-radius:20px;
}

.btn{
width:100%;
justify-content:center;
}

}

</style>

</head>

<body>

<!-- MOBILE MENU BUTTON -->

<button class="menu-toggle" onclick="toggleSidebar()">
    <i data-lucide="menu"></i>
</button>

<!-- OVERLAY -->

<div class="sidebar-overlay" onclick="toggleSidebar()"></div>

<!-- SIDEBAR -->

<div class="sidebar" id="sidebar">

<h2>SIBONPASBI</h2>

<a href="dashboard.php" class="active">
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

<a href="riwayat_permohonan.php">
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
<h2>Dashboard User</h2>
<p>Kelola pengajuan uang dan permohonan barang pegawai</p>
</div>

<div class="user-info">

<div class="badge">
USER
</div>

<div>
Halo, <b><?= $_SESSION['nama']; ?></b> 👋
</div>

<a href="../auth/logout.php" class="logout" title="Logout">
<i data-lucide="log-out"></i>
<span>Logout</span>
</a>

</div>

</div>

<!-- STATISTIK UANG -->

<div style="margin-bottom:15px;">
<h3 style="
font-size:22px;
color:var(--primary);
margin-bottom:5px;
">
Statistik Pengajuan Uang
</h3>

<p style="color:var(--gray);font-size:14px;">
Ringkasan seluruh pengajuan bon uang
</p>
</div>

<div class="stats">

<!-- TOTAL -->

<div class="stat-card">

<div>
<p>Total Pengajuan</p>
<h3><?= $total_pengajuan; ?></h3>
</div>

<div class="stat-icon">
<i data-lucide="wallet"></i>
</div>

</div>

<!-- DIPROSES -->

<div class="stat-card">

<div>
<p>Diproses</p>
<h3><?= $diproses; ?></h3>
</div>

<div class="stat-icon">
<i data-lucide="loader-circle"></i>
</div>

</div>

<!-- DICAIRKAN -->

<div class="stat-card">

<div>
<p>Sudah Dicairkan</p>
<h3><?= $dicairkan; ?></h3>
</div>

<div class="stat-icon">
<i data-lucide="banknote"></i>
</div>

</div>

<!-- REVISI -->

<div class="stat-card">

<div>
<p>Revisi LPJ</p>
<h3><?= $revisi; ?></h3>
</div>

<div class="stat-icon">
<i data-lucide="file-warning"></i>
</div>

</div>

<!-- SELESAI -->

<div class="stat-card">

<div>
<p>Selesai</p>
<h3><?= $selesai; ?></h3>
</div>

<div class="stat-icon">
<i data-lucide="badge-check"></i>
</div>

</div>

<!-- DITOLAK -->

<div class="stat-card">

<div>
<p>Ditolak</p>
<h3><?= $ditolak; ?></h3>
</div>

<div class="stat-icon">
<i data-lucide="x-circle"></i>
</div>

</div>

</div>

<!-- PEMBATAS -->

<div class="divider"></div>

<!-- STATISTIK BARANG -->

<div style="margin-bottom:15px;">
<h3 style="
font-size:22px;
color:var(--primary);
margin-bottom:5px;
">
Statistik Permohonan Barang
</h3>

<p style="color:var(--gray);font-size:14px;">
Ringkasan seluruh permintaan barang operasional
</p>
</div>

<div class="stats">

<div class="stat-card">

<div>
<p>Total Permohonan</p>
<h3><?= $total_barang; ?></h3>
</div>

<div class="stat-icon">
<i data-lucide="package"></i>
</div>

</div>

<div class="stat-card">

<div>
<p>Pending</p>
<h3><?= $barang_pending; ?></h3>
</div>

<div class="stat-icon">
<i data-lucide="loader"></i>
</div>

</div>

<div class="stat-card">

<div>
<p>Diproses</p>
<h3><?= $barang_diproses; ?></h3>
</div>

<div class="stat-icon">
<i data-lucide="truck"></i>
</div>

</div>

<div class="stat-card">

<div>
<p>Selesai</p>
<h3><?= $barang_selesai; ?></h3>
</div>

<div class="stat-icon">
<i data-lucide="badge-check"></i>
</div>

</div>

<div class="stat-card">

<div>
<p>Batal</p>
<h3><?= $barang_batal; ?></h3>
</div>

<div class="stat-icon">
<i data-lucide="ban"></i>
</div>

</div>

</div>

<!-- MENU -->

<div class="grid">

<!-- PENGAJUAN UANG -->

<div class="card">

<div class="icon">
<i data-lucide="wallet"></i>
</div>

<h4>Pengajuan Uang</h4>

<p>
Membuat pengajuan bon uang
untuk kebutuhan operasional
dan kegiatan kantor.
</p>

<a href="pengajuan_uang.php" class="btn">
<i data-lucide="plus"></i>
Buat Pengajuan
</a>

</div>

<!-- PERMOHONAN BARANG -->

<div class="card">

<div class="icon">
<i data-lucide="package"></i>
</div>

<h4>Permohonan Barang</h4>

<p>
Mengajukan permintaan barang
untuk kebutuhan operasional
dan perlengkapan kantor.
</p>

<a href="permohonan_barang.php" class="btn">
<i data-lucide="box"></i>
Ajukan Barang
</a>

</div>

<!-- RIWAYAT -->

<div class="card">

<div class="icon">
<i data-lucide="history"></i>
</div>

<h4>Riwayat Pengajuan</h4>

<p>
Melihat seluruh riwayat
pengajuan uang beserta
status approval.
</p>

<a href="riwayat_pengajuan.php" class="btn">
<i data-lucide="eye"></i>
Lihat Riwayat
</a>

</div>

</div>

</div>

<script>
lucide.createIcons();
</script>

<script>

lucide.createIcons();

function toggleSidebar(){

document.getElementById("sidebar").classList.toggle("active");

document.querySelector(".sidebar-overlay")
.classList.toggle("active");

}

</script>

</body>
</html>