<?php
session_start();
include "koneksi.php";

// Cek apakah ada parameter id
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Pastikan id berupa angka untuk keamanan

    // Hapus data siswa dari database
    $query = "DELETE FROM siswa WHERE id = $id";
    if (mysqli_query($conn, $query)) {
        // Jika berhasil, redirect ke dashboard
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    // Jika tidak ada id, redirect ke dashboard
    header("Location: dashboard.php");
    exit;
}
?>
