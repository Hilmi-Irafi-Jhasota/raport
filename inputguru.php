<?php
session_start();
include "koneksi.php";

// ==========================
// Ambil Data Mapel untuk dropdown
// ==========================
$mapel_query = mysqli_query($conn, "SELECT id_mapel, nama_mapel FROM mapel ORDER BY nama_mapel");
$id_mapel_list = [];
while ($row = mysqli_fetch_assoc($mapel_query)) {
    $id_mapel_list[$row['id_mapel']] = $row['nama_mapel'];
}

// ==========================
// Query Guru
// ==========================
$guru_query = "
    SELECT g.*, m.nama_mapel 
    FROM guru g
    LEFT JOIN mapel m ON g.id_mapel = m.id_mapel
    ORDER BY g.id DESC
";
$data_guru = mysqli_query($conn, $guru_query);
if (!$data_guru) { die("Query guru gagal: " . mysqli_error($conn)); }

// ==========================
// Filter (Jika dipakai)
// ==========================
$search_guru = isset($_GET['search_guru']) ? mysqli_real_escape_string($conn, $_GET['search_guru']) : '';
$filter_kelas_guru = isset($_GET['filter_kelas_guru']) ? intval($_GET['filter_kelas_guru']) : 0;

// ==========================
// Tambah Guru
// ==========================
if (isset($_POST['tambah_guru'])) {
    $nama     = mysqli_real_escape_string($conn, $_POST['nama_guru']);
    $password = mysqli_real_escape_string($conn, $_POST['password_guru']);
    $status_guru = mysqli_real_escape_string($conn, $_POST['status_guru']);
    $kelas    = intval($_POST['kelas_guru']);
    $id_mapel = intval($_POST['id_mapel']);

    // Cek nama guru duplikat
    $cek_guru = mysqli_query($conn, "SELECT * FROM guru WHERE nama='$nama'");
    if(mysqli_num_rows($cek_guru) > 0){
        $_SESSION['error'] = "Nama guru sudah terdaftar!";
    } else {
        mysqli_query($conn, "INSERT INTO guru (nama, password, id_kelas, status_guru, id_mapel) 
                             VALUES ('$nama','$password','$kelas','$status_guru','$id_mapel')");
        $_SESSION['success'] = "Guru berhasil ditambahkan!";
    }
    header("Location: inputguru.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Input Guru</title>
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
    <div class="col-md-3 p-4 sidebar">
        <h3>Admin Dashboard</h3>
        <p>Selamat datang, <?= $_SESSION['nama'] ?? '' ?></p>
        <a href="logout.php" class="btn btn-danger mb-4 w-100">Logout</a>

        <ul class="nav flex-column mb-3">
            <li class="nav-item mb-2"><a class="nav-link btn btn-outline-primary" href="dashboard_admin.php">Dashboard Admin</a></li>
            <li class="nav-item mb-2"><a class="nav-link btn btn-outline-primary" href="inputsiswa.php">Input Siswa</a></li>
            <li class="nav-item mb-2"><a class="nav-link btn btn-outline-secondary" href="inputguru.php">Input Guru</a></li>
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
        <div class="card mb-4">
            <div class="card-header"><h4>Tambah Guru</h4></div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nama Guru</label>
                        <input type="text" name="nama_guru" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="text" name="password_guru" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status Guru</label>
                        <select name="status_guru" class="form-select" required>
                            <option value="">-- Pilih Status --</option>
                            <option value="Wali Kelas">Wali Kelas</option>
                            <option value="Guru Pelajaran">Guru Pelajaran</option>
                        </select>
                    </div>

                    <!-- Dropdown Mapel -->
                    <div class="mb-3">
                        <label class="form-label">Mata Pelajaran</label>
                        <select name="id_mapel" class="form-select" required>
                            <option value="">-- Pilih Mapel --</option>
                            <?php foreach($id_mapel_list as $id => $nama_mapel): ?>
                                <option value="<?= $id ?>"><?= htmlspecialchars($nama_mapel) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kelas</label>
                        <select name="kelas_guru" class="form-select" required>
                            <option value="">-- Pilih Kelas --</option>
                            <?php for($i=1;$i<=6;$i++): ?>
                                <option value="<?= $i ?>"><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <button type="submit" name="tambah_guru" class="btn btn-success">Tambah Guru</button>
                </form>
            </div>
        </div>

        <!-- Tabel Guru -->
        <h4>Daftar Guru</h4>
        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Kelas</th>
                    <th>Status</th>
                    <th>Mapel</th>
                    <th>Aksi</th>
                </tr>
                <?php $no=1; while($row=mysqli_fetch_assoc($data_guru)): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                        <td><?= $row['id_kelas'] ?></td>
                        <td><?= $row['status_guru'] ?></td>
                        <td><?= htmlspecialchars($row['nama_mapel'] ?? '-') ?></td>

                        <td>
                            <a href="edit_guru.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">✏️ Edit</a>
                            <a href="hapus_guru.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus guru?')">🗑️ Hapus</a>
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
