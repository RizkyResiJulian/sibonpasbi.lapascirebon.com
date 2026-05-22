<?php
session_start();

if(!isset($_SESSION['login'])){
    header("Location: ../auth/login.php");
    exit;
}

$allowed = ['ppk','kalapas','bendahara'];

if(!in_array($_SESSION['role'],$allowed)){
    echo "Akses ditolak";
    exit;
}

include "../config/koneksi.php";

$role    = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

/* =========================
   FILTER STATUS
========================= */

$whereStatus = "";

if($role == "ppk"){

    $whereStatus = "permohonan_uang.status='pending'";

}
elseif($role == "kalapas"){

    $whereStatus = "permohonan_uang.status='approve_ppk'";

}
elseif($role == "bendahara"){

    $whereStatus = "
    (
        permohonan_uang.status='approve_kalapas'
        OR permohonan_uang.status='lpj'
        OR permohonan_uang.status='revisi'
    )
    ";
}

/* =========================
   PROSES APPROVE
========================= */

if(isset($_POST['approve'])){

    $id       = (int)$_POST['id'];
    $catatan  = mysqli_real_escape_string($conn,$_POST['catatan']);

    if($role == "ppk"){

        mysqli_query($conn,"
        UPDATE permohonan_uang
        SET
        status='approve_ppk',
        approve_ppk_by='$user_id',
        approve_ppk_at=NOW()
        WHERE id='$id'
        ");

        $aksi = "Approve PPK";
    }

    elseif($role == "kalapas"){

        mysqli_query($conn,"
        UPDATE permohonan_uang
        SET
        status='approve_kalapas',
        approve_kalapas_by='$user_id',
        approve_kalapas_at=NOW()
        WHERE id='$id'
        ");

        $aksi = "Approve Kalapas";
    }

    elseif($role == "bendahara"){

        $cek = mysqli_fetch_assoc(mysqli_query($conn,"
        SELECT status
        FROM permohonan_uang
        WHERE id='$id'
        "));

        /* CAIRKAN DANA */

        if($cek['status'] == 'approve_kalapas'){

            mysqli_query($conn,"
            UPDATE permohonan_uang
            SET
            status='dicairkan',
            dicairkan_by='$user_id',
            dicairkan_at=NOW()
            WHERE id='$id'
            ");

            $aksi = "Dana Dicairkan";
        }

        /* VERIFIKASI LPJ */

        elseif($cek['status'] == 'lpj'){

            mysqli_query($conn,"
            UPDATE permohonan_uang
            SET
            status='selesai'
            WHERE id='$id'
            ");

            $aksi = "LPJ Diverifikasi";
        }
    }

    mysqli_query($conn,"
    INSERT INTO log_approval_uang(
    pengajuan_id,
    user_id,
    role,
    aksi,
    catatan
    ) VALUES(
    '$id',
    '$user_id',
    '$role',
    '$aksi',
    '$catatan'
    )
    ");

    header("Location: approval_pengajuan.php");
    exit;
}

/* =========================
   PROSES TOLAK
========================= */

if(isset($_POST['tolak'])){

    $id       = (int)$_POST['id'];
    $catatan  = mysqli_real_escape_string($conn,$_POST['catatan']);

    $cek = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT status
    FROM permohonan_uang
    WHERE id='$id'
    "));

    // =========================
    // KHUSUS LPJ -> REVISI
    // =========================

    if($role == "bendahara" && $cek['status'] == 'lpj'){

        mysqli_query($conn,"
        UPDATE permohonan_uang
        SET status='revisi'
        WHERE id='$id'
        ");

        $aksi = "LPJ Revisi";

    }else{

        mysqli_query($conn,"
        UPDATE permohonan_uang
        SET status='ditolak'
        WHERE id='$id'
        ");

        $aksi = "Ditolak";
    }

    mysqli_query($conn,"
    INSERT INTO log_approval_uang(
    pengajuan_id,
    user_id,
    role,
    aksi,
    catatan
    ) VALUES(
    '$id',
    '$user_id',
    '$role',
    '$aksi',
    '$catatan'
    )
    ");

    header("Location: approval_pengajuan.php");
    exit;
}

/* =========================
   QUERY
========================= */

$query = mysqli_query($conn,"
SELECT 
permohonan_uang.*,
users.nama,
users.nip
FROM permohonan_uang
LEFT JOIN users
ON permohonan_uang.user_id = users.id
WHERE $whereStatus
ORDER BY permohonan_uang.id DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Approval Pengajuan</title>
<link rel="shortcut icon" href="../assets/img/logo1.ico">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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
--warning:#f59e0b;
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

.main{
flex:1;
padding:30px;
}

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

.card{
background:white;
border-radius:24px;
padding:25px;
margin-bottom:25px;
box-shadow:0 10px 25px rgba(0,0,0,.08);
transition:.3s;
}

.card:hover{
transform:translateY(-4px);
}

.card-header{
display:flex;
justify-content:space-between;
align-items:center;
flex-wrap:wrap;
gap:15px;
margin-bottom:20px;
}

.card-header h3{
font-size:22px;
color:var(--primary);
}

.card-header p{
color:var(--gray);
font-size:14px;
margin-top:5px;
}

.status{
padding:8px 15px;
border-radius:999px;
background:#fef3c7;
color:#92400e;
font-size:13px;
font-weight:600;
}

.info-grid{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
gap:18px;
margin-bottom:25px;
}

.info-box{
background:#f8fafc;
padding:18px;
border-radius:18px;
}

.info-box span{
font-size:13px;
color:var(--gray);
display:block;
margin-bottom:5px;
}

.info-box h4{
font-size:16px;
color:var(--primary);
}

.keperluan{
background:#f8fafc;
padding:20px;
border-radius:18px;
margin-bottom:25px;
line-height:1.8;
color:#475569;
}

.table-wrapper{
overflow-x:auto;
border-radius:18px;
border:1px solid #eee;
}

table{
width:100%;
border-collapse:collapse;
min-width:700px;
}

th{
background:#f8fafc;
padding:15px;
font-size:14px;
text-align:left;
color:var(--primary);
}

td{
padding:15px;
border-top:1px solid #eee;
font-size:14px;
}

.total{
margin-top:20px;
display:flex;
justify-content:flex-end;
}

.total-box{
background:#0f172a;
padding:18px 25px;
border-radius:18px;
color:white;
text-align:right;
min-width:250px;
}

.total-box h2{
margin-top:6px;
color:#facc15;
}

textarea{
width:100%;
height:110px;
padding:18px;
border:none;
border-radius:18px;
background:#f8fafc;
margin-top:25px;
resize:none;
outline:none;
font-size:14px;
}

.action{
display:flex;
gap:15px;
margin-top:20px;
flex-wrap:wrap;
}

.btn{
border:none;
padding:14px 22px;
border-radius:16px;
font-weight:600;
cursor:pointer;
display:flex;
align-items:center;
gap:10px;
transition:.3s;
}

.btn:hover{
transform:translateY(-2px);
}

.btn-approve{
background:var(--success);
color:white;
}

.btn-reject{
background:var(--danger);
color:white;
}

.lpj-box{
margin-top:25px;
padding:20px;
border-radius:18px;
background:#ecfdf5;
border:1px solid #bbf7d0;
display:flex;
justify-content:space-between;
align-items:center;
flex-wrap:wrap;
gap:15px;
}

.lpj-box h4{
color:#166534;
margin-bottom:6px;
}

.lpj-box p{
color:#166534;
font-size:14px;
}

.btn-lpj{
padding:12px 18px;
border-radius:14px;
background:#166534;
color:white;
text-decoration:none;
font-weight:600;
display:flex;
align-items:center;
gap:8px;
}

.empty{
background:white;
padding:60px 30px;
border-radius:24px;
text-align:center;
box-shadow:0 10px 25px rgba(0,0,0,.08);
}

.empty i{
width:70px;
height:70px;
margin-bottom:15px;
color:#cbd5e1;
}

.empty h3{
margin-bottom:10px;
color:var(--primary);
}

.empty p{
color:var(--gray);
}

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

<div class="sidebar">

<h2>SIBONPASBI</h2>

<a href="dashboard.php">
<i data-lucide="layout-dashboard"></i>
Dashboard
</a>

<a href="approval_pengajuan.php" class="active">
<i data-lucide="clipboard-check"></i>
Approval Pengajuan
</a>

<a href="riwayat_pengajuan.php">
<i data-lucide="history"></i>
Riwayat Pengajuan
</a>

</div>

<div class="main">

<div class="topbar">

<div>
<h2>Approval Pengajuan</h2>
<p>Verifikasi dan approval pengajuan bon uang</p>
</div>

<div class="user-info">

<div class="badge">
<?= strtoupper($_SESSION['role']); ?>
</div>

<div>
Halo, <b><?= $_SESSION['nama']; ?></b> 👋
</div>

<a href="../auth/logout.php" class="logout">
<i data-lucide="log-out"></i>
Logout
</a>

</div>

</div>

<?php if(mysqli_num_rows($query) > 0){ ?>

<?php while($data = mysqli_fetch_assoc($query)){ ?>

<?php

$id = $data['id'];

$detail = mysqli_query($conn,"
SELECT *
FROM detail_pengajuan_uang
WHERE pengajuan_id='$id'
");
?>

<div class="card">

<div class="card-header">

<div>

<h3><?= $data['nomor_pengajuan']; ?></h3>

<p>
<?= date('d M Y',strtotime($data['tanggal'])); ?>
</p>

</div>

<div class="status">

<?php
if($data['status'] == 'lpj'){
    echo "Menunggu Verifikasi LPJ";
}else{
    echo "Menunggu Approval";
}
?>

</div>

</div>

<div class="info-grid">

<div class="info-box">
<span>Nama Pegawai</span>
<h4><?= $data['nama']; ?></h4>
</div>

<div class="info-box">
<span>NIP</span>
<h4><?= $data['nip']; ?></h4>
</div>

<div class="info-box">
<span>Bidang</span>
<h4><?= $data['bidang']; ?></h4>
</div>

</div>

<div class="keperluan">

<b>Keperluan :</b>

<div style="margin-top:10px;">
<?= nl2br(htmlspecialchars($data['keperluan'])); ?>
</div>

</div>

<div class="table-wrapper">

<table>

<thead>

<tr>
<th>Uraian</th>
<th>Subtotal</th>
</tr>

</thead>

<tbody>

<?php while($d = mysqli_fetch_assoc($detail)){ ?>

<tr>

<td><?= htmlspecialchars($d['uraian']); ?></td>

<td>
Rp <?= number_format($d['subtotal'],0,',','.'); ?>
</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

<div class="total">

<div class="total-box">

<p>Total Pengajuan</p>

<h2>
Rp <?= number_format($data['total'],0,',','.'); ?>
</h2>

</div>

</div>

<?php if($data['status'] == 'lpj'){ ?>

<div class="lpj-box">

<div>

<h4>LPJ Sudah Diupload</h4>

<p>
Silahkan lakukan verifikasi LPJ pegawai.
</p>

</div>

<a 
href="../uploads/lpj/<?= $data['file_lpj']; ?>"
target="_blank"
class="btn-lpj">

<i data-lucide="file-text"></i>
Lihat LPJ

</a>

</div>

<?php } ?>

<form method="POST">

<input type="hidden" name="id" value="<?= $data['id']; ?>">

<textarea
name="catatan"
placeholder="Tambahkan catatan approval / penolakan (opsional)..."
></textarea>

<div class="action">

    <?php if($role == "bendahara" && $data['status'] == 'lpj'){ ?>

        <button type="submit" name="approve" class="btn btn-approve">
            <i data-lucide="check"></i>
            Verifikasi LPJ (Selesai)
        </button>

    <?php } elseif($role == "bendahara" && $data['status'] == 'approve_kalapas'){ ?>

        <button type="submit" name="approve" class="btn btn-approve">
            <i data-lucide="check"></i>
            Cairkan Dana
        </button>

    <?php } else { ?>

        <button type="submit" name="approve" class="btn btn-approve">
            <i data-lucide="check"></i>
            Approve
        </button>

    <?php } ?>

    <?php if($role == "bendahara" && $data['status'] == 'lpj'){ ?>

        <button type="submit" name="tolak" class="btn btn-reject">
            <i data-lucide="refresh-ccw"></i>
            Minta Revisi LPJ
        </button>

    <?php } else { ?>

        <button type="submit" name="tolak" class="btn btn-reject">
            <i data-lucide="x"></i>
            Tolak
        </button>

    <?php } ?>

</div>

</form>

</div>

<?php } ?>

<?php } else { ?>

<div class="empty">

<i data-lucide="inbox"></i>

<h3>Tidak Ada Pengajuan</h3>

<p>
Belum ada pengajuan yang perlu diproses saat ini.
</p>

</div>

<?php } ?>

</div>

<script>
lucide.createIcons();
</script>

</body>
</html>