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

// Ambil data siswa
$search_siswa = isset($_GET['search_siswa']) ? mysqli_real_escape_string($conn, $_GET['search_siswa']) : '';
$filter_kelas_siswa = isset($_GET['filter_kelas_siswa']) ? intval($_GET['filter_kelas_siswa']) : 0;

$siswa_query = "SELECT * FROM siswa WHERE 1";

// Guru hanya melihat kelas sendiri
$siswa_query .= " AND kelas='$id_kelas'";

// Search
if($search_siswa){
    $siswa_query .= " AND (nama LIKE '%$search_siswa%' OR nis LIKE '%$search_siswa%')";
}

$siswa_query .= " ORDER BY id DESC";

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
                <li class="nav-item mb-2"><a class="nav-link btn btn-outline-primary" href="input_nilai.php">Input NIlai Siswa</a></li>
                <li class="nav-item mb-2"><a class="nav-link btn btn-outline-primary" href="rekap_nilai.php">Rekap Nilai Siswa</a></li>
                <li class="nav-item mb-2"><a class="nav-link btn btn-outline-secondary" href="edit_nilai.php">Edit Nilai Siswa</a></li>
            <li class="nav-item mb-2"><a class="nav-link btn btn-outline-secondary" href="print_raport.php">Print Raport Siswa</a></li>
            
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

            <!-- Form Search & Filter Siswa -->
            
            <?php
            
// Ambil total nilai per siswa di kelas guru
$ranking_query = "
    SELECT s.id, s.nama, s.nis, SUM(n.nilai) as total_nilai, ROUND(AVG(n.nilai),2) as rata_rata
    FROM siswa s
    LEFT JOIN nilai n ON s.id = n.id_siswa
    WHERE s.kelas='$id_kelas'
    GROUP BY s.id
    ORDER BY total_nilai DESC
";

$ranking_result = mysqli_query($conn, $ranking_query);
?>
<div class="card mb-4">
    <div class="card-header"><h4>Ranking Siswa Kelas <?= htmlspecialchars($id_kelas) ?></h4></div>
    <div class="card-body table-responsive" style="max-height:400px; overflow-y:auto;">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Nama</th>
                    <th>NIS</th>
                    <th>Total Nilai</th>
                    <th>Rata-rata</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $rank=1;
                while($r = mysqli_fetch_assoc($ranking_result)):
                ?>
                <tr>
                    <td><?= $rank++; ?></td>
                    <td><?= htmlspecialchars($r['nama']) ?></td>
                    <td><?= htmlspecialchars($r['nis']) ?></td>
                    <td><?= $r['total_nilai'] ?? 0 ?></td>
                    <td><?= $r['rata_rata'] ?? 0 ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
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
            <h4>Daftar Siswa (Kelas <?= htmlspecialchars($id_kelas) ?>)</h4>
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
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td><?= htmlspecialchars($row['nis']) ?></td>
                            <td><?= htmlspecialchars($row['kelas']) ?></td>
                            <td>
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
