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

/* HITUNG DATA USERS */

$total_users = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM users"));

$total_admin = mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM users 
WHERE role='admin'
"));

$total_operator = mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM users 
WHERE role='operator'
"));

$total_superadmin = mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM users 
WHERE role='superadmin'
"));

$total_aktif = mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM users 
WHERE status='aktif'
"));

$total_nonaktif = mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM users 
WHERE status='nonaktif'
"));
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Dashboard Superadmin</title>
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
grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
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

/* MOBILE */

@media(max-width:900px){

body{
flex-direction:column;
}

.sidebar{
width:100%;
}

.main{
padding:20px;
}

}

</style>

</head>

<body>

<!-- SIDEBAR -->

<div class="sidebar">

<h2>SIBONPASBI</h2>

<a href="dashboard.php" class="active">
<i data-lucide="layout-dashboard"></i>
Dashboard
</a>

<a href="users.php">
<i data-lucide="users"></i>
Manajemen Users
</a>

<a href="riwayat_pengajuan.php">
<i data-lucide="history"></i>
Riwayat Pengajuan
</a>

</div>

<!-- MAIN -->

<div class="main">

<div class="topbar">

<div>
<h2>Dashboard Superadmin</h2>
<p>Kelola seluruh akun pengguna sistem</p>
</div>

<div class="user-info">

<div class="badge">
SUPERADMIN
</div>

<div>
Halo, <b><?= $_SESSION['nama']; ?></b> 👋
</div>

<a href="../auth/logout.php" class="logout">
Logout
</a>

</div>

</div>

<!-- STATISTIK -->

<div class="stats">

<?php
$stats = [
["Total Users",$total_users,"users"],
["Admin",$total_admin,"shield"],
["Operator",$total_operator,"settings"],
["Superadmin",$total_superadmin,"crown"],
["Aktif",$total_aktif,"check-circle"],
["Nonaktif",$total_nonaktif,"x-circle"]
];

foreach($stats as $s){
?>

<div class="stat-card">

<div>
<p><?= $s[0]; ?></p>
<h3><?= $s[1]; ?></h3>
</div>

<div class="stat-icon">
<i data-lucide="<?= $s[2]; ?>"></i>
</div>

</div>

<?php } ?>

</div>

<!-- MENU -->

<div class="grid">

<div class="card">

<div class="icon">
<i data-lucide="users"></i>
</div>

<h4>Data Users</h4>

<p>
Melihat seluruh data pengguna sistem
beserta role dan status akun.
</p>

<a href="users.php" class="btn">
<i data-lucide="eye"></i>
Lihat Data
</a>

</div>

<div class="card">

<div class="icon">
<i data-lucide="user-plus"></i>
</div>

<h4>Tambah User</h4>

<p>
Menambahkan akun baru untuk admin,
operator, bendahara, dan pegawai.
</p>

<a href="tambah_user.php" class="btn">
<i data-lucide="plus"></i>
Tambah
</a>

</div>

</div>

</div>

<script>
lucide.createIcons();
</script>

</body>
</html>