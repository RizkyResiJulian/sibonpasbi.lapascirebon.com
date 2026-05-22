<?php
session_start();
include "config/koneksi.php";

// =======================
// PROSES KRITIK & SARAN
// =======================

if(isset($_POST['kirim_saran'])){

    $nama   = htmlspecialchars($_POST['nama']);
    $nip = preg_replace('/[^0-9]/', '', $_POST['nip']);
    $bidang = htmlspecialchars($_POST['bidang']);
    $isi    = htmlspecialchars($_POST['isi']);

    if(empty($nama) || empty($nip) || empty($bidang) || empty($isi)){
        $_SESSION['success'] = "Semua field wajib diisi.";
        header("Location: ".$_SERVER['PHP_SELF']."#contact");
        exit;
    }

    $stmt = mysqli_prepare($conn,"
        INSERT INTO saran(nama,nip,bidang,isi,tanggal)
        VALUES(?,?,?,?,NOW())
    ");

    mysqli_stmt_bind_param($stmt,"ssss",$nama,$nip,$bidang,$isi);
    mysqli_stmt_execute($stmt);

    $_SESSION['success'] = "Terima kasih atas kritik dan sarannya!";

    header("Location: ".$_SERVER['PHP_SELF']."#contact");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>SIBONPASBI - Sistem Informasi Bon Lapas Kesambi</title>

<link rel="shortcut icon" href="logo1.ico">

<link rel="preconnect" href="https://fonts.googleapis.com">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<script src="https://unpkg.com/lucide@latest"></script>

<style>

:root{
--primary:#0f172a;
--primary2:#1e293b;
--accent:#d4af37;
--accent-dark:#b8962e;
--bg:#f8fafc;
--white:#ffffff;
--text:#0f172a;
--text-light:#64748b;

--shadow:0 5px 15px rgba(0,0,0,0.08);
--shadow-lg:0 12px 30px rgba(0,0,0,0.15);
}

*{
margin:0;
padding:0;
box-sizing:border-box;
}

html{
scroll-behavior:smooth;
}

body{
font-family:'Poppins',sans-serif;
background:var(--bg);
color:var(--text);
overflow-x:hidden;
}

/* ================= HEADER ================= */

header{
position:fixed;
top:0;
left:0;
width:100%;
background:rgba(255,255,255,0.95);
backdrop-filter:blur(10px);
box-shadow:var(--shadow);
z-index:1000;
}

.nav-container{
max-width:1200px;
margin:auto;
padding:15px 20px;
display:flex;
justify-content:space-between;
align-items:center;
gap:15px;
}

.logo{
display:flex;
align-items:center;
gap:10px;
font-size:20px;
font-weight:700;
color:var(--primary);
}

.logo img{
width:45px;
height:45px;
object-fit:cover;
border-radius:10px;
}

.nav-links{
display:flex;
align-items:center;
gap:25px;
list-style:none;
flex-wrap:wrap;
}

.nav-links a{
text-decoration:none;
color:var(--text);
font-weight:500;
position:relative;
transition:.3s;
}

.nav-links a::after{
content:'';
position:absolute;
left:0;
bottom:-5px;
width:0;
height:2px;
background:var(--accent);
transition:.3s;
}

.nav-links a:hover::after{
width:100%;
}

/* ================= BUTTON ================= */

.btn{
padding:12px 22px;
border:none;
border-radius:40px;
font-weight:600;
cursor:pointer;
text-decoration:none;
display:inline-flex;
align-items:center;
justify-content:center;
gap:8px;
transition:.3s;
font-size:15px;
}

.btn-primary{
background:linear-gradient(135deg,var(--accent),var(--accent-dark));
color:#0f172a;
}

.btn-primary:hover{
transform:translateY(-3px);
box-shadow:var(--shadow-lg);
}

.btn-outline{
border:2px solid var(--accent);
color:var(--accent);
background:transparent;
}

.btn-outline:hover{
background:var(--accent);
color:#0f172a;
}

/* ================= HERO ================= */

.hero{
padding:140px 20px 80px;
background:linear-gradient(135deg,#0f172a,#1e293b,#334155);
color:white;
}

.hero-container{
max-width:1200px;
margin:auto;
display:grid;
grid-template-columns:1fr 1fr;
gap:50px;
align-items:center;
}

.hero-content h1{
font-size:3.2rem;
line-height:1.2;
margin-bottom:20px;
}

.hero-content span{
color:var(--accent);
}

.hero-content p{
color:#cbd5e1;
line-height:1.8;
margin-bottom:30px;
}

.hero-content div{
justify-content:center;
}

.hero-buttons{
display:flex;
gap:15px;
flex-wrap:wrap;
}

.hero-image{
position:relative;
}

.hero-image img{
width:100%;
border-radius:25px;
box-shadow:var(--shadow-lg);
}

/* FLOAT CARD */

.floating-card{
position:absolute;
top:20px;
left:-20px;
background:white;
color:#0f172a;
padding:12px 18px;
border-radius:14px;
box-shadow:var(--shadow-lg);
display:flex;
align-items:center;
gap:10px;
font-weight:600;
animation:float 3s ease-in-out infinite;
border-left:5px solid var(--accent);
}

.floating-card i{
color:var(--accent);
}

@keyframes float{
0%,100%{
transform:translateY(0);
}
50%{
transform:translateY(-10px);
}
}

/* ================= SECTION ================= */

section{
padding:80px 20px;
}

.section-title{
font-size:2rem;
text-align:center;
margin-bottom:10px;
color:var(--primary);
}

.section-subtitle{
text-align:center;
color:var(--text-light);
margin-bottom:50px;
}

/* ================= FEATURES ================= */

/* ================= FEATURES ================= */

.features-grid{
max-width:1200px;
margin:auto;
display:grid;
grid-template-columns:repeat(3,1fr);
gap:25px;
align-items:stretch;
}

.feature-card{
background:white;
padding:30px;
border-radius:22px;
box-shadow:var(--shadow);
text-align:center;
transition:.3s;
border-top:5px solid var(--accent);

display:flex;
flex-direction:column;

height:100%;
}

.feature-card:hover{
transform:translateY(-8px);
box-shadow:var(--shadow-lg);
}

.feature-icon{
width:70px;
height:70px;
margin:auto;
margin-bottom:20px;
border-radius:50%;
background:var(--primary);
display:flex;
align-items:center;
justify-content:center;
color:var(--accent);
flex-shrink:0;
}

.feature-card h3{
margin-bottom:12px;
color:var(--primary);
font-size:20px;
}

.feature-card p{
color:var(--text-light);
line-height:1.7;
font-size:14px;

flex-grow:1;

display:flex;
align-items:center;
justify-content:center;
}

/* ================= RESPONSIVE FEATURES ================= */

@media(max-width:992px){

.features-grid{
grid-template-columns:repeat(2,1fr);
}

}

@media(max-width:600px){

.features-grid{
grid-template-columns:1fr;
}

}

/* ================= ACCESS ================= */

.access-section{
background:linear-gradient(135deg,#0f172a,#1e293b);
}

.access-section .section-title{
color:white;
}

.access-section .section-subtitle{
color:#cbd5e1;
}

.access-grid{
max-width:1200px;
margin:auto;
display:grid;
grid-template-columns:1fr 1fr;
gap:30px;
}

.access-card{
background:white;
border-radius:24px;
overflow:hidden;
box-shadow:var(--shadow);
transition:.3s;
}

.access-card:hover{
transform:translateY(-8px);
box-shadow:var(--shadow-lg);
}

.access-header{
background:var(--accent);
padding:25px;
font-size:20px;
font-weight:600;
}

.access-body{
padding:25px;
}

.access-body ul{
list-style:none;
margin-bottom:25px;
}

.access-body li{
margin-bottom:12px;
line-height:1.7;
}

/* ================= FORM ================= */

.form-container{
max-width:700px;
margin:auto;
background:white;
padding:35px;
border-radius:25px;
box-shadow:var(--shadow);
border-top:5px solid var(--accent);
}

.form-group{
margin-bottom:20px;
}

.form-group label{
display:block;
margin-bottom:8px;
font-weight:600;
}

.form-group input,
.form-group textarea{
width:100%;
padding:14px;
border:1px solid #d1d5db;
border-radius:12px;
font-family:'Poppins';
font-size:15px;
transition:.3s;
}

.form-group input:focus,
.form-group textarea:focus{
outline:none;
border-color:var(--accent);
box-shadow:0 0 0 3px rgba(212,175,55,0.2);
}

/* ================= FOOTER ================= */

footer{
background:#020617;
color:#cbd5e1;
text-align:center;
padding:30px 20px;
}

/* ================= MOBILE ================= */

@media(max-width:768px){

header{
padding:0;
}

.nav-container{
flex-direction:column;
padding:12px 15px;
gap:12px;
}

.logo{
font-size:18px;
}

.logo img{
width:40px;
height:40px;
}

.nav-links{
width:100%;
justify-content:center;
gap:14px;
}

.nav-links a{
font-size:14px;
}

header .btn{
width:100%;
max-width:220px;
padding:10px 18px;
font-size:14px;
}

/* HERO */

.hero{
padding:220px 18px 60px;
}

.hero-container{
grid-template-columns:1fr;
gap:35px;
text-align:center;
}

.hero-content h1{
font-size:2rem;
line-height:1.3;
margin-bottom:16px;
}

.hero-content p{
font-size:14px;
line-height:1.8;
margin-bottom:22px;
}

.hero-buttons{
flex-direction:column;
gap:12px;
}

.hero-buttons .btn{
width:100%;
}

.hero-image img{
border-radius:18px;
}

.floating-card{
position:relative;
top:0;
left:0;
margin-top:15px;
justify-content:center;
font-size:14px;
padding:10px 14px;
}

/* SECTION */

section{
padding:60px 18px;
}

.section-title{
font-size:1.6rem;
line-height:1.3;
}

.section-subtitle{
font-size:14px;
margin-bottom:35px;
}

/* FEATURES */

.features-grid{
grid-template-columns:1fr;
gap:20px;
}

.feature-card{
padding:24px 20px;
}

.feature-card h3{
font-size:18px;
}

.feature-card p{
font-size:14px;
}

/* ACCESS */

.access-grid{
grid-template-columns:1fr;
gap:20px;
}

.access-header{
padding:20px;
}

.access-header h3{
font-size:18px;
}

.access-body{
padding:20px;
}

/* FORM */

.form-container{
padding:24px 18px;
border-radius:20px;
}

.form-group input,
.form-group textarea{
font-size:14px;
padding:12px;
}

.btn{
width:100%;
justify-content:center;
}

/* FOOTER */

footer{
padding:25px 18px;
font-size:14px;
}

}

/* ================= ACCESS IMPROVE ================= */

.access-grid{
max-width:1200px;
margin:auto;
display:grid;
grid-template-columns:repeat(auto-fit,minmax(320px,1fr));
gap:30px;
}

.access-card{
background:white;
border-radius:24px;
overflow:hidden;
box-shadow:0 10px 25px rgba(0,0,0,0.08);
transition:.3s;
border:1px solid rgba(255,255,255,0.1);
}

.access-card:hover{
transform:translateY(-8px);
box-shadow:0 18px 40px rgba(0,0,0,0.15);
}

.access-header{
padding:25px;
background:linear-gradient(135deg,#d4af37,#b8962e);
display:flex;
align-items:center;
gap:18px;
color:#0f172a;
}

.access-header h3{
font-size:20px;
margin-bottom:4px;
}

.access-header span{
font-size:13px;
opacity:0.8;
}

.access-icon{
width:60px;
height:60px;
border-radius:18px;
background:rgba(255,255,255,0.25);
display:flex;
align-items:center;
justify-content:center;
backdrop-filter:blur(10px);
flex-shrink:0;
}

.access-icon i{
width:28px;
height:28px;
}

.access-body{
padding:25px;
}

.access-body ul{
list-style:none;
margin-bottom:25px;
}

.access-body li{
margin-bottom:14px;
line-height:1.7;
color:#475569;
font-size:14px;
display:flex;
align-items:flex-start;
gap:8px;
}

</style>

</head>

<body>

<!-- ================= HEADER ================= -->

<header>

<div class="nav-container">

<div class="logo">
<img src="assets/img/logo2.png">
<span>SIBONPASBI</span>
</div>

<ul class="nav-links">
<li><a href="#home">Beranda</a></li>
<li><a href="#features">Layanan</a></li>
<li><a href="#public">Permohonan</a></li>
<li><a href="#contact">Saran</a></li>
</ul>

<a href="auth/login.php" class="btn btn-outline">
Login
</a>

</div>

</header>

<!-- ================= HERO ================= -->
<?php
if (isset($_GET['pesan']) && $_GET['pesan'] == 'login_dulu') {
    echo "
    <div style='
    position:fixed;
    top:90px;
    right:20px;
    z-index:9999;
    background:#fff3cd;
    color:#856404;
    padding:15px 20px;
    border-radius:12px;
    box-shadow:0 5px 15px rgba(0,0,0,0.1);
    font-weight:500;
    '>
    Silahkan login terlebih dahulu.
    </div>
    ";
}
?>

<section class="hero" id="home">

<div class="hero-container">

<div class="hero-content">

<h1>
Sistem Informasi
<span>BON</span> Lapas Kesambi
</h1>

<p>
Platform digital untuk pengajuan keuangan, permohonan barang,
pengelolaan inventaris kantor,
serta sistem laporan BMN secara modern,
cepat, dan efisien.
</p>

<div style="
display:flex;
gap:10px;
flex-wrap:wrap;
margin-bottom:25px;
">

<span style="
background:rgba(255,255,255,0.1);
padding:8px 14px;
border-radius:30px;
font-size:14px;
">
📦 Inventaris
</span>

<span style="
background:rgba(255,255,255,0.1);
padding:8px 14px;
border-radius:30px;
font-size:14px;
">
💰 Keuangan
</span>

<span style="
background:rgba(255,255,255,0.1);
padding:8px 14px;
border-radius:30px;
font-size:14px;
">
📑 BMN
</span>

<span style="
background:rgba(255,255,255,0.1);
padding:8px 14px;
border-radius:30px;
font-size:14px;
">
🛠 Pelayanan Pegawai
</span>

</div>

<div class="hero-buttons">

<a href="auth/login.php?pesan=login_dulu" class="btn btn-primary">
    <i data-lucide="shopping-cart"></i>
    Permohonan Barang
</a>

<a href="auth/login.php?pesan=login_dulu" class="btn btn-primary">
    <i data-lucide="wallet"></i>
    Pengajuan Keuangan
</a>

<button class="btn btn-outline" onclick="scrollToSection('contact')">
<i data-lucide="message-circle"></i>
Kritik & Saran
</button>

</div>

</div>

<div class="hero-image">

<img src="assets/img/lapas1.jpeg">

<div class="floating-card">
<i data-lucide="users"></i>
125+ Pegawai Aktif
</div>

</div>

</div>

</section>

<!-- ================= FEATURES ================= -->

<section class="features" id="features">

<h2 class="section-title">
Layanan Sub Bagian Umum & Bon Keuangan
</h2>

<p class="section-subtitle">
Sistem digital untuk pengelolaan inventaris dan pelayanan pegawai
</p>

<div class="features-grid">

<div class="feature-card">

<div class="feature-icon">
<i data-lucide="shopping-cart"></i>
</div>

<h3>Permohonan Barang</h3>

<p>
Pengajuan barang menjadi lebih cepat,
mudah, dan dapat diakses secara online.
</p>

</div>

<div class="feature-card">

<div class="feature-icon">
<i data-lucide="wallet"></i>
</div>

<h3>Pengajuan Keuangan</h3>

<p>
Membantu proses pengajuan kebutuhan keuangan
secara digital, cepat, dan terdokumentasi.
</p>

</div>

<div class="feature-card">

<div class="feature-icon">
<i data-lucide="package"></i>
</div>

<h3>Manajemen Inventaris</h3>

<p>
Mempermudah pengelolaan stok barang
dan pencatatan inventaris kantor.
</p>

</div>

<div class="feature-card">

<div class="feature-icon">
<i data-lucide="clipboard-list"></i>
</div>

<h3>Pengelolaan BMN</h3>

<p>
Mempermudah monitoring dan pencatatan
Barang Milik Negara secara terintegrasi.
</p>

</div>

<div class="feature-card">

<div class="feature-icon">
<i data-lucide="bar-chart-3"></i>
</div>

<h3>Laporan Otomatis</h3>

<p>
Laporan barang masuk dan keluar
tersusun secara otomatis dan rapi.
</p>

</div>

<div class="feature-card">

<div class="feature-icon">
<i data-lucide="shield-check"></i>
</div>

<h3>Approval Digital</h3>

<p>
Proses persetujuan dilakukan secara digital,
cepat, transparan, dan terdokumentasi otomatis.
</p>

</div>

</div>

</section>

<!-- ================= AKSES ================= -->

<!-- ================= AKSES ================= -->

<section class="access-section" id="public">

<h2 class="section-title">
Akses Pegawai
</h2>

<p class="section-subtitle">
Layanan digital yang tersedia untuk seluruh pegawai
</p>

<div class="access-grid">

    <!-- PERMOHONAN BARANG -->
    <div class="access-card">

        <div class="access-header">
            <div class="access-icon">
                <i data-lucide="shopping-cart"></i>
            </div>

            <div>
                <h3>Permohonan Barang</h3>
                <span>Pengajuan kebutuhan inventaris</span>
            </div>
        </div>

        <div class="access-body">

            <ul>
                <li>✔ Pengajuan barang lebih mudah</li>
                <li>✔ Proses cepat & terstruktur</li>
                <li>✔ Bisa diakses dari perangkat mobile</li>
                <li>✔ Sistem verifikasi otomatis</li>
            </ul>

            <a href="auth/login.php?pesan=login_dulu" class="btn btn-primary">
                <i data-lucide="shopping-cart"></i>
                Ajukan Sekarang
            </a>

        </div>

    </div>

    <!-- PENGAJUAN KEUANGAN -->
    <div class="access-card">

        <div class="access-header">
            <div class="access-icon">
                <i data-lucide="wallet"></i>
            </div>

            <div>
                <h3>Pengajuan Keuangan</h3>
                <span>Pengajuan kebutuhan anggaran</span>
            </div>
        </div>

        <div class="access-body">

            <ul>
                <li>✔ Pengajuan keuangan lebih cepat</li>
                <li>✔ Riwayat pengajuan tersimpan</li>
                <li>✔ Proses verifikasi digital</li>
                <li>✔ Dapat dipantau secara online</li>
            </ul>

            <a href="auth/login.php?pesan=login_dulu" class="btn btn-primary">
                <i data-lucide="wallet"></i>
                Ajukan Sekarang
            </a>

        </div>

    </div>

    <!-- KRITIK SARAN -->
    <div class="access-card">

        <div class="access-header">
            <div class="access-icon">
                <i data-lucide="message-circle"></i>
            </div>

            <div>
                <h3>Kritik & Saran</h3>
                <span>Masukan untuk pelayanan</span>
            </div>
        </div>

        <div class="access-body">

            <ul>
                <li>✔ Kritik langsung terkirim</li>
                <li>✔ Membantu peningkatan layanan</li>
                <li>✔ Mudah digunakan seluruh pegawai</li>
                <li>✔ Tampilan modern & responsif</li>
            </ul>

            <button class="btn btn-primary" onclick="scrollToSection('contact')">
                <i data-lucide="message-circle"></i>
                Isi Form
            </button>

        </div>

    </div>

</div>

</section>

<!-- ================= FORM ================= -->

<section id="contact">

<h2 class="section-title">
Kritik & Saran
</h2>

<p class="section-subtitle">
Berikan masukan untuk meningkatkan pelayanan Sub Bagian Umum
</p>

<div class="form-container">

<?php
if(isset($_SESSION['success'])){
    echo "
    <div style='
    background:#dcfce7;
    color:#166534;
    padding:15px;
    border-radius:12px;
    margin-bottom:20px;
    font-weight:500;
    '>
    ".$_SESSION['success']."
    </div>
    ";

    unset($_SESSION['success']);
}
?>

<form method="POST">

<div class="form-group">
<label>Nama</label>
<input type="text" name="nama" required placeholder="Masukkan nama">
</div>

<div class="form-group">
<label>NIP</label>
<input type="text" name="nip" required placeholder="Masukkan NIP">
</div>

<div class="form-group">
<label>Bidang</label>
<input type="text" name="bidang" required placeholder="Contoh: Sub Bagian Umum">
</div>

<div class="form-group">
<label>Kritik / Saran</label>

<textarea 
name="isi"
rows="5"
required
placeholder="Tulis kritik atau saran..."
></textarea>

</div>

<button type="submit" name="kirim_saran" class="btn btn-primary">
<i data-lucide="send"></i>
Kirim Kritik & Saran
</button>

</form>

</div>

</section>

<!-- ================= FOOTER ================= -->

<footer>
<p>
© 2026 SIBONPASBI - Sistem Informasi BON Lapas Kesambi
</p>

<p style="margin-top:10px;font-size:14px;color:#94a3b8;">
Lapas Kelas I Cirebon - Created by Rizky Resi Julian
</p>
</footer>

<script>

lucide.createIcons();

function scrollToSection(id){

document.getElementById(id).scrollIntoView({
behavior:'smooth'
});

}

</script>

</body>
</html>