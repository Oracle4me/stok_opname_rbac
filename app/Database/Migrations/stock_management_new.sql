-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 22 Bulan Mei 2026 pada 05.07
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
-- Database: `stock_management`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `barang`
--

CREATE TABLE `barang` (
  `id` int(11) NOT NULL,
  `tanggal` date DEFAULT NULL,
  `code` varchar(20) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `kategori_id` int(10) DEFAULT NULL,
  `satuan_id` int(10) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` tinyint(10) DEFAULT NULL,
  `last_edited_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_edited_by` tinyint(10) DEFAULT NULL,
  `is_deleted` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `distribusi_barang`
--

CREATE TABLE `distribusi_barang` (
  `id` int(11) NOT NULL,
  `barang_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `team_leader` varchar(100) DEFAULT NULL,
  `area` varchar(150) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori_barang`
--

CREATE TABLE `kategori_barang` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kategori_barang`
--

INSERT INTO `kategori_barang` (`id`, `nama`) VALUES
(1, 'Material Marketing Bergerak'),
(2, 'Material Marketing Tetap'),
(3, 'Merchandise'),
(4, 'Sales Kit');

-- --------------------------------------------------------

--
-- Struktur dari tabel `mutasi_stok`
--

CREATE TABLE `mutasi_stok` (
  `id` int(11) NOT NULL,
  `barang_id` int(11) NOT NULL,
  `tipe` enum('masuk','keluar','opname','koreksi','void') NOT NULL,
  `qty_before` int(11) NOT NULL DEFAULT 0,
  `qty_after` int(11) NOT NULL DEFAULT 0,
  `selisih` int(11) NOT NULL,
  `referensi_id` int(11) DEFAULT NULL,
  `referensi_tipe` varchar(50) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `label` varchar(150) NOT NULL,
  `group_name` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `last_edited_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `permissions`
--

INSERT INTO `permissions` (`id`, `nama`, `label`, `group_name`, `created_at`, `last_edited_at`) VALUES
(1, 'user_view', 'Lihat User', 'Manajemen User', NULL, NULL),
(2, 'user_create', 'Tambah User', 'Manajemen User', NULL, NULL),
(3, 'user_edit', 'Edit User', 'Manajemen User', NULL, NULL),
(4, 'user_delete', 'Hapus User', 'Manajemen User', NULL, NULL),
(5, 'role_view', 'Lihat Role', 'Manajemen Role', NULL, NULL),
(6, 'role_create', 'Tambah Role', 'Manajemen Role', NULL, NULL),
(7, 'role_edit', 'Edit Role', 'Manajemen Role', NULL, NULL),
(8, 'role_delete', 'Hapus Role', 'Manajemen Role', NULL, NULL),
(9, 'permission_view', 'Lihat Hak Akses', 'Manajemen Hak Akses', NULL, NULL),
(10, 'permission_edit', 'Edit Hak Akses', 'Manajemen Hak Akses', NULL, NULL),
(11, 'barang_view', 'Lihat Barang', 'Master Barang', NULL, NULL),
(12, 'barang_create', 'Tambah Barang', 'Master Barang', NULL, NULL),
(13, 'barang_edit', 'Edit Barang', 'Master Barang', NULL, NULL),
(14, 'barang_delete', 'Hapus Barang', 'Master Barang', NULL, NULL),
(15, 'stok_view', 'Lihat Stok Opname', 'Stok Opname', NULL, NULL),
(16, 'stok_create', 'Buat Stok Opname', 'Stok Opname', NULL, NULL),
(17, 'stok_edit', 'Edit Draft Opname', 'Stok Opname', NULL, NULL),
(18, 'stok_delete_draft', 'Hapus Draft Opname', 'Stok Opname', NULL, NULL),
(19, 'stok_draft', 'Draft Opname', 'Stok Opname', NULL, NULL),
(20, 'stok_final', 'Finalisasi Opname', 'Stok Opname', NULL, NULL),
(21, 'stok_masuk_view', 'Lihat Stok Masuk', 'Stok Masuk', NULL, NULL),
(22, 'stok_masuk_create', 'Tambah Stok Masuk', 'Stok Masuk', NULL, NULL),
(23, 'stok_masuk_detail', 'Detail Stok Masuk', 'Stok Masuk', NULL, NULL),
(24, 'stok_masuk_edit', 'Edit Stok Masuk', 'Stok Masuk', NULL, NULL),
(25, 'stok_masuk_delete', 'Hapus Stok Masuk', 'Stok Masuk', NULL, NULL),
(26, 'distribusi_barang_view', 'Lihat Distribusi Barang', 'Distribusi Barang', NULL, NULL),
(27, 'distribusi_barang_create', 'Tambah Distribusi Barang', 'Distribusi Barang', NULL, NULL),
(28, 'distribusi_barang_detail', 'Detail Distribusi Barang', 'Distribusi Barang', NULL, NULL),
(29, 'distribusi_barang_edit', 'Edit Distribusi Barang', 'Distribusi Barang', NULL, NULL),
(30, 'distribusi_barang_delete', 'Hapus Distribusi Barang', 'Distribusi Barang', NULL, NULL),
(31, 'laporan_view', 'Lihat Laporan', 'Laporan', NULL, NULL),
(32, 'laporan_rekap_view', 'Lihat Rekap', 'Laporan', NULL, NULL),
(33, 'laporan_detail_view', 'Lihat Detail', 'Laporan', NULL, NULL),
(34, 'laporan_mutasi_view', 'Lihat Mutasi', 'Laporan', NULL, NULL),
(35, 'laporan_export', 'Export Laporan', 'Laporan', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `is_active` tinyint(10) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `last_edited_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_edited_by` int(10) UNSIGNED DEFAULT NULL,
  `is_deleted` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `value` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `last_edited_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_edited_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `role_permissions`
--

INSERT INTO `role_permissions` (`id`, `role_id`, `permission_id`, `value`, `created_at`, `created_by`, `last_edited_at`, `last_edited_by`) VALUES
(1, 1, 1, 1, '2026-05-22 10:06:09', NULL, '2026-05-22 03:06:09', NULL),
(2, 1, 2, 1, '2026-05-22 10:06:09', NULL, '2026-05-22 03:06:09', NULL),
(3, 1, 3, 1, '2026-05-22 10:06:09', NULL, '2026-05-22 03:06:09', NULL),
(4, 1, 4, 1, '2026-05-22 10:06:09', NULL, '2026-05-22 03:06:09', NULL),
(5, 1, 5, 1, '2026-05-22 10:06:09', NULL, '2026-05-22 03:06:09', NULL),
(6, 1, 6, 1, '2026-05-22 10:06:09', NULL, '2026-05-22 03:06:09', NULL),
(7, 1, 7, 1, '2026-05-22 10:06:09', NULL, '2026-05-22 03:06:09', NULL),
(8, 1, 8, 1, '2026-05-22 10:06:09', NULL, '2026-05-22 03:06:09', NULL),
(9, 1, 9, 1, '2026-05-22 10:06:09', NULL, '2026-05-22 03:06:09', NULL),
(10, 1, 10, 1, '2026-05-22 10:06:09', NULL, '2026-05-22 03:06:09', NULL),
(11, 1, 11, 1, '2026-05-22 10:06:09', NULL, '2026-05-22 03:06:09', NULL),
(12, 1, 12, 1, '2026-05-22 10:06:09', NULL, '2026-05-22 03:06:09', NULL),
(13, 1, 13, 1, '2026-05-22 10:06:09', NULL, '2026-05-22 03:06:09', NULL),
(14, 1, 14, 1, '2026-05-22 10:06:09', NULL, '2026-05-22 03:06:09', NULL),
(15, 1, 15, 1, '2026-05-22 10:06:09', NULL, '2026-05-22 03:06:09', NULL),
(16, 1, 16, 1, '2026-05-22 10:06:09', NULL, '2026-05-22 03:06:09', NULL),
(17, 1, 17, 1, '2026-05-22 10:06:09', NULL, '2026-05-22 03:06:09', NULL),
(18, 1, 18, 1, '2026-05-22 10:06:09', NULL, '2026-05-22 03:06:09', NULL),
(19, 1, 19, 1, '2026-05-22 10:06:09', NULL, '2026-05-22 03:06:09', NULL),
(20, 1, 20, 1, '2026-05-22 10:06:09', NULL, '2026-05-22 03:06:09', NULL),
(21, 1, 21, 1, '2026-05-22 10:06:09', NULL, '2026-05-22 03:06:09', NULL),
(22, 1, 22, 1, '2026-05-22 10:06:09', NULL, '2026-05-22 03:06:09', NULL),
(23, 1, 23, 1, '2026-05-22 10:06:09', NULL, '2026-05-22 03:06:09', NULL),
(24, 1, 24, 1, '2026-05-22 10:06:09', NULL, '2026-05-22 03:06:09', NULL),
(25, 1, 25, 1, '2026-05-22 10:06:09', NULL, '2026-05-22 03:06:09', NULL),
(26, 1, 26, 1, '2026-05-22 10:06:09', NULL, '2026-05-22 03:06:09', NULL),
(27, 1, 27, 1, '2026-05-22 10:06:09', NULL, '2026-05-22 03:06:09', NULL),
(28, 1, 28, 1, '2026-05-22 10:06:09', NULL, '2026-05-22 03:06:09', NULL),
(29, 1, 29, 1, '2026-05-22 10:06:09', NULL, '2026-05-22 03:06:09', NULL),
(30, 1, 30, 1, '2026-05-22 10:06:09', NULL, '2026-05-22 03:06:09', NULL),
(31, 1, 31, 1, '2026-05-22 10:06:09', NULL, '2026-05-22 03:06:09', NULL),
(32, 1, 32, 1, '2026-05-22 10:06:09', NULL, '2026-05-22 03:06:09', NULL),
(33, 1, 33, 1, '2026-05-22 10:06:09', NULL, '2026-05-22 03:06:09', NULL),
(34, 1, 34, 1, '2026-05-22 10:06:09', NULL, '2026-05-22 03:06:09', NULL),
(35, 1, 35, 1, '2026-05-22 10:06:09', NULL, '2026-05-22 03:06:09', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `satuan_barang`
--

CREATE TABLE `satuan_barang` (
  `id` int(11) NOT NULL,
  `nama` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `satuan_barang`
--

INSERT INTO `satuan_barang` (`id`, `nama`) VALUES
(1, 'pcs'),
(2, 'lembar'),
(3, 'box'),
(4, 'roll'),
(5, 'pack');

-- --------------------------------------------------------

--
-- Struktur dari tabel `stok_barang`
--

CREATE TABLE `stok_barang` (
  `id` int(11) NOT NULL,
  `barang_id` int(11) NOT NULL,
  `qty` int(11) DEFAULT 0,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `stok_opname`
--

CREATE TABLE `stok_opname` (
  `id` int(11) NOT NULL,
  `tanggal` date DEFAULT NULL,
  `nama_barang` varchar(255) DEFAULT NULL,
  `status` enum('draft','final') DEFAULT 'draft',
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `stok_opname_detail`
--

CREATE TABLE `stok_opname_detail` (
  `id` int(11) NOT NULL,
  `stok_opname_id` int(11) NOT NULL,
  `barang_id` int(11) NOT NULL,
  `stok_sistem` int(11) DEFAULT 0,
  `stok_fisik` int(11) DEFAULT 0,
  `selisih` int(11) DEFAULT 0,
  `keterangan` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(225) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role_id` int(10) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` tinyint(10) DEFAULT NULL,
  `last_edited_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_edited_by` tinyint(10) DEFAULT NULL,
  `is_deleted` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `nama`, `email`, `password`, `role_id`, `status`, `created_at`, `created_by`, `last_edited_at`, `last_edited_by`, `is_deleted`) VALUES
(1, 'Admin', 'Administrator', 'admin@gmail.com', '$2y$10$A0UQInAlU23LVNiEwVUECuyl.zEJ21jybVDsqaHhV/ct7v7RzcFhe', 1, 1, '2026-05-21 10:56:44', NULL, '2026-05-21 10:56:44', NULL, 0);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indeks untuk tabel `distribusi_barang`
--
ALTER TABLE `distribusi_barang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `barang_id` (`barang_id`);

--
-- Indeks untuk tabel `kategori_barang`
--
ALTER TABLE `kategori_barang`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `mutasi_stok`
--
ALTER TABLE `mutasi_stok`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_barang` (`barang_id`),
  ADD KEY `idx_tipe` (`tipe`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indeks untuk tabel `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama` (`nama`);

--
-- Indeks untuk tabel `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_role_permission` (`role_id`,`permission_id`);

--
-- Indeks untuk tabel `satuan_barang`
--
ALTER TABLE `satuan_barang`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `stok_barang`
--
ALTER TABLE `stok_barang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `barang_id` (`barang_id`);

--
-- Indeks untuk tabel `stok_opname`
--
ALTER TABLE `stok_opname`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `stok_opname_detail`
--
ALTER TABLE `stok_opname_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stok_opname_id` (`stok_opname_id`),
  ADD KEY `barang_id` (`barang_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `barang`
--
ALTER TABLE `barang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `distribusi_barang`
--
ALTER TABLE `distribusi_barang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kategori_barang`
--
ALTER TABLE `kategori_barang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `mutasi_stok`
--
ALTER TABLE `mutasi_stok`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT untuk tabel `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT untuk tabel `satuan_barang`
--
ALTER TABLE `satuan_barang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `stok_barang`
--
ALTER TABLE `stok_barang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `stok_opname`
--
ALTER TABLE `stok_opname`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `stok_opname_detail`
--
ALTER TABLE `stok_opname_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `distribusi_barang`
--
ALTER TABLE `distribusi_barang`
  ADD CONSTRAINT `distribusi_barang_ibfk_1` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `mutasi_stok`
--
ALTER TABLE `mutasi_stok`
  ADD CONSTRAINT `mutasi_stok_ibfk_1` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `stok_barang`
--
ALTER TABLE `stok_barang`
  ADD CONSTRAINT `stok_barang_ibfk_1` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`);

--
-- Ketidakleluasaan untuk tabel `stok_opname_detail`
--
ALTER TABLE `stok_opname_detail`
  ADD CONSTRAINT `stok_opname_detail_ibfk_1` FOREIGN KEY (`stok_opname_id`) REFERENCES `stok_opname` (`id`),
  ADD CONSTRAINT `stok_opname_detail_ibfk_2` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
