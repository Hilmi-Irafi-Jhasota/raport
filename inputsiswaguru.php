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
if($filter_kelas_guru){
    $guru_query .= " AND id_kelas='$filter_kelas_guru'";
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
    $kelas = $_POST['kelas_siswa'];

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
    $kelas    = $_POST['kelas_guru'];

    $cek_guru = mysqli_query($conn, "SELECT * FROM guru WHERE nama='$nama'");
    if(mysqli_num_rows($cek_guru) > 0){
        $_SESSION['error'] = "Nama guru sudah terdaftar!";
    } else {
        mysqli_query($conn, "INSERT INTO guru (nama, password, id_kelas) VALUES ('$nama','$password','$kelas')");
        $_SESSION['success'] = "Guru berhasil ditambahkan!";
    }
    header("Location: dashboard_admin.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .sidebar {
            height: 100vh;
            position: sticky;
            top: 0;
            overflow-y: auto;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row vh-100">

        <!-- Sidebar -->
        <div class="col-md-3 bg-light p-4 sidebar">
            <h3>Guru Dashboard</h3>
            <p>Selamat datang, <?= $_SESSION['nama'] ?? '' ?></p>
            <a href="logout.php" class="btn btn-danger mb-4 w-100">Logout</a>

            <ul class="nav flex-column mb-3">
                <li class="nav-item mb-2"><a class="nav-link btn btn-outline-primary" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item mb-2"><a class="nav-link btn btn-outline-primary" href="inputsiswaguru.php">Input Siswa</a></li>
                <li class="nav-item mb-2"><a class="nav-link btn btn-outline-primary" href="input_nilai.php">Input NIlai Siswa</a></li>
                <li class="nav-item mb-2"><a class="nav-link btn btn-outline-primary" href="rekap_nilai.php">Rekap Nilai Siswa</a></li>
                <li class="nav-item mb-2"><a class="nav-link btn btn-outline-secondary" href="edit_nilai.php">Edit Nilai Siswa</a></li>
            
            </ul>
            <?php
            if(isset($_SESSION['error'])){
                echo '<div class="alert alert-danger mt-3">'.$_SESSION['error'].'</div>';
                unset($_SESSION['error']);
            }
            if(isset($_SESSION['success'])){
                echo '<div class="alert alert-success mt-3">'.$_SESSION['success'].'</div>';
                unset($_SESSION['success']);
            }
            ?>
        </div>

        <!-- Konten utama -->
        <div class="col-md-9 p-4 overflow-auto" style="height: 100vh;">

            <!-- Form Input Siswa -->
            <div class="card mb-4" id="inputSiswa">
                <div class="card-header"><h4>Tambah Siswa</h4></div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Nama Siswa</label>
                            <input type="text" name="nama_siswa" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">NIS</label>
                            <input type="text" name="nis_siswa" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kelas</label>
                            <select name="kelas_siswa" class="form-select" required>
                                <option value="">-- Pilih Kelas --</option>
                                <?php for($i=1;$i<=6;$i++): ?>
                                    <option value="<?= $i ?>"><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <button type="submit" name="tambah_siswa" class="btn btn-primary">Tambah Siswa</button>
                    </form>
                </div>
            </div>

            <!-- Search & Filter Siswa -->
             <form method="GET" class="mb-3">
                <div class="row g-2">
                    <div class="col-md-6">
                        <input type="text" name="search_siswa" class="form-control" placeholder="Cari siswa..." value="<?= htmlspecialchars($_GET['search_siswa'] ?? '') ?>">
                    </div>
                
                    <div class="col-md-2">
                        <button class="btn btn-outline-primary w-100" type="submit">🔍Cari</button>
                    </div>
                </div>
            </form>

            <!-- Tabel Siswa -->
            <h4>Daftar Siswa</h4>
            <div class="table-responsive mb-4">
                <table class="table table-striped table-bordered align-middle">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>NIS</th>
                        <th>Kelas</th>
                        <th>Aksi</th>
                    </tr>
                    <?php $no=1; while($row=mysqli_fetch_assoc($data_siswa)): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= $row['nama'] ?></td>
                            <td><?= $row['nis'] ?></td>
                            <td><?= $row['kelas'] ?></td>
                            <td>
                                <a href="edit_siswa.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">✏️ Edit</a>
                                <a href="hapus_siswa.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus siswa?')">🗑️ Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>

    
            
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
