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

if(isset($_POST['simpan'])){
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);

    mysqli_query($conn,"INSERT INTO kategori(nama_kategori) VALUES('$nama')");
    header("Location: kategori.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Tambah Kategori</title>
<link rel="shortcut icon" href="../assets/img/logo1.ico">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<script src="https://unpkg.com/lucide@latest"></script>

<style>

:root{
--primary:#0f172a;
--primary-light:#1e293b;
--accent:#d4af37;
--bg:#f1f5f9;
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

/* CARD FORM */
.card{
background:white;
padding:35px;
border-radius:20px;
box-shadow:var(--shadow);
max-width:500px;
}

/* FORM */
.form-group{
margin-bottom:20px;
}

label{
display:block;
margin-bottom:8px;
font-weight:500;
color:#334155;
}

.input-wrapper{
position:relative;
}

.input-wrapper i{
position:absolute;
left:12px;
top:50%;
transform:translateY(-50%);
color:#94a3b8;
}

input{
width:100%;
padding:12px 12px 12px 38px;
border-radius:10px;
border:1px solid #e2e8f0;
font-size:14px;
transition:.2s;
}

input:focus{
border-color:var(--accent);
outline:none;
box-shadow:0 0 0 2px rgba(212,175,55,0.2);
}

/* BUTTON */
.btn{
padding:10px 20px;
border-radius:25px;
border:none;
font-weight:500;
cursor:pointer;
transition:.2s;
}

.btn-save{
background:var(--accent);
color:#0f172a;
}

.btn-save:hover{
opacity:0.9;
}

.btn-back{
background:#e2e8f0;
color:#334155;
text-decoration:none;
margin-left:10px;
}

.btn-back:hover{
background:#cbd5e1;
}

/* HEADER ICON */
.form-title{
display:flex;
align-items:center;
gap:10px;
margin-bottom:20px;
font-size:18px;
font-weight:600;
color:var(--primary);
}

.form-title i{
color:var(--accent);
}

</style>

</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">

<h2>SIBONPASBI</h2>

<a href="dashboard.php"><i data-lucide="home"></i> Dashboard</a>
<a href="barang.php"><i data-lucide="package"></i> Barang</a>
<a href="kategori.php" class="active"><i data-lucide="tag"></i> Kategori</a>
<a href="permohonan.php"><i data-lucide="clipboard-list"></i> Permohonan</a>
<a href="saran.php"><i data-lucide="message-circle"></i> Kritik & Saran</a>

</div>

<!-- MAIN -->
<div class="main">

<div class="topbar">
<h2>Tambah Kategori</h2>

<div>
Halo, <?= htmlspecialchars($_SESSION['nama']); ?>
<a href="../auth/logout.php" class="logout">Logout</a>
</div>
</div>

<div class="card">

<div class="form-title">
<i data-lucide="tag"></i>
Tambah Kategori Baru
</div>

<form method="POST">

<div class="form-group">
<label>Nama Kategori</label>

<div class="input-wrapper">
<i data-lucide="edit-3"></i>
<input type="text" name="nama" placeholder="Masukkan nama kategori..." required>
</div>

</div>

<button class="btn btn-save" name="simpan">
<i data-lucide="save"></i> Simpan
</button>

<a href="kategori.php" class="btn btn-back">
Kembali
</a>

</form>

</div>

</div>

<script>
lucide.createIcons();
</script>

</body>
</html>