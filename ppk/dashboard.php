<?php
session_start();

if(!isset($_SESSION['login'])){
    header("Location: ../auth/login.php");
    exit;
}

if($_SESSION['role'] != "ppk"){
    echo "Akses ditolak";
    exit;
}

include "../config/koneksi.php";

/* STATISTIK */

$total_pending = mysqli_num_rows(mysqli_query($conn,"
SELECT id FROM permohonan_uang
WHERE status='pending'
"));

$total_disetujui = mysqli_num_rows(mysqli_query($conn,"
SELECT id FROM permohonan_uang
WHERE status='approve_ppk'
"));

$total_ditolak = mysqli_num_rows(mysqli_query($conn,"
SELECT id FROM permohonan_uang
WHERE status='ditolak'
"));

$total_semua = mysqli_num_rows(mysqli_query($conn,"
SELECT id FROM permohonan_uang
"));
?>
<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Dashboard PPK</title>
<link rel="shortcut icon" href="../assets/img/logo1.ico">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<script src="https://unpkg.com/lucide@latest"></script>

<style>

:root{
--primary:#0f172a;
--accent:#d4af37;
--bg:#f1f5f9;
--white:#fff;
--gray:#64748b;
--danger:#ef4444;
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
}

/* SIDEBAR */

.sidebar{
width:260px;
background:var(--primary);
padding:25px;
color:white;
}

.sidebar h2{
color:var(--accent);
margin-bottom:35px;
}

.sidebar a{
display:flex;
align-items:center;
gap:12px;
padding:13px 15px;
border-radius:14px;
color:white;
text-decoration:none;
margin-bottom:12px;
transition:.3s;
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
font-size:28px;
color:var(--primary);
}

.topbar p{
color:var(--gray);
margin-top:5px;
}

.user-box{
display:flex;
align-items:center;
gap:15px;
}

.badge{
background:var(--accent);
padding:8px 15px;
border-radius:30px;
font-size:13px;
font-weight:600;
}

.logout{
background:var(--danger);
padding:10px 18px;
border-radius:30px;
color:white;
text-decoration:none;
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

.card-stat{
background:white;
padding:25px;
border-radius:22px;
display:flex;
justify-content:space-between;
align-items:center;
box-shadow:0 10px 25px rgba(0,0,0,.08);
}

.card-stat h3{
font-size:32px;
color:var(--primary);
margin-top:8px;
}

.card-stat p{
color:var(--gray);
}

.icon{
width:60px;
height:60px;
border-radius:18px;
background:rgba(212,175,55,.15);
display:flex;
align-items:center;
justify-content:center;
color:var(--accent);
}

/* MENU */

.grid{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
gap:25px;
}

.menu-card{
background:white;
padding:30px;
border-radius:22px;
text-align:center;
box-shadow:0 10px 25px rgba(0,0,0,.08);
}

.menu-icon{
width:75px;
height:75px;
background:var(--primary);
color:var(--accent);
border-radius:22px;
display:flex;
align-items:center;
justify-content:center;
margin:auto;
margin-bottom:18px;
}

.menu-card h4{
margin-bottom:10px;
font-size:20px;
}

.menu-card p{
font-size:14px;
color:var(--gray);
line-height:1.7;
margin-bottom:20px;
}

.btn{
display:inline-flex;
align-items:center;
gap:8px;
padding:12px 22px;
border-radius:30px;
background:linear-gradient(135deg,#d4af37,#b8962e);
text-decoration:none;
font-weight:600;
color:#0f172a;
}

@media(max-width:900px){

body{
flex-direction:column;
}

.sidebar{
width:100%;
}

}

</style>

</head>

<body>

<div class="sidebar">

<h2>SIBONPASBI</h2>

<a href="dashboard.php" class="active">
<i data-lucide="layout-dashboard"></i>
Dashboard
</a>

<a href="approval_pengajuan.php">
<i data-lucide="clipboard-check"></i>
Approval Pengajuan
</a>

</div>

<div class="main">

<div class="topbar">

<div>
<h2>Dashboard PPK</h2>
<p>Approval awal pengajuan bon uang</p>
</div>

<div class="user-box">

<div class="badge">
PPK
</div>

<div>
Halo, <b><?= $_SESSION['nama']; ?></b>
</div>

<a href="../auth/logout.php" class="logout">
<i data-lucide="log-out"></i>
Logout
</a>

</div>

</div>

<div class="stats">

<div class="card-stat">
<div>
<p>Total Pengajuan</p>
<h3><?= $total_semua; ?></h3>
</div>
<div class="icon">
<i data-lucide="files"></i>
</div>
</div>

<div class="card-stat">
<div>
<p>Menunggu Approval</p>
<h3><?= $total_pending; ?></h3>
</div>
<div class="icon">
<i data-lucide="clock-3"></i>
</div>
</div>

<div class="card-stat">
<div>
<p>Sudah ACC</p>
<h3><?= $total_disetujui; ?></h3>
</div>
<div class="icon">
<i data-lucide="check-circle"></i>
</div>
</div>

<div class="card-stat">
<div>
<p>Ditolak</p>
<h3><?= $total_ditolak; ?></h3>
</div>
<div class="icon">
<i data-lucide="x-circle"></i>
</div>
</div>

</div>

<div class="grid">

<div class="menu-card">

<div class="menu-icon">
<i data-lucide="clipboard-check"></i>
</div>

<h4>Approval Pengajuan</h4>

<p>
Melakukan verifikasi dan approval
pengajuan uang dari pegawai.
</p>

<a href="approval_pengajuan.php" class="btn">
<i data-lucide="arrow-right"></i>
Buka Approval
</a>

</div>

</div>

</div>

<script>
lucide.createIcons();
</script>

</body>
</html>