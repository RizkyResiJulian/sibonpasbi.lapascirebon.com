<?php

include "../config/koneksi.php";

$id = $_GET['id'];

mysqli_query($conn,"DELETE FROM saran WHERE id='$id'");

header("Location: saran.php");

?>