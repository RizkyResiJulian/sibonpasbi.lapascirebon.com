<?php
session_start();

if(!isset($_SESSION['login'])){
    header("Location: auth/login.php");
    exit;
}

if($_SESSION['role'] != "admin"){
    echo "Akses ditolak";
    exit;
}

/* =========================
   LOAD LIBRARY
========================= */

require 'vendor/autoload.php';
include "config/koneksi.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

/* =========================
   FILTER BULAN & TAHUN
========================= */

$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');

/* =========================
   NAMA BULAN
========================= */

$nama_bulan = [
    1 => 'Januari',
    2 => 'Februari',
    3 => 'Maret',
    4 => 'April',
    5 => 'Mei',
    6 => 'Juni',
    7 => 'Juli',
    8 => 'Agustus',
    9 => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember'
];

/* =========================
   FUNCTION STYLE HEADER
========================= */

function styleHeader($sheet, $range){

    $sheet->getStyle($range)->getFont()->setBold(true);

    $sheet->getStyle($range)->getFill()
    ->setFillType(Fill::FILL_SOLID)
    ->getStartColor()
    ->setARGB('FFDCE6F1');

    $sheet->getStyle($range)
    ->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
}

/* =========================
   FUNCTION BORDER
========================= */

function styleBorder($sheet, $range){

    $sheet->getStyle($range)
    ->getBorders()
    ->getAllBorders()
    ->setBorderStyle(Border::BORDER_THIN);
}

/* =========================
   SPREADSHEET
========================= */

$spreadsheet = new Spreadsheet();

#########################################################
# SHEET 1 : LAPORAN PERMOHONAN
#########################################################

$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Laporan Permohonan');

/* JUDUL */

$sheet->mergeCells('A1:H1');

$sheet->setCellValue(
    'A1',
    'LAPORAN PERMOHONAN BARANG - '.$nama_bulan[(int)$bulan].' '.$tahun
);

$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(15);

$sheet->getStyle('A1')->getAlignment()
->setHorizontal(Alignment::HORIZONTAL_CENTER);

/* HEADER */

$sheet->fromArray([
[
'No',
'Tanggal',
'No Permohonan',
'Nama',
'NIP',
'Bidang',
'Detail Barang',
'Status'
]
],NULL,'A3');

styleHeader($sheet,'A3:H3');

/* DATA */

