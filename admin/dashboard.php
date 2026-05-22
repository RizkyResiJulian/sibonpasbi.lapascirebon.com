<?php 
session_start();

if(!isset($_SESSION['login'])){
    header("Location: ../auth/login.php");
    exit;
}

if($_SESSION['role']!="admin"){
    echo "Akses ditolak";
    exit;
}

include "../config/koneksi.php";

/* HITUNG DATA */

$barang = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM barang"));
$kategori = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM kategori"));
$permohonan = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM permohonan"));
$saran = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM saran"));

/* TOTAL PERMOHONAN BULAN INI */

$query_total = mysqli_query($conn,"
SELECT COUNT(*) as total
FROM permohonan
WHERE 
MONTH(tanggal_pesan)=MONTH(CURRENT_DATE())
AND YEAR(tanggal_pesan)=YEAR(CURRENT_DATE())
");

$data_total = mysqli_fetch_assoc($query_total);
$total_bulan = $data_total['total'] ?? 0;

/* TOTAL PERMOHONAN SELESAI */

$query_selesai = mysqli_query($conn,"
SELECT COUNT(*) as total
FROM permohonan
WHERE status='selesai'
");

$data_selesai = mysqli_fetch_assoc($query_selesai);
$total_selesai = $data_selesai['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Dashboard Operator</title>
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
width:240px;
background:var(--primary);
color:white;
padding:25px;
display:flex;
flex-direction:column;
}

.sidebar h2{
margin-bottom:30px;
font-size:20px;
}

.sidebar a{
color:white;
text-decoration:none;
margin-bottom:12px;
display:flex;
align-items:center;
gap:10px;
padding:10px;
border-radius:8px;
transition:.3s;
}

.sidebar a:hover{
background:rgba(255,255,255,0.1);
}

/* MAIN */

.main{
flex:1;
padding:30px;
}

/* HEADER */

.topbar{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:30px;
}

.logout{
background:#ef4444;
color:white;
padding:8px 16px;
border-radius:20px;
text-decoration:none;
}

/* STATS */

.stats{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
gap:20px;
margin-bottom:30px;
}

.stat-card{
background:white;
padding:25px;
border-radius:15px;
box-shadow:0 10px 20px rgba(0,0,0,0.08);
display:flex;
justify-content:space-between;
align-items:center;
}

.stat-card h3{
font-size:26px;
color:var(--primary);
}

.stat-highlight{
border-left:5px solid var(--accent);
}

/* MENU */

.grid{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
gap:20px;
}

.card{
background:white;
padding:30px;
border-radius:15px;
text-align:center;
box-shadow:0 10px 20px rgba(0,0,0,0.08);
transition:.3s;
}

.card:hover{
transform:translateY(-5px);
}

.icon{
width:60px;
height:60px;
margin:auto;
background:var(--primary);
color:var(--accent);
display:flex;
align-items:center;
justify-content:center;
border-radius:50%;
margin-bottom:15px;
}

.btn{
display:inline-block;
margin-top:10px;
padding:8px 18px;
background:var(--accent);
color:#0f172a;
text-decoration:none;
border-radius:20px;
font-weight:600;
}

</style>

</head>

<body>

<!-- SIDEBAR -->

<div class="sidebar">

<h2>SIBONPASBI</h2>

<a href="#">
<i data-lucide="home"></i>
Dashboard
</a>

<a href="barang.php">
<i data-lucide="package"></i>
Barang
</a>

<a href="kategori.php">
<i data-lucide="tag"></i>
Kategori
</a>

<a href="permohonan.php">
<i data-lucide="clipboard-list"></i>
Permohonan
</a>

<a href="saran.php">
<i data-lucide="message-circle"></i>
Kritik & Saran
</a>

<a href="laporan.php">
<i data-lucide="file-text"></i>
Laporan
</a>

</div>

<!-- MAIN -->

<div class="main">

<div class="topbar">

<h2>Dashboard</h2>

<div>
Halo, <?php echo $_SESSION['nama']; ?> 👋
<a href="../auth/logout.php" class="logout">Logout</a>
</div>

</div>

<!-- STATISTIK -->

<div class="stats">

<div class="stat-card stat-highlight">
<div>
<p>Permohonan Bulan Ini</p>
<h3><?php echo $total_bulan; ?></h3>
</div>
<i data-lucide="calendar"></i>
</div>

<div class="stat-card">
<div>
<p>Barang</p>
<h3><?php echo $barang ?></h3>
</div>
<i data-lucide="package"></i>
</div>

<div class="stat-card">
<div>
<p>Kategori</p>
<h3><?php echo $kategori ?></h3>
</div>
<i data-lucide="tag"></i>
</div>

<div class="stat-card">
<div>
<p>Total Permohonan</p>
<h3><?php echo $permohonan ?></h3>
</div>
<i data-lucide="shopping-cart"></i>
</div>

<div class="stat-card">
<div>
<p>Selesai</p>
<h3><?php echo $total_selesai; ?></h3>
</div>
<i data-lucide="check-circle"></i>
</div>

<div class="stat-card">
<div>
<p>Kritik & Saran</p>
<h3><?php echo $saran; ?></h3>
</div>
<i data-lucide="message-square"></i>
</div>

</div>

<!-- MENU -->

<div class="grid">

<div class="card">
<div class="icon">
<i data-lucide="package"></i>
</div>
<h4>Manajemen Barang</h4>
<a href="barang.php" class="btn">Buka</a>
</div>

<div class="card">
<div class="icon">
<i data-lucide="tag"></i>
</div>
<h4>Kategori</h4>
<a href="kategori.php" class="btn">Buka</a>
</div>

<div class="card">
<div class="icon">
<i data-lucide="shopping-cart"></i>
</div>
<h4>Permohonan</h4>
<a href="permohonan.php" class="btn">Buka</a>
</div>

<div class="card">
<div class="icon">
<i data-lucide="message-circle"></i>
</div>
<h4>Kritik & Saran</h4>
<a href="saran.php" class="btn">Buka</a>
</div>

</div>

</div>

<script>
lucide.createIcons();
</script>

</body>
</html>