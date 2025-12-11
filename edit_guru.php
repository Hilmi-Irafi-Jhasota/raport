<?php
session_start();
include "koneksi.php";

// Pastikan admin login
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: index.php");
    exit;
}

// Ambil data guru
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$guru_data = [];
if($id){
    $result = mysqli_query($conn, "SELECT * FROM guru WHERE id='$id'");
    if(mysqli_num_rows($result) > 0){
        $guru_data = mysqli_fetch_assoc($result);
    } else {
        $_SESSION['error'] = "Guru tidak ditemukan!";
        header("Location: daftar_guru.php");
        exit;
    }
}

// Ambil daftar mapel
$mapel_query = mysqli_query($conn, "SELECT * FROM mapel ORDER BY nama_mapel ASC");

// Status enum
$status_guru_options = ['Wali Kelas', 'Guru Pelajaran'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Guru</title>
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
        <h3>Admin Dashboard</h3>
        <p>Selamat datang, <?= htmlspecialchars($_SESSION['nama']) ?></p>
        <a href="logout.php" class="btn btn-danger mb-4 w-100">Logout</a>

        <ul class="nav flex-column mb-3">
            <li class="nav-item mb-2"><a class="nav-link btn btn-outline-primary" href="dashboard_admin.php">Dashboard Admin</a></li>
            <li class="nav-item mb-2"><a class="nav-link btn btn-outline-primary" href="inputsiswa.php">Input Siswa</a></li>
            <li class="nav-item mb-2"><a class="nav-link btn btn-outline-secondary" href="inputguru.php">Input Guru</a></li>
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

    <!-- Konten utama -->
    <div class="col-md-9 p-4 overflow-auto" style="height: 100vh;">
        <h3>Edit Guru</h3>

        <form method="POST" action="proses_edit_guru.php">
            <input type="hidden" name="id" value="<?= $guru_data['id'] ?>">

            <div class="mb-3">
                <label class="form-label">Nama Guru</label>
                <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($guru_data['nama']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="text" name="password" class="form-control" value="<?= htmlspecialchars($guru_data['password']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Status Guru</label>
                <select name="status_guru" class="form-select" required>
                    <option value="">-- Pilih Status --</option>
                    <?php foreach($status_guru_options as $status): ?>
                        <option value="<?= $status ?>" <?= ($guru_data['status_guru'] == $status) ? 'selected' : '' ?>><?= $status ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Mata Pelajaran</label>
                <select name="id_mapel" class="form-select" required>
                    <option value="">-- Pilih Mapel --</option>
                    <?php while($m = mysqli_fetch_assoc($mapel_query)): ?>
                        <option value="<?= $m['id_mapel'] ?>" <?= ($guru_data['id_mapel'] == $m['id_mapel']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($m['nama_mapel']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Kelas</label>
                <select name="kelas_guru" class="form-select" required>
                    <option value="">-- Pilih Kelas --</option>
                    <?php for($i=1;$i<=6;$i++): ?>
                        <option value="<?= $i ?>" <?= ($guru_data['id_kelas'] == $i) ? 'selected' : '' ?>><?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="inputguru.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
