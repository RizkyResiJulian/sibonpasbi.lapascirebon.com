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

$success = "";
$error = "";

/* =========================
   PROSES GANTI PASSWORD
========================= */

if(isset($_POST['ganti_password'])){

    $password_lama = trim($_POST['password_lama']);
    $password_baru = trim($_POST['password_baru']);
    $konfirmasi    = trim($_POST['konfirmasi_password']);

    // ambil password lama dari database
    $query = mysqli_query($conn,"
    SELECT password
    FROM users
    WHERE id='$user_id'
    ");

    $user = mysqli_fetch_assoc($query);

    // cek password lama MD5
    if(md5($password_lama) != $user['password']){

        $error = "Password lama tidak sesuai";

    }elseif($password_baru != $konfirmasi){

        $error = "Konfirmasi password tidak cocok";

    }elseif(strlen($password_baru) < 6){

        $error = "Password minimal 6 karakter";

    }else{

        // hash md5 password baru
        $password_hash = md5($password_baru);

        mysqli_query($conn,"
        UPDATE users
        SET password='$password_hash'
        WHERE id='$user_id'
        ");

        session_destroy();

        echo "
        <script>
            alert('Password berhasil diganti, silahkan login kembali');
            window.location='../auth/login.php';
        </script>
        ";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Ganti Password</title>

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
--danger:#ef4444;
--success:#22c55e;
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

.user-info{
display:flex;
align-items:center;
gap:15px;
flex-wrap:wrap;
}

.badge{
background:var(--accent);
color:#0f172a;
padding:8px 14px;
border-radius:30px;
font-size:13px;
font-weight:600;
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

/* CARD */

.card{
background:white;
max-width:650px;
padding:35px;
border-radius:25px;
box-shadow:0 10px 25px rgba(0,0,0,.08);
}

.card h3{
font-size:24px;
color:var(--primary);
margin-bottom:10px;
}

.card p{
color:var(--gray);
font-size:14px;
margin-bottom:25px;
}

/* FORM */

.form-group{
margin-bottom:20px;
}

.form-group label{
display:block;
margin-bottom:8px;
font-size:14px;
font-weight:600;
color:var(--primary);
}

.form-group input{
width:100%;
padding:14px 16px;
border:1px solid #cbd5e1;
border-radius:14px;
font-size:14px;
outline:none;
transition:.3s;
}

.form-group input:focus{
border-color:var(--accent);
box-shadow:0 0 0 4px rgba(212,175,55,.15);
}

.input-password{
position:relative;
}

.input-password input{
padding-right:50px;
}

.toggle-password{
position:absolute;
right:15px;
top:50%;
transform:translateY(-50%);
cursor:pointer;
color:#64748b;
transition:.3s;
}

.toggle-password:hover{
color:var(--accent);
}

/* VALIDATION */

.validation-text{
margin-top:8px;
font-size:13px;
font-weight:500;
display:none;
transition:.3s;
}

.validation-success{
color:#16a34a;
display:block;
}

.validation-error{
color:#dc2626;
display:block;
}

/* BORDER STATUS */

.input-success{
border-color:#22c55e !important;
box-shadow:0 0 0 4px rgba(34,197,94,.15) !important;
}

.input-error{
border-color:#ef4444 !important;
box-shadow:0 0 0 4px rgba(239,68,68,.15) !important;
}

/* BUTTON DISABLED */

.btn:disabled{
opacity:.6;
cursor:not-allowed;
transform:none !important;
}

.btn{
width:100%;
border:none;
padding:14px;
border-radius:14px;
background:linear-gradient(135deg,#d4af37,#b8962e);
color:#0f172a;
font-weight:700;
cursor:pointer;
font-size:15px;
transition:.3s;
}

.btn:hover{
transform:translateY(-3px);
}

/* ALERT */

.alert{
padding:14px 18px;
border-radius:14px;
margin-bottom:20px;
font-size:14px;
font-weight:500;
}

.alert-error{
background:#fee2e2;
color:#991b1b;
}

.alert-success{
background:#dcfce7;
color:#166534;
}

/* MOBILE */

/* ================= MOBILE ================= */

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

padding:25px 18px;
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
margin-bottom:22px;
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
align-items:flex-start;
gap:14px;
margin-bottom:22px;
}

.topbar h2{
font-size:24px;
line-height:1.3;
}

.topbar p{
font-size:13px;
}

/* USER INFO */

.user-info{
width:100%;
flex-direction:column;
align-items:flex-start;
gap:10px;
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
max-width:100%;
padding:24px 18px;
border-radius:20px;
}

.card h3{
font-size:22px;
line-height:1.4;
}

.card p{
font-size:13px;
line-height:1.7;
}

/* FORM */

.form-group{
margin-bottom:18px;
}

.form-group label{
font-size:13px;
}

.form-group input{
padding:13px 14px;
font-size:14px;
border-radius:12px;
}

.input-password input{
padding-right:45px;
}

.toggle-password{
right:14px;
}

/* ALERT */

.alert{
padding:13px 15px;
font-size:13px;
border-radius:12px;
}

/* BUTTON */

.btn{
padding:13px;
font-size:14px;
border-radius:12px;
}

/* VALIDATION */

.validation-text{
font-size:12px;
margin-top:8px;
}

}

.form-group input{
width:100%;
padding:14px 16px;
border:1px solid #cbd5e1;
border-radius:14px;
font-size:14px;
outline:none;
transition:.25s ease;
}

.validation-text{
margin-top:10px;
font-size:13px;
font-weight:600;
opacity:0;
transform:translateY(-3px);
transition:.25s ease;
}

.validation-success,
.validation-error{
opacity:1;
transform:translateY(0);
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

<a href="ganti_password.php" class="active">
<i data-lucide="key-round"></i>
Ganti Password
</a>

</div>

<!-- MAIN -->

<div class="main">

<div class="topbar">

<div>
<h2>Ganti Password</h2>
<p>Ubah password akun Anda dengan aman</p>
</div>

<div class="user-info">

<div class="badge">
USER
</div>

<div>
Halo, <b><?= $_SESSION['nama']; ?></b> 👋
</div>

<a href="../auth/logout.php" class="logout" title="Logout">
<i data-lucide="log-out"></i>
Logout
</a>

</div>

</div>

<div class="card">

<h3>Keamanan Akun</h3>

<p>
Masukkan password lama terlebih dahulu sebelum mengganti password baru.
</p>

<?php if($error != ""){ ?>

<div class="alert alert-error">
<?= $error; ?>
</div>

<?php } ?>

<form method="POST" id="formPassword">

<!-- PASSWORD LAMA -->

<div class="form-group">

<label>Password Lama</label>

<div class="input-password">

<input 
type="password"
name="password_lama"
id="password_lama"
required>

<div 
class="toggle-password"
toggle="#password_lama">

<i data-lucide="eye"></i>

</div>

</div>

</div>

<!-- PASSWORD BARU -->

<div class="form-group">

<label>Password Baru</label>

<div class="input-password">

<input 
type="password"
name="password_baru"
id="password_baru"
required>

<div 
class="toggle-password"
toggle="#password_baru">

<i data-lucide="eye"></i>

</div>

</div>

</div>

<!-- KONFIRMASI -->

<div class="form-group">

<label>Konfirmasi Password Baru</label>

<div class="input-password">

<input 
type="password"
name="konfirmasi_password"
id="konfirmasi_password"
required>

<div 
class="toggle-password"
toggle="#konfirmasi_password">

<i data-lucide="eye"></i>

</div>

</div>

<div id="validationText" class="validation-text"></div>

</div>

<button 
type="submit"
name="ganti_password"
class="btn"
id="submitBtn">

Ganti Password

</button>

</form>

</div>

</div>

<script>
lucide.createIcons();
</script>

<script>
lucide.createIcons();

/* =========================
   SHOW / HIDE PASSWORD
========================= */

document.querySelectorAll(".toggle-password").forEach(button => {

    button.addEventListener("click", function(){

        const target = this.getAttribute("toggle");

        const input = document.querySelector(target);

        const icon = this.querySelector("i");

        if(input.type === "password"){

            input.type = "text";

            icon.setAttribute("data-lucide","eye-off");

        }else{

            input.type = "password";

            icon.setAttribute("data-lucide","eye");

        }

        lucide.createIcons();
    });

});

/* =========================
   VALIDASI PASSWORD
========================= */

const passwordBaru   = document.getElementById("password_baru");
const konfirmasi     = document.getElementById("konfirmasi_password");
const validationText = document.getElementById("validationText");
const submitBtn      = document.getElementById("submitBtn");

function cekPassword(){

    const pass1 = passwordBaru.value;
    const pass2 = konfirmasi.value;

    // reset
    validationText.innerHTML = "";

    konfirmasi.classList.remove(
        "input-success",
        "input-error"
    );

    // kosong
    if(pass2 === ""){

        validationText.className = "validation-text";

        submitBtn.disabled = false;

        return;
    }

    // password terlalu pendek
    if(pass1.length < 6){

        validationText.className =
        "validation-text validation-error";

        validationText.innerHTML =
        "✗ Password minimal 6 karakter";

        passwordBaru.classList.add("input-error");

        submitBtn.disabled = true;

        return;

    }else{

        passwordBaru.classList.remove("input-error");
    }

    // cocok
    if(pass1 === pass2){

        validationText.className =
        "validation-text validation-success";

        validationText.innerHTML =
        "✓ Password cocok";

        konfirmasi.classList.add("input-success");

        submitBtn.disabled = false;

    }else{

        validationText.className =
        "validation-text validation-error";

        validationText.innerHTML =
        "✗ Password tidak cocok";

        konfirmasi.classList.add("input-error");

        submitBtn.disabled = true;
    }

}

passwordBaru.addEventListener("input", cekPassword);
konfirmasi.addEventListener("input", cekPassword);

/* =========================
   TOGGLE SIDEBAR
========================= */

function toggleSidebar(){

document.getElementById("sidebar")
.classList.toggle("active");

document.querySelector(".sidebar-overlay")
.classList.toggle("active");

}
</script>
</body>
</html>