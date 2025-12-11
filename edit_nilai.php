<?php
session_start();
include "koneksi.php";

// Pastikan guru login
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'guru'){
    header("Location: index.php");
    exit;
}

$id_kelas = $_SESSION['id_kelas'];

// Ambil daftar siswa sesuai kelas guru
$siswa_query = mysqli_query($conn, "SELECT * FROM siswa WHERE kelas='$id_kelas' ORDER BY nama ASC");

// Ambil daftar mapel
$mapel_query = mysqli_query($conn, "SELECT * FROM mapel ORDER BY nama_mapel ASC");
$mapel_list = [];
while($m = mysqli_fetch_assoc($mapel_query)){
    $mapel_list[$m['id_mapel']] = $m['nama_mapel'];
}

// Ambil semua siswa + nilai mereka
$query = "SELECT s.id AS id_siswa, s.nama, n.id_mapel, n.nilai
          FROM siswa s
          LEFT JOIN nilai n ON s.id = n.id_siswa
          WHERE s.kelas='$id_kelas'
          ORDER BY s.nama ASC";

$result = mysqli_query($conn, $query);

$data = [];
while($row = mysqli_fetch_assoc($result)){
    $id_siswa = $row['id_siswa'];
    if(!isset($data[$id_siswa])){
        $data[$id_siswa] = [
            'nama' => $row['nama'],
            'nilai' => []
        ];
    }
    if($row['id_mapel']){
        $data[$id_siswa]['nilai'][$row['id_mapel']] = $row['nilai'];
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Nilai Seluruh Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .sidebar { height: 100vh; position: sticky; top: 0; overflow-y: auto; }

        /* WARNA NILAI */
        .nilai-merah { background: #ff9999 !important; font-weight: bold; color: #700; }
        .nilai-hijau { background: #b7ffb7 !important; font-weight: bold; color: #064b00; }
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
        <h3>Edit Nilai Seluruh Siswa</h3>

        <form method="POST" action="proses_edit_nilai.php">
            <div class="table-responsive mt-3">
                <table class="table table-bordered table-striped text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>Nama Siswa</th>
                            <?php foreach($mapel_list as $nama_mapel): ?>
                                <th><?= htmlspecialchars($nama_mapel) ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($data as $id_siswa => $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <?php foreach($mapel_list as $id_mapel => $nama_mapel): 
                                $nilai_cell = $row['nilai'][$id_mapel] ?? '';
                                $kelas_warna = ($nilai_cell !== "" && $nilai_cell < 70) ? "nilai-merah" : (($nilai_cell !== "") ? "nilai-hijau" : "");
                            ?>
                            <td>
                                <input type="number" 
                                       class="form-control text-center <?= $kelas_warna ?>" 
                                       min="0" max="100" 
                                       name="nilai[<?= $id_siswa ?>][<?= $id_mapel ?>]" 
                                       value="<?= $nilai_cell ?>">
                            </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Simpan Semua Nilai</button>
        </form>
    </div>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
