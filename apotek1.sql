-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 22 Agu 2025 pada 08.34
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
(4, 'obat keras', 'uploads/1755586650_obt keras.jpg', 'keren'),
(5, 'obat bebas terbatas', 'uploads/1755586611_bebas terbatas.jpg', 'fgggff'),
(6, 'obat jamu', 'uploads/1755586573_obat jamu.png', '262662626'),
(7, 'obat bebas', 'uploads/1755586526_obat  bebas.webp', 'bebass ');

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
(27, 'mantap', '085719498408', 8, 'aktif'),
(28, 'ozan ', '2828928', 0, 'aktif');

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
(107, 'bodrex', 7, 2000, 5000, 164, '2025-08-19', 'bodrex.webp'),
(109, 'kokain', 4, 2000, 5000, 82, '2025-08-27', 'kokain.jpg'),
(110, 'paramex', 7, 2000, 5000, 194, '2025-08-20', 'paramex.webp');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_member` int(11) NOT NULL,
  `nama_member` varchar(100) NOT NULL,
  `tanggal` datetime DEFAULT current_timestamp(),
  `total_harga` decimal(10,2) DEFAULT NULL,
  `diskon` int(255) NOT NULL,
  `dibayar` int(255) NOT NULL,
  `kembalian` int(255) NOT NULL,
  `no_hp` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transaksi`
--

