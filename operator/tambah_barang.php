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

/* AMBIL KATEGORI */
$kategori = mysqli_query($conn,"SELECT * FROM kategori");

/* SIMPAN DATA */
if(isset($_POST['simpan'])){

    $kategori_id = intval($_POST['kategori']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $stok = intval($_POST['stok']);

    $nama_file = NULL;

    /* UPLOAD GAMBAR */
    if(!empty($_FILES['gambar']['name'])){

        $gambar = $_FILES['gambar']['name'];
        $tmp = $_FILES['gambar']['tmp_name'];

        $ext = strtolower(pathinfo($gambar, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];

        if(in_array($ext,$allowed)){

            $nama_file = time()."_".uniqid().".".$ext;
            $path = "../uploads/barang/".$nama_file;

            if(!move_uploaded_file($tmp,$path)){
                echo "<script>alert('Gagal upload gambar');</script>";
                $nama_file = NULL;
            }

        }else{
            echo "<script>alert('Format gambar tidak didukung!');</script>";
        }
    }

    /* INSERT */
    $query = mysqli_query($conn,"
    INSERT INTO barang (kategori_id,nama_barang,stok,gambar)
    VALUES ('$kategori_id','$nama','$stok','$nama_file')
    ");

    if($query){
        header("Location: barang.php");
        exit;
    }else{
        echo "Error: ".mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Tambah Barang</title>
<link rel="shortcut icon" href="../assets/img/logo1.ico">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<script src="https://unpkg.com/lucide@latest"></script>

<style>

:root{
--primary:#0f172a;
--accent:#d4af37;
--bg:#f1f5f9;
--shadow:0 10px 25px rgba(0,0,0,0.08);
}

*{margin:0;padding:0;box-sizing:border-box;font-family:Poppins;}

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

.sidebar h2{margin-bottom:30px;}

.sidebar a{
color:white;
text-decoration:none;
margin-bottom:12px;
display:flex;
align-items:center;
gap:10px;
padding:10px;
border-radius:8px;
}

.sidebar a:hover{background:rgba(255,255,255,0.1);}
.sidebar a.active{background:rgba(255,255,255,0.15);}

/* MAIN */
.main{flex:1;padding:30px;}

.topbar{
display:flex;
justify-content:space-between;
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
padding:35px;
border-radius:20px;
box-shadow:var(--shadow);
max-width:550px;
}

/* FORM */
.form-group{margin-bottom:20px;}

label{
display:block;
margin-bottom:8px;
font-weight:500;
}

input,select{
width:100%;
padding:12px;
border-radius:10px;
border:1px solid #e2e8f0;
transition:.2s;
}

input:focus,select:focus{
border-color:var(--accent);
outline:none;
box-shadow:0 0 0 2px rgba(212,175,55,0.2);
}

/* BUTTON */
.btn{
padding:10px 20px;
border-radius:25px;
border:none;
cursor:pointer;
font-weight:500;
}

.btn-save{
background:var(--accent);
color:#0f172a;
}

.btn-back{
background:#e2e8f0;
color:#334155;
text-decoration:none;
margin-left:10px;
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
<h2>Tambah Barang</h2>

<div>
Halo, <?= htmlspecialchars($_SESSION['nama']); ?>
<a href="../auth/logout.php" class="logout">Logout</a>
</div>
</div>

<div class="card">

<form method="POST" enctype="multipart/form-data">

<div class="form-group">
<label>Nama Barang</label>
<input type="text" name="nama" required>
</div>

<div class="form-group">
<label>Kategori</label>
<select name="kategori" required>
<option value="">-- Pilih Kategori --</option>
<?php while($k=mysqli_fetch_assoc($kategori)){ ?>
<option value="<?= $k['id']; ?>"><?= $k['nama_kategori']; ?></option>
<?php } ?>
</select>
</div>

<div class="form-group">
<label>Stok</label>
<input type="number" name="stok" required>
</div>

<div class="form-group">
<label>Gambar Barang</label>
<input type="file" name="gambar" accept="image/*">
</div>

<button class="btn btn-save" name="simpan">
<i data-lucide="save"></i> Simpan
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