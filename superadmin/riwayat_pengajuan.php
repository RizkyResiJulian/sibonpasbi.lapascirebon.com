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

// =======================
// AMBIL DATA USER LOGIN
// =======================

$user_id = $_SESSION['user_id'];

$stmtUser = mysqli_prepare($conn,"
SELECT nama
FROM users
WHERE id=?
");

mysqli_stmt_bind_param($stmtUser,"i",$user_id);
mysqli_stmt_execute($stmtUser);

$resultUser = mysqli_stmt_get_result($stmtUser);
$user = mysqli_fetch_assoc($resultUser);

// =======================
// QUERY DATA PENGAJUAN
// =======================

$query = mysqli_query($conn,"
SELECT pu.*, u.nama, u.nip, u.bidang
FROM permohonan_uang pu
LEFT JOIN users u ON pu.user_id = u.id
ORDER BY pu.id DESC
");

// =======================
// TOTAL STATISTIK
// =======================

$total_pengajuan = mysqli_num_rows($query);

mysqli_data_seek($query,0);

$total_pending = mysqli_num_rows(mysqli_query($conn,"
SELECT id
FROM permohonan_uang
WHERE status='pending'
"));

$total_ppk = mysqli_num_rows(mysqli_query($conn,"
SELECT id
FROM permohonan_uang
WHERE status='approve_ppk'
"));

$total_kalapas = mysqli_num_rows(mysqli_query($conn,"
SELECT id
FROM permohonan_uang
WHERE status='approve_kalapas'
"));

$total_dicairkan = mysqli_num_rows(mysqli_query($conn,"
SELECT id
FROM permohonan_uang
WHERE status='dicairkan'
"));

$total_lpj = mysqli_num_rows(mysqli_query($conn,"
SELECT id
FROM permohonan_uang
WHERE status='lpj'
"));

$total_revisi = mysqli_num_rows(mysqli_query($conn,"
SELECT id
FROM permohonan_uang
WHERE status='revisi'
"));

$total_selesai = mysqli_num_rows(mysqli_query($conn,"
SELECT id
FROM permohonan_uang
WHERE status='selesai'
"));

$total_ditolak = mysqli_num_rows(mysqli_query($conn,"
SELECT id
FROM permohonan_uang
WHERE status='ditolak'
"));

?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Riwayat Pengajuan Superadmin</title>
<link rel="shortcut icon" href="../assets/img/logo1.ico">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<script src="https://unpkg.com/lucide@latest"></script>

<style>

:root{
--primary:#0f172a;
--primary-light:#1e293b;
--accent:#d4af37;
--accent2:#facc15;
--bg:#f1f5f9;
--card:#ffffff;
--text:#0f172a;
--gray:#64748b;
--border:#e2e8f0;
--danger:#ef4444;
--success:#22c55e;
--warning:#f59e0b;
--shadow:0 10px 25px rgba(0,0,0,.08);
--shadow-lg:0 18px 40px rgba(0,0,0,.12);
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
font-weight:700;
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
font-size:30px;
color:var(--primary);
margin-bottom:5px;
}

.topbar p{
color:var(--gray);
font-size:14px;
}

.user-info{
display:flex;
align-items:center;
gap:15px;
flex-wrap:wrap;
}

.badge{
background:linear-gradient(135deg,#d4af37,#facc15);
color:#0f172a;
padding:8px 16px;
border-radius:30px;
font-size:13px;
font-weight:700;
}

.logout{
background:var(--danger);
color:white;
padding:11px 18px;
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
transform:translateY(-3px);
}

/* USER BOX */

.user-box{
background:linear-gradient(135deg,#0f172a,#1e293b);
padding:28px;
border-radius:26px;
color:white;
margin-bottom:30px;
display:flex;
justify-content:space-between;
align-items:center;
flex-wrap:wrap;
gap:20px;
box-shadow:var(--shadow-lg);
}

.user-left h3{
font-size:24px;
margin-bottom:8px;
}

.user-left p{
opacity:.9;
font-size:14px;
margin-bottom:5px;
}

.user-right{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(150px,1fr));
gap:15px;
width:100%;
}

/* MINI STAT */

.mini-stat{
background:rgba(255,255,255,.08);
padding:16px 18px;
border-radius:18px;
backdrop-filter:blur(6px);
border:1px solid rgba(255,255,255,.08);
}

.mini-stat p{
font-size:13px;
opacity:.8;
margin-bottom:8px;
}

.mini-stat h3{
font-size:28px;
color:var(--accent2);
}

/* HISTORY CARD */

.history-card{
background:var(--card);
border-radius:28px;
padding:28px;
margin-bottom:28px;
box-shadow:var(--shadow);
transition:.3s;
border:1px solid transparent;
}

.history-card:hover{
transform:translateY(-5px);
border-color:#e2e8f0;
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
font-weight:600;
line-height:1.6;
}

/* TABLE */

.table-wrapper{
overflow-x:auto;
border-radius:18px;
border:1px solid var(--border);
}

.table{
width:100%;
border-collapse:collapse;
min-width:700px;
}

.table th{
background:#f8fafc;
padding:16px;
text-align:left;
font-size:13px;
color:var(--primary);
font-weight:700;
}

.table td{
padding:16px;
border-top:1px solid var(--border);
font-size:14px;
color:#334155;
}

.table tr:hover{
background:#fafafa;
}

/* TOTAL */

.total-box{
margin-top:22px;
display:flex;
justify-content:space-between;
align-items:center;
gap:20px;
flex-wrap:wrap;
}

.total{
background:linear-gradient(135deg,#0f172a,#1e293b);
color:white;
padding:20px 24px;
border-radius:20px;
min-width:280px;
box-shadow:var(--shadow);
}

.total p{
font-size:14px;
opacity:.8;
margin-bottom:8px;
}

.total h2{
font-size:30px;
color:var(--accent2);
}

/* DELETE */

.btn-delete{
background:#ef4444;
color:white;
text-decoration:none;
padding:13px 20px;
border-radius:16px;
font-weight:600;
display:flex;
align-items:center;
gap:8px;
transition:.3s;
}

.btn-delete:hover{
transform:translateY(-3px);
background:#dc2626;
}

/* EMPTY */

.empty{
background:white;
padding:60px 30px;
border-radius:28px;
text-align:center;
box-shadow:var(--shadow);
}

.empty i{
width:80px;
height:80px;
margin-bottom:20px;
color:#94a3b8;
}

.empty h3{
font-size:24px;
margin-bottom:10px;
color:var(--primary);
}

.empty p{
color:var(--gray);
line-height:1.7;
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

.user-box{
flex-direction:column;
align-items:flex-start;
}

.user-right{
width:100%;
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

<a href="users.php">
<i data-lucide="users"></i>
Manajemen Users
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
<p>Daftar seluruh pengajuan bon uang pegawai</p>
</div>

<div class="user-info">

<div class="badge">
SUPERADMIN
</div>

<div>
Halo, <b><?= htmlspecialchars($user['nama']); ?></b> 👋
</div>

<a href="../auth/logout.php" class="logout">
<i data-lucide="log-out"></i>
Logout
</a>

</div>

</div>

<!-- SUMMARY -->

<div class="user-box">

<div class="user-left">

<h3>Statistik Pengajuan</h3>

<p>Monitoring seluruh pengajuan bon uang sistem</p>

</div>

<div class="user-right">

<div class="mini-stat">
<p>Total</p>
<h3><?= $total_pengajuan; ?></h3>
</div>

<div class="mini-stat">
<p>Pending</p>
<h3><?= $total_pending; ?></h3>
</div>

<div class="mini-stat">
<p>Approve PPK</p>
<h3><?= $total_ppk; ?></h3>
</div>

<div class="mini-stat">
<p>Approve Kalapas</p>
<h3><?= $total_kalapas; ?></h3>
</div>

<div class="mini-stat">
<p>Dicairkan</p>
<h3><?= $total_dicairkan; ?></h3>
</div>

<div class="mini-stat">
<p>LPJ</p>
<h3><?= $total_lpj; ?></h3>
</div>

<div class="mini-stat">
<p>Revisi</p>
<h3><?= $total_revisi; ?></h3>
</div>

<div class="mini-stat">
<p>Selesai</p>
<h3><?= $total_selesai; ?></h3>
</div>

<div class="mini-stat">
<p>Ditolak</p>
<h3><?= $total_ditolak; ?></h3>
</div>

</div>

</div>

<?php if(mysqli_num_rows($query) > 0){ ?>

<?php while($data = mysqli_fetch_assoc($query)){ ?>

<?php

$detail = mysqli_query($conn,"
SELECT *
FROM detail_pengajuan_uang
WHERE pengajuan_id='".$data['id']."'
");

$statusClass = "pending";
$statusText  = "Pending";

switch($data['status']){

    case 'pending':
        $statusClass = "pending";
        $statusText  = "Menunggu Approval";
    break;

    case 'approve_ppk':
    case 'approve_kalapas':
    case 'dicairkan':
    case 'lpj':
    case 'selesai':
        $statusClass = "approve";
        $statusText  = strtoupper($data['status']);
    break;

    case 'ditolak':
    case 'revisi':
        $statusClass = "reject";
        $statusText  = strtoupper($data['status']);
    break;
}

?>

<div class="history-card">

<div class="history-header">

<div>

<h3>
<?= htmlspecialchars($data['nomor_pengajuan']); ?>
</h3>

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
<span>Nama Pegawai</span>
<h4><?= htmlspecialchars($data['nama']); ?></h4>
</div>

<div class="info-box">
<span>NIP</span>
<h4><?= htmlspecialchars($data['nip']); ?></h4>
</div>

<div class="info-box">
<span>Bidang</span>
<h4><?= htmlspecialchars($data['bidang']); ?></h4>
</div>

<div class="info-box">
<span>Keperluan</span>
<h4><?= nl2br(htmlspecialchars($data['keperluan'])); ?></h4>
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

<div class="total-box">

<div class="total">

<p>Total Pengajuan</p>

<h2>
Rp <?= number_format($data['total'],0,',','.'); ?>
</h2>

</div>

<a 
href="hapus_pengajuan.php?id=<?= $data['id']; ?>"
class="btn-delete"
onclick="return confirm('Yakin ingin menghapus pengajuan ini?')">

<i data-lucide="trash-2"></i>
Hapus Pengajuan

</a>

</div>

</div>

<?php } ?>

<?php } else { ?>

<div class="empty">

<i data-lucide="file-x"></i>

<h3>Belum Ada Pengajuan</h3>

<p>
Belum ada data pengajuan bon uang pada sistem.
</p>

</div>

<?php } ?>

</div>

<script>
lucide.createIcons();
</script>

</body>
</html>