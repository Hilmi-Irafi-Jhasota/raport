if (isset($_POST['tambah_bulk'])) {
    $bulk_data = $_POST['bulk_siswa'];
    
    // Pisahkan tiap baris
    $rows = explode("\n", $bulk_data);

    foreach ($rows as $row) {
        $row = trim($row);
        if (empty($row)) continue;

        // Pisahkan tiap kolom (Nama, NIS, Kelas)
        $cols = explode(",", $row);
        if (count($cols) != 3) continue;

        $nama  = mysqli_real_escape_string($conn, trim($cols[0]));
        $nis   = mysqli_real_escape_string($conn, trim($cols[1]));
        $kelas = mysqli_real_escape_string($conn, trim($cols[2]));

        mysqli_query($conn, "INSERT INTO siswa (nama, nis, kelas) VALUES ('$nama', '$nis', '$kelas')")
            or die(mysqli_error($conn));
    }

    header("Location: dashboard.php");
    exit;
}
