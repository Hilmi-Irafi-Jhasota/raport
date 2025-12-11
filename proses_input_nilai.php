<?php
session_start();
include "koneksi.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'guru'){
    header("Location: input_nilai.php");
    exit;
}

$id_siswa = $_POST['id_siswa'];
$id_mapel = $_POST['id_mapel'];
$nilai    = $_POST['nilai'];
$keterangan = $_POST['keterangan'] ?? '';

// Cek apakah nilai sudah ada
$cek = mysqli_query($conn, "SELECT * FROM nilai WHERE id_siswa='$id_siswa' AND id_mapel='$id_mapel'");
if(mysqli_num_rows($cek) > 0){
    $_SESSION['error'] = "Nilai untuk mapel ini sudah diinput. Gunakan halaman edit jika ingin merubahnya.";
    header("Location: input_nilai.php");
    exit;
}

// Insert nilai baru
mysqli_query($conn, "INSERT INTO nilai (id_siswa,id_guru,id_mapel,nilai,keterangan,tanggal_input) 
                    VALUES ('{$id_siswa}','{$_SESSION['id']}','{$id_mapel}','{$nilai}','{$keterangan}',NOW())");

$_SESSION['success'] = "Nilai berhasil disimpan!";
header("Location: input_nilai.php");
exit;
?>
