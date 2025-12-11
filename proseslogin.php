<?php
session_start();
include "koneksi.php";

$username = mysqli_real_escape_string($conn, $_POST['username']);
$password = $_POST['password'];
$role     = $_POST['role'];

if($role === "admin"){
    $query = mysqli_query($conn, "SELECT * FROM admin WHERE username='$username' LIMIT 1");

} elseif($role === "guru"){
    $query = mysqli_query($conn, "SELECT * FROM guru WHERE nama='$username' LIMIT 1");

} else {
    $_SESSION['error'] = "Role tidak valid!";
    header("Location: index.php");
    exit;
}

if(mysqli_num_rows($query) > 0){
    $user = mysqli_fetch_assoc($query);

    if($password === $user['password']){

        $_SESSION['login'] = true;
        $_SESSION['role']  = $role;

        if($role == "admin"){
            $_SESSION['nama'] = $user['nama_lengkap'];

            header("Location: dashboard_admin.php");
            exit;

        } else { 
            // ROLE GURU
            $_SESSION['nama']     = $user['nama'];       // nama guru
            $_SESSION['id_guru']  = $user['id'];         // <-- WAJIB ADA
            $_SESSION['id_kelas'] = $user['id_kelas'];   // kelas yang dia ajar

            header("Location: dashboard.php");
            exit;
        }

    } else {
        $_SESSION['error'] = "Password salah!";
        header("Location: index.php");
        exit;
    }

} else {
    $_SESSION['error'] = "Username/Nama tidak ditemukan!";
    header("Location: index.php");
    exit;
}
?>
