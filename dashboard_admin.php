<?php
session_start();
include "koneksi.php";

// ==========================
// Ambil Data Siswa & Guru
// ==========================
$search_siswa = isset($_GET['search_siswa']) ? mysqli_real_escape_string($conn, $_GET['search_siswa']) : '';
$filter_kelas_siswa = isset($_GET['filter_kelas_siswa']) ? intval($_GET['filter_kelas_siswa']) : 0;

$siswa_query = "SELECT * FROM siswa WHERE 1";
if($search_siswa){
    $siswa_query .= " AND (nama LIKE '%$search_siswa%' OR nis LIKE '%$search_siswa%')";
}
if($filter_kelas_siswa){
    $siswa_query .= " AND kelas='$filter_kelas_siswa'";
}
$siswa_query .= " ORDER BY id DESC";
$data_siswa = mysqli_query($conn, $siswa_query);
if (!$data_siswa) { die("Query siswa gagal: " . mysqli_error($conn)); }

// ==========================
// Guru
// ==========================
$search_guru = isset($_GET['search_guru']) ? mysqli_real_escape_string($conn, $_GET['search_guru']) : '';
$filter_kelas_guru = isset($_GET['filter_kelas_guru']) ? intval($_GET['filter_kelas_guru']) : 0;

$guru_query = "SELECT * FROM guru WHERE 1";
if($search_guru){
    $guru_query .= " AND nama LIKE '%$search_guru%'";
}
$guru_query .= " ORDER BY id DESC";
$data_guru = mysqli_query($conn, $guru_query);
if (!$data_guru) { die("Query guru gagal: " . mysqli_error($conn)); }

// ==========================
// Tambah Siswa
// ==========================
if (isset($_POST['tambah_siswa'])) {
    $nama  = mysqli_real_escape_string($conn, $_POST['nama_siswa']);
    $nis   = mysqli_real_escape_string($conn, $_POST['nis_siswa']);
    $kelas = intval($_POST['kelas_siswa']);

    $cek_nis = mysqli_query($conn, "SELECT * FROM siswa WHERE nis='$nis'");
    if(mysqli_num_rows($cek_nis) > 0){
        $_SESSION['error'] = "NIS sudah terdaftar!";
    } else {
        mysqli_query($conn, "INSERT INTO siswa (nama, nis, kelas) VALUES ('$nama','$nis','$kelas')");
        $_SESSION['success'] = "Siswa berhasil ditambahkan!";
    }
    header("Location: dashboard_admin.php");
    exit;
}

// ==========================
// Tambah Guru
// ==========================
if (isset($_POST['tambah_guru'])) {
    $nama     = mysqli_real_escape_string($conn, $_POST['nama_guru']);
    $password = mysqli_real_escape_string($conn, $_POST['password_guru']);
    $status_guru = mysqli_real_escape_string($conn, $_POST['status_guru']);
    $id_mapel = intval($_POST['id_mapel']);

    $cek_guru = mysqli_query($conn, "SELECT * FROM guru WHERE nama='$nama'");
    if(mysqli_num_rows($cek_guru) > 0){
        $_SESSION['error'] = "Nama guru sudah terdaftar!";
    } else {
        mysqli_query($conn, "INSERT INTO guru (nama, password, status_guru, id_mapel) VALUES ('$nama','$password','$status_guru','$id_mapel')");
        $_SESSION['success'] = "Guru berhasil ditambahkan!";
    }
    header("Location: dashboard_admin.php");
    exit;
}

// ==========================
// Grafik Jumlah Siswa per Kelas
// ==========================
$jumlahSiswaPerKelas = [];
for ($i = 1; $i <= 6; $i++) {
    $q = mysqli_query($conn, "SELECT COUNT(*) AS total FROM siswa WHERE kelas='$i'");
    $d = mysqli_fetch_assoc($q);
    $jumlahSiswaPerKelas[$i] = $d['total'];
}

// ==========================
// Grafik Jumlah Status Guru
// ==========================
$guruPerStatus = [];
$qStatus = mysqli_query($conn, "SELECT status_guru, COUNT(*) AS total FROM guru GROUP BY status_guru");
while ($row = mysqli_fetch_assoc($qStatus)) {
    $guruPerStatus[$row['status_guru']] = $row['total'];
}

