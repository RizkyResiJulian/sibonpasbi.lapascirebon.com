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

if(isset($_POST['simpan'])){

$nama       = htmlspecialchars($_POST['nama']);
$nip        = htmlspecialchars($_POST['nip']);
$bidang     = htmlspecialchars($_POST['bidang']);
$username   = htmlspecialchars($_POST['username']);
$password   = md5($_POST['password']);
$role       = htmlspecialchars($_POST['role']);
$status     = htmlspecialchars($_POST['status']);

/* CEK USERNAME */

$cek = mysqli_query($conn,"
SELECT * FROM users 
WHERE username='$username'
");

if(mysqli_num_rows($cek) > 0){

    $error = "Username sudah digunakan!";

}else{

    mysqli_query($conn,"
    INSERT INTO users
    (nama,nip,bidang,username,password,role,status)
    VALUES
    ('$nama','$nip','$bidang','$username','$password','$role','$status')
    ");

    $_SESSION['success'] = "User berhasil ditambahkan";

    header("Location: users.php");
    exit;
}

}
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Tambah User</title>
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
font-size:28px;
color:var(--primary);
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
border-radius:24px;
padding:30px;
box-shadow:0 10px 25px rgba(0,0,0,.08);
max-width:850px;
}

/* FORM */

.form-grid{
display:grid;
grid-template-columns:1fr 1fr;
gap:20px;
}

.form-group{
margin-bottom:20px;
}

.form-group.full{
grid-column:1 / 3;
}

label{
display:block;
margin-bottom:8px;
font-weight:600;
color:var(--primary);
font-size:14px;
}

input,
select{
width:100%;
padding:14px;
border:1px solid #d1d5db;
border-radius:14px;
font-size:14px;
font-family:Poppins;
transition:.3s;
background:white;
}

input:focus,
select:focus{
outline:none;
border-color:var(--accent);
box-shadow:0 0 0 4px rgba(212,175,55,.15);
}

/* BUTTON */

.btn{
display:inline-flex;
align-items:center;
justify-content:center;
gap:8px;
padding:12px 22px;
border:none;
border-radius:30px;
font-weight:600;
font-size:14px;
cursor:pointer;
text-decoration:none;
transition:.3s;
}

.btn-primary{
background:linear-gradient(135deg,#d4af37,#b8962e);
color:#0f172a;
}

.btn-secondary{
background:#e2e8f0;
color:#0f172a;
}

.btn:hover{
transform:translateY(-2px);
}

/* ALERT */

.alert{
padding:14px 18px;
border-radius:14px;
margin-bottom:20px;
font-size:14px;
font-weight:500;
}

.alert-danger{
background:#fee2e2;
color:#991b1b;
}

/* ACTION */

.form-action{
display:flex;
gap:15px;
margin-top:10px;
flex-wrap:wrap;
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

.form-grid{
grid-template-columns:1fr;
}

.form-group.full{
grid-column:auto;
}

.card{
padding:20px;
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

<a href="users.php" class="active">
<i data-lucide="users"></i>
Manajemen Users
</a>

</div>

<!-- MAIN -->

<div class="main">

<!-- TOPBAR -->

<div class="topbar">

<div>
<h2>Tambah User</h2>
<p>Tambahkan akun pengguna baru ke dalam sistem</p>
</div>

<div class="user-info">

<div class="badge">
SUPERADMIN
</div>

<div>
Halo, <b><?= $_SESSION['nama']; ?></b> 👋
</div>

<a href="../auth/logout.php" class="logout">
Logout
</a>

</div>

</div>

<!-- CARD -->

<div class="card">

<?php if(isset($error)){ ?>

<div class="alert alert-danger">
<?= $error; ?>
</div>

<?php } ?>

<form method="POST">

<div class="form-grid">

<!-- NAMA -->

<div class="form-group">
<label>Nama Lengkap</label>
<input type="text" name="nama" required>
</div>

<!-- NIP -->

<div class="form-group">
<label>NIP</label>
<input type="text" name="nip" required>
</div>

<!-- BIDANG -->

<div class="form-group">
<label>Bidang</label>

<select name="bidang" required>

<option value="">-- Pilih Bidang --</option>

<option value="Kepala Lembaga Pemasyarakatan">Kepala Lembaga Pemasyarakatan</option>
<option value="Tata Usaha">Tata Usaha</option>
<option value="KPLP">KPLP</option>
<option value="Pembinaan">Pembinaan</option>
<option value="Kamtib">Kamtib</option>
<option value="Giatja">Giatja</option>
<option value="Sub Bagian Umum">Sub Bagian Umum</option>
<option value="Sub Bagian Kepegawaian">Sub Bagian Kepegawaian</option>
<option value="Sub Bagian Keuangan">Sub Bagian Keuangan</option>
<option value="Portatib">Portatib</option>
<option value="Keamanan">Keamanan</option>
<option value="Bimkemas">Bimkemas</option>
<option value="Bimker">Bimker</option>
<option value="Sarker">Sarker</option>
<option value="Registrasi">Registrasi</option>
<option value="PHK">PHK</option>

</select>

</div>

<!-- USERNAME -->

<div class="form-group">
<label>Username</label>
<input type="text" name="username" required>
</div>

<!-- PASSWORD -->

<div class="form-group">
<label>Password</label>
<input type="password" name="password" required>
</div>

<!-- ROLE -->

<div class="form-group">
<label>Role</label>

<select name="role" required>

<option value="">-- Pilih Role --</option>

<option value="admin">Admin</option>
<option value="operator">Operator</option>
<option value="user">User</option>
<option value="kalapas">Kalapas</option>
<option value="kabagtu">Kabag TU</option>
<option value="bendahara">Bendahara</option>
<option value="ppk">PPK</option>
<option value="superadmin">Superadmin</option>

</select>

</div>

<!-- STATUS -->

<div class="form-group full">
<label>Status</label>

<select name="status" required>
<option value="aktif">Aktif</option>
<option value="nonaktif">Nonaktif</option>
</select>

</div>

</div>

<!-- BUTTON -->

<div class="form-action">

<button type="submit" name="simpan" class="btn btn-primary">
<i data-lucide="save"></i>
Simpan User
</button>

<a href="users.php" class="btn btn-secondary">
<i data-lucide="arrow-left"></i>
Kembali
</a>

</div>

</form>

</div>

</div>

<script>
lucide.createIcons();
</script>

</body>
</html>