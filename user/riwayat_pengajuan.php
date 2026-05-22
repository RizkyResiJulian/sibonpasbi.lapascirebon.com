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

$user_id = $_SESSION['user_id'];

// =======================
// AMBIL DATA USER
// =======================

$stmtUser = mysqli_prepare($conn,"
    SELECT nama,nip,bidang
    FROM users
    WHERE id=?
");

mysqli_stmt_bind_param($stmtUser,"i",$user_id);
mysqli_stmt_execute($stmtUser);

$resultUser = mysqli_stmt_get_result($stmtUser);
$user = mysqli_fetch_assoc($resultUser);

// =======================
// AMBIL DATA PENGAJUAN
// =======================

$query = mysqli_query($conn,"
SELECT *
FROM permohonan_uang
WHERE user_id='$user_id'
ORDER BY id DESC
");

// =======================
// TOTAL STATISTIK
// =======================

$total_pengajuan = mysqli_num_rows($query);

mysqli_data_seek($query,0);

/* PENDING */

$total_pending = mysqli_num_rows(mysqli_query($conn,"
SELECT *
FROM permohonan_uang
WHERE user_id='$user_id'
AND status='pending'
"));

/* DIPROSES
   pending approval:
   - approve_ppk
   - approve_kalapas
*/

$total_diproses = mysqli_num_rows(mysqli_query($conn,"
SELECT *
FROM permohonan_uang
WHERE user_id='$user_id'
AND (
    status='approve_ppk'
    OR status='approve_kalapas'
    OR status='pending'
)
"));

/* SUDAH DICAIRKAN */

$total_dicairkan = mysqli_num_rows(mysqli_query($conn,"
SELECT *
FROM permohonan_uang
WHERE user_id='$user_id'
AND status='dicairkan'
"));

/* REVISI LPJ */

$total_revisi = mysqli_num_rows(mysqli_query($conn,"
SELECT *
FROM permohonan_uang
WHERE user_id='$user_id'
AND status='revisi'
"));

/* SELESAI */

$total_selesai = mysqli_num_rows(mysqli_query($conn,"
SELECT *
FROM permohonan_uang
WHERE user_id='$user_id'
AND status='selesai'
"));

/* DITOLAK */

$total_ditolak = mysqli_num_rows(mysqli_query($conn,"
SELECT *
FROM permohonan_uang
WHERE user_id='$user_id'
AND status='ditolak'
"));

?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Riwayat Pengajuan</title>
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
justify-content:flex-end;
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

/* APPROVAL TRACK */

.approval-track{
display:flex;
align-items:center;
justify-content:space-between;
gap:10px;
margin-bottom:28px;
overflow-x:auto;
padding-bottom:5px;
}

.step{
display:flex;
flex-direction:column;
align-items:center;
min-width:90px;
position:relative;
}

.circle{
width:55px;
height:55px;
border-radius:50%;
background:#e2e8f0;
display:flex;
align-items:center;
justify-content:center;
color:#64748b;
margin-bottom:10px;
transition:.3s;
}

.step.done .circle{
background:linear-gradient(135deg,#22c55e,#16a34a);
color:white;
box-shadow:0 10px 20px rgba(34,197,94,.25);
}

.step span{
font-size:12px;
font-weight:600;
text-align:center;
color:#475569;
}

.line{
height:4px;
flex:1;
background:#e2e8f0;
border-radius:999px;
min-width:40px;
}

.line.active{
background:linear-gradient(90deg,#22c55e,#16a34a);
}

/* ================= MOBILE ================= */

.menu-toggle{
display:flex;
position:fixed;
top:14px;
left:14px;
z-index:3001;

width:48px;
height:48px;

border:none;
border-radius:14px;

background:var(--primary);
color:white;

align-items:center;
justify-content:center;

cursor:pointer;

box-shadow:0 10px 25px rgba(0,0,0,.18);
}

/* kasih jarak khusus saat sidebar aktif */

.sidebar.active h2{
margin-top:50px;
}

.sidebar-overlay{
display:none;
}

@media(max-width:900px){

body{
flex-direction:column;
overflow-x:hidden;
}

/* TOGGLE BUTTON */

.menu-toggle{
display:flex;
position:fixed;
top:14px;
left:14px;
z-index:3001;

width:48px;
height:48px;

border:none;
border-radius:14px;

background:var(--primary);
color:white;

align-items:center;
justify-content:center;

cursor:pointer;

box-shadow:0 10px 25px rgba(0,0,0,.18);
}

/* OVERLAY */

.sidebar-overlay{
position:fixed;
inset:0;
background:rgba(0,0,0,.45);

z-index:2998;

opacity:0;
visibility:hidden;

transition:.3s;
}

.sidebar-overlay.active{
opacity:1;
visibility:visible;
}

/* SIDEBAR */

.sidebar{
position:fixed;

top:0;
left:-280px;

width:260px;
height:100vh;

background:var(--primary);

padding:20px 16px;

overflow-y:auto;

z-index:3000;

transition:.35s ease;
}

.sidebar.active{
left:0;
}

.sidebar h2{
font-size:20px;
margin-bottom:22px;
text-align:center;
line-height:1.2;
padding-top:0;
}

.sidebar a{
padding:13px 14px;
font-size:14px;
border-radius:14px;
margin-bottom:10px;
}

/* MAIN */

.main{
width:100%;
padding:82px 16px 20px;
}

/* TOPBAR */

.topbar{
flex-direction:column;
align-items:flex-start;
gap:18px;
margin-bottom:24px;
}

.topbar h2{
font-size:24px;
line-height:1.4;
}

.topbar p{
font-size:13px;
line-height:1.6;
}

/* USER INFO */

.user-info{
width:100%;
display:flex;
flex-wrap:wrap;
gap:10px;
align-items:flex-start;
}

.badge{
font-size:12px;
padding:7px 12px;
}

/* LOGOUT */

.logout{
position:fixed;
top:14px;
right:14px;

z-index:3001;

width:48px;
height:48px;

padding:0;

border-radius:14px;

justify-content:center;

font-size:0;

box-shadow:0 10px 25px rgba(0,0,0,.18);
}

.logout i{
width:20px;
height:20px;
margin:0;
}

/* USER BOX */

.user-box{
padding:20px;
border-radius:22px;
gap:18px;
margin-bottom:24px;
}

.user-left{
width:100%;
}

.user-left h3{
font-size:21px;
line-height:1.4;
word-break:break-word;
}

.user-left p{
font-size:13px;
line-height:1.7;
word-break:break-word;
}

/* MINI STAT */

.user-right{
grid-template-columns:repeat(2,1fr);
gap:12px;
width:100%;
}

.mini-stat{
padding:16px;
border-radius:16px;
}

.mini-stat p{
font-size:12px;
}

.mini-stat h3{
font-size:22px;
}

/* HISTORY CARD */

.history-card{
padding:20px;
border-radius:22px;
margin-bottom:22px;
}

.history-header{
gap:16px;
margin-bottom:20px;
padding-bottom:18px;
}

.history-header h3{
font-size:18px;
line-height:1.5;
word-break:break-word;
}

.history-header p{
font-size:13px;
display:flex;
align-items:center;
gap:8px;
flex-wrap:wrap;
}

.status{
font-size:12px;
padding:9px 14px;
}

/* APPROVAL */

.approval-track{
gap:8px;
padding-bottom:10px;
}

.step{
min-width:75px;
}

.circle{
width:46px;
height:46px;
}

.step span{
font-size:11px;
line-height:1.4;
}

.line{
min-width:25px;
}

/* INFO GRID */

.info-grid{
grid-template-columns:1fr;
gap:14px;
margin-bottom:20px;
}

.info-box{
padding:16px;
border-radius:16px;
}

.info-box h4{
font-size:14px;
line-height:1.7;
}

/* TABLE */

.table-wrapper{
border-radius:16px;
}

.table{
min-width:600px;
}

.table th{
padding:14px;
font-size:12px;
}

.table td{
padding:14px;
font-size:13px;
}

/* TOTAL */

.total-box{
margin-top:18px;
}

.total{
width:100%;
min-width:auto;
padding:18px;
border-radius:18px;
}

.total p{
font-size:13px;
}

.total h2{
font-size:24px;
line-height:1.4;
word-break:break-word;
}

/* LPJ */

.lpj-box{
padding:18px;
border-radius:18px;
margin-top:20px;
}

.lpj-upload{
flex-direction:column;
align-items:flex-start;
gap:18px;
}

.lpj-left h4{
font-size:17px;
line-height:1.5;
}

.lpj-left p{
font-size:13px;
line-height:1.7;
}

.lpj-right{
width:100%;
flex-direction:column;
align-items:stretch;
gap:12px;
}

.lpj-right input[type="file"]{
width:100%;
font-size:13px;
}

.btn-lpj,
.btn-view-lpj{
width:100%;
justify-content:center;
}

/* FEEDBACK */

.feedback-box{
padding:18px;
border-radius:18px;
}

.feedback-box p{
font-size:13px;
line-height:1.8;
}

/* ACTION */

.action-box{
margin-top:18px;
}

.btn-delete-pengajuan{
width:100%;
justify-content:center;
}

/* EMPTY */

.empty{
padding:50px 20px;
border-radius:22px;
}

.empty h3{
font-size:22px;
line-height:1.4;
}

.empty p{
font-size:14px;
line-height:1.8;
}

}

/* LPJ */

.lpj-box{
margin-top:25px;
background:#f8fafc;
border:1px solid #e2e8f0;
padding:22px;
border-radius:20px;
}

.lpj-upload{
display:flex;
justify-content:space-between;
align-items:center;
gap:20px;
flex-wrap:wrap;
}

.lpj-left h4{
font-size:18px;
color:var(--primary);
margin-bottom:6px;
}

.lpj-left p{
font-size:14px;
color:var(--gray);
line-height:1.6;
}

.lpj-right{
display:flex;
align-items:center;
gap:12px;
flex-wrap:wrap;
}

.btn-lpj{
border:none;
padding:12px 18px;
border-radius:14px;
background:linear-gradient(135deg,#d4af37,#facc15);
font-weight:700;
cursor:pointer;
display:flex;
align-items:center;
gap:8px;
transition:.3s;
}

.btn-lpj:hover{
transform:translateY(-3px);
}

.lpj-success{
margin-top:20px;
background:#dcfce7;
border:1px solid #bbf7d0;
padding:18px 20px;
border-radius:18px;
display:flex;
justify-content:space-between;
align-items:center;
gap:20px;
flex-wrap:wrap;
}

.lpj-success h4{
color:#166534;
margin-bottom:5px;
}

.lpj-success p{
font-size:14px;
color:#166534;
}

/* FEEDBACK */

.feedback-box{
margin-bottom:25px;
background:#fff7ed;
border:1px solid #fdba74;
padding:22px;
border-radius:20px;
}

.feedback-title{
display:flex;
align-items:center;
gap:10px;
margin-bottom:12px;
font-weight:700;
color:#9a3412;
}

.feedback-title i{
width:20px;
height:20px;
}

.feedback-box p{
color:#7c2d12;
line-height:1.8;
font-size:14px;
}

.btn-view-lpj{
padding:11px 18px;
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

.btn-delete-pengajuan{
background:#ef4444;
color:white;
border:none;
padding:12px 18px;
border-radius:14px;
cursor:pointer;
font-weight:600;
display:flex;
align-items:center;
gap:8px;
transition:.3s;
text-decoration:none;
}

.btn-delete-pengajuan:hover{
transform:translateY(-3px);
background:#dc2626;
}

.action-box{
margin-top:20px;
display:flex;
justify-content:flex-end;
}

</style>

</head>

<body>

<!-- MOBILE MENU -->

<button class="menu-toggle" onclick="toggleSidebar()">
    <i data-lucide="menu"></i>
</button>

<div class="sidebar-overlay" onclick="toggleSidebar()"></div>

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

<a href="riwayat_pengajuan.php" class="active">
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
<h2>Riwayat Pengajuan</h2>
<p>Daftar seluruh pengajuan bon uang pegawai</p>
</div>

<div class="user-info">

<div class="badge">
USER
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

<!-- USER SUMMARY -->

<div class="user-box">

<div class="user-left">

<h3><?= htmlspecialchars($user['nama']); ?></h3>

<p>NIP : <?= htmlspecialchars($user['nip']); ?></p>

<p>Bidang : <?= htmlspecialchars($user['bidang']); ?></p>

</div>

<div class="user-right">

<div class="mini-stat">
<p>Total</p>
<h3><?= $total_pengajuan; ?></h3>
</div>

<div class="mini-stat">
<p>Diproses</p>
<h3><?= $total_diproses; ?></h3>
</div>

<div class="mini-stat">
<p>Sudah Cair</p>
<h3><?= $total_dicairkan; ?></h3>
</div>

<div class="mini-stat">
<p>Revisi LPJ</p>
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

$pengajuan_id = $data['id'];

/* =========================
   AMBIL FEEDBACK TERAKHIR
========================= */

$feedback = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT *
FROM log_approval_uang
WHERE pengajuan_id='$pengajuan_id'
AND (
    aksi='Ditolak'
    OR aksi='LPJ Revisi'
)
ORDER BY id DESC
LIMIT 1
"));

$detail = mysqli_query($conn,"
SELECT *
FROM detail_pengajuan_uang
WHERE pengajuan_id='$pengajuan_id'
");

// =======================
// STATUS APPROVAL
// =======================

$statusClass = "pending";
$statusText  = "Menunggu Approval";

switch($data['status']){

    case 'pending':
        $statusClass = "pending";
        $statusText  = "Menunggu Approval PPK";
    break;

    case 'approve_ppk':
        $statusClass = "approve";
        $statusText  = "Disetujui PPK";
    break;

    case 'approve_kalapas':
        $statusClass = "approve";
        $statusText  = "Disetujui Kalapas";
    break;

    case 'dicairkan':
        $statusClass = "approve";
        $statusText  = "Dana Dicairkan";
    break;

    case 'ditolak':
        $statusClass = "reject";
        $statusText  = "Ditolak";
    break;

    case 'lpj':
        $statusClass = "approve";
        $statusText  = "LPJ Sedang Diverifikasi";
    break;
    
    case 'revisi':
        $statusClass = "pending";
        $statusText  = "Revisi LPJ";
    break;

    case 'selesai':
        $statusClass = "approve";
        $statusText  = "Selesai";
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
<i data-lucide="calendar-days"></i>
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

<!-- PROGRESS APPROVAL -->

<div class="approval-track">

<!-- PPK -->

<div class="step <?= in_array($data['status'],[
    'approve_ppk',
    'approve_kalapas',
    'dicairkan',
    'lpj',
    'selesai'
]) ? 'done' : ''; ?>">
    
    <div class="circle">
        <i data-lucide="badge-dollar-sign"></i>
    </div>

    <span>PPK</span>

</div>

<div class="line <?= in_array($data['status'],[
    'approve_kalapas',
    'dicairkan',
    'lpj',
    'selesai'
]) ? 'active' : ''; ?>"></div>

<!-- KALAPAS -->

<div class="step <?= in_array($data['status'],[
    'approve_kalapas',
    'dicairkan',
    'lpj',
    'selesai'
]) ? 'done' : ''; ?>">
    
    <div class="circle">
        <i data-lucide="shield-check"></i>
    </div>

    <span>Kalapas</span>

</div>

<div class="line <?= in_array($data['status'],[
    'dicairkan',
    'lpj',
    'selesai'
]) ? 'active' : ''; ?>"></div>

<!-- DICAIRKAN -->

<div class="step <?= in_array($data['status'],[
    'dicairkan',
    'lpj',
    'selesai'
]) ? 'done' : ''; ?>">
    
    <div class="circle">
        <i data-lucide="wallet"></i>
    </div>

    <span>Dicairkan</span>

</div>

<div class="line <?= in_array($data['status'],[
    'lpj',
    'selesai'
]) ? 'active' : ''; ?>"></div>

<!-- LPJ -->

<div class="step <?= in_array($data['status'],[
    'lpj',
    'selesai'
]) ? 'done' : ''; ?>">
    
    <div class="circle">
        <i data-lucide="file-check"></i>
    </div>

    <span>Upload LPJ</span>

</div>

<div class="line <?= $data['status'] == 'selesai' ? 'active' : ''; ?>"></div>

<!-- SELESAI -->

<div class="step <?= $data['status'] == 'selesai' ? 'done' : ''; ?>">
    
    <div class="circle">
        <i data-lucide="badge-check"></i>
    </div>

    <span>Selesai</span>

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
<span>Status Approval</span>

<h4>
<?php

$statusLabel = [
    'pending'          => 'Menunggu Approval PPK',
    'approve_ppk'      => 'Disetujui PPK',
    'approve_kalapas'  => 'Disetujui Kalapas',
    'dicairkan'        => 'Dana Dicairkan',
    'ditolak'          => 'Ditolak',
    'lpj'              => 'LPJ Sudah Diupload',
    'revisi'          => 'Revisi LPJ',
    'selesai'          => 'Pengajuan Selesai'
];

echo $statusLabel[$data['status']] ?? $data['status'];

?>
</h4>
</div>

</div>

<?php if(
    !empty($feedback['catatan'])
    &&
    (
        $data['status'] == 'ditolak'
        || $data['status'] == 'revisi'
    )
){ ?>

<div class="feedback-box">

<div class="feedback-title">
    
<i data-lucide="message-square-warning"></i>

<span>
Feedback <?= strtoupper($feedback['role']); ?>
</span>

</div>

<p>
<?= nl2br(htmlspecialchars($feedback['catatan'])); ?>
</p>

</div>

<?php } ?>

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

<?php if(
    $data['status'] == 'dicairkan' ||
    $data['status'] == 'lpj' ||
    $data['status'] == 'revisi'
){ ?>

<div class="lpj-box">

<form 
action="upload_lpj.php" 
method="POST" 
enctype="multipart/form-data">

<input 
type="hidden" 
name="id" 
value="<?= $data['id']; ?>">

<div class="lpj-upload">

<div class="lpj-left">
    
<h4>
<?= $data['status'] == 'revisi' 
    ? 'Upload Revisi LPJ'
    : 'Upload LPJ'; ?>
</h4>

<p>
<?php if($data['status'] == 'revisi'){ ?>

Silahkan upload ulang LPJ yang sudah direvisi.

<?php } else { ?>

Upload file LPJ format PDF, Word, Excel,
atau gambar pendukung.

<?php } ?>
</p>

</div>

<div class="lpj-right">

<?php if(!empty($data['file_lpj'])){ ?>

<a 
href="../uploads/lpj/<?= $data['file_lpj']; ?>" 
target="_blank"
class="btn-view-lpj">

<i data-lucide="file-text"></i>
Lihat LPJ Sebelumnya

</a>

<?php } ?>

<input 
type="file" 
name="lpj"
accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
required>

<button type="submit" class="btn-lpj">

<i data-lucide="upload"></i>

<?= $data['status'] == 'revisi' 
    ? 'Upload Revisi'
    : 'Upload LPJ'; ?>

</button>

</div>

</div>

</form>

</div>

<?php } ?>

<?php if(!empty($data['lpj_file'])){ ?>

<div class="lpj-success">

<div>

<h4>LPJ Sudah Diupload</h4>

<p>
File laporan pertanggungjawaban telah dikirim.
</p>

</div>

<a 
href="../uploads/lpj/<?= $data['lpj_file']; ?>" 
target="_blank"
class="btn-view-lpj">

<i data-lucide="file-text"></i>
Lihat LPJ

</a>

</div>

<?php } ?>

<?php if($data['status'] == 'pending'){ ?>

<div class="action-box">

<a 
href="hapus_pengajuan.php?id=<?= $data['id']; ?>"
class="btn-delete-pengajuan"
onclick="return confirm('Yakin ingin menghapus pengajuan ini?')">

<i data-lucide="trash-2"></i>
Hapus Pengajuan

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

<h3>Belum Ada Pengajuan</h3>

<p>
Anda belum pernah membuat pengajuan bon uang.
</p>

</div>

<?php } ?>

</div>

<script>
lucide.createIcons();
// SIDEBAR MOBILE

function toggleSidebar(){

document.querySelector('.sidebar')
.classList.toggle('active');

document.querySelector('.sidebar-overlay')
.classList.toggle('active');

}

// AUTO CLOSE MENU

document.querySelectorAll('.sidebar a')
.forEach(link=>{

link.addEventListener('click',()=>{

document.querySelector('.sidebar')
.classList.remove('active');

document.querySelector('.sidebar-overlay')
.classList.remove('active');

});

});
</script>

</body>
</html>