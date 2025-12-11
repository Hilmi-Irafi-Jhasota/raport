<?php
session_start();
include "koneksi.php";

// Pastikan session login valid
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'guru'){
    $_SESSION['error'] = "Silakan login terlebih dahulu!";
    header("Location: index.php");
    exit;
}

$role = $_SESSION['role'];
$id_kelas = $_SESSION['id_kelas'] ?? 0;

// Ambil data siswa kelas guru
$siswa_query = "SELECT * FROM siswa WHERE kelas='$id_kelas' ORDER BY nama ASC";
$data_siswa = mysqli_query($conn, $siswa_query);
if(!$data_siswa){
    die("Query siswa gagal: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Guru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .sidebar { height: 100vh; position: sticky; top: 0; overflow-y: auto; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row vh-100">

        <!-- Sidebar -->
        <div class="col-md-3 bg-light p-4 sidebar">
            <h3>Guru Dashboard</h3>
            <p>Selamat datang, <?= htmlspecialchars($_SESSION['nama']) ?></p>
            <a href="logout.php" class="btn btn-danger mb-4 w-100">Logout</a>

            <ul class="nav flex-column mb-3">
                <li class="nav-item mb-2"><a class="nav-link btn btn-outline-primary" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item mb-2"><a class="nav-link btn btn-outline-primary" href="inputsiswaguru.php">Input Siswa</a></li>
                <li class="nav-item mb-2"><a class="nav-link btn btn-outline-primary" href="input_nilai.php">Input Nilai</a></li>
                <li class="nav-item mb-2"><a class="nav-link btn btn-outline-primary" href="rekap_nilai.php">Rekap Nilai</a></li>
                <li class="nav-item mb-2"><a class="nav-link btn btn-outline-secondary" href="print_raport.php">Print Raport</a></li>
            </ul>
        </div>

        <!-- Konten utama -->
        <div class="col-md-9 p-4 overflow-auto" style="height: 100vh;">
            <h4>Daftar Siswa Kelas <?= htmlspecialchars($id_kelas) ?></h4>

            <div class="table-responsive">
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
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td><?= htmlspecialchars($row['nis']) ?></td>
                            <td><?= htmlspecialchars($row['kelas']) ?></td>
                            <td>
                                <a href="print.php?id=<?= $row['id'] ?>" target="_blank" class="btn btn-sm btn-primary">📄 Print Raport</a>
                                <a href="edit_siswa.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">✏️ Edit</a>
                                <a href="hapus_siswa.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus siswa?')">🗑️ Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
