<?php
$notif = "";

if(isset($_GET['pesan'])){

    if($_GET['pesan'] == 'login_dulu'){

        $notif = '
        <div class="alert alert-warning">
            <i data-lucide="shield-alert"></i>
            <span>Silahkan login terlebih dahulu untuk melanjutkan.</span>
        </div>
        ';

    }

}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Login Admin</title>
<link rel="shortcut icon" href="../assets/img/logo1.ico">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<script src="https://unpkg.com/lucide@latest"></script>

<style>

:root{
--primary:#0f172a;
--primary-light:#1e293b;
--accent:#d4af37;
--accent-dark:#b8962e;
--text:#1f2937;
--text-light:#6b7280;
--border:#e5e7eb;
--shadow:0 10px 25px rgba(0,0,0,0.1);
}

*{
margin:0;
padding:0;
box-sizing:border-box;
}

body{
font-family:'Poppins',sans-serif;
height:100vh;
}

/* BACK BUTTON */

.back-btn{
position:absolute;
top:25px;
left:25px;
display:flex;
align-items:center;
gap:6px;
text-decoration:none;
background:white;
color:var(--primary);
padding:8px 14px;
border-radius:50px;
font-size:14px;
font-weight:500;
box-shadow:var(--shadow);
transition:.3s;
z-index:10;
}

.back-btn:hover{
background:var(--accent);
color:#0f172a;
transform:translateY(-2px);
}

/* CONTAINER */

.container{
display:flex;
height:100vh;
}

/* LEFT */

.left{
flex:1;
background:
linear-gradient(rgba(15,23,42,0.85),rgba(30,41,59,0.9)),
url('lapas1.jpeg');
background-size:cover;
background-position:center;
display:flex;
align-items:center;
justify-content:center;
color:white;
text-align:center;
padding:40px;

clip-path: polygon(
0 0,
85% 0,
100% 50%,
85% 100%,
0 100%
);
}

.left h1{
font-size:40px;
margin-bottom:10px;
color:var(--accent);
}

.left p{
max-width:400px;
opacity:0.9;
}

/* RIGHT */

.right{
flex:1;
display:flex;
justify-content:center;
align-items:center;
background:#f8fafc;
}

/* CARD */

.login-card{
background:white;
padding:40px;
border-radius:20px;
width:380px;
box-shadow:var(--shadow);
text-align:center;
border-top:5px solid var(--accent);
}

.logo{
width:70px;
margin-bottom:12px;
}

.login-card h2{
font-weight:600;
color:var(--primary);
}

.login-card p{
font-size:14px;
color:var(--text-light);
margin-bottom:25px;
}

/* FORM */

.form-group{
margin-bottom:18px;
text-align:left;
}

.form-group label{
font-size:14px;
font-weight:500;
margin-bottom:6px;
display:block;
}

/* INPUT */

.input-wrap{
display:flex;
align-items:center;
border:1px solid var(--border);
border-radius:10px;
padding:10px;
gap:8px;
transition:.3s;
}

.input-wrap:focus-within{
border-color:var(--accent);
box-shadow:0 0 0 2px rgba(212,175,55,0.2);
}

.input-wrap i{
color:#9ca3af;
}

.input-wrap input{
border:none;
outline:none;
width:100%;
font-family:'Poppins';
font-size:14px;
}

/* BUTTON */