INSERT INTO `transaksi` (`id`, `id_user`, `id_member`, `nama_member`, `tanggal`, `total_harga`, `diskon`, `dibayar`, `kembalian`, `no_hp`) VALUES
(289, NULL, 26, '', '2025-08-17 13:08:00', 5000.00, 1000, 20000, 16000, ''),
(290, NULL, 26, '', '2025-08-17 13:18:08', 5000.00, 1000, 20000, 16000, ''),
(291, NULL, 26, '', '2025-08-17 13:18:36', 5000.00, 1000, 20000, 16000, ''),
(292, NULL, 26, '', '2025-08-17 13:19:56', 5000.00, 1500, 20000, 16500, ''),
(293, NULL, 26, '', '2025-08-17 13:27:37', 5000.00, 0, 20000, 15000, ''),
(294, NULL, 0, '', '2025-08-17 14:49:48', 5000.00, 0, 20000, 15000, ''),
(295, NULL, 26, '', '2025-08-17 14:58:16', 5000.00, 0, 20000, 15000, ''),
(296, NULL, 26, '', '2025-08-17 15:09:11', 10000.00, 0, 20000, 10000, ''),
(297, NULL, 26, '', '2025-08-17 15:10:31', 10000.00, 1700, 20000, 11700, ''),
(298, NULL, 0, '', '2025-08-17 15:13:10', 10000.00, 0, 20000, 10000, ''),
(299, NULL, 0, '', '2025-08-17 15:14:25', 5000.00, 0, 20000, 15000, ''),
(300, NULL, 27, '', '2025-08-19 14:18:16', 10000.00, 0, 20000, 10000, ''),
(301, NULL, 27, '', '2025-08-19 14:20:22', 5000.00, 1000, 20000, 16000, ''),
(302, NULL, 0, '', '2025-08-19 21:19:42', 5000.00, 0, 20000, 15000, ''),
(303, NULL, 27, '', '2025-08-19 21:21:22', 15000.00, 0, 20000, 5000, ''),
(304, NULL, 27, '', '2025-08-19 21:22:01', 5000.00, 1500, 5000, 1500, ''),
(305, NULL, 0, '', '2025-08-20 12:48:58', 10000.00, 0, 20000, 10000, ''),
(306, NULL, 0, '', '2025-08-20 12:50:04', 5000.00, 0, 20000, 15000, ''),
(307, NULL, 0, '', '2025-08-20 12:57:08', 5000.00, 0, 20000, 15000, ''),
(308, NULL, 0, '', '2025-08-20 13:00:11', 5000.00, 0, 20000, 15000, ''),
(309, NULL, 0, '', '2025-08-20 13:05:03', 5000.00, 0, 20000, 15000, ''),
(310, NULL, 0, '', '2025-08-20 13:06:08', 5000.00, 0, 20000, 15000, ''),
(311, NULL, 0, '', '2025-08-20 13:08:19', 5000.00, 0, 20000, 15000, ''),
(312, NULL, 0, '', '2025-08-20 13:08:49', 10000.00, 0, 20000, 10000, ''),
(313, NULL, 27, '', '2025-08-20 13:12:39', 5000.00, 0, 20000, 15000, ''),
(314, NULL, 27, '', '2025-08-20 13:15:02', 5000.00, 1000, 20000, 16000, ''),
(315, NULL, 27, '', '2025-08-20 13:18:39', 10000.00, 0, 20000, 10000, ''),
(316, NULL, 0, '', '2025-08-20 13:19:54', 5000.00, 0, 10000, 5000, ''),
(317, NULL, 27, '', '2025-08-20 15:04:50', 10000.00, 1400, 20000, 11400, '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi_detail`
--

CREATE TABLE `transaksi_detail` (
  `id` int(11) NOT NULL,
  `id_transaksi` int(11) DEFAULT NULL,
  `id_produk` int(11) DEFAULT NULL,
  `nama_produk` varchar(150) DEFAULT NULL,
  `jumlah` int(11) DEFAULT NULL,
  `harga_satuan` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transaksi_detail`
--

INSERT INTO `transaksi_detail` (`id`, `id_transaksi`, `id_produk`, `nama_produk`, `jumlah`, `harga_satuan`) VALUES
(267, 289, 107, NULL, 1, 5000.00),
(268, 290, 107, NULL, 1, 5000.00),
(269, 291, 107, NULL, 1, 5000.00),
(270, 292, 107, NULL, 1, 5000.00),
(271, 293, 107, NULL, 1, 5000.00),
(272, 294, 107, NULL, 1, 5000.00),
(273, 295, 107, NULL, 1, 5000.00),
(274, 296, 108, NULL, 1, 5000.00),
(275, 296, 107, NULL, 1, 5000.00),
(276, 297, 108, NULL, 1, 5000.00),
(277, 297, 107, NULL, 1, 5000.00),
(278, 298, 108, NULL, 1, 5000.00),
(279, 298, 107, NULL, 1, 5000.00),
(280, 299, 107, NULL, 1, 5000.00),
(281, 300, 109, NULL, 1, 5000.00),
(282, 300, 107, NULL, 1, 5000.00),
(283, 301, 107, NULL, 1, 5000.00),
(284, 302, 107, NULL, 1, 5000.00),
(285, 303, 109, NULL, 2, 5000.00),
(286, 303, 110, NULL, 1, 5000.00),
(287, 304, 107, NULL, 1, 5000.00),
(288, 305, 109, NULL, 1, 5000.00),
(289, 305, 110, NULL, 1, 5000.00),
(290, 306, 109, NULL, 1, 5000.00),
(291, 307, 109, NULL, 1, 5000.00),
(292, 308, 109, NULL, 1, 5000.00),
(293, 309, 109, NULL, 1, 5000.00),
(294, 310, 109, NULL, 1, 5000.00),
(295, 311, 109, NULL, 1, 5000.00),
(296, 312, 110, NULL, 1, 5000.00),
(297, 312, 109, NULL, 1, 5000.00),
(298, 313, 109, NULL, 1, 5000.00),
(299, 314, 109, NULL, 1, 5000.00),
(300, 315, 109, NULL, 1, 5000.00),
(301, 315, 110, NULL, 1, 5000.00),
(302, 316, 109, NULL, 1, 5000.00),
(303, 317, 110, NULL, 1, 5000.00),
(304, 317, 109, NULL, 1, 5000.00);

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
(51, 'marsel', '$2y$10$8KyZbe4KFmtvRu42NEJ9AON3BXOhtPi1MG6aoeNeRNlRLZxeZWp6.', 'superadmin', 'fahrialatas151@gmail.com', '1754313354_6890b28a47dea.jpg', 0),
(60, 'mantap12', '$2y$10$UF9tzCQkZWd0YHs5YGzPh.iksa/yjgBy7u/.UTyUNcFJbIeIz1yKe', 'kasir', 'mantap1@gmail.com', '1754617737_689557893987d.jpg', 0),
(61, 'mantap', '$2y$10$kOtRMCzvcsEVA72JNRmq5uO0pfpbdshpkrC1Imq1MO/50hpcIzwfq', 'kasir', 'hahahaha5@gmail.com', '1755586347_68a41f2b438d8.png', 0);

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
  ADD KEY `id_member` (`id_member`),
  ADD KEY `transaksi_ibfk_1` (`id_user`);

--
-- Indeks untuk tabel `transaksi_detail`
--
ALTER TABLE `transaksi_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_transaksi` (`id_transaksi`),
  ADD KEY `transaksi_detail_ibfk_2` (`id_produk`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT untuk tabel `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=318;

--
-- AUTO_INCREMENT untuk tabel `transaksi_detail`
--
ALTER TABLE `transaksi_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=305;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

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
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `transaksi_detail`
--
ALTER TABLE `transaksi_detail`
  ADD CONSTRAINT `transaksi_detail_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