$query = mysqli_query($conn,"
SELECT *
FROM permohonan
WHERE MONTH(tanggal_pesan)='$bulan'
AND YEAR(tanggal_pesan)='$tahun'
ORDER BY tanggal_pesan DESC
");

$row = 4;
$no = 1;

while($d = mysqli_fetch_assoc($query)){

    $detail = mysqli_query($conn,"
    SELECT 
        barang.nama_barang,
        detail_permohonan.jumlah
    FROM detail_permohonan
    JOIN barang
    ON detail_permohonan.barang_id = barang.id
    WHERE permohonan_id='".$d['id']."'
    ");

    $detail_barang = "";

    while($item = mysqli_fetch_assoc($detail)){

        $detail_barang .=
        $item['nama_barang'].
        " x".$item['jumlah']."\n";
    }

    $sheet->setCellValue('A'.$row,$no++);
    $sheet->setCellValue(
        'B'.$row,
        date('d-m-Y',strtotime($d['tanggal_pesan']))
    );

    $sheet->setCellValue(
        'C'.$row,
        $d['nomor_permohonan']
    );

    $sheet->setCellValue(
        'D'.$row,
        $d['nama']
    );

    /* AGAR NIP TIDAK BERUBAH JADI ANGKA SCIENTIFIC */

    $sheet->setCellValue(
        'E'.$row,
        "'".$d['nip']
    );

    $sheet->setCellValue(
        'F'.$row,
        $d['bidang']
    );

    $sheet->setCellValue(
        'G'.$row,
        trim($detail_barang)
    );

    $sheet->setCellValue(
        'H'.$row,
        ucfirst($d['status'])
    );

    $row++;
}

/* STYLE */

styleBorder($sheet,'A3:H'.($row-1));

$sheet->getStyle('G4:G'.$row)
->getAlignment()
->setWrapText(true);

foreach(range('A','H') as $col){

    $sheet->getColumnDimension($col)
    ->setAutoSize(true);
}

#########################################################
# SHEET 2 : BARANG TERBANYAK DIMOHONKAN
#########################################################

$spreadsheet->createSheet();

$sheet2 = $spreadsheet->setActiveSheetIndex(1);

$sheet2->setTitle('Barang Terbanyak');

$sheet2->mergeCells('A1:C1');

$sheet2->setCellValue(
    'A1',
    'BARANG TERBANYAK DIMOHONKAN'
);

$sheet2->getStyle('A1')->getFont()
->setBold(true)
->setSize(15);

$sheet2->fromArray([
[
'No',
'Nama Barang',
'Total Dimohonkan'
]
],NULL,'A3');

styleHeader($sheet2,'A3:C3');

/* DATA */

$query_barang = mysqli_query($conn,"
SELECT 
    barang.nama_barang,
    SUM(detail_permohonan.jumlah) as total
FROM detail_permohonan
JOIN barang
ON detail_permohonan.barang_id = barang.id
JOIN permohonan
ON detail_permohonan.permohonan_id = permohonan.id
WHERE MONTH(permohonan.tanggal_pesan)='$bulan'
AND YEAR(permohonan.tanggal_pesan)='$tahun'
GROUP BY barang.id
ORDER BY total DESC
");

$row2 = 4;
$no2 = 1;

while($d = mysqli_fetch_assoc($query_barang)){

    $sheet2->setCellValue('A'.$row2,$no2++);
    $sheet2->setCellValue('B'.$row2,$d['nama_barang']);
    $sheet2->setCellValue('C'.$row2,$d['total']);

    $row2++;
}

styleBorder($sheet2,'A3:C'.($row2-1));

foreach(range('A','C') as $col){

    $sheet2->getColumnDimension($col)
    ->setAutoSize(true);
}

#########################################################
# SHEET 3 : DATA STOK BARANG
#########################################################

$spreadsheet->createSheet();

$sheet3 = $spreadsheet->setActiveSheetIndex(2);

$sheet3->setTitle('Stok Barang');

$sheet3->mergeCells('A1:E1');

$sheet3->setCellValue(
    'A1',
    'DATA STOK BARANG BULAN '.$nama_bulan[(int)$bulan].' '.$tahun
);

$sheet3->getStyle('A1')->getFont()
->setBold(true)
->setSize(15);

$sheet3->fromArray([
[
'No',
'Nama Barang',
'Kategori',
'Stok',
'Status'
]
],NULL,'A3');

styleHeader($sheet3,'A3:E3');

/* DATA */

$query_stok = mysqli_query($conn,"
SELECT 
    barang.*,
    kategori.nama_kategori
FROM barang
LEFT JOIN kategori
ON barang.kategori_id = kategori.id
ORDER BY barang.nama_barang ASC
");

$row3 = 4;
$no3 = 1;

while($d = mysqli_fetch_assoc($query_stok)){

    $status_stok = ($d['stok'] <= 5)
    ? 'Stok Menipis'
    : 'Aman';

    $sheet3->setCellValue('A'.$row3,$no3++);
    $sheet3->setCellValue('B'.$row3,$d['nama_barang']);
    $sheet3->setCellValue('C'.$row3,$d['nama_kategori']);
    $sheet3->setCellValue('D'.$row3,$d['stok']);
    $sheet3->setCellValue('E'.$row3,$status_stok);

    $row3++;
}

styleBorder($sheet3,'A3:E'.($row3-1));

foreach(range('A','E') as $col){

    $sheet3->getColumnDimension($col)
    ->setAutoSize(true);
}

#########################################################
# SET ACTIVE SHEET
#########################################################

$spreadsheet->setActiveSheetIndex(0);

#########################################################
# DOWNLOAD EXCEL
#########################################################

$filename = 'laporan_permohonan_'.$bulan.'_'.$tahun.'.xlsx';

$writer = new Xlsx($spreadsheet);

if(ob_get_length()){
    ob_end_clean();
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

header(
'Content-Disposition: attachment; filename="'.$filename.'"'
);

header('Cache-Control: max-age=0');

$writer->save('php://output');
exit;
?>