<?php
session_start();

if(!isset($_SESSION['login'])){
    header("Location: ../auth/login.php");
    exit;
}

if($_SESSION['role']!="operator"){
    echo "Akses ditolak";
    exit;
}

include "../config/koneksi.php";

// =======================
// AMBIL DATA
// =======================
$data = mysqli_query($conn,"SELECT * FROM saran ORDER BY tanggal DESC");

if(!$data){
    die("Query error: ".mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Data Kritik & Saran</title>
<link rel="shortcut icon" href="../assets/img/logo1.ico">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<script src="https://unpkg.com/lucide@latest"></script>

<style>

:root{
--primary:#0f172a;
--primary-light:#1e293b;
--accent:#d4af37;
--bg:#f1f5f9;
--text:#1e293b;
--white:#ffffff;
--shadow:0 10px 25px rgba(0,0,0,0.08);
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

.sidebar a.active{
background:rgba(255,255,255,0.15);
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
}

.logout{
background:#ef4444;
color:white;
padding:8px 16px;
border-radius:20px;
text-decoration:none;
}

/* CARD */
.card{
background:white;
padding:25px;
border-radius:15px;
box-shadow:var(--shadow);
}

/* HEADER TABLE */
.table-header{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:20px;
flex-wrap:wrap;
gap:10px;
}

/* SEARCH */
.search{
padding:10px 14px;
border-radius:10px;
border:1px solid #ddd;
min-width:250px;
}

/* BUTTON */
.btn{
padding:8px 18px;
border-radius:20px;
text-decoration:none;
font-size:13px;
font-weight:500;
}

.btn-delete{
background:#ef4444;
color:white;
}

/* TABLE */
.table-container{
max-height:420px;
overflow-y:auto;
border-radius:10px;
border:1px solid #eee;
}

table{
width:100%;
border-collapse:collapse;
}

th{
background:var(--primary);
color:white;
padding:12px;
position:sticky;
top:0;
z-index:2;
text-align:left;
}

td{
padding:12px;
border-bottom:1px solid #eee;
}

tr:hover{
background:#f8fafc;
}

/* BADGE */
.badge{
background:rgba(212,175,55,0.15);
color:#b8962e;
padding:4px 12px;
border-radius:20px;
font-size:12px;
}

</style>

<script>
function cariSaran(){
let input=document.getElementById("search").value.toLowerCase();
let rows=document.querySelectorAll("tbody tr");

rows.forEach(row=>{
let nama=row.children[1].innerText.toLowerCase();
row.style.display = nama.includes(input) ? "" : "none";
});
}
</script>

</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">

<h2>SIBONPASBI</h2>

<a href="dashboard.php"><i data-lucide="home"></i> Dashboard</a>
<a href="barang.php"><i data-lucide="package"></i> Barang</a>
<a href="kategori.php"><i data-lucide="tag"></i> Kategori</a>
<a href="permohonan.php"><i data-lucide="clipboard-list"></i> Permohonan</a>
<a href="saran.php" class="active"><i data-lucide="message-circle"></i> Kritik & Saran</a>

</div>

<!-- MAIN -->
<div class="main">

<div class="topbar">
<h2>Data Kritik & Saran</h2>

<div>
Halo, <?= htmlspecialchars($_SESSION['nama']); ?>
<a href="../auth/logout.php" class="logout">Logout</a>
</div>
</div>

<div class="card">

<div class="table-header">
<input type="text" id="search" class="search" placeholder="Cari nama..." onkeyup="cariSaran()">
</div>

<div class="table-container">

<table>
<thead>
<tr>
<th>No</th>
<th>Nama</th>
<th>NIP</th>
<th>Bidang</th>
<th>Saran</th>
<th>Tanggal</th>
<th>Aksi</th>
</tr>
</thead>

<tbody>

<?php $no=1; while($row=mysqli_fetch_assoc($data)){ ?>

<tr>

<td><?= $no++; ?></td>

<td><b><?= htmlspecialchars($row['nama']); ?></b></td>

<td><?= htmlspecialchars($row['nip']); ?></td>

<td>
<span class="badge">
<?= htmlspecialchars($row['bidang']); ?>
</span>
</td>

<td><?= htmlspecialchars($row['isi']); ?></td>

<td><?= htmlspecialchars($row['tanggal']); ?></td>

<td>
<a href="hapus_saran.php?id=<?= $row['id']; ?>" 
class="btn btn-delete"
onclick="return confirm('Hapus saran ini?')">
Hapus
</a>
</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>

<script>
lucide.createIcons();
</script>

</body>
</html>