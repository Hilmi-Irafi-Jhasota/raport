-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 11, 2025 at 03:37 AM
-- Server version: 10.4.19-MariaDB
-- PHP Version: 7.4.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `raport`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `nama_lengkap`) VALUES
(1, 'admin1', 'admin1', 'hilmi irafi');

-- --------------------------------------------------------

--
-- Table structure for table `guru`
--

CREATE TABLE `guru` (
  `id` int(10) NOT NULL,
  `nama` varchar(25) NOT NULL,
  `id_mapel` int(11) NOT NULL,
  `status_guru` enum('Wali Kelas','Guru Pelajaran') NOT NULL,
  `password` varchar(25) NOT NULL,
  `id_kelas` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `guru`
--

INSERT INTO `guru` (`id`, `nama`, `id_mapel`, `status_guru`, `password`, `id_kelas`) VALUES
(4, 'ojak', 1, 'Wali Kelas', '1234', 3),
(12, 'jeya', 1, 'Wali Kelas', '1234', 5),
(13, 'cipa', 13, 'Wali Kelas', '1234', 3);

-- --------------------------------------------------------

--
-- Table structure for table `kelas`
--

CREATE TABLE `kelas` (
  `id_kelas` int(11) NOT NULL,
  `kelas` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `kelas`
--

INSERT INTO `kelas` (`id_kelas`, `kelas`) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(5, 5),
(6, 6),
(9, 9);

-- --------------------------------------------------------

--
-- Table structure for table `mapel`
--

CREATE TABLE `mapel` (
  `id_mapel` int(11) NOT NULL,
  `nama_mapel` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `mapel`
--

INSERT INTO `mapel` (`id_mapel`, `nama_mapel`) VALUES
(1, 'matematika'),
(2, 'B.indonesia'),
(3, 'B.Inggris'),
(4, 'Matematika'),
(5, 'Bahasa Indonesia'),
(6, 'Bahasa Inggris'),
(7, 'Ilmu Pengetahuan Alam (IPA)'),
(8, 'Ilmu Pengetahuan Sosial (IPS)'),
(9, 'Pendidikan Jasmani, Olahraga, dan Kesehatan (PJOK)'),
(10, 'Pendidikan Agama'),
(11, 'Seni Budaya'),
(12, 'Prakarya dan Kewirausahaan'),
(13, 'TIK (Teknologi Informasi dan Komunikasi)');

-- --------------------------------------------------------

--
-- Table structure for table `nilai`
--

CREATE TABLE `nilai` (
  `id` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `id_guru` int(11) NOT NULL,
  `id_mapel` int(11) NOT NULL,
  `nilai` int(11) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `tanggal_input` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `nilai`
--

INSERT INTO `nilai` (`id`, `id_siswa`, `id_guru`, `id_mapel`, `nilai`, `keterangan`, `tanggal_input`) VALUES
(3, 24, 4, 1, 91, '', '2025-12-08 08:29:34');

-- --------------------------------------------------------

--
-- Table structure for table `siswa`
--

CREATE TABLE `siswa` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `nis` varchar(50) NOT NULL,
  `kelas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `siswa`
--

INSERT INTO `siswa` (`id`, `nama`, `nis`, `kelas`) VALUES
(1, 'akbar', '12345', 2),
(3, '0087654301', '0', 1),
(4, '0087654302', '0', 1),
(5, '0087654303', '0', 2),
(6, '0087654304', '0', 2),
(7, '0087654305', '0', 3),
(8, '0087654306', '0', 3),
(9, '0087654307', '0', 4),
(10, '0087654308', '0', 4),
(11, '0087654309', '0', 5),
(12, '0087654310', '0', 6),
(13, '0087654301', '0', 1),
(14, '0087654302', '0', 1),
(15, '0087654303', '0', 2),
(16, '0087654304', '0', 2),
(17, '0087654305', '0', 3),
(18, '0087654306', '0', 3),
(19, '0087654307', '0', 4),
(20, '0087654308', '0', 4),
(21, '0087654309', '0', 5),
(22, '0087654310', '0', 6),
(23, 'syifa', '0', 1),
(24, 'syifa', '12345', 3),
(25, '0087654301', 'Aulia Putri Ramadhani', 1),
(26, '0087654302', 'Damar Bagas Prakoso', 1),
(27, '0087654303', 'Salsabila Nur Rizky', 2),
(28, '0087654304', 'Muhammad Arya Putra', 2),
(29, '0087654305', 'Keisha Annisa Maharani', 3),
(30, '0087654306', 'Rafli Akbar Nugraha', 3),
(31, '0087654307', 'Zahra Mutia Salsabila', 4),
(32, '0087654308', 'Fikri Rizqullah Ramadhan', 4),
(33, '0087654309', 'Laila Azzahra Hanun', 5),
(34, '0087654310', 'Bintang Pratama Putra', 6),
(35, '0087654301', 'Aulia Putri Ramadhani', 1),
(36, '0087654302', 'Damar Bagas Prakoso', 1),
(37, '0087654303', 'Salsabila Nur Rizky', 2),
(38, '0087654304', 'Muhammad Arya Putra', 2),
(39, '0087654305', 'Keisha Annisa Maharani', 3),
(40, '0087654306', 'Rafli Akbar Nugraha', 3),
(41, '0087654307', 'Zahra Mutia Salsabila', 4),
(42, '0087654308', 'Fikri Rizqullah Ramadhan', 4),
(43, '0087654309', 'Laila Azzahra Hanun', 5),
(44, '0087654310', 'Bintang Pratama Putra', 6),
(45, '0087654301', 'Aulia Putri Ramadhani', 1),
(46, '0087654302', 'Damar Bagas Prakoso', 1),
(47, '0087654303', 'Salsabila Nur Rizky', 2),
(48, '0087654304', 'Muhammad Arya Putra', 2),
(49, '0087654305', 'Keisha Annisa Maharani', 3),
(50, '0087654306', 'Rafli Akbar Nugraha', 3),
(51, '0087654307', 'Zahra Mutia Salsabila', 4),
(52, '0087654308', 'Fikri Rizqullah Ramadhan', 4),
(53, '0087654309', 'Laila Azzahra Hanun', 5),
(54, '0087654310', 'Bintang Pratama Putra', 6),
(55, '0087654301', 'Aulia Putri Ramadhani', 1),
(56, '0087654302', 'Damar Bagas Prakoso', 1),
(57, '0087654303', 'Salsabila Nur Rizky', 2),
(58, '0087654304', 'Muhammad Arya Putra', 2),
(59, '0087654305', 'Keisha Annisa Maharani', 3),
(60, '0087654306', 'Rafli Akbar Nugraha', 3),
(61, '0087654307', 'Zahra Mutia Salsabila', 4),
(62, '0087654308', 'Fikri Rizqullah Ramadhan', 4),
(63, '0087654309', 'Laila Azzahra Hanun', 5),
(64, '0087654310', 'Bintang Pratama Putra', 6),
(65, '0087654301', 'Aulia Putri Ramadhani', 1),
(66, '0087654302', 'Damar Bagas Prakoso', 1),
(67, '0087654303', 'Salsabila Nur Rizky', 2),
(68, '0087654304', 'Muhammad Arya Putra', 2),
(69, '0087654305', 'Keisha Annisa Maharani', 3),
(70, '0087654306', 'Rafli Akbar Nugraha', 3),
(71, '0087654307', 'Zahra Mutia Salsabila', 4),
(72, '0087654308', 'Fikri Rizqullah Ramadhan', 4),
(73, '0087654309', 'Laila Azzahra Hanun', 5),
(74, '0087654310', 'Bintang Pratama Putra', 6),
(75, '0087654301', 'Aulia Putri Ramadhani', 1),
(76, '0087654302', 'Damar Bagas Prakoso', 1),
(77, '0087654303', 'Salsabila Nur Rizky', 2),
(78, '0087654304', 'Muhammad Arya Putra', 2),
(79, '0087654305', 'Keisha Annisa Maharani', 3),
(80, '0087654306', 'Rafli Akbar Nugraha', 3),
(81, '0087654307', 'Zahra Mutia Salsabila', 4),
(82, '0087654308', 'Fikri Rizqullah Ramadhan', 4),
(83, '0087654309', 'Laila Azzahra Hanun', 5),
(84, '0087654310', 'Bintang Pratama Putra', 6),
(85, '0087654301', 'Aulia Putri Ramadhani', 1),
(86, '0087654302', 'Damar Bagas Prakoso', 1),
(87, '0087654303', 'Salsabila Nur Rizky', 2),
(88, '0087654304', 'Muhammad Arya Putra', 2),
(89, '0087654305', 'Keisha Annisa Maharani', 3),
(90, '0087654306', 'Rafli Akbar Nugraha', 3),
(91, '0087654307', 'Zahra Mutia Salsabila', 4),
(92, '0087654308', 'Fikri Rizqullah Ramadhan', 4),
(93, '0087654309', 'Laila Azzahra Hanun', 5),
(94, '0087654310', 'Bintang Pratama Putra', 6),
(95, '0087654301', 'Aulia Putri Ramadhani', 1),
(96, '0087654302', 'Damar Bagas Prakoso', 1),
(97, '0087654303', 'Salsabila Nur Rizky', 2),
(98, '0087654304', 'Muhammad Arya Putra', 2),
(99, '0087654305', 'Keisha Annisa Maharani', 3),
(100, '0087654306', 'Rafli Akbar Nugraha', 3),
(101, '0087654307', 'Zahra Mutia Salsabila', 4),
(102, '0087654308', 'Fikri Rizqullah Ramadhan', 4),
(103, '0087654309', 'Laila Azzahra Hanun', 5),
(104, '0087654310', 'Bintang Pratama Putra', 6),
(105, 'kabar tanjung', '12314324', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `guru`
--
ALTER TABLE `guru`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_kelas` (`id_kelas`),
  ADD KEY `id_mapel` (`id_mapel`);

--
-- Indexes for table `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`id_kelas`);

--
-- Indexes for table `mapel`
--
ALTER TABLE `mapel`
  ADD PRIMARY KEY (`id_mapel`);

--
-- Indexes for table `nilai`
--
ALTER TABLE `nilai`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_siswa` (`id_siswa`),
  ADD KEY `id_guru` (`id_guru`),
  ADD KEY `id_mapel` (`id_mapel`);

--
-- Indexes for table `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kelas` (`kelas`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `guru`
--
ALTER TABLE `guru`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `kelas`
--
ALTER TABLE `kelas`
  MODIFY `id_kelas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `mapel`
--
ALTER TABLE `mapel`
  MODIFY `id_mapel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `nilai`
--
ALTER TABLE `nilai`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `siswa`
--
ALTER TABLE `siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `guru`
--
ALTER TABLE `guru`
  ADD CONSTRAINT `fk_guru_mapel` FOREIGN KEY (`id_mapel`) REFERENCES `mapel` (`id_mapel`),
  ADD CONSTRAINT `guru_ibfk_1` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id_kelas`);

--
-- Constraints for table `nilai`
--
ALTER TABLE `nilai`
  ADD CONSTRAINT `nilai_ibfk_1` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id`),
  ADD CONSTRAINT `nilai_ibfk_2` FOREIGN KEY (`id_guru`) REFERENCES `guru` (`id`),
  ADD CONSTRAINT `nilai_ibfk_3` FOREIGN KEY (`id_mapel`) REFERENCES `mapel` (`id_mapel`);

--
-- Constraints for table `siswa`
--
ALTER TABLE `siswa`
  ADD CONSTRAINT `siswa_ibfk_1` FOREIGN KEY (`kelas`) REFERENCES `kelas` (`id_kelas`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
