<?php
include "../config/koneksi.php";

// =======================
// AMBIL & VALIDASI INPUT
// =======================
$nama   = trim($_POST['nama'] ?? '');
$nip    = trim($_POST['nip'] ?? '');
$bidang = trim($_POST['bidang'] ?? '');
$jumlah = $_POST['jumlah'] ?? [];

if(empty($nama) || empty($nip) || empty($bidang)){
    die("<h3>Data tidak lengkap</h3><a href='order.php'>Kembali</a>");
}

// =======================
// CEK ADA BARANG DIPILIH
// =======================
$ada = false;
foreach($jumlah as $id_barang => $qty){
    if((int)$qty > 0){
        $ada = true;
        break;
    }
}

if(!$ada){
    die("<h3>Anda belum memilih barang</h3><a href='order.php'>Kembali</a>");
}

// =======================
// MULAI TRANSAKSI
// =======================
mysqli_begin_transaction($conn);

try {

    // =======================
    // BUAT KODE PERMOHONAN
    // =======================
    $kode_permohonan = "PMH".date("YmdHis");

    // =======================
    // INSERT PERMOHONAN
    // =======================
    $status = "pending"; // default
    $verifikasi = "pending"; // default

    $stmt = mysqli_prepare($conn,"
        INSERT INTO permohonan 
        (nomor_permohonan, nama, nip, bidang, status, verifikasi, tanggal_pesan)
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");

    if(!$stmt){
        throw new Exception("Prepare gagal (permohonan): " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "ssssss",
        $kode_permohonan,
        $nama,
        $nip,
        $bidang,
        $status,
        $verifikasi
    );

    if(!mysqli_stmt_execute($stmt)){
        throw new Exception("Gagal insert permohonan");
    }

    mysqli_stmt_close($stmt);

    $id_permohonan = mysqli_insert_id($conn);

    // =======================
    // LOOP DETAIL BARANG
    // =======================
    foreach($jumlah as $id_barang => $qty){

        $id_barang = (int)$id_barang;
        $qty = (int)$qty;

        if($qty > 0){

            // =======================
            // CEK STOK (TANPA NGURANGIN)
            // =======================
            $stmt = mysqli_prepare($conn,"
                SELECT nama_barang, stok 
                FROM barang 
                WHERE id = ?
            ");

            if(!$stmt){
                throw new Exception("Prepare cek stok gagal: " . mysqli_error($conn));
            }

            mysqli_stmt_bind_param($stmt,"i",$id_barang);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            $data = mysqli_fetch_assoc($result);

            mysqli_stmt_close($stmt);

            if(!$data){
                throw new Exception("Barang tidak ditemukan");
            }

            if($qty > $data['stok']){
                throw new Exception("Stok ".$data['nama_barang']." tidak mencukupi");
            }

            // =======================
            // INSERT DETAIL
            // =======================
            $stmt = mysqli_prepare($conn,"
                INSERT INTO detail_permohonan
                (permohonan_id, barang_id, jumlah)
                VALUES (?, ?, ?)
            ");

            if(!$stmt){
                throw new Exception("Prepare detail gagal: " . mysqli_error($conn));
            }

            mysqli_stmt_bind_param($stmt,"iii",
                $id_permohonan,
                $id_barang,
                $qty
            );

            if(!mysqli_stmt_execute($stmt)){
                throw new Exception("Gagal insert detail");
            }

            mysqli_stmt_close($stmt);
        }
    }

    // =======================
    // COMMIT
    // =======================
    mysqli_commit($conn);

    header("Location: bukti_permohonan.php?kode=".$kode_permohonan);
    exit;

} catch (Exception $e) {

    // =======================
    // ROLLBACK
    // =======================
    mysqli_rollback($conn);

    echo "<h3>Error: ".$e->getMessage()."</h3>";
    echo "<a href='order.php'>Kembali</a>";
}