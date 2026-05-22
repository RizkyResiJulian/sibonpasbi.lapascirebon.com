<?php
session_start();

if(!isset($_SESSION['login'])){
    header("Location: ../auth/login.php");
    exit;
}

if($_SESSION['role']!="admin"){
    echo "Akses ditolak";
    exit;
}

include "../config/koneksi.php";

// =======================
// FUNCTION STATUS OTOMATIS
// =======================
function getStatus($row){
    if(!empty($row['bukti_penyerahan'])){
        return "selesai";
    }
    if($row['verifikasi'] == "ditolak"){
        return "batal";
    }
    if($row['verifikasi'] == "disetujui"){
        return "diproses";
    }
    return "pending";
}

// =======================
// UPDATE VERIFIKASI
// =======================
if(isset($_POST['update_verifikasi'])){

    $id = (int)$_POST['id'];
    $verifikasi = $_POST['verifikasi'];

    $allowed = ['pending','disetujui','ditolak'];
    if(!in_array($verifikasi,$allowed)){
        die("Verifikasi tidak valid");
    }

    // =======================
    // TENTUKAN STATUS OTOMATIS
    // =======================

    $status = "pending";

    if($verifikasi == "disetujui"){
        $status = "diproses";
    }
    elseif($verifikasi == "ditolak"){
        $status = "batal";
    }
    elseif($verifikasi == "pending"){
        $status = "pending";
    }

    // =======================
    // UPDATE VERIFIKASI + STATUS
    // =======================

    $stmt = mysqli_prepare($conn,"
        UPDATE permohonan 
        SET verifikasi=?, status=? 
        WHERE id=?
    ");

    mysqli_stmt_bind_param(
        $stmt,
        "ssi",
        $verifikasi,
        $status,
        $id
    );

    mysqli_stmt_execute($stmt);

    header("Location: permohonan.php");
    exit;
}

// =======================
// AMBIL DATA
// =======================
$data = mysqli_query($conn,"
SELECT 
    p.*,
    GROUP_CONCAT(CONCAT(b.nama_barang,' x ',d.jumlah) SEPARATOR '<br>') as barang_list
FROM permohonan p
LEFT JOIN detail_permohonan d ON p.id = d.permohonan_id
LEFT JOIN barang b ON d.barang_id = b.id
GROUP BY p.id
ORDER BY p.id DESC
");

if(!$data){
    die("Query error: ".mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Permohonan Barang</title>
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

.card{
background:white;
padding:25px;
border-radius:15px;
box-shadow:var(--shadow);
}

.search{
padding:10px;
border-radius:10px;
border:1px solid #ddd;
min-width:250px;
margin-bottom:20px;
}

.table-container{
max-height:420px;
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

tr:hover{background:#f8fafc;}

.status{
padding:5px 12px;
border-radius:20px;
font-size:12px;
color:white;
}

.pending{background:#f59e0b;}
.diproses{background:#3b82f6;}
.selesai{background:#22c55e;}
.batal{background:#ef4444;}

.badge{
display:inline-block;
padding:4px 10px;
border-radius:20px;
font-size:12px;
color:white;
margin-bottom:5px;
}

.ver-pending{background:#f59e0b;}
.ver-disetujui{background:#3b82f6;}
.ver-ditolak{background:#ef4444;}

.btn{
padding:6px 12px;
background:var(--accent);
border:none;
border-radius:6px;
cursor:pointer;
font-weight:500;
margin-top:5px;
}

.btn-print{
background:#22c55e;
color:white;
}

.btn-icon{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:34px;
    height:34px;
    border-radius:8px;
    background:#e2e8f0; /* abu soft */
    color:#0f172a;
    transition:0.2s;
    text-decoration:none;
}

.btn-icon:hover{
    background:#cbd5f5; /* hover soft */
    transform:scale(1.05);
}

.btn-icon i{
    width:18px;
    height:18px;
}

button:disabled{
    background:#ccc;
    cursor:not-allowed;
}
</style>

<script>
function cari(){
let input = document.getElementById("search").value.toLowerCase();
let rows = document.querySelectorAll("tbody tr");

rows.forEach(row=>{
let nama = row.children[2].innerText.toLowerCase();
let barang = row.children[3].innerText.toLowerCase();

row.style.display = (nama.includes(input) || barang.includes(input)) ? "" : "none";
});
}
</script>

</head>

<body>

<div class="sidebar">
<h2>SIBONPASBI</h2>

<a href="dashboard.php"><i data-lucide="home"></i> Dashboard</a>
<a href="barang.php"><i data-lucide="package"></i> Barang</a>
<a href="kategori.php"><i data-lucide="tag"></i> Kategori</a>
<a href="permohonan.php" class="active"><i data-lucide="clipboard-list"></i> Permohonan</a>
<a href="saran.php"><i data-lucide="message-circle"></i> Kritik & Saran</a>
<a href="laporan.php"><i data-lucide="file-text"></i> Laporan</a>
</div>

<div class="main">

<div class="topbar">
<h2>Permohonan Barang</h2>

<div>
Halo, <?= htmlspecialchars($_SESSION['nama']); ?>
<a href="../auth/logout.php" class="logout">Logout</a>
</div>
</div>

<div class="card">

<input type="text" id="search" class="search" placeholder="Cari..." onkeyup="cari()">

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
<th>Aksi</th>
<th>Bukti Penyerahan</th>
</tr>
</thead>

<tbody>

<?php 
$no=1; 
while($row=mysqli_fetch_assoc($data)){ 
$status = getStatus($row);
?>

<tr>

<td><?= $no++; ?></td>

<td><?= htmlspecialchars($row['tanggal_pesan']); ?></td>

<td><?= htmlspecialchars($row['nama']); ?></td>

<td><?= $row['barang_list'] ?: 'Tidak ada'; ?></td>

<!-- STATUS -->
<td>
<span class="status <?= $status; ?>">
<?= $status; ?>
</span>
</td>

<!-- VERIFIKASI -->
<td>
<span class="badge ver-<?= $row['verifikasi']; ?>">
<?= $row['verifikasi']; ?>
</span>
</td>

<!-- AKSI -->
<td>
<form method="POST">

<input type="hidden" name="id" value="<?= $row['id']; ?>">

<select name="verifikasi">

<option value="pending"
<?= $row['verifikasi']=="pending"?"selected":"" ?>>
Pending
</option>

<option value="disetujui"
<?= $row['verifikasi']=="disetujui"?"selected":"" ?>>
Disetujui
</option>

<option value="ditolak"
<?= $row['verifikasi']=="ditolak"?"selected":"" ?>>
Ditolak
</option>

</select>

<button name="update_verifikasi" class="btn">
Update
</button>

</form>
</td>

<!-- BUKTI -->
<td align="center">

<?php if(!empty($row['bukti_penyerahan'])){ ?>

<a href="../uploads/bukti/<?= $row['bukti_penyerahan']; ?>" target="_blank">

<img 
src="../uploads/bukti/<?= $row['bukti_penyerahan']; ?>" 
width="70"
style="
border-radius:8px;
border:1px solid #ddd;
object-fit:cover;
">

</a>

<?php } else { ?>

<span style="font-size:12px;color:#999;">
Belum upload
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