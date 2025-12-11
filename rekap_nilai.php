<?php
session_start();
include "koneksi.php";

// Pastikan login sebagai guru
if(!isset($_SESSION['role']) || $_SESSION['role'] != "guru"){
    header("Location: index.php");
    exit;
}

$id_kelas = $_SESSION['id_kelas'];

// Ambil daftar siswa di kelas guru
$siswa_query = mysqli_query($conn, "SELECT * FROM siswa WHERE kelas='$id_kelas' ORDER BY nama ASC");

// Ambil daftar mapel
$mapel_query = mysqli_query($conn, "SELECT * FROM mapel ORDER BY nama_mapel ASC");

// Ambil filter dari GET
$id_siswa = isset($_GET['id_siswa']) ? intval($_GET['id_siswa']) : 0;
$id_mapel  = isset($_GET['id_mapel']) ? intval($_GET['id_mapel']) : 0;

$nilai_siswa = [];
$total_nilai = 0;

if($id_siswa > 0){
    $nilai_query = "
        SELECT 
            n.nilai, n.keterangan, n.tanggal_input,
            m.nama_mapel, m.id_mapel
        FROM nilai n
        JOIN mapel m ON n.id_mapel = m.id_mapel
        WHERE n.id_siswa = $id_siswa
    ";
    if($id_mapel > 0){
        $nilai_query .= " AND n.id_mapel = $id_mapel";
    }
    $nilai_query .= " ORDER BY m.nama_mapel ASC";

    $nilai_result = mysqli_query($conn, $nilai_query);
    if($nilai_result){
        while($row = mysqli_fetch_assoc($nilai_result)){
            $nilai_siswa[] = $row;
            $total_nilai += $row['nilai'];
        }
    }
}

// Ambil nama siswa untuk ditampilkan
$nama_siswa = "";
if($id_siswa > 0){
    $s = mysqli_query($conn, "SELECT nama FROM siswa WHERE id='$id_siswa'");
    if($s && mysqli_num_rows($s) > 0){
        $nama_siswa = mysqli_fetch_assoc($s)['nama'];
    }
}
$mapel_list = [];
$mapel_sql = mysqli_query($conn, "SELECT id_mapel, nama_mapel FROM mapel ORDER BY nama_mapel ASC");
while($m = mysqli_fetch_assoc($mapel_sql)){
    $mapel_list[$m['id_mapel']] = $m['nama_mapel'];
}

// --- AMBIL SEMUA SISWA + NILAI MEREKA ---
$query = "
    SELECT s.id AS id_siswa, s.nama, 
           n.id_mapel, n.nilai
    FROM siswa s
    LEFT JOIN nilai n ON s.id = n.id_siswa
    WHERE s.kelas = '$id_kelas'
    ORDER BY s.nama ASC
";

$result = mysqli_query($conn, $query);

$data = [];
$mapel_total = [];  
$mapel_count = [];  

while ($row = mysqli_fetch_assoc($result)) {
    $id_siswa = $row['id_siswa'];

    if (!isset($data[$id_siswa])) {
        $data[$id_siswa] = [
            "nama" => $row['nama'],
            "nilai" => [],
            "total" => 0,
            "count" => 0,
            "rata2" => 0
        ];
    }

    if ($row['id_mapel']) {
        $nilai = $row['nilai'];
        $data[$id_siswa]["nilai"][$row['id_mapel']] = $nilai;

        // total siswa
        $data[$id_siswa]["total"] += $nilai;
        $data[$id_siswa]["count"] += 1;

        // total mapel
        if (!isset($mapel_total[$row['id_mapel']])) {
            $mapel_total[$row['id_mapel']] = 0;
            $mapel_count[$row['id_mapel']] = 0;
        }

        $mapel_total[$row['id_mapel']] += $nilai;
        $mapel_count[$row['id_mapel']] += 1;
    }
}

