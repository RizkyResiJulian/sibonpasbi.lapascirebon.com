<?php
session_start();
include "../config/koneksi.php";

$username = $_POST['username'];
$password = md5($_POST['password']);

$query = mysqli_query($conn,"
    SELECT * FROM users 
    WHERE username='$username' 
    AND password='$password'
    AND status='aktif'
");

$data = mysqli_fetch_assoc($query);

if($data){

    $_SESSION['login'] = true;
    $_SESSION['user_id'] = $data['id'];
    $_SESSION['nama'] = $data['nama'];
    $_SESSION['nip'] = $data['nip'];
    $_SESSION['role'] = $data['role'];

    // redirect berdasarkan role
    if($data['role']=="operator"){

        header("Location: ../operator/dashboard.php");
        exit;

    } elseif($data['role']=="admin"){

        header("Location: ../admin/dashboard.php");
        exit;

    } elseif($data['role']=="user"){

        header("Location: ../user/dashboard.php");
        exit;

    } elseif($data['role']=="superadmin"){

        header("Location: ../superadmin/dashboard.php");
        exit;

    } elseif($data['role']=="kabagtu"){

        header("Location: ../kabagtu/dashboard.php");
        exit;

    } elseif($data['role']=="ppk"){

        header("Location: ../ppk/dashboard.php");
        exit;

    } elseif($data['role']=="kalapas"){

        header("Location: ../kalapas/dashboard.php");
        exit;

    } elseif($data['role']=="bendahara"){

        header("Location: ../bendahara/dashboard.php");
        exit;

    }
    echo $_SESSION['role'];

} else {

    echo "Login gagal";

}
?>