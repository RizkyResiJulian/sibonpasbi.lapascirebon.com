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

$data = mysqli_query($conn,"SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Manajemen Users</title>
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
max-width:100%;
}

/* TOPBAR */

.topbar{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:30px;
flex-wrap:wrap;
gap:20px;
padding-right:30px;
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
margin-right:20px;
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

/* BUTTON */

.btn{
display:inline-flex;
align-items:center;
justify-content:center;
gap:8px;
padding:10px 20px;
background:linear-gradient(135deg,#d4af37,#b8962e);
color:#0f172a;
text-decoration:none;
border-radius:30px;
font-weight:600;
transition:.3s;
border:none;
cursor:pointer;
font-size:14px;
}

.btn:hover{
transform:translateY(-3px);
}

/* ACTION BAR */

.action-bar{
margin-bottom:20px;
}

/* TABLE */

.table-wrap{
background:white;
border-radius:22px;
padding:25px;
box-shadow:0 10px 25px rgba(0,0,0,.08);
overflow-x:auto;
}

.table-header{
margin-bottom:20px;
}

.table-header h3{
font-size:20px;
color:var(--primary);
margin-bottom:5px;
}

.table-header p{
font-size:14px;
color:var(--gray);
}

.table{
width:100%;
border-collapse:collapse;
min-width:850px;
}

.table th{
background:#f8fafc;
color:var(--primary);
font-size:13px;
font-weight:600;
text-transform:uppercase;
letter-spacing:.5px;
padding:16px;
text-align:left;
}

.table td{
padding:16px;
border-bottom:1px solid #e2e8f0;
font-size:14px;
vertical-align:middle;
}

.table tbody tr{
transition:.2s;
}

.table tbody tr:hover{
background:#f8fafc;
}

/* STATUS */

.status{
padding:6px 12px;
border-radius:30px;
font-size:12px;
font-weight:600;
display:inline-block;
}

.status-active{
background:#dcfce7;
color:#166534;
}

.status-nonactive{
background:#fee2e2;
color:#991b1b;
}

/* ACTION */

.action{
display:flex;
align-items:center;
gap:8px;
}

.btn-edit,
.btn-delete{
width:38px;
height:38px;
display:flex;
align-items:center;
justify-content:center;
border-radius:12px;
transition:.3s;
text-decoration:none;
}

.btn-edit{
background:#0ea5e9;
color:white;
}

.btn-delete{
background:#ef4444;
color:white;
}

.btn-edit:hover,
.btn-delete:hover{
transform:translateY(-2px);
opacity:.9;
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

<a href="riwayat_pengajuan.php">
<i data-lucide="history"></i>
Riwayat Pengajuan
</a>

</div>

<!-- MAIN -->

<div class="main">

<!-- TOPBAR -->

<div class="topbar">

<div>
<h2>Manajemen Users</h2>
<p>Kelola seluruh akun pengguna sistem</p>
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

<!-- BUTTON TAMBAH -->

<div class="action-bar">

<a href="tambah_user.php" class="btn">
<i data-lucide="plus"></i>
Tambah User
</a>

</div>

<!-- TABLE -->

<div class="table-wrap">

<div class="table-header">
<h3>Data Users</h3>
<p>Daftar seluruh akun pengguna yang terdaftar di sistem</p>
</div>

<table class="table">

<thead>

<tr>
<th>No</th>
<th>Nama</th>
<th>NIP</th>
<th>Bidang</th>
<th>Username</th>
<th>Role</th>
<th>Status</th>
<th>Aksi</th>
</tr>

</thead>

<tbody>

<?php $no=1; while($d=mysqli_fetch_assoc($data)){ ?>

<tr>

<td><?= $no++; ?></td>

<td><?= $d['nama']; ?></td>

<td><?= $d['nip']; ?></td>

<td><?= $d['bidang']; ?></td>

<td><?= $d['username']; ?></td>

<td><?= ucfirst($d['role']); ?></td>

<td>

<span class="status <?= $d['status']=='aktif' ? 'status-active' : 'status-nonactive'; ?>">
<?= ucfirst($d['status']); ?>
</span>

</td>

<td>

<div class="action">

<a href="edit_user.php?id=<?= $d['id']; ?>" class="btn-edit">
<i data-lucide="pencil"></i>
</a>

<a 
href="hapus_user.php?id=<?= $d['id']; ?>"
class="btn-delete"
onclick="return confirm('Yakin hapus user?')"
>
<i data-lucide="trash-2"></i>
</a>

</div>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

<script>
lucide.createIcons();
</script>

</body>
</html>