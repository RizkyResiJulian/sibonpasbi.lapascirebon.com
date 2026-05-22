<?php
session_start();

if(!isset($_SESSION['login'])){
    header("Location: ../auth/login.php");
    exit;
}

if($_SESSION['role'] != "bendahara"){
    echo "Akses ditolak";
    exit;
}

include "../config/koneksi.php";

/* =========================
   STATISTIK
========================= */

$total_semua = mysqli_num_rows(mysqli_query($conn,"
SELECT id FROM permohonan_uang
"));

$total_pending = mysqli_num_rows(mysqli_query($conn,"
SELECT id FROM permohonan_uang
WHERE status='approve_kalapas'
"));

$total_dicairkan = mysqli_num_rows(mysqli_query($conn,"
SELECT id 
FROM permohonan_uang
WHERE status IN ('dicairkan','selesai','revisi','lpj')
"));

$total_lpj = mysqli_num_rows(mysqli_query($conn,"
SELECT id FROM permohonan_uang
WHERE status='lpj'
"));

$total_uang_dicairkan = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT SUM(total) as total_uang
FROM permohonan_uang
WHERE status IN ('dicairkan','selesai')
"))['total_uang'];

$total_uang_dicairkan = $total_uang_dicairkan ?: 0;

/* =========================
   DATA PENGAJUAN
========================= */

$query = mysqli_query($conn,"
SELECT pu.*, u.nama, u.bidang
FROM permohonan_uang pu
JOIN users u ON pu.user_id=u.id
ORDER BY pu.id DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Riwayat Pengajuan Bendahara</title>
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
--success:#22c55e;
--warning:#f59e0b;
--border:#e2e8f0;
}

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:Poppins,sans-serif;
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
flex-wrap:wrap;
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

/* HISTORY CARD */

.history-card{
background:white;
padding:28px;
border-radius:24px;
margin-bottom:25px;
box-shadow:0 10px 25px rgba(0,0,0,.08);
}

.history-header{
display:flex;
justify-content:space-between;
align-items:flex-start;
gap:20px;
flex-wrap:wrap;
margin-bottom:25px;
padding-bottom:20px;
border-bottom:1px solid var(--border);
}

.history-header h3{
font-size:22px;
color:var(--primary);
margin-bottom:8px;
}

.history-header p{
font-size:14px;
color:var(--gray);
margin-bottom:4px;
}

/* STATUS */

.status{
padding:10px 16px;
border-radius:999px;
font-size:13px;
font-weight:700;
display:inline-flex;
align-items:center;
gap:8px;
}

.pending{
background:#fef3c7;
color:#92400e;
}

.approve{
background:#dcfce7;
color:#166534;
}

.reject{
background:#fee2e2;
color:#991b1b;
}

/* INFO GRID */

.info-grid{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
gap:18px;
margin-bottom:25px;
}

.info-box{
background:#f8fafc;
padding:18px;
border-radius:18px;
border:1px solid #e2e8f0;
}

.info-box span{
font-size:12px;
color:var(--gray);
display:block;
margin-bottom:8px;
}

.info-box h4{
font-size:15px;
color:var(--primary);
line-height:1.6;
}

/* TABLE */

.table-wrapper{
overflow-x:auto;
border-radius:18px;
border:1px solid var(--border);
margin-top:20px;
}

.table{
width:100%;
border-collapse:collapse;
min-width:700px;
}

.table th{
background:#f8fafc;
padding:15px;
text-align:left;
font-size:13px;
color:var(--primary);
}

.table td{
padding:15px;
border-top:1px solid var(--border);
font-size:14px;
}

/* TOTAL */

.total-box{
margin-top:22px;
display:flex;
justify-content:flex-end;
}

.total{
background:linear-gradient(135deg,#0f172a,#1e293b);
color:white;
padding:20px 24px;
border-radius:20px;
min-width:260px;
}

.total p{
font-size:14px;
opacity:.8;
margin-bottom:8px;
}

.total h2{
font-size:28px;
color:#facc15;
}

/* LPJ */

.lpj-box{
margin-top:20px;
background:#dcfce7;
border:1px solid #bbf7d0;
padding:20px;
border-radius:20px;
display:flex;
justify-content:space-between;
align-items:center;
gap:20px;
flex-wrap:wrap;
}

.btn-lpj{
padding:12px 18px;
border-radius:14px;
background:#166534;
color:white;
text-decoration:none;
display:flex;
align-items:center;
gap:8px;
font-size:14px;
font-weight:600;
}

/* EMPTY */

.empty{
background:white;
padding:60px 30px;
border-radius:24px;
text-align:center;
box-shadow:0 10px 25px rgba(0,0,0,.08);
}

.empty h3{
margin-top:15px;
margin-bottom:10px;
}

.empty p{
color:var(--gray);
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

<a href="dashboard.php">
<i data-lucide="layout-dashboard"></i>
Dashboard
</a>

<a href="approval_pengajuan.php">
<i data-lucide="clipboard-check"></i>
Approval Pengajuan
</a>

<a href="riwayat_pengajuan.php" class="active">
<i data-lucide="history"></i>
Riwayat Pengajuan
</a>

</div>

<!-- MAIN -->

<div class="main">

<!-- TOPBAR -->

<div class="topbar">

<div>
<h2>Riwayat Pengajuan</h2>
<p>Monitoring seluruh proses pengajuan dan LPJ pegawai</p>
</div>

<div class="user-box">

<div class="badge">
Bendahara
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

<!-- STATS -->

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
<p>Menunggu Dicairkan</p>
<h3><?= $total_pending; ?></h3>
</div>
<div class="icon">
<i data-lucide="clock-3"></i>
</div>
</div>

<div class="card-stat">
<div>
<p>Sudah Dicairkan</p>
<h3><?= $total_dicairkan; ?></h3>
</div>
<div class="icon">
<i data-lucide="wallet"></i>
</div>
</div>

<div class="card-stat">
<div>
<p>LPJ Masuk</p>
<h3><?= $total_lpj; ?></h3>
</div>
<div class="icon">
<i data-lucide="file-check"></i>
</div>
</div>

<div class="card-stat">
    <div>
        <p>Total Uang Dicairkan</p>
        <h3>Rp <?= number_format($total_uang_dicairkan,0,',','.'); ?></h3>
    </div>
    <div class="icon">
        <i data-lucide="banknote"></i>
    </div>
</div>

</div>

<!-- DATA -->

<?php if(mysqli_num_rows($query) > 0){ ?>

<?php while($data = mysqli_fetch_assoc($query)){ ?>

<?php

$statusClass = "pending";
$statusText = "Pending";

switch($data['status']){

    case 'approve_kalapas':
        $statusClass = "pending";
        $statusText = "Menunggu Dicairkan";
    break;

    case 'dicairkan':
        $statusClass = "approve";
        $statusText = "Dana Dicairkan";
    break;

    case 'lpj':
        $statusClass = "approve";
        $statusText = "LPJ Sudah Upload";
    break;

    case 'selesai':
        $statusClass = "approve";
        $statusText = "Selesai";
    break;

    case 'ditolak':
        $statusClass = "reject";
        $statusText = "Ditolak";
    break;
}

$detail = mysqli_query($conn,"
SELECT *
FROM detail_pengajuan_uang
WHERE pengajuan_id='$data[id]'
");

?>

<div class="history-card">

<div class="history-header">

<div>

<h3><?= $data['nomor_pengajuan']; ?></h3>

<p><?= $data['nama']; ?></p>

<p><?= $data['bidang']; ?></p>

<p>
<?= date('d M Y',strtotime($data['tanggal'])); ?>
</p>

</div>

<div class="status <?= $statusClass; ?>">

<?php if($statusClass == "approve"){ ?>
<i data-lucide="check-circle"></i>
<?php } elseif($statusClass == "reject"){ ?>
<i data-lucide="x-circle"></i>
<?php } else { ?>
<i data-lucide="clock-3"></i>
<?php } ?>

<?= $statusText; ?>

</div>

</div>

<div class="info-grid">

<div class="info-box">
<span>Keperluan</span>

<h4>
<?= nl2br(htmlspecialchars($data['keperluan'])); ?>
</h4>
</div>

<div class="info-box">
<span>Status</span>

<h4><?= $statusText; ?></h4>
</div>

</div>

<div class="table-wrapper">

<table class="table">

<thead>
<tr>
<th>Uraian</th>
<th>Subtotal</th>
</tr>
</thead>

<tbody>

<?php while($d = mysqli_fetch_assoc($detail)){ ?>

<tr>

<td><?= htmlspecialchars($d['uraian']); ?></td>

<td>
Rp <?= number_format($d['subtotal'],0,',','.'); ?>
</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

<?php if(!empty($data['file_lpj'])){ ?>

<div class="lpj-box">

<div>

<h4>LPJ Sudah Diupload</h4>

<p>
Tanggal Upload :
<?= date('d M Y',strtotime($data['tanggal_lpj'])); ?>
</p>

</div>

<a 
href="../uploads/lpj/<?= $data['file_lpj']; ?>"
target="_blank"
class="btn-lpj">

<i data-lucide="file-text"></i>
Lihat LPJ

</a>

</div>

<?php } ?>

<div class="total-box">

<div class="total">

<p>Total Pengajuan</p>

<h2>
Rp <?= number_format($data['total'],0,',','.'); ?>
</h2>

</div>

</div>

</div>

<?php } ?>

<?php } else { ?>

<div class="empty">

<i data-lucide="file-x"></i>

<h3>Belum Ada Data</h3>

<p>
Belum ada riwayat pengajuan.
</p>

</div>

<?php } ?>

</div>

<script>
lucide.createIcons();
</script>

</body>
</html>