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
// VALIDASI ID
// =======================
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if($id <= 0){
    die("ID tidak valid");
}

// =======================
// AMBIL DATA BARANG
// =======================
$stmt = mysqli_prepare($conn,"SELECT * FROM barang WHERE id=?");
mysqli_stmt_bind_param($stmt,"i",$id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

if(!$row){
    die("Data tidak ditemukan");
}

// =======================
// AMBIL KATEGORI
// =======================
$kategori = mysqli_query($conn,"SELECT * FROM kategori");

// =======================
// UPDATE DATA
// =======================
if(isset($_POST['update'])){

    $kategori_id = (int)$_POST['kategori'];
    $nama = trim($_POST['nama']);
    $stok = (int)$_POST['stok'];

    $nama_file = $row['gambar'];

    // upload gambar
    if(!empty($_FILES['gambar']['name'])){
        $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];

        if(!in_array($ext,$allowed)){
            die("Format gambar tidak valid");
        }

        $nama_file = time().".".$ext;
        $path = "../uploads/barang/".$nama_file;

        if(move_uploaded_file($_FILES['gambar']['tmp_name'],$path)){
            if(!empty($row['gambar']) && file_exists("../uploads/barang/".$row['gambar'])){
                unlink("../uploads/barang/".$row['gambar']);
            }
        }
    }

    $stmt = mysqli_prepare($conn,"
        UPDATE barang SET
        kategori_id=?,
        nama_barang=?,
        stok=?,
        gambar=?
        WHERE id=?
    ");

    mysqli_stmt_bind_param($stmt,"isisi",
        $kategori_id,
        $nama,
        $stok,
        $nama_file,
        $id
    );

    mysqli_stmt_execute($stmt);

    header("Location: barang.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Edit Barang</title>
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
max-width:500px;
}

/* FORM */
.form-group{
margin-bottom:18px;
}

label{
display:block;
margin-bottom:6px;
font-weight:500;
}

input,select{
width:100%;
padding:10px;
border-radius:8px;
border:1px solid #ccc;
}

/* BUTTON */
.btn{
padding:8px 18px;
border-radius:20px;
text-decoration:none;
font-size:13px;
font-weight:500;
border:none;
cursor:pointer;
}

.btn-update{
background:#22c55e;
color:white;
}

.btn-back{
background:#64748b;
color:white;
margin-left:10px;
}

/* IMAGE */
.img-preview{
width:100px;
border-radius:10px;
margin-bottom:10px;
}

</style>

</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
<h2>SIBONPASBI</h2>

<a href="dashboard.php"><i data-lucide="home"></i> Dashboard</a>
<a href="barang.php" class="active"><i data-lucide="package"></i> Barang</a>
<a href="kategori.php"><i data-lucide="tag"></i> Kategori</a>
<a href="permohonan.php"><i data-lucide="clipboard-list"></i> Permohonan</a>
<a href="saran.php"><i data-lucide="message-circle"></i> Kritik & Saran</a>
</div>

<!-- MAIN -->
<div class="main">

<div class="topbar">
<h2>Edit Barang</h2>

<div>
Halo, <?= htmlspecialchars($_SESSION['nama']); ?>
<a href="../auth/logout.php" class="logout">Logout</a>
</div>
</div>

<div class="card">

<form method="POST" enctype="multipart/form-data">

<div class="form-group">
<label>Nama Barang</label>
<input type="text" name="nama" value="<?= htmlspecialchars($row['nama_barang']); ?>" required>
</div>

<div class="form-group">
<label>Kategori</label>
<select name="kategori">
<?php while($k=mysqli_fetch_assoc($kategori)){ ?>
<option value="<?= $k['id']; ?>" <?= $row['kategori_id']==$k['id']?'selected':''; ?>>
<?= htmlspecialchars($k['nama_kategori']); ?>
</option>
<?php } ?>
</select>
</div>

<div class="form-group">
<label>Stok</label>
<input type="number" name="stok" value="<?= $row['stok']; ?>" required>
</div>

<div class="form-group">
<label>Gambar</label>

<?php if(!empty($row['gambar'])){ ?>
<img src="../uploads/barang/<?= htmlspecialchars($row['gambar']); ?>" class="img-preview">
<?php } ?>

<input type="file" name="gambar">
</div>

<button class="btn btn-update" name="update">
<i data-lucide="save"></i> Update
</button>

<a href="barang.php" class="btn btn-back">Kembali</a>

</form>

</div>

</div>

<script>
lucide.createIcons();
</script>

</body>
</html>