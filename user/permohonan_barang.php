<?php
session_start();

// =======================
// CEK LOGIN
// =======================
if(!isset($_SESSION['login'])){
    header("Location: ../auth/login.php");
    exit;
}

// =======================
// CEK ROLE
// =======================
if($_SESSION['role'] != "user"){
    echo "Akses ditolak";
    exit;
}

include "../config/koneksi.php";

// =======================
// CEK USER ID
// =======================
if(!isset($_SESSION['user_id'])){
    die("Session user tidak ditemukan");
}

$user_id = (int)$_SESSION['user_id'];

// =======================
// AMBIL DATA USER LOGIN
// =======================
$stmtUser = mysqli_prepare($conn,"
    SELECT nama, nip, bidang
    FROM users
    WHERE id=?
");

mysqli_stmt_bind_param($stmtUser,"i",$user_id);
mysqli_stmt_execute($stmtUser);

$resultUser = mysqli_stmt_get_result($stmtUser);
$user = mysqli_fetch_assoc($resultUser);

// =======================
// AMBIL DATA BARANG
// =======================
$query = mysqli_query($conn,"
    SELECT * FROM barang
    WHERE status='aktif'
");
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Permohonan Barang | SIBONPASBI</title>
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
--white:#ffffff;
--danger:#ef4444;
--success:#22c55e;
--shadow:0 10px 25px rgba(0,0,0,.08);
--shadow-lg:0 20px 40px rgba(0,0,0,.12);
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

padding-left:50px;
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

.user-top{
display:flex;
align-items:center;
gap:15px;
flex-wrap:wrap;
}

.role-badge{
background:var(--accent);
color:#0f172a;
padding:8px 14px;
border-radius:30px;
font-size:13px;
font-weight:600;
white-space:nowrap;
}

.logout{
background:var(--danger);
color:white;
padding:10px 18px;
border-radius:30px;
text-decoration:none;
font-size:14px;
font-weight:500;
display:flex;
align-items:center;
gap:8px;
}

/* USER BOX */

.user-box{
background:linear-gradient(135deg,#0f172a,#1e293b);
color:white;
padding:25px;
border-radius:24px;
margin-bottom:25px;
box-shadow:var(--shadow-lg);
display:flex;
justify-content:space-between;
align-items:center;
flex-wrap:wrap;
gap:20px;
}

.user-info h3{
font-size:24px;
margin-bottom:8px;
line-height:1.4;
word-break:break-word;
}

.user-info p{
opacity:.9;
font-size:14px;
margin-top:3px;
word-break:break-word;
}

.total-box{
background:rgba(255,255,255,.12);
padding:18px 24px;
border-radius:18px;
text-align:center;
min-width:160px;
}

.total-box p{
font-size:14px;
opacity:.9;
margin-bottom:5px;
}

.total-box h2{
font-size:34px;
color:#facc15;
}

/* SEARCH */

.search-box{
margin-bottom:30px;
position:relative;
}

.search-box input{
width:100%;
padding:16px 20px 16px 52px;
border:none;
border-radius:18px;
background:white;
box-shadow:var(--shadow);
font-size:15px;
outline:none;
}

.search-box i{
position:absolute;
left:18px;
top:16px;
color:var(--gray);
}

/* GRID */

.grid{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
gap:25px;
}

/* CARD */

.card{
background:white;
border-radius:24px;
overflow:hidden;
box-shadow:var(--shadow);
transition:.3s;
}

.card:hover{
transform:translateY(-6px);
box-shadow:var(--shadow-lg);
}

.card img{
width:100%;
height:210px;
object-fit:cover;
background:#e2e8f0;
}

.card-content{
padding:20px;
}

.card-title{
font-size:18px;
font-weight:600;
margin-bottom:12px;
color:var(--primary);
line-height:1.5;
word-break:break-word;
}

.badge{
display:inline-block;
padding:6px 14px;
border-radius:999px;
font-size:12px;
font-weight:600;
margin-bottom:16px;
}

.ready{
background:#dcfce7;
color:#15803d;
}

.empty{
background:#fee2e2;
color:#dc2626;
}

/* QTY */

.qty-box{
display:flex;
align-items:center;
justify-content:center;
gap:12px;
margin-top:10px;
}

.qty-btn{
width:40px;
height:40px;
border:none;
border-radius:14px;
background:#e2e8f0;
font-size:18px;
font-weight:bold;
cursor:pointer;
transition:.2s;
}

.qty-btn:hover{
background:#cbd5e1;
}

.qty-input{
width:80px;
height:42px;
text-align:center;
border:1px solid #cbd5e1;
border-radius:14px;
font-size:16px;
font-weight:600;
outline:none;
}

/* SUBMIT */

.submit-area{
position:sticky;
bottom:20px;
margin-top:35px;
z-index:50;
}

.submit-btn{
width:100%;
padding:18px;
border:none;
border-radius:22px;
background:linear-gradient(135deg,#d4af37,#b8962e);
font-size:17px;
font-weight:700;
cursor:pointer;
display:flex;
justify-content:center;
align-items:center;
gap:10px;
box-shadow:var(--shadow-lg);
transition:.3s;
color:#0f172a;
}

.submit-btn:hover{
transform:translateY(-3px);
}

/* MOBILE BUTTON */

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

cursor:pointer;

align-items:center;
justify-content:center;

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

/* MOBILE */

@media(max-width:900px){

body{
flex-direction:column;
}

/* BUTTON */

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

/* MAIN */

.main{
width:100%;
padding:85px 16px 20px;
}

/* TOPBAR */

.topbar{
flex-direction:column;
align-items:flex-start;
gap:18px;
margin-bottom:25px;
}

.topbar h2{
font-size:24px;
}

.topbar p{
font-size:14px;
}

/* USER */

.user-top{
width:100%;
display:flex;
flex-wrap:wrap;
gap:12px;
}

.logout{
position:fixed;
top:15px;
right:15px;

width:48px;
height:48px;

padding:0;

border-radius:14px;

justify-content:center;

font-size:0;

z-index:2001;
}

.logout i{
margin:0;
}

/* USER BOX */

.user-box{
padding:20px;
border-radius:20px;
}

.user-info h3{
font-size:20px;
}

.user-info p{
font-size:13px;
}

.total-box{
width:100%;
padding:16px;
border-radius:16px;
}

.total-box h2{
font-size:28px;
}

/* SEARCH */

.search-box input{
padding:14px 18px 14px 48px;
font-size:14px;
border-radius:16px;
}

/* GRID */

.grid{
grid-template-columns:1fr;
gap:18px;
}

/* CARD */

.card{
border-radius:20px;
}

.card img{
height:190px;
}

.card-content{
padding:18px;
}

.card-title{
font-size:16px;
}

/* QTY */

.qty-btn{
width:38px;
height:38px;
font-size:17px;
}

.qty-input{
width:70px;
height:40px;
font-size:15px;
}

/* SUBMIT */

.submit-btn{
padding:15px;
font-size:15px;
border-radius:18px;
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

<!-- SIDEBAR -->

<div class="sidebar" id="sidebar">

<h2>SIBONPASBI</h2>

<a href="dashboard.php">
<i data-lucide="layout-dashboard"></i>
Dashboard
</a>

<a href="pengajuan_uang.php">
<i data-lucide="wallet"></i>
Pengajuan Uang
</a>

<a href="permohonan_barang.php" class="active">
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

<!-- MAIN -->

<div class="main">

<!-- TOPBAR -->

<div class="topbar">

<div>
<h2>Permohonan Barang</h2>
<p>Pilih barang kebutuhan operasional pegawai</p>
</div>

<div class="user-top">

<div class="role-badge">
USER
</div>

<div>
Halo, <b><?= htmlspecialchars($_SESSION['nama']); ?></b> 👋
</div>

<a href="../auth/logout.php" class="logout">
<i data-lucide="log-out"></i>
Logout
</a>

</div>

</div>

<!-- USER BOX -->

<div class="user-box">

<div class="user-info">
<h3><?= htmlspecialchars($user['nama']) ?></h3>
<p>NIP : <?= htmlspecialchars($user['nip']) ?></p>
<p><?= htmlspecialchars($user['bidang']) ?></p>
</div>

<div class="total-box">
<p>Total Dipilih</p>
<h2 id="totalItem">0</h2>
</div>

</div>

<!-- SEARCH -->

<div class="search-box">

<i data-lucide="search"></i>

<input 
type="text" 
id="search"
placeholder="Cari barang...">

</div>

<!-- FORM -->

<form method="POST" action="proses_pesan.php">

<input type="hidden" name="nama" value="<?= htmlspecialchars($user['nama']) ?>">
<input type="hidden" name="nip" value="<?= htmlspecialchars($user['nip']) ?>">
<input type="hidden" name="bidang" value="<?= htmlspecialchars($user['bidang']) ?>">

<div class="grid" id="gridBarang">

<?php while($data=mysqli_fetch_assoc($query)){ ?>

<div class="card item-card">

<?php if(!empty($data['gambar'])){ ?>

<img 
src="../uploads/barang/<?= htmlspecialchars($data['gambar']) ?>"
alt="<?= htmlspecialchars($data['nama_barang']) ?>">

<?php } else { ?>

<img 
src="../uploads/no-image.png"
alt="No Image">

<?php } ?>

<div class="card-content">

<div class="card-title">
<?= htmlspecialchars($data['nama_barang']) ?>
</div>

<?php if($data['stok'] > 0){ ?>

<span class="badge ready">
Stok Tersedia
</span>

<div class="qty-box">

<button 
type="button"
class="qty-btn minus">
-
</button>

<input 
type="number"
class="qty-input"
name="jumlah[<?= $data['id'] ?>]"
value="0"
min="0"
max="<?= $data['stok'] ?>">

<button 
type="button"
class="qty-btn plus">
+
</button>

</div>

<?php } else { ?>

<span class="badge empty">
Stok Habis
</span>

<?php } ?>

</div>

</div>

<?php } ?>

</div>

<div class="submit-area">

<button type="submit" class="submit-btn">

<i data-lucide="send"></i>
Kirim Permohonan

</button>

</div>

</form>

</div>

<script>

lucide.createIcons();

// SEARCH

document.getElementById('search').addEventListener('keyup',function(){

let value = this.value.toLowerCase();
let cards = document.querySelectorAll('.item-card');

cards.forEach(card=>{

let text = card.innerText.toLowerCase();

card.style.display = text.includes(value)
? 'block'
: 'none';

});

});

// COUNTER

function updateTotal(){

let total = 0;

document.querySelectorAll('.qty-input').forEach(input=>{

total += parseInt(input.value) || 0;

});

document.getElementById('totalItem').innerText = total;

}

document.querySelectorAll('.plus').forEach(btn=>{

btn.addEventListener('click',function(){

let input = this.parentElement.querySelector('.qty-input');

if(parseInt(input.value) < parseInt(input.max)){

input.value = parseInt(input.value) + 1;

updateTotal();

}

});

});

document.querySelectorAll('.minus').forEach(btn=>{

btn.addEventListener('click',function(){

let input = this.parentElement.querySelector('.qty-input');

if(parseInt(input.value) > 0){

input.value = parseInt(input.value) - 1;

updateTotal();

}

});

});

document.querySelectorAll('.qty-input').forEach(input=>{

input.addEventListener('input',updateTotal);

});

</script>

<script>

function toggleSidebar(){

document.getElementById("sidebar")
.classList.toggle("active");

document.querySelector(".sidebar-overlay")
.classList.toggle("active");

}

</script>

</body>
</html>