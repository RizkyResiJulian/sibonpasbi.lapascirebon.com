<?php
session_start();

if(!isset($_SESSION['login'])){
    header("Location: ../auth/login.php");
    exit;
}

if($_SESSION['role'] != "operator"){
    echo "Akses ditolak";
    exit;
}

include "../config/koneksi.php";

// =======================
// UPLOAD BUKTI
// =======================

if(isset($_POST['upload_bukti'])){

    $id = (int)$_POST['id'];

    // =======================
    // CEK DATA PERMOHONAN
    // =======================

    $cek = mysqli_query($conn,"
        SELECT *
        FROM permohonan
        WHERE id='$id'
    ");

    $c = mysqli_fetch_assoc($cek);

    if(!$c){
        die("Data tidak ditemukan");
    }

    // =======================
    // VALIDASI VERIFIKASI
    // =======================

    if($c['verifikasi'] != 'disetujui'){
        die("Permohonan belum disetujui");
    }

    // =======================
    // CEK SUDAH UPLOAD
    // =======================

    if(!empty($c['bukti_penyerahan'])){
        header("Location: permohonan.php");
        exit;
    }

    // =======================
    // VALIDASI FILE
    // =======================

    if(empty($_FILES['bukti']['name'])){
        die("File belum dipilih");
    }

    if($_FILES['bukti']['error'] != 0){
        die("Upload gagal");
    }

    $ext = strtolower(pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION));

    $allowed = ['jpg','jpeg','png','webp'];

    if(!in_array($ext, $allowed)){
        die("Format file tidak didukung");
    }

    // =======================
    // FOLDER UPLOAD
    // =======================

    if(!is_dir("../uploads/bukti")){
        mkdir("../uploads/bukti",0777,true);
    }

    // =======================
    // NAMA FILE
    // =======================

    $nama_file = "bukti_" . time() . "_" . rand(1000,9999) . "." . $ext;

    $path = "../uploads/bukti/" . $nama_file;

    // =======================
    // PROSES UPLOAD
    // =======================

    if(move_uploaded_file($_FILES['bukti']['tmp_name'], $path)){

        // =======================
        // UPDATE STATUS DATABASE
        // =======================

        $update = mysqli_query($conn,"
            UPDATE permohonan
            SET
                bukti_penyerahan = '$nama_file',
                status = 'selesai'
            WHERE id = '$id'
        ");

        if(!$update){
            die(mysqli_error($conn));
        }

        // =======================
        // AMBIL DETAIL BARANG
        // =======================

        $detail = mysqli_query($conn,"
            SELECT barang_id, jumlah
            FROM detail_permohonan
            WHERE permohonan_id = '$id'
        ");

        while($d = mysqli_fetch_assoc($detail)){

            $barang_id = (int)$d['barang_id'];
            $qty = (int)$d['jumlah'];

            // =======================
            // KURANGI STOK
            // =======================

            mysqli_query($conn,"
                UPDATE barang
                SET stok = stok - $qty
                WHERE id = '$barang_id'
            ");

            // =======================
            // LOG STOK
            // =======================

            mysqli_query($conn,"
                INSERT INTO stok_log
                (
                    barang_id,
                    jenis,
                    jumlah,
                    keterangan,
                    created_at
                )
                VALUES
                (
                    '$barang_id',
                    'keluar',
                    '$qty',
                    'Penyerahan permohonan ID $id',
                    NOW()
                )
            ");
        }

    } else {

        die("Gagal upload file");

    }

    header("Location: permohonan.php");
    exit;
}

// =======================
// AMBIL DATA PERMOHONAN
// =======================

$data = mysqli_query($conn,"
    SELECT
        p.*,
        GROUP_CONCAT(
            CONCAT(
                b.nama_barang,
                ' x ',
                d.jumlah
            )
            SEPARATOR '<br>'
        ) as barang_list

    FROM permohonan p

    LEFT JOIN detail_permohonan d
    ON p.id = d.permohonan_id

    LEFT JOIN barang b
    ON d.barang_id = b.id

    GROUP BY p.id

    ORDER BY p.id DESC
");

if(!$data){
    die("Query Error : " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Permohonan Barang</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<script src="https://unpkg.com/lucide@latest"></script>

<style>

:root{
--primary:#0f172a;
--accent:#d4af37;
--bg:#f1f5f9;
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
}

/* SEARCH */

.search{
padding:10px;
border-radius:10px;
border:1px solid #ddd;
min-width:250px;
margin-bottom:20px;
}

/* TABLE */

.table-container{
max-height:500px;
overflow-y:auto;
border-radius:10px;
border:1px solid #eee;
}

table{
width:100%;
border-collapse:collapse;
}

th{
background:var(--primary);
color:white;
padding:12px;
position:sticky;
top:0;
}

td{
padding:12px;
border-bottom:1px solid #eee;
vertical-align:top;
}

tr:hover{
background:#f8fafc;
}

/* STATUS */

.status{
padding:5px 12px;
border-radius:20px;
font-size:12px;
color:white;
font-weight:500;
}

.pending{
background:#f59e0b;
}

.diproses{
background:#3b82f6;
}

.selesai{
background:#22c55e;
}

.batal{
background:#ef4444;
}

/* VERIFIKASI */

.badge{
display:inline-block;
padding:4px 10px;
border-radius:20px;
font-size:12px;
color:white;
font-weight:500;
}

.ver-pending{
background:#f59e0b;
}

.ver-disetujui{
background:#3b82f6;
}

.ver-ditolak{
background:#ef4444;
}

/* BUTTON */

.btn{
padding:6px 12px;
background:var(--accent);
border:none;
border-radius:6px;
cursor:pointer;
font-weight:500;
margin-top:5px;
}

.btn:disabled{
background:#ccc;
cursor:not-allowed;
}

/* ICON BUTTON */

.btn-icon{
display:inline-flex;
align-items:center;
justify-content:center;
width:36px;
height:36px;
border-radius:8px;
background:#e2e8f0;
color:#0f172a;
text-decoration:none;
transition:.2s;
}

.btn-icon:hover{
background:#cbd5e1;
transform:scale(1.05);
}

/* IMAGE */

.preview{
margin-top:10px;
width:70px;
border-radius:8px;
border:1px solid #ddd;
}

/* MOBILE */

@media(max-width:768px){

body{
flex-direction:column;
}

.sidebar{
width:100%;
}

.main{
padding:20px;
}

.topbar{
flex-direction:column;
align-items:flex-start;
gap:10px;
}

.search{
width:100%;
min-width:100%;
}

table{
font-size:13px;
}

}

</style>

<script>

function cari(){

    let input = document.getElementById("search").value.toLowerCase();

    let rows = document.querySelectorAll("tbody tr");

    rows.forEach(row=>{

        let nama = row.children[2].innerText.toLowerCase();

        let barang = row.children[3].innerText.toLowerCase();

        if(
            nama.includes(input) ||
            barang.includes(input)
        ){
            row.style.display = "";
        } else {
            row.style.display = "none";
        }

    });

}

</script>

</head>

<body>

<!-- SIDEBAR -->

<div class="sidebar">

<h2>SIBONPASBI</h2>

<a href="dashboard.php">
<i data-lucide="home"></i>
Dashboard
</a>

<a href="barang.php">
<i data-lucide="package"></i>
Barang
</a>

<a href="kategori.php">
<i data-lucide="tag"></i>
Kategori
</a>

<a href="permohonan.php" class="active">
<i data-lucide="clipboard-list"></i>
Permohonan
</a>

<a href="saran.php">
<i data-lucide="message-circle"></i>
Kritik & Saran
</a>

</div>

<!-- MAIN -->

<div class="main">

<div class="topbar">

<h2>Permohonan Barang</h2>

<div>
Halo, <?= htmlspecialchars($_SESSION['nama']); ?>
<a href="../auth/logout.php" class="logout">
Logout
</a>
</div>

</div>

<div class="card">

<input
type="text"
id="search"
class="search"
placeholder="Cari nama atau barang..."
onkeyup="cari()"
>

<div class="table-container">

<table>

<thead>

<tr>
<th>No</th>
<th>Tanggal</th>
<th>Nama Pemohon</th>
<th>Barang & Jumlah</th>
<th>Status</th>
<th>Verifikasi</th>
<th>Upload Bukti</th>
<th>Cetak</th>
</tr>

</thead>

<tbody>

<?php
$no = 1;

while($row = mysqli_fetch_assoc($data)){

$status = $row['status'];
?>

<tr>

<td><?= $no++; ?></td>

<td>
<?= htmlspecialchars($row['tanggal_pesan']); ?>
</td>

<td>
<?= htmlspecialchars($row['nama']); ?>
</td>

<td>
<?= $row['barang_list'] ?: 'Tidak ada'; ?>
</td>

<td>
<span class="status <?= $status; ?>">
<?= ucfirst($status); ?>
</span>
</td>

<td>
<span class="badge ver-<?= $row['verifikasi']; ?>">
<?= ucfirst($row['verifikasi']); ?>
</span>
</td>

<td>

<form method="POST" enctype="multipart/form-data">

<input
type="hidden"
name="id"
value="<?= $row['id']; ?>"
>

<input
type="file"
name="bukti"
<?= ($row['verifikasi'] != 'disetujui' || !empty($row['bukti_penyerahan'])) ? 'disabled' : ''; ?>
>

<br>

<button
type="submit"
name="upload_bukti"
class="btn"
<?= ($row['verifikasi'] != 'disetujui' || !empty($row['bukti_penyerahan'])) ? 'disabled' : ''; ?>
>
Upload
</button>

</form>

<?php if(!empty($row['bukti_penyerahan'])){ ?>

<a
href="../uploads/bukti/<?= $row['bukti_penyerahan']; ?>"
target="_blank"
>

<img
src="../uploads/bukti/<?= $row['bukti_penyerahan']; ?>"
class="preview"
>

</a>

<?php } ?>

</td>

<td align="center">

<?php if(!empty($row['bukti_penyerahan'])){ ?>

<a
href="cetak_bon.php?id=<?= $row['id']; ?>"
target="_blank"
class="btn-icon"
title="Cetak Bon"
>
<i data-lucide="printer"></i>
</a>

<?php } else { ?>

<span style="font-size:12px;color:#999;">
Belum selesai
</span>

<?php } ?>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>

<script>
lucide.createIcons();
</script>

</body>
</html>