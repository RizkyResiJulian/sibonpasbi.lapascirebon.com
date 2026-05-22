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

// AMBIL DATA USER
$stmt = mysqli_prepare($conn,"
    SELECT nama,nip,bidang
    FROM users
    WHERE id=?
");

mysqli_stmt_bind_param($stmt,"i",$user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Pengajuan Uang</title>
<link rel="shortcut icon" href="../assets/img/logo1.ico">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<script src="https://unpkg.com/lucide@latest"></script>

<style>

:root{
--primary:#0f172a;
--accent:#d4af37;
--bg:#f1f5f9;
--text:#1e293b;
--gray:#64748b;
--danger:#ef4444;
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

padding-left:15px;
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
font-size:28px;
color:var(--primary);
}

.topbar p{
color:var(--gray);
margin-top:5px;
}

.badge{
background:var(--accent);
padding:8px 14px;
border-radius:30px;
font-size:13px;
font-weight:600;
color:#0f172a;
}

.logout{
background:var(--danger);
color:white;
padding:10px 18px;
border-radius:30px;
text-decoration:none;
display:flex;
align-items:center;
gap:8px;
}

/* CARD */

.card{
background:white;
padding:30px;
border-radius:24px;
box-shadow:0 10px 25px rgba(0,0,0,.08);
}

.user-box{
background:linear-gradient(135deg,#0f172a,#1e293b);
padding:25px;
border-radius:24px;
color:white;
margin-bottom:25px;
}

.user-box h3{
margin-bottom:10px;
}

.user-box p{
opacity:.9;
margin-bottom:5px;
}

/* FORM */

.form-group{
margin-bottom:20px;
}

label{
display:block;
margin-bottom:8px;
font-weight:600;
color:var(--primary);
}

input,
textarea{
width:100%;
padding:14px;
border:1px solid #ddd;
border-radius:14px;
font-size:14px;
}

textarea{
resize:vertical;
min-height:100px;
}

/* DETAIL */

.detail-item{
background:#f8fafc;
padding:20px;
border-radius:18px;
margin-bottom:20px;
border:1px solid #e2e8f0;
}

.detail-top{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:15px;
}

.detail-top h4{
color:var(--primary);
font-size:15px;
}

.btn-delete{
border:none;
background:#fee2e2;
color:#dc2626;
width:34px;
height:34px;
border-radius:50%;
cursor:pointer;
display:flex;
align-items:center;
justify-content:center;
font-size:18px;
font-weight:700;
transition:.3s;
}

.btn-delete:hover{
background:#fecaca;
transform:scale(1.08);
}

.hidden-delete{
display:none;
}

.btn-delete:hover{
background:#fecaca;
transform:scale(1.05);
}

.row{
display:grid;
grid-template-columns:2fr 1fr 1fr 1fr;
gap:15px;
}

.total-box{
margin-top:20px;
background:#0f172a;
color:white;
padding:20px;
border-radius:18px;
text-align:right;
}

.total-box h2{
color:#facc15;
}

/* BUTTON */

.btn{
border:none;
padding:14px 20px;
border-radius:14px;
cursor:pointer;
font-weight:600;
display:inline-flex;
align-items:center;
gap:8px;
}

.btn-add{
background:#e2e8f0;
}

.btn-submit{
background:linear-gradient(135deg,#d4af37,#b8962e);
margin-top:20px;
width:100%;
justify-content:center;
font-size:16px;
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
overflow-x:hidden;
}

/* MENU BUTTON */

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

padding:25px 15px;
}

.sidebar.active{
left:0;
}

.sidebar-overlay.active{
display:block;
opacity:1;
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
width:100%;
padding:85px 16px 20px;
}

/* TOPBAR */

.topbar{
flex-direction:column;
align-items:flex-start !important;
}

.topbar h2{
font-size:24px;
line-height:1.3;
}

.topbar p{
font-size:13px;
}

.badge{
font-size:12px;
padding:7px 12px;
}

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

/* CARD */

.card{
padding:22px 18px;
border-radius:20px;
}

/* USER BOX */

.user-box{
padding:20px;
border-radius:20px;
margin-bottom:22px;
}

.user-box h3{
font-size:18px;
line-height:1.4;
}

.user-box p{
font-size:13px;
}

/* FORM */

.form-group{
margin-bottom:18px;
}

label{
font-size:13px;
margin-bottom:7px;
}

input,
textarea{
padding:13px;
font-size:14px;
border-radius:12px;
}

/* DETAIL ITEM */

.detail-item{
padding:16px;
border-radius:16px;
margin-bottom:16px;
}

.detail-top{
margin-bottom:12px;
}

.detail-top h4{
font-size:14px;
}

/* ROW MOBILE */

.row{
grid-template-columns:1fr;
gap:12px;
}

/* DELETE BUTTON */

.btn-delete{
width:32px;
height:32px;
font-size:16px;
}

/* TOTAL BOX */

.total-box{
padding:18px;
border-radius:16px;
margin-top:18px;
}

.total-box p{
font-size:13px;
}

.total-box h2{
font-size:24px;
margin-top:4px;
}

/* BUTTON */

.btn{
width:100%;
justify-content:center;
padding:13px;
font-size:14px;
border-radius:14px;
}

.btn-add{
margin-top:5px;
}

.btn-submit{
margin-top:18px;
}

/* TEXT */

h3{
line-height:1.4;
}

}

.user-info{
display:flex;
align-items:center;
gap:15px;
flex-wrap:wrap;
}

@media(max-width:900px){

.user-info{
width:100%;
display:flex;
flex-direction:column;
align-items:flex-start !important;
justify-content:flex-start !important;
text-align:left !important;
gap:12px;
}

.user-info > div:not(.badge){
width:100%;
text-align:left !important;
}

.badge{
width:auto !important;
display:inline-flex;
align-items:center;
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

<div class="sidebar" id="sidebar">

<h2>SIBONPASBI</h2>

<a href="dashboard.php">
<i data-lucide="layout-dashboard"></i>
Dashboard
</a>

<a href="pengajuan_uang.php" class="active">
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

<div class="main">

<div class="topbar">

<div>
<h2>Pengajuan Uang</h2>
<p>Buat pengajuan bon uang operasional</p>
</div>

<div class="user-info">

<div class="badge">
USER
</div>

<div>
Halo, <b><?= $user['nama']; ?></b>
</div>

<a href="../auth/logout.php" class="logout">
<i data-lucide="log-out"></i>
Logout
</a>

</div>

</div>

<form action="proses_pengajuan_uang.php" method="POST">

<div class="card">

<div class="user-box">
<h3><?= $user['nama']; ?></h3>
<p>NIP : <?= $user['nip']; ?></p>
<p><?= $user['bidang']; ?></p>
</div>

<div class="form-group">
<label>Keperluan</label>

<textarea 
name="keperluan"
required
placeholder="Masukkan keperluan pengajuan..."></textarea>
</div>

<div id="detailContainer">

<div class="detail-item">

<div class="detail-top">

<h4>Item Pengajuan</h4>

<button type="button" class="btn-delete hidden-delete" onclick="hapusItem(this)">
    ✕
</button>

</div>

<div class="row">

<div>
<label>Uraian</label>

<input
type="text"
name="uraian[]"
required
placeholder="Masukkan uraian">
</div>

<div>
<label>Jumlah (Rp)</label>

<input
type="number"
name="subtotal[]"
class="subtotal"
required
placeholder="0">
</div>

</div>

</div>

</div>

<button type="button" class="btn btn-add" id="addItem">
<i data-lucide="plus"></i>
Tambah Item
</button>

<div class="total-box">
<p>Total Pengajuan</p>
<h2 id="grandTotal">Rp 0</h2>
</div>

<input type="hidden" name="total" id="totalInput">

<button type="submit" class="btn btn-submit">
<i data-lucide="send"></i>
Kirim Pengajuan
</button>

</div>

</form>

</div>

<script>

lucide.createIcons();

const detailContainer = document.getElementById('detailContainer');

/* =========================
   TAMBAH ITEM
========================= */

document.getElementById('addItem').addEventListener('click', () => {

    const item = document.querySelector('.detail-item').cloneNode(true);

    item.querySelectorAll('input').forEach(input => {
        input.value = '';
    });

    detailContainer.appendChild(item);

    attachEvents();
    updateDeleteButtons();

});

/* =========================
   HAPUS ITEM
========================= */

function hapusItem(button){

    button.closest('.detail-item').remove();

    updateGrandTotal();
    updateDeleteButtons();

}

function updateDeleteButtons(){

    const items = document.querySelectorAll('.detail-item');

    items.forEach((item,index)=>{

        const btn = item.querySelector('.btn-delete');

        if(index === 0){

            btn.classList.add('hidden-delete');

        }else{

            btn.classList.remove('hidden-delete');

        }

    });

}

/* =========================
   EVENT TOTAL
========================= */

function attachEvents(){

    document.querySelectorAll('.subtotal').forEach(input => {

        input.oninput = updateGrandTotal;

    });

}

/* =========================
   HITUNG TOTAL
========================= */

function updateGrandTotal(){

    let total = 0;

    document.querySelectorAll('.subtotal').forEach(input => {

        total += parseInt(input.value) || 0;

    });

    document.getElementById('grandTotal').innerText =
    'Rp ' + total.toLocaleString('id-ID');

    document.getElementById('totalInput').value = total;

}

attachEvents();

updateDeleteButtons();

</script>

<script>

function toggleSidebar(){

document.getElementById("sidebar").classList.toggle("active");

document.querySelector(".sidebar-overlay")
.classList.toggle("active");

}

</script>

</body>
</html>