// ==========================
// Grafik Jumlah Guru per Mapel
// ==========================
$qMapel = mysqli_query($conn, "
    SELECT m.nama_mapel, COUNT(g.id) AS total
    FROM mapel m
    LEFT JOIN guru g ON g.id_mapel = m.id_mapel
    GROUP BY m.id_mapel, m.nama_mapel
");
$guruPerMapel = [];
while ($row = mysqli_fetch_assoc($qMapel)) {
    $guruPerMapel[$row['nama_mapel']] = $row['total'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background: #f8f9fa; }
        .sidebar { height: 100vh; position: sticky; top: 0; overflow-y: auto; }
    </style>
</head>
<body>
<div class="container-fluid">
<div class="row vh-100">

    <!-- Sidebar -->
    <div class="col-md-3 p-4 sidebar bg-light">
        <h3>Admin Dashboard</h3>
        <p>Selamat datang, <?= $_SESSION['nama'] ?? '' ?></p>
        <a href="logout.php" class="btn btn-danger mb-4 w-100">Logout</a>
        <ul class="nav flex-column mb-3">
            <li class="nav-item mb-2"><a class="nav-link btn btn-outline-primary" href="dashboard_admin.php">Dashboard Admin</a></li>
            <li class="nav-item mb-2"><a class="nav-link btn btn-outline-primary" href="inputsiswa.php">Input Siswa</a></li>
            <li class="nav-item mb-2"><a class="nav-link btn btn-outline-secondary" href="inputguru.php">Input Guru</a></li>
        </ul>
        <?php
        if(isset($_SESSION['error'])){ echo '<div class="alert alert-danger mt-3">'.$_SESSION['error'].'</div>'; unset($_SESSION['error']); }
        if(isset($_SESSION['success'])){ echo '<div class="alert alert-success mt-3">'.$_SESSION['success'].'</div>'; unset($_SESSION['success']); }
        ?>
    </div>

    <!-- Konten utama -->
    <div class="col-md-9 p-4 overflow-auto" style="height: 100vh;">
        
        <!-- Grafik Siswa per Kelas -->
        <div class="card mb-4">
            <div class="card-header"><h4>Jumlah Siswa per Kelas</h4></div>
            <div class="card-body"><canvas id="chartSiswa"></canvas></div>
        </div>

        <!-- Grafik Status Guru -->
        <div class="card mb-4">
            <div class="card-header"><h4>Jumlah Guru per Status</h4></div>
            <div class="card-body"><canvas id="chartStatusGuru"></canvas></div>
        </div>

        <!-- Grafik Guru per Mapel -->
        <div class="card mb-4">
            <div class="card-header"><h4>Jumlah Guru per Mapel</h4></div>
            <div class="card-body"><canvas id="chartGuruMapel"></canvas></div>
        </div>

    </div>
</div>
</div>

<script>
// --- Grafik Siswa per Kelas ---
const ctxSiswa = document.getElementById('chartSiswa').getContext('2d');
new Chart(ctxSiswa, {
    type: 'bar',
    data: {
        labels: ['Kelas 1','Kelas 2','Kelas 3','Kelas 4','Kelas 5','Kelas 6'],
        datasets: [{
            label: 'Jumlah Siswa',
            data: <?= json_encode(array_values($jumlahSiswaPerKelas)) ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.7)'
        }]
    }
});

// --- Grafik Status Guru ---
const ctxStatusGuru = document.getElementById('chartStatusGuru').getContext('2d');
new Chart(ctxStatusGuru, {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_keys($guruPerStatus)) ?>,
        datasets: [{
            label: 'Jumlah Guru',
            data: <?= json_encode(array_values($guruPerStatus)) ?>,
            backgroundColor: '#ff6384'
        }]
    },
    options: { responsive:true, scales:{ y:{ beginAtZero:true } } }
});

// --- Grafik Guru per Mapel ---
const ctxGuruMapel = document.getElementById('chartGuruMapel').getContext('2d');
new Chart(ctxGuruMapel, {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_keys($guruPerMapel)) ?>,
        datasets: [{
            label: 'Jumlah Guru',
            data: <?= json_encode(array_values($guruPerMapel)) ?>,
            backgroundColor: '#4bc0c0'
        }]
    },
    options: { responsive:true, scales:{ y:{ beginAtZero:true } } }
});
</script>

</body>
</html>
