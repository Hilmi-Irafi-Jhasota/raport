<?php
session_start();
include "koneksi.php";

// Cek apakah ada parameter id
if (!isset($_GET['id'])) {
    header("Location: dashboard_admin.php");
    exit;
}

$id = intval($_GET['id']); // Pastikan id angka

// Ambil data siswa berdasarkan id
$result = mysqli_query($conn, "SELECT * FROM siswa WHERE id = $id");
if (mysqli_num_rows($result) == 0) {
    echo "Data siswa tidak ditemukan.";
    exit;
}

$siswa = mysqli_fetch_assoc($result);

// Proses update data
if (isset($_POST['update'])) {
    $nama  = mysqli_real_escape_string($conn, $_POST['nama']);
    $nis   = mysqli_real_escape_string($conn, $_POST['nis']);
    $kelas = intval($_POST['kelas']);

    $query = "UPDATE siswa SET nama='$nama', nis='$nis', kelas='$kelas' WHERE id=$id";
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Data siswa berhasil diperbarui!";
        header("Location: dashboard_admin.php");
        exit;
    } else {
        $_SESSION['error'] = "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .sidebar { height: 100vh; position: sticky; top: 0; overflow-y: auto; background: #f1f1f1; padding: 20px; }
    </style>
</head>
<body>
<div class="container-fluid">
<div class="row vh-100">

    <!-- Sidebar -->
    <div class="col-md-3 sidebar">
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
        <h2>Edit Siswa</h2>
       

        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nama Siswa</label>
                        <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($siswa['nama']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">NIS</label>
                        <input type="text" name="nis" class="form-control" value="<?= htmlspecialchars($siswa['nis']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kelas</label>
                        <select name="kelas" class="form-select" required>
                            <option value="">-- Pilih Kelas --</option>
                            <?php for ($i=1; $i<=6; $i++): ?>
                                <option value="<?= $i ?>" <?= ($siswa['kelas'] == $i) ? 'selected' : '' ?>><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <button type="submit" name="update" class="btn btn-success">💾 Simpan</button>
                </form>
            </div>
        </div>
    </div>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
