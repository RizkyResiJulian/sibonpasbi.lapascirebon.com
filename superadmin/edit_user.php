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

$id = $_GET['id'];

$data = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT * FROM users 
WHERE id='$id'
"));

if(!$data){
    echo "User tidak ditemukan";
    exit;
}

if(isset($_POST['update'])){

    $nama     = mysqli_real_escape_string($conn,$_POST['nama']);
    $nip      = mysqli_real_escape_string($conn,$_POST['nip']);
    $bidang   = mysqli_real_escape_string($conn,$_POST['bidang']);
    $username = mysqli_real_escape_string($conn,$_POST['username']);
    $role     = mysqli_real_escape_string($conn,$_POST['role']);
    $status   = mysqli_real_escape_string($conn,$_POST['status']);

    $password = $_POST['password'];

    // jika password diisi
    if(!empty($password)){

        $password_md5 = md5($password);

        mysqli_query($conn,"
        UPDATE users SET
        nama='$nama',
        nip='$nip',
        bidang='$bidang',
        username='$username',
        password='$password_md5',
        role='$role',
        status='$status'
        WHERE id='$id'
        ");

    }else{

        mysqli_query($conn,"
        UPDATE users SET
        nama='$nama',
        nip='$nip',
        bidang='$bidang',
        username='$username',
        role='$role',
        status='$status'
        WHERE id='$id'
        ");

    }

    header("Location: users.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Edit User</title>
<link rel="shortcut icon" href="../assets/img/logo1.ico">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<script src="https://unpkg.com/lucide@latest"></script>

<style>

:root{
--primary:#0f172a;
--accent:#d4af37;
--bg:#f1f5f9;
--white:#ffffff;
--gray:#64748b;
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
width:250px;
background:var(--primary);
padding:25px;
color:white;
}

.sidebar h2{
margin-bottom:30px;
color:var(--accent);
}

.sidebar a{
display:flex;
align-items:center;
gap:10px;
text-decoration:none;
color:white;
padding:12px 14px;
border-radius:12px;
margin-bottom:12px;
transition:.3s;
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

.topbar{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:25px;
flex-wrap:wrap;
gap:15px;
}

/* CARD */

.form-card{
background:white;
padding:30px;
border-radius:20px;
box-shadow:0 10px 25px rgba(0,0,0,.08);
max-width:700px;
}

/* FORM */

.form-group{
margin-bottom:20px;
}

.form-group label{
display:block;
margin-bottom:8px;
font-weight:600;
color:var(--primary);
}

.form-group input,
.form-group select{
width:100%;
padding:14px;
border:1px solid #cbd5e1;
border-radius:12px;
font-size:14px;
outline:none;
transition:.3s;
}

.form-group input:focus,
.form-group select:focus{
border-color:var(--accent);
box-shadow:0 0 0 3px rgba(212,175,55,.2);
}

/* BUTTON */

.btn{
border:none;
padding:12px 20px;
border-radius:30px;
font-weight:600;
cursor:pointer;
display:inline-flex;
align-items:center;
gap:8px;
text-decoration:none;
font-size:14px;
transition:.3s;
}

.btn-primary{
background:linear-gradient(135deg,#d4af37,#b8962e);
color:#0f172a;
}

.btn-primary:hover{
transform:translateY(-2px);
}

.btn-secondary{
background:#e2e8f0;
color:#0f172a;
}

.action{
display:flex;
gap:12px;
margin-top:25px;
flex-wrap:wrap;
}

small{
color:var(--gray);
font-size:12px;
display:block;
margin-top:6px;
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

<div class="topbar">

<div>
<h2>Edit User</h2>
<p style="color:#64748b">
Perbarui data pengguna sistem
</p>
</div>

</div>

<div class="form-card">

<form method="POST">

<div class="form-group">
<label>Nama</label>
<input 
type="text" 
name="nama" 
required
value="<?= $data['nama']; ?>"
>
</div>

<div class="form-group">
<label>NIP</label>
<input 
type="text" 
name="nip" 
required
value="<?= $data['nip']; ?>"
>
</div>

<div class="form-group">
<label>Bidang</label>

<select name="bidang" required>

<?php
$bidang_list = [
"Kepala Lembaga Pemasyarakatan",
"Tata Usaha",
"KPLP",
"Pembinaan",
"Kamtib",
"Giatja",
"Sub Bagian Umum",
"Sub Bagian Kepegawaian",
"Sub Bagian Keuangan",
"Portatib",
"Keamanan",
"Bimkemas",
"Bimker",
"Sarker",
"Registrasi",
"PHK"
];

foreach($bidang_list as $b){
    $selected = ($data['bidang'] == $b) ? 'selected' : '';
    echo "<option value='$b' $selected>$b</option>";
}
?>

</select>
</div>

<div class="form-group">
<label>Username</label>
<input 
type="text" 
name="username" 
required
value="<?= $data['username']; ?>"
>
</div>

<div class="form-group">
<label>Password Baru</label>
<input 
type="password" 
name="password"
placeholder="Kosongkan jika tidak diubah"
>

<small>
Biarkan kosong jika password tidak ingin diganti
</small>
</div>

<div class="form-group">
<label>Role</label>

<select name="role" required>

<?php
$roles = [
"admin",
"operator",
"user",
"kalapas",
"kabagtu",
"bendahara",
"ppk",
"superadmin"
];

foreach($roles as $r){
    $selected = ($data['role'] == $r) ? 'selected' : '';
    echo "<option value='$r' $selected>$r</option>";
}
?>

</select>

</div>

<div class="form-group">
<label>Status</label>

<select name="status" required>

<option value="aktif" <?= $data['status']=='aktif' ? 'selected' : ''; ?>>
Aktif
</option>

<option value="nonaktif" <?= $data['status']=='nonaktif' ? 'selected' : ''; ?>>
Nonaktif
</option>

</select>
</div>

<div class="action">

<button type="submit" name="update" class="btn btn-primary">
<i data-lucide="save"></i>
Simpan Perubahan
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