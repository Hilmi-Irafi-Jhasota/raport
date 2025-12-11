<?php
session_start();
include "koneksi.php";

// Pastikan admin login
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: index.php");
    exit;
}

// Hapus guru jika id dikirim
if(isset($_GET['id'])){
    $id = intval($_GET['id']);
    
    // Cek apakah guru ada
    $cek = mysqli_query($conn, "SELECT * FROM guru WHERE id='$id'");
    if(mysqli_num_rows($cek) > 0){
        mysqli_query($conn, "DELETE FROM guru WHERE id='$id'");
        $_SESSION['success'] = "Guru berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Guru tidak ditemukan!";
    }
    header("Location: inputguru.php");
    exit;
}
?>
