-- Adminer 4.8.1 MySQL 8.0.30 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

CREATE DATABASE `diarimu` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `diarimu`;

DROP TABLE IF EXISTS `mu_anggota`;
CREATE TABLE `mu_anggota` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nbm` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `created_at` datetime NOT NULL,
  `tempat_lahir` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `alamat` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `no_hp` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `updated_at` datetime NOT NULL,
  `created_by` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `mu_anggota` (`id`, `nbm`, `nama`, `tanggal_lahir`, `created_at`, `tempat_lahir`, `alamat`, `no_hp`, `email`, `updated_at`, `created_by`) VALUES
(1,	'123',	'AD',	'2024-11-28',	'2024-12-05 06:53:30',	'AD',	'ADA',	'085799441371',	'adi.sabwa@gmail.com',	'2024-12-05 06:53:30',	1),
(2,	'1',	'1',	'2025-04-30',	'2025-05-03 13:11:52',	'1',	'1',	'1',	'',	'2025-05-03 14:12:21',	0),
(18,	'1',	'1',	'2025-04-30',	'2025-05-03 14:11:37',	'1',	'1',	'122',	'',	'2025-05-03 14:11:37',	0),
(19,	'1',	'1',	'2025-04-30',	'2025-05-03 14:12:44',	'1',	'1',	'123456',	'',	'2025-05-03 14:13:08',	0),
(20,	'',	'',	'0000-00-00',	'2025-05-03 14:23:44',	'',	'',	'',	'',	'2025-05-03 14:24:00',	0),
(21,	'1',	'1',	'0000-00-00',	'2025-05-03 14:30:14',	'1',	'1',	'1',	'1',	'2025-05-03 14:30:14',	0),
(22,	'1',	'1',	'0000-00-00',	'2025-05-03 14:30:57',	'1',	'1',	'1',	'1',	'2025-05-03 14:30:57',	0),
(23,	'1',	'1',	'0000-00-00',	'2025-05-03 14:31:15',	'1',	'1',	'1',	'1',	'2025-05-03 14:31:15',	0),
(24,	'',	'etr',	'2025-05-07',	'2025-05-03 14:44:21',	'gs',	'afs',	'00123456',	'',	'2025-05-03 14:45:01',	0);

DROP TABLE IF EXISTS `mu_group_kolom`;
CREATE TABLE `mu_group_kolom` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_tabel` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `group` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `group_icon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `mu_group_kolom` (`id`, `nama_tabel`, `group`, `group_icon`) VALUES
(1,	'mu_pengguna',	'Pengguna',	'mdi:users'),
(2,	'mu_pengguna_akses',	'Akses Pengguna',	'mdi:users'),
(3,	'pdm_pcm',	'Daftar PCM',	''),
(4,	'pdm_prm',	'Daftar PRM',	''),
(5,	'mu_anggota',	'Anggota',	''),
(6,	'mu_quran_baca',	'Membaca Al-Qur\'an',	''),
(7,	'mu_quran_hafal',	'Menghafal Al Qur\'an',	''),
(8,	'mu_quran_kaji',	'Mengkaji Al Qur\'an',	'');

DROP TABLE IF EXISTS `mu_nama_kolom`;
CREATE TABLE `mu_nama_kolom` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_group` int DEFAULT NULL,
  `order` int DEFAULT NULL,
  `nama_kolom` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `is_table` enum('0','1') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `is_fk` enum('0','1') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '0',
  `nama_fk` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `view_kolom` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `tipe` enum('int','string','float','date') NOT NULL DEFAULT 'string',
  `label` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `input` varchar(20) NOT NULL DEFAULT 'input',
  `prepend` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `append` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `from_user` enum('0','1') NOT NULL DEFAULT '1',
  `input_only` enum('0','1') NOT NULL DEFAULT '0',
  `required` enum('0','1') NOT NULL DEFAULT '1',
  `rules` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `default` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `search` enum('0','1') NOT NULL DEFAULT '0',
  `function` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `function_input` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `function_submit` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `show` enum('1','0') NOT NULL DEFAULT '1',
  `width` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `min_width` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `align` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `sortable` enum('0','1') NOT NULL DEFAULT '1',
  `allow_add` enum('0','1') NOT NULL DEFAULT '0',
  `add_href` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `add_reset` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `created_at` datetime DEFAULT NULL,
  `pilihan` varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id_group` (`id_group`),
  CONSTRAINT `mu_nama_kolom_ibfk_2` FOREIGN KEY (`id_group`) REFERENCES `mu_group_kolom` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

INSERT INTO `mu_nama_kolom` (`id`, `id_group`, `order`, `nama_kolom`, `is_table`, `is_fk`, `nama_fk`, `view_kolom`, `tipe`, `label`, `input`, `prepend`, `append`, `from_user`, `input_only`, `required`, `rules`, `default`, `search`, `function`, `function_input`, `function_submit`, `show`, `width`, `min_width`, `align`, `sortable`, `allow_add`, `add_href`, `add_reset`, `created_at`, `pilihan`) VALUES
(2,	1,	2,	'password',	'0',	'0',	'',	'',	'string',	'Password',	'input',	'',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'md5',	'1',	'',	'',	'',	'1',	'0',	'',	'',	'2024-11-08 09:19:28',	''),
(4,	1,	1,	'unit',	'0',	'0',	'',	'',	'string',	'Unit Kerja',	'input',	'',	'',	'0',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'1',	'0',	'',	'',	'2024-11-08 09:19:28',	''),
(5,	1,	1,	'role',	'0',	'0',	'',	'',	'string',	'Role',	'select',	'',	'',	'1',	'0',	'1',	NULL,	'user',	'0',	'ucFirst',	'',	'',	'1',	'',	'',	'',	'1',	'0',	'',	'',	'2024-11-08 09:19:28',	'user,operator,admin'),
(7,	2,	1,	'id_pengguna',	'0',	'1',	'',	'',	'string',	'Pengguna',	'select',	'',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'1',	'0',	'',	'',	'2024-11-08 09:19:28',	'PenggunaModel'),
(8,	2,	2,	'id_akses',	'0',	'1',	'',	'aplikasi',	'string',	'Aplikasi',	'input',	'',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'1',	'0',	'',	'',	'2024-11-08 09:19:28',	'ListAksesModel'),
(9,	3,	3,	'pcm',	'0',	'0',	'',	'',	'string',	'Nama PCM',	'input',	'',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'1',	'0',	'',	'',	'2024-11-25 13:17:56',	''),
(10,	4,	1,	'prm',	'0',	'0',	'',	'',	'string',	'Nama Ranting',	'input',	'',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'1',	'0',	'',	'',	'2024-11-25 13:17:56',	''),
(11,	4,	0,	'id_pcm',	'0',	'0',	'',	'',	'string',	'Nama Cabang',	'select',	'',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'1',	'0',	'',	'',	'2024-11-25 13:22:36',	'PcmModel'),
(15,	5,	2,	'nama',	NULL,	'0',	'',	'',	'string',	'Nama Anggota',	'input',	'',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'200px',	'',	'1',	'0',	'',	'',	NULL,	''),
(16,	5,	1,	'nbm',	NULL,	'0',	'',	'',	'string',	'NBM',	'input',	'',	'',	'1',	'0',	'0',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(17,	5,	4,	'tanggal_lahir',	NULL,	'0',	'',	'',	'string',	'Tanggal Lahir',	'date',	'',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(18,	5,	3,	'tempat_lahir',	NULL,	'0',	'',	'',	'string',	'Tempat Lahir',	'input',	'',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(19,	5,	5,	'alamat',	NULL,	'0',	'',	'',	'string',	'Alamat',	'textarea',	'',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(23,	1,	0,	'id_anggota',	'0',	'0',	'',	'',	'string',	'Nama Anggota',	'select',	'',	'',	'1',	'0',	'1',	'is_unique[mu_pengguna.id_anggota,id,{id}]',	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'1',	'1',	'data/anggota/store',	'data/anggota/reset_options',	NULL,	'AnggotaModel'),
(31,	5,	6,	'no_hp',	NULL,	'0',	'',	'',	'string',	'Nomor HP',	'input',	'',	'',	'1',	'0',	'1',	'is_unique[mu_anggota.no_hp,id,{id}]',	'',	'0',	'',	'',	'toPhoneNumber',	'1',	'',	'',	'center',	'1',	'0',	'',	'',	NULL,	''),
(32,	5,	7,	'email',	NULL,	'0',	'',	'',	'string',	'Email',	'input',	'',	'',	'1',	'0',	'0',	'is_unique[mu_anggota.email,id,{id}]',	'',	'0',	'',	'',	'',	'1',	'',	'',	'center',	'1',	'0',	'',	'',	NULL,	''),
(33,	6,	2,	'tanggal',	NULL,	'0',	'',	'',	'date',	'Tanggal Membaca',	'date',	'',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(34,	6,	3,	'tingkat',	NULL,	'0',	'',	'',	'string',	'Tingkat Membaca',	'radio',	'',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	'pemula,lanjut'),
(35,	6,	4,	'jenis_buku',	NULL,	'0',	'',	'',	'string',	'Buku Pembelajaran',	'select',	'',	'',	'1',	'0',	'0',	NULL,	'al-qur\'an',	'0',	'',	'',	'',	'1',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	'iqra,qiroati,al-qur\'an'),
(37,	6,	5,	'hal',	NULL,	'0',	'',	'',	'string',	'Halaman',	'input',	'Hal. ',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(38,	6,	6,	'juz',	NULL,	'0',	'',	'',	'string',	'Juz',	'input',	'Juz ',	'',	'1',	'0',	'0',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(39,	6,	7,	'surat',	NULL,	'0',	'',	'',	'string',	'Surat',	'input',	'Surat',	'',	'1',	'0',	'0',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(40,	6,	8,	'ayat',	NULL,	'0',	'',	'',	'string',	'Ayat',	'input',	'Ayat ke ',	'',	'1',	'0',	'0',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(44,	7,	1,	'id_anggota',	NULL,	'0',	'',	'',	'string',	'Nama Anggota',	'seelct',	'',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'1',	'1',	'data/anggota/store',	'data/anggota/reset_options',	NULL,	'AnggotaModel'),
(45,	7,	2,	'tanggal',	NULL,	'0',	'',	'',	'date',	'Tanggal Setoran',	'date',	'',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(48,	7,	5,	'hal_mulai',	NULL,	'0',	'',	'',	'string',	'Halaman',	'input',	'Hal. ',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(49,	7,	5,	'juz_mulai',	NULL,	'0',	'',	'',	'string',	'Juz',	'input',	'Juz ',	'',	'1',	'0',	'0',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(50,	7,	5,	'surat_mulai',	NULL,	'0',	'',	'',	'string',	'Surat',	'input',	'Surat',	'',	'1',	'0',	'0',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(51,	7,	5,	'ayat_mulai',	NULL,	'0',	'',	'',	'string',	'Ayat',	'input',	'Ayat ke ',	'',	'1',	'0',	'0',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(74,	7,	6,	'hal_selesai',	NULL,	'0',	'',	'',	'string',	'Halaman',	'input',	'Hal. ',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(75,	7,	6,	'juz_selesai',	NULL,	'0',	'',	'',	'string',	'Juz',	'input',	'Juz ',	'',	'1',	'0',	'0',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(76,	7,	6,	'surat_selesai',	NULL,	'0',	'',	'',	'string',	'Surat',	'input',	'Surat',	'',	'1',	'0',	'0',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(77,	7,	6,	'ayat_selesai',	NULL,	'0',	'',	'',	'string',	'Ayat',	'input',	'Ayat ke ',	'',	'1',	'0',	'0',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(81,	8,	1,	'id_anggota',	NULL,	'0',	'',	'',	'string',	'Nama Anggota',	'seelct',	'',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'1',	'1',	'data/anggota/store',	'data/anggota/reset_options',	NULL,	'AnggotaModel'),
(82,	8,	2,	'tanggal',	NULL,	'0',	'',	'',	'date',	'Tanggal Setoran',	'date',	'',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(83,	8,	5,	'hal_mulai',	NULL,	'0',	'',	'',	'string',	'Halaman',	'input',	'Hal. ',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(84,	8,	5,	'juz_mulai',	NULL,	'0',	'',	'',	'string',	'Juz',	'input',	'Juz ',	'',	'1',	'0',	'0',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(85,	8,	5,	'surat_mulai',	NULL,	'0',	'',	'',	'string',	'Surat',	'input',	'Surat',	'',	'1',	'0',	'0',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(86,	8,	5,	'ayat_mulai',	NULL,	'0',	'',	'',	'string',	'Ayat',	'input',	'Ayat ke ',	'',	'1',	'0',	'0',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(87,	8,	6,	'hal_selesai',	NULL,	'0',	'',	'',	'string',	'Halaman',	'input',	'Hal. ',	'',	'1',	'0',	'1',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(88,	8,	6,	'juz_selesai',	NULL,	'0',	'',	'',	'string',	'Juz',	'input',	'Juz ',	'',	'1',	'0',	'0',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(89,	8,	6,	'surat_selesai',	NULL,	'0',	'',	'',	'string',	'Surat',	'input',	'Surat',	'',	'1',	'0',	'0',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(90,	8,	6,	'ayat_selesai',	NULL,	'0',	'',	'',	'string',	'Ayat',	'input',	'Ayat ke ',	'',	'1',	'0',	'0',	NULL,	'',	'0',	'',	'',	'',	'1',	'',	'',	'',	'1',	'0',	'',	'',	NULL,	''),
(91,	1,	3,	'passwordconf',	'0',	'0',	'',	'',	'string',	'Konfirmasi Password',	'input',	'',	'',	'1',	'1',	'1',	'matches[password]',	'',	'0',	'',	'',	'md5',	'1',	'',	'',	'',	'1',	'0',	'',	'',	'2024-11-08 09:19:28',	'');

DROP TABLE IF EXISTS `mu_pengguna`;
CREATE TABLE `mu_pengguna` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_anggota` int NOT NULL,
  `password` varchar(200) NOT NULL,
  `unit` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `role` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT 'user',
  `vertical` enum('0','1') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_by` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_anggota` (`id_anggota`),
  CONSTRAINT `mu_pengguna_ibfk_3` FOREIGN KEY (`id_anggota`) REFERENCES `mu_anggota` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

