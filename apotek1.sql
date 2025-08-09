-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 09 Agu 2025 pada 18.05
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `apotek1`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

CREATE TABLE `kategori` (
  `id` int(11) NOT NULL,
  `nama_kategori` varchar(50) NOT NULL,
  `gambar` varchar(255) NOT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kategori`
--

INSERT INTO `kategori` (`id`, `nama_kategori`, `gambar`, `keterangan`) VALUES
(3, 'bagaimana', 'uploads/1754283547_logo_png.png', 'caranya ini0'),
(4, 'roni', 'uploads/1754283555_gambar sekolah.jpg', 'keren'),
(5, 'wawan', 'uploads/1754279096_gambar sekolah.jpg', 'mantap');

-- --------------------------------------------------------

--
-- Struktur dari tabel `keranjang`
--

CREATE TABLE `keranjang` (
  `id` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_produk` int(11) DEFAULT NULL,
  `jumlah` int(11) DEFAULT NULL,
  `waktu_dibuat` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `login_log`
--

CREATE TABLE `login_log` (
  `id` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `waktu_login` datetime DEFAULT current_timestamp(),
  `waktu_logout` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `members`
--

CREATE TABLE `members` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `poin` int(11) DEFAULT 0,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `members`
--

INSERT INTO `members` (`id`, `nama`, `no_hp`, `poin`, `status`) VALUES
(9, 'rahmat', '09898989', 8, 'aktif');

-- --------------------------------------------------------

--
-- Struktur dari tabel `produk`
--

CREATE TABLE `produk` (
  `id` int(11) NOT NULL,
  `nama_produk` varchar(100) NOT NULL,
  `id_kategori` int(11) DEFAULT NULL,
  `harga_beli` decimal(10,0) NOT NULL,
  `harga_jual` decimal(10,0) NOT NULL,
  `stok` int(11) NOT NULL,
  `kadaluarsa` date NOT NULL,
  `gambar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `produk`
--

INSERT INTO `produk` (`id`, `nama_produk`, `id_kategori`, `harga_beli`, `harga_jual`, `stok`, `kadaluarsa`, `gambar`) VALUES
(83, 'budi1', 5, 2000, 5000, 110, '2025-08-20', 'Logo_smkn71.jpg'),
(84, 'bodrex', 5, 2000, 5000, 38, '2025-08-31', 'a782a5b475f99b995245eb4b1a6a11f4.jpg'),
(85, 'mouse', 3, 2000, 5000, 0, '2025-08-20', 'Logo_smkn71.jpg'),
(86, 'ronai cabeyyy', 4, 2000, 5000, 1979, '2025-08-29', 'a782a5b475f99b995245eb4b1a6a11f4.jpg'),
(87, 'goni', 4, 2000, 5000, 200, '2025-08-06', '674002ec5b1ce.jpg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_member` int(11) DEFAULT NULL,
  `nama_member` varchar(100) NOT NULL,
  `tanggal` datetime DEFAULT current_timestamp(),
  `total_harga` decimal(10,2) DEFAULT NULL,
  `dibayar` int(255) NOT NULL,
  `kembalian` int(255) NOT NULL,
  `no_hp` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transaksi`
--

INSERT INTO `transaksi` (`id`, `id_user`, `id_member`, `nama_member`, `tanggal`, `total_harga`, `dibayar`, `kembalian`, `no_hp`) VALUES
(74, 60, 9, 'rahmat', '2025-08-08 19:46:25', 5000.00, 20000, 15000, '09898989'),
(75, 60, 9, 'rahmat', '2025-08-08 20:37:38', 5000.00, 20000, 15000, '09898989'),
(76, 60, 9, 'rahmat', '2025-08-08 20:44:59', 5000.00, 20000, 15000, '09898989'),
(77, 60, 9, 'rahmat', '2025-08-08 20:48:41', 5000.00, 20000, 15000, '09898989'),
(78, 60, 9, 'rahmat', '2025-08-08 20:54:34', 5000.00, 20000, 15000, '09898989'),
(79, 60, 9, 'rahmat', '2025-08-08 21:00:59', 5000.00, 20000, 15000, '09898989'),
(80, 60, 9, 'rahmat', '2025-08-08 21:03:43', 5000.00, 20000, 15000, '09898989'),
(81, 60, 9, 'rahmat', '2025-08-08 21:09:33', 5000.00, 20000, 15000, '09898989'),
(82, 60, 9, 'rahmat', '2025-08-08 21:09:54', 5000.00, 20000, 15000, '09898989'),
(83, 60, 9, 'rahmat', '2025-08-08 21:10:29', 5000.00, 20000, 15000, '09898989'),
(84, 60, 9, 'rahmat', '2025-08-08 21:10:52', 5000.00, 20000, 15000, '09898989'),
(85, 60, 9, '', '2025-08-08 21:13:50', 5000.00, 20000, 15000, '09898989'),
(86, 60, 9, '', '2025-08-08 21:15:07', 5000.00, 20000, 15000, '09898989'),
(87, 60, 9, '', '2025-08-08 21:28:05', 10000.00, 20000, 10000, '09898989'),
(88, 60, 9, '', '2025-08-08 21:28:20', 5000.00, 20000, 15000, '09898989'),
(89, 60, 9, '', '2025-08-08 21:31:20', 5000.00, 20000, 15000, '09898989'),
(90, 60, 9, 'rahmat', '2025-08-08 21:37:52', 5000.00, 20000, 15000, '09898989'),
(91, 60, 9, 'rahmat', '2025-08-09 14:16:55', 5000.00, 20000, 15000, '09898989'),
(92, 60, 9, 'rahmat', '2025-08-09 14:21:45', 5000.00, 20000, 15000, '09898989'),
(93, 60, 9, 'rahmat', '2025-08-09 14:25:58', 10000.00, 20000, 10000, '09898989'),
(94, 60, 9, 'rahmat', '2025-08-09 14:29:27', 5000.00, 20000, 15000, '09898989'),
(95, 60, 9, 'rahmat', '2025-08-09 14:33:00', 5000.00, 20000, 15000, '09898989'),
(96, NULL, 9, '', '2025-08-09 15:33:33', 5000.00, 20000, 20000, ''),
(97, NULL, 9, '', '2025-08-09 15:34:39', 5000.00, 20000, 20000, ''),
(98, NULL, 9, '', '2025-08-09 15:35:32', 5000.00, 20000, 20000, ''),
(99, NULL, 9, '', '2025-08-09 15:38:19', 5000.00, 20000, 20000, ''),
(100, NULL, 9, '', '2025-08-09 15:39:29', 5000.00, 20000, 13000, ''),
(101, NULL, 9, '', '2025-08-09 15:40:51', 10000.00, 200000, 188000, ''),
(102, NULL, 9, '', '2025-08-09 15:43:34', 5000.00, 5000, 0, ''),
(103, NULL, 9, '', '2025-08-09 15:47:24', 5000.00, 20000, 15000, ''),
(104, NULL, 9, '', '2025-08-09 15:51:46', 5000.00, 20000, 15000, ''),
(105, NULL, 9, '', '2025-08-09 15:52:27', 10000.00, 20000, 11000, ''),
(106, NULL, 9, '', '2025-08-09 15:53:03', 10000.00, 20000, 10000, ''),
(107, NULL, 9, '', '2025-08-09 15:53:27', 10000.00, 20000, 11900, ''),
(108, NULL, 9, '', '2025-08-09 15:57:49', 10000.00, 20000, 10000, ''),
(109, NULL, 9, '', '2025-08-09 16:01:58', 10000.00, 20000, 10000, ''),
(110, NULL, 9, '', '2025-08-09 16:07:41', 15000.00, 20000, 7800, ''),
(111, NULL, 9, '', '2025-08-09 16:08:43', 15000.00, 20000, 7800, ''),
(112, NULL, 9, '', '2025-08-09 16:11:34', 5000.00, 10000, 5000, ''),
(113, NULL, 9, '', '2025-08-09 16:12:09', 5000.00, 2000, -3000, ''),
(114, NULL, 9, '', '2025-08-09 16:12:43', 5000.00, 20000, 16000, ''),
(116, NULL, NULL, '', '2025-08-09 16:21:52', 0.00, 0, 0, ''),
(117, NULL, NULL, '', '2025-08-09 16:26:14', 0.00, 0, 0, ''),
(118, NULL, NULL, '', '2025-08-09 16:27:02', 0.00, 0, 0, ''),
(119, NULL, NULL, '', '2025-08-09 16:30:34', 0.00, 0, 0, ''),
(120, NULL, NULL, '', '2025-08-09 16:32:20', 0.00, 0, 0, ''),
(121, NULL, NULL, '', '2025-08-09 16:32:44', 0.00, 0, 0, ''),
(122, NULL, NULL, '', '2025-08-09 16:36:49', 0.00, 0, 0, ''),
(123, NULL, NULL, '', '2025-08-09 16:37:30', 0.00, 0, 0, ''),
(124, NULL, NULL, '', '2025-08-09 16:42:53', 0.00, 0, 0, ''),
(125, NULL, NULL, '', '2025-08-09 16:43:30', 0.00, 0, 0, ''),
(126, NULL, NULL, '', '2025-08-09 16:47:41', 0.00, 0, 0, ''),
(127, NULL, NULL, '', '2025-08-09 16:49:50', 0.00, 0, 0, ''),
(128, NULL, NULL, '', '2025-08-09 16:52:35', 0.00, 0, 0, ''),
(129, NULL, NULL, '', '2025-08-09 16:52:37', 0.00, 0, 0, ''),
(130, NULL, NULL, '', '2025-08-09 16:52:37', 0.00, 0, 0, ''),
(131, NULL, NULL, '', '2025-08-09 16:52:37', 0.00, 0, 0, ''),
(132, NULL, NULL, '', '2025-08-09 16:52:54', 5000.00, 20000, 15000, ''),
(133, NULL, NULL, '', '2025-08-09 16:54:50', 5000.00, 20000, 15000, ''),
(134, NULL, NULL, '', '2025-08-09 16:55:10', 5000.00, 20000, 15000, ''),
(135, NULL, NULL, '', '2025-08-09 16:55:37', 5000.00, 20000, 15000, ''),
(136, NULL, 9, '', '2025-08-09 16:56:14', 5000.00, 20000, 15000, ''),
(137, NULL, 9, '', '2025-08-09 16:56:43', 5000.00, 20000, 15000, ''),
(138, NULL, 9, '', '2025-08-09 16:57:18', 5000.00, 20000, 16400, ''),
(139, NULL, NULL, '', '2025-08-09 16:58:04', 10000.00, 20000, 10000, ''),
(140, NULL, 9, '', '2025-08-09 16:58:43', 10000.00, 20000, 10000, ''),
(141, NULL, 9, '', '2025-08-09 16:59:26', 10000.00, 20000, 11300, ''),
(142, NULL, 9, '', '2025-08-09 16:59:52', 5000.00, 200000, 195000, ''),
(143, NULL, NULL, '', '2025-08-09 17:01:17', 5000.00, 20000, 15000, ''),
(144, NULL, NULL, '', '2025-08-09 17:17:13', 15000.00, 20000, 5000, ''),
(145, NULL, NULL, '', '2025-08-09 17:18:04', 10000.00, 20000, 10000, ''),
(146, NULL, NULL, '', '2025-08-09 17:18:48', 5000.00, 20000, 15000, ''),
(147, NULL, NULL, '', '2025-08-09 17:22:55', 5000.00, 20000, 15000, ''),
(148, NULL, NULL, '', '2025-08-09 17:23:20', 5000.00, 20000, 15000, ''),
(149, NULL, 9, '', '2025-08-09 17:25:16', 5000.00, 20000, 16300, ''),
(150, NULL, 9, '', '2025-08-09 17:26:36', 5000.00, 50000, 45000, ''),
(151, NULL, NULL, '', '2025-08-09 17:26:59', 5000.00, 20000, 15000, ''),
(152, NULL, 9, '', '2025-08-09 17:27:22', 5000.00, 20000, 15000, ''),
(153, NULL, NULL, '', '2025-08-09 17:32:37', 10000.00, 20000, 10000, ''),
(154, NULL, 9, '', '2025-08-09 17:37:15', 10000.00, 20000, 11300, ''),
(155, NULL, NULL, '', '2025-08-09 17:44:28', 20000.00, 20000, 0, ''),
(156, NULL, 9, '', '2025-08-09 17:45:06', 5000.00, 20000, 15000, ''),
(157, NULL, 9, '', '2025-08-09 17:45:24', 5000.00, 20000, 16300, ''),
(158, NULL, NULL, '', '2025-08-09 17:51:50', 5000.00, 20000, 15000, ''),
(159, NULL, 9, '', '2025-08-09 17:52:30', 5000.00, 20000, 15000, ''),
(160, NULL, 9, '', '2025-08-09 17:53:19', 10000.00, 20000, 11200, '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi_detail`
--

CREATE TABLE `transaksi_detail` (
  `id` int(11) NOT NULL,
  `id_transaksi` int(11) DEFAULT NULL,
  `id_produk` int(11) DEFAULT NULL,
  `jumlah` int(11) DEFAULT NULL,
  `harga_satuan` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transaksi_detail`
--

INSERT INTO `transaksi_detail` (`id`, `id_transaksi`, `id_produk`, `jumlah`, `harga_satuan`) VALUES
(72, 74, 85, 1, 5000.00),
(73, 75, 86, 1, 5000.00),
(74, 76, 86, 1, 5000.00),
(75, 77, 86, 1, 5000.00),
(76, 78, 85, 1, 5000.00),
(77, 79, 85, 1, 5000.00),
(78, 80, 86, 1, 5000.00),
(79, 81, 86, 1, 5000.00),
(80, 82, 85, 1, 5000.00),
(81, 83, 86, 1, 5000.00),
(82, 84, 85, 1, 5000.00),
(83, 85, 85, 1, 5000.00),
(84, 86, 86, 1, 5000.00),
(85, 87, 85, 1, 5000.00),
(86, 87, 86, 1, 5000.00),
(87, 88, 86, 1, 5000.00),
(88, 89, 86, 1, 5000.00),
(89, 90, 86, 1, 5000.00),
(91, 92, 86, 1, 5000.00),
(92, 93, 86, 1, 5000.00),
(93, 93, 85, 1, 5000.00),
(94, 94, 85, 1, 5000.00),
(95, 95, 85, 1, 5000.00),
(96, 97, 86, 1, 5000.00),
(97, 98, 86, 1, 5000.00),
(98, 100, 85, 1, 5000.00),
(99, 101, 86, 1, 5000.00),
(100, 101, 85, 1, 5000.00),
(101, 102, 86, 1, 5000.00),
(105, 111, 86, 1, 5000.00),
(106, 111, 85, 2, 5000.00),
(107, 112, 86, 1, 5000.00),
(108, 113, 85, 1, 5000.00),
(109, 114, 86, 1, 5000.00),
(110, 116, 86, 1, 5000.00),
(111, 117, 85, 1, 5000.00),
(112, 118, 85, 1, 5000.00),
(114, 120, 85, 1, 5000.00),
(115, 121, 84, 1, 5000.00),
(116, 122, 85, 1, 5000.00),
(117, 123, 85, 1, 5000.00),
(118, 124, 85, 1, 5000.00),
(119, 125, 85, 2, 5000.00),
(120, 126, 85, 1, 5000.00),
(121, 127, 85, 1, 5000.00),
(122, 132, 85, 1, 5000.00),
(123, 134, 85, 1, 5000.00),
(124, 135, 84, 1, 5000.00),
(125, 136, 84, 1, 5000.00),
(126, 137, 84, 1, 5000.00),
(127, 138, 84, 1, 5000.00),
(128, 139, 84, 1, 5000.00),
(129, 139, 83, 1, 5000.00),
(130, 140, 83, 1, 5000.00),
(131, 140, 84, 1, 5000.00),
(132, 141, 84, 1, 5000.00),
(133, 141, 83, 1, 5000.00),
(134, 142, 84, 1, 5000.00),
(135, 143, 84, 1, 5000.00),
(136, 144, 84, 3, 5000.00),
(137, 145, 84, 2, 5000.00),
(138, 146, 84, 1, 5000.00),
(139, 147, 84, 1, 5000.00),
(140, 148, 84, 1, 5000.00),
(141, 149, 84, 1, 5000.00),
(142, 150, 84, 1, 5000.00),
(143, 151, 84, 1, 5000.00),
(144, 152, 84, 1, 5000.00),
(145, 153, 84, 2, 5000.00),
(146, 154, 84, 2, 5000.00),
(147, 155, 84, 4, 5000.00),
(148, 156, 84, 1, 5000.00),
(149, 157, 84, 1, 5000.00),
(150, 158, 84, 1, 5000.00),
(151, 159, 84, 1, 5000.00),
(152, 160, 84, 1, 5000.00),
(153, 160, 83, 1, 5000.00);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('superadmin','kasir') NOT NULL,
  `email` varchar(100) NOT NULL,
  `gambar` varchar(255) NOT NULL,
  `status_login` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `email`, `gambar`, `status_login`) VALUES
(51, 'marsel', '$2y$10$.5lUOlCR13mzxJuoPdzbQuHy3FJnFRFTAMtCDg5HQyEAfKhgLw2K2', 'superadmin', 'fahrialatas151@gmail.com', '1754313354_6890b28a47dea.jpg', 1),
(60, 'mantap12', '$2y$10$/H5lOl.X5JyGCe19Y6hvieMsveZHf5wSUE0XSC7/J82Hej80wkjuO', 'kasir', 'mantap1@gmail.com', '1754617737_689557893987d.jpg', 1);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `keranjang`
--
ALTER TABLE `keranjang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indeks untuk tabel `login_log`
--
ALTER TABLE `login_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produk_ibfk_1` (`id_kategori`);

--
-- Indeks untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_member` (`id_member`);

--
-- Indeks untuk tabel `transaksi_detail`
--
ALTER TABLE `transaksi_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_transaksi` (`id_transaksi`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `keranjang`
--
ALTER TABLE `keranjang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `login_log`
--
ALTER TABLE `login_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=161;

--
-- AUTO_INCREMENT untuk tabel `transaksi_detail`
--
ALTER TABLE `transaksi_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=154;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `keranjang`
--
ALTER TABLE `keranjang`
  ADD CONSTRAINT `keranjang_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `keranjang_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id`);

--
-- Ketidakleluasaan untuk tabel `login_log`
--
ALTER TABLE `login_log`
  ADD CONSTRAINT `login_log_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `produk`
--
ALTER TABLE `produk`
  ADD CONSTRAINT `produk_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `transaksi_ibfk_2` FOREIGN KEY (`id_member`) REFERENCES `members` (`id`);

--
-- Ketidakleluasaan untuk tabel `transaksi_detail`
--
ALTER TABLE `transaksi_detail`
  ADD CONSTRAINT `transaksi_detail_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id`),
  ADD CONSTRAINT `transaksi_detail_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
