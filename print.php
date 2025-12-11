<?php
session_start();
include "koneksi.php";

// Cek id siswa
if (!isset($_GET['id'])) {
    die("ID siswa tidak ditemukan.");
}
$id_siswa = intval($_GET['id']);

// Ambil data siswa
$siswa_query = mysqli_query($conn, "SELECT * FROM siswa WHERE id = $id_siswa");
if (mysqli_num_rows($siswa_query) == 0) {
    die("Data siswa tidak ditemukan.");
}
$siswa = mysqli_fetch_assoc($siswa_query);

// Data sekolah, bisa diubah sesuai kebutuhan
$sekolah = "SMP Negeri 1 Contoh";
$alamat_sekolah = "Jl. Pendidikan No.123, Kota Contoh";
$tahun_ajaran = "2025/2026";
$semester = "Ganjil";
$fase = "1"; // contoh

// Ambil nilai dan capaian kompetensi
$nilai_query = mysqli_query($conn, "
    SELECT m.nama_mapel, n.nilai
    FROM nilai n
    JOIN mapel m ON n.id_mapel = m.id_mapel
    WHERE n.id_siswa = $id_siswa
");

// Simpan nilai
$nilai_data = [];
while ($row = mysqli_fetch_assoc($nilai_query)) {
    $nilai_data[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Print Rapor <?= htmlspecialchars($siswa['nama']) ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; font-size: 12pt; }
        h2 { text-align: center; }
        table.header-info { width: 100%; margin-bottom: 20px; }
        table.header-info td { padding: 4px 8px; }
        table.raport { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.raport th, table.raport td {
            border: 1px solid #000;
            padding: 6px 10px;
            vertical-align: top;
        }
        table.raport th {
            background-color: #ddd;
            text-align: center;
        }
        .no { width: 30px; text-align: center; }
        .nilai { width: 70px; text-align: center; }
        .capaian-kompetensi { width: 50%; }
        @media print {
            button#printBtn { display: none; }
        }
    </style>
</head>
<body>

<h2>LAPORAN HASIL BELAJAR<br>(RAPOR)</h2>

<table class="header-info">
    <tr>
        <td>Nama Peserta Didik</td><td>:</td><td><?= htmlspecialchars($siswa['nama']) ?></td>
        <td>Kelas</td><td>:</td><td><?= htmlspecialchars($siswa['kelas']) ?></td>
    </tr>
    <tr>
        <td>NISN / NIS</td><td>:</td><td><?= htmlspecialchars($siswa['nis']) ?></td>
        <td>Fase</td><td>:</td><td><?= htmlspecialchars($fase) ?></td>
    </tr>
    <tr>
        <td>Sekolah</td><td>:</td><td><?= htmlspecialchars($sekolah) ?></td>
        <td>Semester</td><td>:</td><td><?= htmlspecialchars($semester) ?></td>
    </tr>
    <tr>
        <td>Alamat</td><td>:</td><td><?= htmlspecialchars($alamat_sekolah) ?></td>
        <td>Tahun Ajaran</td><td>:</td><td><?= htmlspecialchars($tahun_ajaran) ?></td>
    </tr>
</table>

<hr>

<table class="raport">
    <thead>
        <tr>
            <th class="no">No</th>
            <th>Mata Pelajaran</th>
            <th class="nilai">Nilai Akhir</th>
            <th class="capaian-kompetensi">Capaian Kompetensi</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($nilai_data) > 0): ?>
            <?php $no = 1; foreach ($nilai_data as $item): ?>
            <tr>
                <td class="no"><?= $no++ ?></td>
                <td><?= htmlspecialchars($item['nama_mapel']) ?></td>
                <td class="nilai"><?= htmlspecialchars($item['nilai']) ?></td>
                <td><?= nl2br(htmlspecialchars($item['capaian_kompetensi'])) ?></td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="4" style="text-align:center;">Belum ada data nilai</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<button id="printBtn" onclick="window.print()" style="margin-top: 20px;">🖨️ Print</button>

</body>
</html>