foreach ($data as &$d) {
    $d["rata2"] = $d["count"] > 0 ? round($d["total"] / $d["count"], 2) : 0;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Rekap Nilai Siswa</title>
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
            <p>Selamat datang, <?= htmlspecialchars($_SESSION['nama'] ?? '') ?></p>
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
            <h2>Rekap Nilai Siswa</h2>

            <!-- Filter Siswa & Mapel -->
            <form method="GET" class="mb-4">
                <div class="row g-2">
                    <div class="col-md-4">
                        <select name="id_siswa" class="form-select" required>
                            <option value="">-- Pilih Siswa --</option>
                            <?php
                            mysqli_data_seek($siswa_query, 0);
                            while($siswa = mysqli_fetch_assoc($siswa_query)):
                            ?>
                                <option value="<?= $siswa['id'] ?>" <?= ($id_siswa == $siswa['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($siswa['nama']) ?> (<?= htmlspecialchars($siswa['nis']) ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select name="id_mapel" class="form-select">
                            <option value="0">-- Semua Mapel --</option>
                            <?php
                            mysqli_data_seek($mapel_query, 0);
                            while($m = mysqli_fetch_assoc($mapel_query)):
                            ?>
                                <option value="<?= $m['id_mapel'] ?>" <?= ($id_mapel == $m['id_mapel']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($m['nama_mapel']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
                    </div>
                </div>
            </form>

            <?php if($id_siswa > 0): ?>
                <h4>Nilai Siswa: <?= htmlspecialchars($nama_siswa) ?></h4>
                <?php if(count($nilai_siswa) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Nilai</th>
                                    <th>Keterangan</th>
                                    <th>Tanggal Input</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no=1; 
                                $mapel_sum = []; // total per mapel
                                foreach($nilai_siswa as $n): 
                                    // simpan sum per mapel
                                    $mapel_sum[$n['nama_mapel']][] = $n['nilai'];
                                ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($n['nama_mapel']) ?></td>
                                    <td><?= htmlspecialchars($n['nilai']) ?></td>
                                    <td><?= htmlspecialchars($n['keterangan']) ?></td>
                                    <td><?= htmlspecialchars($n['tanggal_input']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="2">Total Nilai</th>
                                    <th><?= $total_nilai ?></th>
                                    <th colspan="2"></th>
                                </tr>
                                <?php foreach($mapel_sum as $mapel => $nilai_arr): 
                                    $sum_mapel = array_sum($nilai_arr);
                                    $avg_mapel = count($nilai_arr) > 0 ? round($sum_mapel/count($nilai_arr),2) : 0;
                                ?>
                                    <tr>
                                        <th colspan="2">Mapel: <?= htmlspecialchars($mapel) ?> (Total)</th>
                                        <th><?= $sum_mapel ?></th>
                                        <th>Rata-rata</th>
                                        <th><?= $avg_mapel ?></th>
                                    </tr>
                                <?php endforeach; ?>
                            </tfoot>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">Belum ada nilai untuk siswa ini pada mapel yang dipilih.</div>
                <?php endif; ?>
            <?php endif; ?>
            <div class="table-responsive mt-4">
<table class="table table-bordered table-striped">
    <thead class="table">
        <tr>
            <th>Nama Siswa</th>
            <?php foreach ($mapel_list as $mapel): ?>
                <th><?= htmlspecialchars($mapel) ?></th>
            <?php endforeach; ?>
            <th>Total</th>
            <th>Rata-rata</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($data as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['nama']) ?></td>

                <?php foreach ($mapel_list as $id_mapel => $nama_mapel): ?>
                    <td><?= isset($row['nilai'][$id_mapel]) ? $row['nilai'][$id_mapel] : "-" ?></td>
                <?php endforeach; ?>

                <td><b><?= $row['total'] ?></b></td>
                <td><b><?= $row['rata2'] ?></b></td>
            </tr>
        <?php endforeach; ?>
    </tbody>

    <tfoot class="table-secondary">
        <tr>
            <th>Rata-rata per Mapel</th>
            <?php foreach ($mapel_list as $id_mapel => $nama_mapel): ?>
                <th>
                    <?= isset($mapel_total[$id_mapel]) 
                        ? round($mapel_total[$id_mapel] / $mapel_count[$id_mapel], 2) 
                        : "-" 
                    ?>
                </th>
            <?php endforeach; ?>
            <th colspan="2"></th>
        </tr>
    </tfoot>
</table>
</div>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
