<?php
session_start();
include "koneksi.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: index.php");
    exit;
}

if(isset($_POST['id'])){
    $id = intval($_POST['id']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $status_guru = mysqli_real_escape_string($conn, $_POST['status_guru']);
    $id_mapel = intval($_POST['id_mapel']);
    $kelas_guru = intval($_POST['kelas_guru']);

    $update = mysqli_query($conn, "UPDATE guru SET 
        nama='$nama',
        password='$password',
        status_guru='$status_guru',
        id_mapel='$id_mapel',
        id_kelas='$kelas_guru'
        WHERE id='$id'
    ");

    if($update){
        $_SESSION['success'] = "Data guru berhasil diperbarui!";
    } else {
        $_SESSION['error'] = "Gagal memperbarui data: ".mysqli_error($conn);
    }
}

header("Location: inputguru.php");
exit;
?>