INSERT INTO `mu_pengguna` (`id`, `id_anggota`, `password`, `unit`, `role`, `vertical`, `created_at`, `updated_at`, `created_by`) VALUES
(1,	1,	'4f19ecc280c47d1d7ee7b581f764c9d7',	'Pondok Darul Arqom',	'admin',	'0',	'2024-09-21 14:38:11',	'2025-05-01 07:11:57',	0),
(12,	19,	'77963b7a931377ad4ab5ad6a9cd718aa',	NULL,	'user',	'1',	'2025-05-03 14:12:44',	'2025-05-03 14:13:08',	0),
(13,	20,	'd41d8cd98f00b204e9800998ecf8427e',	NULL,	'user',	'1',	'2025-05-03 14:23:44',	'2025-05-03 14:24:01',	0),
(14,	24,	'77963b7a931377ad4ab5ad6a9cd718aa',	NULL,	'user',	'1',	'2025-05-03 14:45:02',	'2025-05-03 14:45:02',	0);

DROP TABLE IF EXISTS `mu_quran_baca`;
CREATE TABLE `mu_quran_baca` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_anggota` int NOT NULL,
  `tanggal` datetime NOT NULL,
  `tingkat` enum('pemula','lanjut') NOT NULL DEFAULT 'lanjut',
  `jenis_buku` varchar(20) DEFAULT NULL,
  `hal` int NOT NULL,
  `juz` int DEFAULT NULL,
  `surat` varchar(20) DEFAULT NULL,
  `ayat` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_anggota` (`id_anggota`),
  CONSTRAINT `mu_quran_baca_ibfk_1` FOREIGN KEY (`id_anggota`) REFERENCES `mu_anggota` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `mu_quran_hafal`;
CREATE TABLE `mu_quran_hafal` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_anggota` int NOT NULL,
  `tanggal` datetime NOT NULL,
  `hal_mulai` int NOT NULL,
  `hal_selesai` int NOT NULL,
  `juz_mulai` int DEFAULT NULL,
  `juz_selesai` int DEFAULT NULL,
  `surat_mulai` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `surat_selesai` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `ayat_mulai` int DEFAULT NULL,
  `ayat_selesai` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_anggota` (`id_anggota`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `mu_quran_kaji`;
CREATE TABLE `mu_quran_kaji` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_anggota` int NOT NULL,
  `tanggal` datetime NOT NULL,
  `hal_mulai` int NOT NULL,
  `hal_selesai` int NOT NULL,
  `juz_mulai` int DEFAULT NULL,
  `juz_selesai` int DEFAULT NULL,
  `surat_mulai` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `surat_selesai` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `ayat_mulai` int DEFAULT NULL,
  `ayat_selesai` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_anggota` (`id_anggota`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- 2025-05-03 15:57:58
