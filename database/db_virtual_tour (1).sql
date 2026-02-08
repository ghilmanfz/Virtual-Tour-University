-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 10, 2025 at 07:07 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_virtual_tour`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_admin`
--

CREATE TABLE `tb_admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_admin`
--

INSERT INTO `tb_admin` (`id`, `username`, `password`, `created_at`) VALUES
(11, 'admin', '$2y$10$K8rdvR.xAluJYH/CF/FN6.tAl.T7VRHXDhgnjDWEmrdYdfDHVL/rG', '2025-09-10 17:06:26');

-- --------------------------------------------------------

--
-- Table structure for table `tb_content`
--

CREATE TABLE `tb_content` (
  `id` int(11) NOT NULL,
  `section` varchar(50) NOT NULL,
  `content_key` varchar(100) NOT NULL,
  `content_value` text NOT NULL,
  `content_type` enum('text','image','url') DEFAULT 'text',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_content`
--

INSERT INTO `tb_content` (`id`, `section`, `content_key`, `content_value`, `content_type`, `updated_at`) VALUES
(12, '', 'hero_title', 'SELAMAT DATANG DI ', 'text', '2025-09-10 16:15:49'),
(13, '', 'hero_subtitle', 'VIRTUAL TOUR PRODI SISTEM INFORMASI', 'text', '2025-09-10 16:10:42'),
(14, '', 'hero_description', 'Jelajahi fasilitas dan lingkungan kampus Universitas Pamulang secara virtual', 'text', '2025-09-10 16:10:42'),
(15, '', 'facilities_title', 'FASILITAS UNPAM nn', 'text', '2025-09-10 16:20:11'),
(16, '', 'facilities_description', 'Temukan berbagai fasilitas modern yang mendukung proses belajar mengajar di Program Studi Sistem Informasi', 'text', '2025-09-10 16:10:42'),
(17, '', 'about_title', 'Tentang Program Studi Sistem Informasi rawr', 'text', '2025-09-10 17:01:52'),
(18, '', 'about_description', 'Program Studi Sistem Informasi UNPAM menghasilkan lulusan yang kompeten di bidang teknologi informasi dan sistem informasi.', 'text', '2025-09-10 16:10:42'),
(19, '', 'contact_title', 'Hubungi Kami', 'text', '2025-09-10 16:10:42'),
(20, '', 'contact_description', 'Dapatkan informasi lebih lanjut tentang program studi dan fasilitas kampus.', 'text', '2025-09-10 16:10:42'),
(21, '', 'footer_text', 'Universitas Pamulang - Program Studi Sistem Informasi', 'text', '2025-09-10 16:10:42'),
(22, '', 'welcome_message', 'Selamat datang di website resmi Virtual Tour Prodi Sistem Informasi UNPAM', 'text', '2025-09-10 16:10:42');

-- --------------------------------------------------------

--
-- Table structure for table `tb_daily_stats`
--

CREATE TABLE `tb_daily_stats` (
  `id` int(11) NOT NULL,
  `stat_date` date DEFAULT NULL,
  `total_visitors` int(11) DEFAULT 0,
  `virtual_tour_visitors` int(11) DEFAULT 0,
  `kritik_saran_count` int(11) DEFAULT 0,
  `admin_visits` int(11) DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_facilities`
--

CREATE TABLE `tb_facilities` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_facilities`
--

INSERT INTO `tb_facilities` (`id`, `name`, `description`, `image`, `is_active`, `created_at`, `updated_at`) VALUES
(10, 'Perpustakaan', 'bisa membaca skripsi saya yang sudh di upload, Semangat Skripsisiannya adik adik abang lulus duluan ye \r\n', 'asset/perpustakaan 2.webp', 1, '2025-09-10 16:24:01', '2025-09-10 17:01:30'),
(12, 'Ruang kela ber AC', 'rawrr', 'asset/kelas.jpg', 1, '2025-09-10 16:40:14', '2025-09-10 16:40:14');

-- --------------------------------------------------------

--
-- Table structure for table `tb_kritik_saran`
--

CREATE TABLE `tb_kritik_saran` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `kontak` varchar(100) NOT NULL,
  `pesan` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_kritik_saran`
--

INSERT INTO `tb_kritik_saran` (`id`, `nama`, `kontak`, `pesan`, `created_at`) VALUES
(5, 'Budi Santoso', 'budi@email.com', 'Website virtual tour sangat menarik! Saya tertarik untuk mendaftar kuliah di UNPAM. Bisakah saya mendapat informasi lebih lanjut tentang program sistem informasi?', '2025-09-10 15:48:51'),
(7, 'Ahmad Rahman', 'ahmad.rahman@gmail.com', 'Saya alumni UNPAM dan sangat senang melihat perkembangan kampus dengan teknologi virtual tour ini. Semoga bisa terus berkembang!', '2025-09-10 15:48:51'),
(8, 'halah ', 'kaskka', 'nasanj', '2025-09-10 15:49:08'),
(9, 'rawr', '0897827737', 'saya si ganteng gatau kalo lo\r\n', '2025-09-10 16:38:32'),
(10, 'bisa', 'bisa bis', 'bisaaaaa', '2025-09-10 16:38:48');

-- --------------------------------------------------------

--
-- Table structure for table `tb_visitor_stats`
--

CREATE TABLE `tb_visitor_stats` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `page_visited` varchar(255) DEFAULT NULL,
  `visit_date` date DEFAULT NULL,
  `visit_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_admin` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_visitor_stats`
--

INSERT INTO `tb_visitor_stats` (`id`, `ip_address`, `user_agent`, `page_visited`, `visit_date`, `visit_time`, `is_admin`) VALUES
(1, '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', 'virtual_tour.php', '2025-09-10', '2025-09-10 16:49:29', 0),
(2, '192.168.1.101', 'Mozilla/5.0 (Macintosh; Intel Mac OS X)', 'index.php', '2025-09-10', '2025-09-10 16:49:29', 0),
(3, '192.168.1.102', 'Mozilla/5.0 (iPhone; CPU iPhone OS)', 'virtual_tour.php', '2025-09-09', '2025-09-10 16:49:29', 0),
(4, '192.168.1.103', 'Mozilla/5.0 (Android 11)', 'fasilitas.php', '2025-09-09', '2025-09-10 16:49:29', 0),
(5, '192.168.1.104', 'Mozilla/5.0 (Windows NT 10.0)', 'virtual_tour.php', '2025-09-03', '2025-09-10 16:49:29', 0),
(6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'index.php', '2025-09-10', '2025-09-10 17:01:04', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tb_vr_hotspots`
--

CREATE TABLE `tb_vr_hotspots` (
  `id` int(11) NOT NULL,
  `scene_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `target_scene` varchar(100) NOT NULL,
  `position_x` decimal(10,2) NOT NULL,
  `position_y` decimal(10,2) NOT NULL,
  `position_z` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_vr_hotspots`
--

INSERT INTO `tb_vr_hotspots` (`id`, `scene_id`, `name`, `target_scene`, `position_x`, `position_y`, `position_z`, `created_at`) VALUES
(1, 1, 'Ke Ruang Kelas', 'classroom', 2.00, 1.00, -3.00, '2025-09-10 15:36:25'),
(2, 1, 'Ke Perpustakaan', 'library', -2.00, 1.00, -3.00, '2025-09-10 15:36:25'),
(3, 2, 'Kembali ke Entrance', 'entrance', 0.00, 1.00, 3.00, '2025-09-10 15:36:25'),
(4, 2, 'Ke Auditorium', 'auditorium', 3.00, 1.00, 0.00, '2025-09-10 15:36:25'),
(5, 3, 'Kembali ke Entrance', 'entrance', 0.00, 1.00, 3.00, '2025-09-10 15:36:25'),
(6, 4, 'Ke Ruang Kelas', 'classroom', -3.00, 1.00, 0.00, '2025-09-10 15:36:25');

-- --------------------------------------------------------

--
-- Table structure for table `tb_vr_scenes`
--

CREATE TABLE `tb_vr_scenes` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `scene_key` varchar(100) NOT NULL,
  `image_360` text NOT NULL,
  `icon` varchar(100) DEFAULT 'fas fa-door-open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_vr_scenes`
--

INSERT INTO `tb_vr_scenes` (`id`, `name`, `description`, `scene_key`, `image_360`, `icon`, `created_at`) VALUES
(1, 'Entrance Gate', 'Gerbang masuk Universitas Pamulang', 'entrance', 'https://static.republika.co.id/uploads/member/images/news/2x4cu8nrv8.jpg', 'fas fa-door-open', '2025-09-10 15:36:25'),
(2, 'Ruang Kelas', 'Ruang kuliah Prodi Sistem Informasi', 'classroom', 'asset/kelas.jpg', 'fas fa-chalkboard-teacher', '2025-09-10 15:36:25'),
(3, 'Perpustakaan', 'Perpustakaan dengan koleksi lengkap', 'library', 'asset/perpustakaan 2.webp', 'fas fa-book', '2025-09-10 15:36:25'),
(4, 'Auditorium', 'Auditorium untuk acara besar', 'auditorium', 'asset/Auditorium.webp', 'fas fa-theater-masks', '2025-09-10 15:36:25');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_admin`
--
ALTER TABLE `tb_admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_content`
--
ALTER TABLE `tb_content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_daily_stats`
--
ALTER TABLE `tb_daily_stats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `stat_date` (`stat_date`);

--
-- Indexes for table `tb_facilities`
--
ALTER TABLE `tb_facilities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_kritik_saran`
--
ALTER TABLE `tb_kritik_saran`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_visitor_stats`
--
ALTER TABLE `tb_visitor_stats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_date` (`visit_date`),
  ADD KEY `idx_page` (`page_visited`),
  ADD KEY `idx_admin` (`is_admin`);

--
-- Indexes for table `tb_vr_hotspots`
--
ALTER TABLE `tb_vr_hotspots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `scene_id` (`scene_id`);

--
-- Indexes for table `tb_vr_scenes`
--
ALTER TABLE `tb_vr_scenes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `scene_key` (`scene_key`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_admin`
--
ALTER TABLE `tb_admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tb_content`
--
ALTER TABLE `tb_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `tb_daily_stats`
--
ALTER TABLE `tb_daily_stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_facilities`
--
ALTER TABLE `tb_facilities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tb_kritik_saran`
--
ALTER TABLE `tb_kritik_saran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tb_visitor_stats`
--
ALTER TABLE `tb_visitor_stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tb_vr_hotspots`
--
ALTER TABLE `tb_vr_hotspots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tb_vr_scenes`
--
ALTER TABLE `tb_vr_scenes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_vr_hotspots`
--
ALTER TABLE `tb_vr_hotspots`
  ADD CONSTRAINT `tb_vr_hotspots_ibfk_1` FOREIGN KEY (`scene_id`) REFERENCES `tb_vr_scenes` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