.btn{
width:100%;
padding:13px;
border:none;
border-radius:50px;
background:linear-gradient(135deg,#d4af37,#b8962e);
color:#0f172a;
font-weight:600;
font-size:15px;
cursor:pointer;
display:flex;
align-items:center;
justify-content:center;
gap:8px;
transition:.3s;
}

.btn:hover{
transform:translateY(-3px);
box-shadow:0 10px 25px rgba(0,0,0,0.2);
}

/* ALERT */

.alert{
display:flex;
align-items:center;
gap:12px;
padding:14px 18px;
border-radius:14px;
margin-bottom:20px;
font-size:14px;
font-weight:500;
animation:fadeIn .4s ease;
}

.alert i{
width:20px;
height:20px;
flex-shrink:0;
}

.alert-warning{
background:#fff8e1;
color:#92400e;
border:1px solid #fde68a;
}

@keyframes fadeIn{
from{
opacity:0;
transform:translateY(-10px);
}
to{
opacity:1;
transform:translateY(0);
}
}

/* LOGIN CARD IMPROVEMENT */

.login-card{
background:white;
padding:45px;
border-radius:24px;
width:400px;
box-shadow:
0 15px 40px rgba(0,0,0,0.08),
0 5px 15px rgba(0,0,0,0.05);
text-align:center;
border-top:5px solid var(--accent);
position:relative;
overflow:hidden;
}

.login-card::before{
content:'';
position:absolute;
top:-50px;
right:-50px;
width:120px;
height:120px;
background:rgba(212,175,55,0.08);
border-radius:50%;
}

.login-card::after{
content:'';
position:absolute;
bottom:-40px;
left:-40px;
width:100px;
height:100px;
background:rgba(15,23,42,0.05);
border-radius:50%;
}

.logo{
width:80px;
margin-bottom:15px;
filter:drop-shadow(0 5px 10px rgba(0,0,0,0.1));
}

.login-card h2{
font-size:28px;
margin-bottom:8px;
font-weight:700;
color:var(--primary);
}

.login-card p{
font-size:14px;
color:var(--text-light);
margin-bottom:28px;
line-height:1.6;
}

/* INPUT */

.input-wrap{
display:flex;
align-items:center;
border:1px solid var(--border);
border-radius:14px;
padding:14px;
gap:10px;
transition:.3s;
background:#fff;
}

.input-wrap:hover{
border-color:#cbd5e1;
}

.input-wrap:focus-within{
border-color:var(--accent);
box-shadow:0 0 0 4px rgba(212,175,55,0.15);
}

.input-wrap input{
border:none;
outline:none;
width:100%;
font-family:'Poppins';
font-size:14px;
background:transparent;
}

.input-wrap .toggle-password{
cursor:pointer;
transition:.3s;
}

.input-wrap .toggle-password:hover{
color:var(--accent);
}

/* BUTTON */

.btn{
width:100%;
padding:14px;
border:none;
border-radius:50px;
background:linear-gradient(135deg,#d4af37,#b8962e);
color:#0f172a;
font-weight:600;
font-size:15px;
cursor:pointer;
display:flex;
align-items:center;
justify-content:center;
gap:10px;
transition:.3s;
margin-top:10px;
}

.btn:hover{
transform:translateY(-3px);
box-shadow:0 12px 30px rgba(0,0,0,0.18);
}

/* MOBILE */

@media(max-width:500px){

.login-card{
width:100%;
margin:20px;
padding:35px 25px;
border-radius:22px;
}

.back-btn{
top:15px;
left:15px;
}

}

.left-content{
max-width:500px;
}

.badge{
display:inline-flex;
align-items:center;
gap:8px;
background:rgba(255,255,255,0.1);
padding:10px 18px;
border-radius:50px;
margin-bottom:25px;
font-size:14px;
backdrop-filter:blur(10px);
border:1px solid rgba(255,255,255,0.15);
}

.badge i{
color:var(--accent);
width:18px;
height:18px;
}

.left h1{
font-size:52px;
font-weight:700;
line-height:1.1;
margin-bottom:10px;
color:var(--accent);
letter-spacing:1px;
}

.left h2{
font-size:28px;
font-weight:600;
line-height:1.4;
margin-bottom:20px;
color:white;
}

.left p{
max-width:480px;
opacity:0.9;
font-size:15px;
line-height:1.9;
margin-bottom:30px;
color:#e2e8f0;
}

.left-feature{
display:flex;
gap:15px;
flex-wrap:wrap;
justify-content:center;
}

.feature-item{
display:flex;
align-items:center;
gap:8px;
background:rgba(255,255,255,0.08);
padding:12px 18px;
border-radius:14px;
border:1px solid rgba(255,255,255,0.1);
backdrop-filter:blur(8px);
font-size:14px;
font-weight:500;
}

.feature-item i{
width:18px;
height:18px;
color:var(--accent);
}

/* RESPONSIVE */

@media(max-width:900px){

.left{
display:none;
}

.container{
flex-direction:column;
}

}

</style>
</head>

<body>

<a href="../index.php" class="back-btn">
<i data-lucide="arrow-left"></i>
Kembali
</a>

<div class="container">

<!-- LEFT -->
<div class="left">

<div class="left-content">

<div class="badge">
<i data-lucide="shield-check"></i>
Sistem Pelayanan Digital
</div>

<h1>
SIBONPASBI
</h1>

<h2>
Sistem Informasi BON Lapas Kesambi
</h2>

<p>
Platform digital untuk pengajuan barang, pengajuan keuangan,
pengelolaan inventaris, serta layanan administrasi pegawai
secara cepat, modern, dan terintegrasi.
</p>

<div class="left-feature">

<div class="feature-item">
<i data-lucide="package"></i>
<span>Inventaris</span>
</div>

<div class="feature-item">
<i data-lucide="wallet"></i>
<span>Keuangan</span>
</div>

<div class="feature-item">
<i data-lucide="clipboard-list"></i>
<span>BMN</span>
</div>

</div>

</div>

</div>


<!-- RIGHT -->
<div class="right">

<div class="login-card">

<img src="logo2.png" class="logo">

<h2>LOGIN</h2>
<p>Masuk untuk mengelola sistem</p>
<?= $notif ?>

<form action="proses_login.php" method="POST">

<div class="form-group">
<label>Username</label>
<div class="input-wrap">
<i data-lucide="user"></i>
<input type="text" name="username" required placeholder="Username">
</div>
</div>

<div class="form-group">
<label>Password</label>
<div class="input-wrap">
<i data-lucide="lock"></i>

<input 
type="password" 
name="password" 
id="password"
required placeholder="Password">

<i data-lucide="eye" id="togglePassword" class="toggle-password"></i>

</div>
</div>

<button type="submit" class="btn">
<i data-lucide="log-in"></i>
Login
</button>

</form>

</div>

</div>

</div>

<script>

lucide.createIcons();

const togglePassword = document.getElementById("togglePassword");
const password = document.getElementById("password");

togglePassword.addEventListener("click", function () {

    if(password.type === "password"){

        password.type = "text";
        this.setAttribute("data-lucide","eye-off");

    }else{

        password.type = "password";
        this.setAttribute("data-lucide","eye");

    }

    lucide.createIcons();

});

</script>

</body>
</html>