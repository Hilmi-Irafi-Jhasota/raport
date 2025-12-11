<?php
session_start();
include "koneksi.php";

// Cek apakah login sebagai guru
if(!isset($_SESSION['role']) || $_SESSION['role'] != "guru"){
    header("Location: index.php");
    exit;
}

$id_kelas = $_SESSION['id_kelas'];

// ===========================
// AMBIL DATA SISWA
// ===========================
$siswa = mysqli_query($conn, "SELECT * FROM siswa WHERE kelas='$id_kelas' ORDER BY nama ASC");

// ===========================
// AMBIL DATA MAPEL
// ===========================
$mapel_query = mysqli_query($conn, "SELECT * FROM mapel ORDER BY nama_mapel ASC");
$mapel_list = [];
while($m = mysqli_fetch_assoc($mapel_query)){
    $mapel_list[$m['id_mapel']] = $m['nama_mapel'];
}

// ===========================
// SIMPAN NILAI
// ===========================
if(isset($_POST['simpan'])){
    foreach($_POST['nilai'] as $id_siswa => $mapels){
        foreach($mapels as $id_mapel => $nilai){
            if($nilai !== ""){

                // cek apakah nilai sudah ada
                $cek = mysqli_query($conn, 
                    "SELECT * FROM nilai WHERE id_siswa='$id_siswa' AND id_mapel='$id_mapel'"
                );

                if(mysqli_num_rows($cek) > 0){
                    // update
                    mysqli_query($conn,
                        "UPDATE nilai SET nilai='$nilai' 
                         WHERE id_siswa='$id_siswa' AND id_mapel='$id_mapel'"
                    );
                } else {
                    // insert baru
                    mysqli_query($conn,
                        "INSERT INTO nilai(id_siswa, id_mapel, nilai, tanggal_input)
                         VALUES('$id_siswa','$id_mapel','$nilai', NOW())"
                    );
                }
            }
        }
    }

    $_SESSION['success'] = "Nilai berhasil disimpan!";
    header("Location: input_nilai.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Input Nilai Siswa (Excel Mode)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { background: #f8f9fa; }
        input[type=number] { width: 80px; text-align: center; }
        th, td { text-align: center; vertical-align: middle; }
        .sidebar { height: 100vh; position: sticky; top: 0; overflow-y: auto; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row vh-100">

        <!-- SIDEBAR -->
        <div class="col-md-3 bg-light p-4 sidebar">
            <h3>Guru Dashboard</h3>
            <p>Selamat datang, <?= htmlspecialchars($_SESSION['nama'] ?? '') ?></p>

            <a href="logout.php" class="btn btn-danger mb-4 w-100">Logout</a>

            <ul class="nav flex-column mb-3">
                <li class="nav-item mb-2"><a class="nav-link btn btn-outline-primary" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item mb-2"><a class="nav-link btn btn-outline-primary" href="inputsiswaguru.php">Input Siswa</a></li>
                <li class="nav-item mb-2"><a class="nav-link btn btn-primary" href="input_nilai.php">Input Nilai Siswa</a></li>
                <li class="nav-item mb-2"><a class="nav-link btn btn-outline-primary" href="rekap_nilai.php">Rekap Nilai Siswa</a></li>
                <li class="nav-item mb-2"><a class="nav-link btn btn-outline-secondary" href="edit_nilai.php">Edit Nilai Siswa</a></li>
            </ul>

            <?php
            if(isset($_SESSION['error'])){
                echo '<div class="alert alert-danger">'.$_SESSION['error'].'</div>';
                unset($_SESSION['error']);
            }
            if(isset($_SESSION['success'])){
                echo '<div class="alert alert-success">'.$_SESSION['success'].'</div>';
                unset($_SESSION['success']);
            }
            ?>
        </div>

        <!-- HALAMAN UTAMA -->
        <div class="col-md-9 p-4 overflow-auto">

            <h2>Input Nilai Siswa Kelas <?= htmlspecialchars($id_kelas) ?></h2>
            <p class="text-muted">input nilai akhir </p>

            <form method="POST">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-Primary">
                            <tr>
                                <th>Nama Siswa</th>

                                <?php foreach($mapel_list as $nama_mapel): ?>
                                    <th><?= htmlspecialchars($nama_mapel) ?></th>
                                <?php endforeach; ?>

                            </tr>
                        </thead>
                        <tbody>

                        <?php while($s = mysqli_fetch_assoc($siswa)): ?>
                            <tr>
                                <td><?= htmlspecialchars($s['nama']) ?></td>

                                <?php foreach($mapel_list as $id_mapel => $nama_mapel): ?>

                                    <?php
                                    // ambil nilai lama
                                    $nilai_old = mysqli_query($conn,
                                        "SELECT nilai FROM nilai 
                                         WHERE id_siswa='{$s['id']}' AND id_mapel='$id_mapel'"
                                    );
                                    $nilai_value = mysqli_num_rows($nilai_old) ? mysqli_fetch_assoc($nilai_old)['nilai'] : "";
                                    ?>

                                    <td>
                                        <input type="number"
                                               name="nilai[<?= $s['id'] ?>][<?= $id_mapel ?>]"
                                               value="<?= $nilai_value ?>"
                                               min="0" max="100"
                                               class="form-control">
                                    </td>

                                <?php endforeach; ?>

                            </tr>
                        <?php endwhile; ?>

                        </tbody>
                    </table>
                </div>

                <button type="submit" name="simpan" class="btn btn-primary w-100 mt-3">
                    Simpan Semua Nilai
                </button>
            </form>

        </div>
    </div>
</div>

</body>
</html>
