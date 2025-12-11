<?php
session_start();
include "koneksi.php";

// Pastikan admin login
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: index.php");
    exit;
}

// Ambil data dari form
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$nama = isset($_POST['nama']) ? mysqli_real_escape_string($conn, $_POST['nama']) : '';
$password = isset($_POST['password']) ? mysqli_real_escape_string($conn, $_POST['password']) : '';
$status_guru = isset($_POST['status_guru']) ? intval($_POST['status_guru']) : 0;
$kelas_guru = isset($_POST['kelas_guru']) ? intval($_POST['kelas_guru']) : 0;

// Validasi sederhana
if(!$id || !$nama || !$password || !$status_guru || !$kelas_guru){
    $_SESSION['error'] = "Semua field wajib diisi!";
    header("Location: edit_guru.php?id=$id");
    exit;
}

// Cek apakah nama guru sudah dipakai guru lain
$cek = mysqli_query($conn, "SELECT * FROM guru WHERE nama='$nama' AND id!='$id'");
if(mysqli_num_rows($cek) > 0){
    $_SESSION['error'] = "Nama guru sudah digunakan!";
    header("Location: edit_guru.php?id=$id");
    exit;
}

// Update data guru
$update = mysqli_query($conn, "UPDATE guru SET 
    nama='$nama',
    password='$password',
    status_guru='$status_guru',
    id_kelas='$kelas_guru'
    WHERE id='$id'
");

if($update){
    $_SESSION['success'] = "Data guru berhasil diperbarui!";
    header("Location: daftar_guru.php");
    exit;
} else {
    $_SESSION['error'] = "Gagal menyimpan perubahan: " . mysqli_error($conn);
    header("Location: edit_guru.php?id=$id");
    exit;
}
