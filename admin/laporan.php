<?php 
session_start();

if(!isset($_SESSION['login'])){
    header("Location: ../auth/login.php");
    exit;
}

if($_SESSION['role'] != "admin"){
    echo "Akses ditolak";
    exit;
}

include "../config/koneksi.php";

/* =========================
   FILTER BULAN & TAHUN
========================= */

$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');

/* =========================
   TOTAL LAPORAN
========================= */

$query_total = mysqli_query($conn,"
SELECT COUNT(*) as total
FROM permohonan
WHERE MONTH(tanggal_pesan)='$bulan'
AND YEAR(tanggal_pesan)='$tahun'
");

$data_total = mysqli_fetch_assoc($query_total);
$total_laporan = $data_total['total'] ?? 0;

/* =========================
   TOTAL PENDING
========================= */

$query_pending = mysqli_query($conn,"
SELECT COUNT(*) as total
FROM permohonan
WHERE status='pending'
AND MONTH(tanggal_pesan)='$bulan'
AND YEAR(tanggal_pesan)='$tahun'
");

$data_pending = mysqli_fetch_assoc($query_pending);
$total_pending = $data_pending['total'] ?? 0;

/* =========================
   TOTAL DIPROSES
========================= */

$query_diproses = mysqli_query($conn,"
SELECT COUNT(*) as total
FROM permohonan
WHERE status='diproses'
AND MONTH(tanggal_pesan)='$bulan'
AND YEAR(tanggal_pesan)='$tahun'
");

$data_diproses = mysqli_fetch_assoc($query_diproses);
$total_diproses = $data_diproses['total'] ?? 0;

/* =========================
   TOTAL SELESAI
========================= */

$query_selesai = mysqli_query($conn,"
SELECT COUNT(*) as total
FROM permohonan
WHERE status='selesai'
AND MONTH(tanggal_pesan)='$bulan'
AND YEAR(tanggal_pesan)='$tahun'
");

$data_selesai = mysqli_fetch_assoc($query_selesai);
$total_selesai = $data_selesai['total'] ?? 0;

/* =========================
   TOTAL BATAL
========================= */

$query_batal = mysqli_query($conn,"
SELECT COUNT(*) as total
FROM permohonan
WHERE status='batal'
AND MONTH(tanggal_pesan)='$bulan'
AND YEAR(tanggal_pesan)='$tahun'
");

$data_batal = mysqli_fetch_assoc($query_batal);
$total_batal = $data_batal['total'] ?? 0;

/* =========================
   QUERY DATA LAPORAN
========================= */

$query = mysqli_query($conn,"
SELECT *
FROM permohonan
WHERE MONTH(tanggal_pesan)='$bulan'
AND YEAR(tanggal_pesan)='$tahun'
ORDER BY tanggal_pesan DESC
");

if(!$query){
    die("Query Error : ".mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Laporan Permohonan</title>
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
flex-wrap:wrap;
gap:15px;
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

/* CARD */

.card{
background:white;
padding:25px;
border-radius:15px;
box-shadow:0 10px 20px rgba(0,0,0,0.08);
margin-bottom:25px;
}

/* FILTER */

.filter{
display:flex;
gap:15px;
align-items:end;
flex-wrap:wrap;
}

.filter-group{
display:flex;
flex-direction:column;
gap:5px;
}

.filter select,
.filter input{
padding:10px;
border:1px solid #ddd;
border-radius:8px;
min-width:120px;
}

button{
background:var(--accent);
color:#0f172a;
border:none;
padding:10px 18px;
border-radius:10px;
cursor:pointer;
font-weight:600;
}

button:hover{
opacity:0.9;
}

.export-btn{
background:linear-gradient(135deg,#16a34a,#15803d);
color:white;
text-decoration:none;
padding:11px 18px;
border-radius:12px;
font-weight:600;
display:inline-flex;
align-items:center;
gap:8px;
transition:0.25s;
box-shadow:0 6px 15px rgba(22,163,74,0.25);
white-space:nowrap;
}

.export-btn:hover{
transform:translateY(-2px);
box-shadow:0 10px 20px rgba(22,163,74,0.35);
}

.export-btn i{
width:18px;
height:18px;
}

/* TABLE */

.table-responsive{
overflow-x:auto;
}

table{
width:100%;
border-collapse:collapse;
min-width:900px;
}

th{
background:#f8fafc;
padding:14px;
text-align:left;
color:var(--primary);
}

td{
padding:14px;
border-bottom:1px solid #eee;
vertical-align:top;
}

tr:hover{
background:#f9fafb;
}

/* STATUS */

.badge{
padding:6px 12px;
border-radius:20px;
font-size:12px;
font-weight:600;
display:inline-block;
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

.batal{
background:#fee2e2;
color:#991b1b;
}

/* RESPONSIVE */

@media(max-width:768px){

body{
flex-direction:column;
}

.sidebar{
width:100%;
}

.main{
padding:20px;
}

.topbar{
flex-direction:column;
align-items:flex-start;
}

.filter{
flex-direction:column;
align-items:stretch;
}

.filter-group{
width:100%;
}

.filter select,
.filter input,
button{
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

<!-- TOPBAR -->

<div class="topbar">

<h2>Laporan Permohonan</h2>

<div>
Halo, <?= htmlspecialchars($_SESSION['nama']) ?> 👋
<a href="../auth/logout.php" class="logout">Logout</a>
</div>

</div>

<!-- STATISTIK -->

<div class="stats">

<div class="stat-card stat-highlight">
<div>
<p>Total Laporan</p>
<h3><?= $total_laporan ?></h3>
</div>
<i data-lucide="file-text"></i>
</div>

<div class="stat-card">
<div>
<p>Pending</p>
<h3><?= $total_pending ?></h3>
</div>
<i data-lucide="clock-3"></i>
</div>

<div class="stat-card">
<div>
<p>Diproses</p>
<h3><?= $total_diproses ?></h3>
</div>
<i data-lucide="loader"></i>
</div>

<div class="stat-card">
<div>
<p>Selesai</p>
<h3><?= $total_selesai ?></h3>
</div>
<i data-lucide="check-circle"></i>
</div>

<div class="stat-card">
<div>
<p>Batal</p>
<h3><?= $total_batal ?></h3>
</div>
<i data-lucide="x-circle"></i>
</div>

</div>

<!-- FILTER -->

<div class="card">

<form method="GET" class="filter">

<div class="filter-group">
<label>Bulan</label>

<select name="bulan">

<?php
for($i=1;$i<=12;$i++){

$selected = ($bulan == $i) ? "selected" : "";

echo "<option value='$i' $selected>$i</option>";
}
?>

</select>
</div>

<div class="filter-group">
<label>Tahun</label>

<input 
type="number"
name="tahun"
value="<?= htmlspecialchars($tahun) ?>">
</div>

<div class="filter-group">
<button type="submit">
Tampilkan
</button>
</div>

<div class="filter-group">
<label>&nbsp;</label>

<a 
href="../export_laporan.php?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>" 
class="export-btn"
target="_blank"
>
<i data-lucide="file-spreadsheet"></i>
Export Excel
</a>

</div>

</form>

</div>

<!-- TABEL -->

<div class="card">

<div class="table-responsive">

<table>

<tr>
<th>No</th>
<th>Tanggal</th>
<th>No Permohonan</th>
<th>Nama</th>
<th>NIP</th>
<th>Bidang</th>
<th>Detail Barang</th>
<th>Status</th>
</tr>

<?php
$no = 1;

while($d = mysqli_fetch_assoc($query)){
?>

<tr>

<td><?= $no++ ?></td>

<td>
<?= date('d M Y', strtotime($d['tanggal_pesan'])) ?>
</td>

<td>
<?= htmlspecialchars($d['nomor_permohonan']) ?>
</td>

<td>
<?= htmlspecialchars($d['nama']) ?>
</td>

<td>
<?= htmlspecialchars($d['nip']) ?>
</td>

<td>
<?= htmlspecialchars($d['bidang']) ?>
</td>

<td>

<?php

$detail = mysqli_query($conn,"
SELECT barang.nama_barang,
detail_permohonan.jumlah
FROM detail_permohonan
JOIN barang 
ON detail_permohonan.barang_id = barang.id
WHERE permohonan_id='".$d['id']."'
");

while($item = mysqli_fetch_assoc($detail)){

echo "• ".
htmlspecialchars($item['nama_barang']).
" x".
htmlspecialchars($item['jumlah']).
"<br>";
}
?>

</td>

<td>

<?php
$status = strtolower($d['status']);

if($status == "pending"){
echo "<span class='badge pending'>Pending</span>";
}
elseif($status == "diproses"){
echo "<span class='badge diproses'>Diproses</span>";
}
elseif($status == "selesai"){
echo "<span class='badge selesai'>Selesai</span>";
}
elseif($status == "batal"){
echo "<span class='badge batal'>Batal</span>";
}
else{
echo "<span class='badge'>$status</span>";
}
?>

</td>

</tr>

<?php } ?>

</table>

</div>

</div>

</div>

<script>
lucide.createIcons();
</script>

</body>
</html>