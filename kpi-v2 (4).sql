-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 11, 2026 at 09:35 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kpi-v2`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-boost.roster.scan', 'a:2:{s:6:\"roster\";O:21:\"Laravel\\Roster\\Roster\":3:{s:13:\"\0*\0approaches\";O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:11:\"\0*\0packages\";O:32:\"Laravel\\Roster\\PackageCollection\":2:{s:8:\"\0*\0items\";a:8:{i:0;O:22:\"Laravel\\Roster\\Package\":6:{s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^12.0\";s:10:\"\0*\0package\";E:37:\"Laravel\\Roster\\Enums\\Packages:LARAVEL\";s:14:\"\0*\0packageName\";s:17:\"laravel/framework\";s:10:\"\0*\0version\";s:7:\"12.49.0\";s:6:\"\0*\0dev\";b:0;}i:1;O:22:\"Laravel\\Roster\\Package\":6:{s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:7:\"v0.3.11\";s:10:\"\0*\0package\";E:37:\"Laravel\\Roster\\Enums\\Packages:PROMPTS\";s:14:\"\0*\0packageName\";s:15:\"laravel/prompts\";s:10:\"\0*\0version\";s:6:\"0.3.11\";s:6:\"\0*\0dev\";b:0;}i:2;O:22:\"Laravel\\Roster\\Package\":6:{s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:6:\"v0.5.3\";s:10:\"\0*\0package\";E:33:\"Laravel\\Roster\\Enums\\Packages:MCP\";s:14:\"\0*\0packageName\";s:11:\"laravel/mcp\";s:10:\"\0*\0version\";s:5:\"0.5.3\";s:6:\"\0*\0dev\";b:1;}i:3;O:22:\"Laravel\\Roster\\Package\":6:{s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^1.24\";s:10:\"\0*\0package\";E:34:\"Laravel\\Roster\\Enums\\Packages:PINT\";s:14:\"\0*\0packageName\";s:12:\"laravel/pint\";s:10:\"\0*\0version\";s:6:\"1.27.0\";s:6:\"\0*\0dev\";b:1;}i:4;O:22:\"Laravel\\Roster\\Package\":6:{s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^1.41\";s:10:\"\0*\0package\";E:34:\"Laravel\\Roster\\Enums\\Packages:SAIL\";s:14:\"\0*\0packageName\";s:12:\"laravel/sail\";s:10:\"\0*\0version\";s:6:\"1.52.0\";s:6:\"\0*\0dev\";b:1;}i:5;O:22:\"Laravel\\Roster\\Package\":6:{s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^4.3\";s:10:\"\0*\0package\";E:34:\"Laravel\\Roster\\Enums\\Packages:PEST\";s:14:\"\0*\0packageName\";s:12:\"pestphp/pest\";s:10:\"\0*\0version\";s:5:\"4.3.2\";s:6:\"\0*\0dev\";b:1;}i:6;O:22:\"Laravel\\Roster\\Package\":6:{s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:6:\"12.5.8\";s:10:\"\0*\0package\";E:37:\"Laravel\\Roster\\Enums\\Packages:PHPUNIT\";s:14:\"\0*\0packageName\";s:15:\"phpunit/phpunit\";s:10:\"\0*\0version\";s:6:\"12.5.8\";s:6:\"\0*\0dev\";b:1;}i:7;O:22:\"Laravel\\Roster\\Package\":6:{s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:10:\"\0*\0package\";E:41:\"Laravel\\Roster\\Enums\\Packages:TAILWINDCSS\";s:14:\"\0*\0packageName\";s:11:\"tailwindcss\";s:10:\"\0*\0version\";s:6:\"4.1.18\";s:6:\"\0*\0dev\";b:1;}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:21:\"\0*\0nodePackageManager\";E:43:\"Laravel\\Roster\\Enums\\NodePackageManager:NPM\";}s:9:\"timestamp\";i:1770563111;}', 1770649511);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `daily_reports`
--

CREATE TABLE `daily_reports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `status` enum('pending','approved') NOT NULL DEFAULT 'pending',
  `catatan_manager` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `daily_reports`
--

INSERT INTO `daily_reports` (`id`, `user_id`, `tanggal`, `status`, `catatan_manager`, `created_at`, `updated_at`) VALUES
(11, 5, '2026-02-09', 'approved', NULL, '2026-02-08 21:26:32', '2026-02-08 21:33:20'),
(12, 5, '2026-02-09', 'approved', NULL, '2026-02-08 21:35:06', '2026-02-08 21:35:24'),
(13, 5, '2026-02-10', 'approved', NULL, '2026-02-09 20:19:25', '2026-02-09 20:19:25'),
(14, 5, '2026-02-10', 'approved', NULL, '2026-02-09 20:20:20', '2026-02-09 20:20:20'),
(15, 5, '2026-02-10', 'approved', NULL, '2026-02-09 20:20:53', '2026-02-09 21:06:51'),
(16, 10, '2026-02-10', 'approved', NULL, '2026-02-09 21:20:03', '2026-02-09 21:20:03'),
(17, 10, '2026-02-10', 'approved', NULL, '2026-02-09 21:20:47', '2026-02-09 23:35:18'),
(18, 10, '2026-02-10', 'approved', NULL, '2026-02-09 23:34:18', '2026-02-09 23:34:32'),
(19, 10, '2026-02-10', 'approved', NULL, '2026-02-09 23:54:44', '2026-02-09 23:54:44'),
(20, 10, '2026-02-10', 'approved', NULL, '2026-02-09 23:54:45', '2026-02-09 23:54:56'),
(21, 10, '2026-02-10', 'approved', NULL, '2026-02-09 23:55:32', '2026-02-09 23:55:40'),
(22, 5, '2026-02-10', 'approved', NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(23, 5, '2026-02-09', 'approved', NULL, '2026-02-09 07:06:20', '2026-02-09 07:06:20'),
(24, 5, '2026-02-08', 'approved', NULL, '2026-02-08 07:06:20', '2026-02-08 07:06:20'),
(25, 5, '2026-02-07', 'approved', NULL, '2026-02-07 07:06:20', '2026-02-07 07:06:20'),
(26, 5, '2026-02-06', 'approved', NULL, '2026-02-06 07:06:20', '2026-02-06 07:06:20'),
(27, 5, '2026-02-05', 'approved', NULL, '2026-02-05 07:06:20', '2026-02-05 07:06:20'),
(28, 5, '2026-02-04', 'approved', NULL, '2026-02-04 07:06:20', '2026-02-04 07:06:20'),
(29, 5, '2026-02-03', 'approved', NULL, '2026-02-03 07:06:20', '2026-02-03 07:06:20'),
(30, 5, '2026-02-02', 'approved', NULL, '2026-02-02 07:06:20', '2026-02-02 07:06:20'),
(31, 5, '2026-02-01', 'approved', NULL, '2026-02-01 07:06:20', '2026-02-01 07:06:20'),
(32, 5, '2026-01-31', 'approved', NULL, '2026-01-31 07:06:20', '2026-01-31 07:06:20'),
(33, 5, '2026-01-30', 'approved', NULL, '2026-01-30 07:06:20', '2026-01-30 07:06:20'),
(34, 5, '2026-01-29', 'approved', NULL, '2026-01-29 07:06:20', '2026-01-29 07:06:20'),
(35, 5, '2026-01-28', 'approved', NULL, '2026-01-28 07:06:20', '2026-01-28 07:06:20'),
(36, 7, '2026-02-10', 'approved', NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(37, 7, '2026-02-09', 'approved', NULL, '2026-02-09 07:06:20', '2026-02-09 07:06:20'),
(38, 7, '2026-02-08', 'approved', NULL, '2026-02-08 07:06:20', '2026-02-08 07:06:20'),
(39, 7, '2026-02-07', 'approved', NULL, '2026-02-07 07:06:20', '2026-02-07 07:06:20'),
(40, 7, '2026-02-06', 'approved', NULL, '2026-02-06 07:06:20', '2026-02-06 07:06:20'),
(41, 7, '2026-02-05', 'approved', NULL, '2026-02-05 07:06:20', '2026-02-05 07:06:20'),
(42, 7, '2026-02-04', 'approved', NULL, '2026-02-04 07:06:20', '2026-02-04 07:06:20'),
(43, 7, '2026-02-03', 'approved', NULL, '2026-02-03 07:06:20', '2026-02-03 07:06:20'),
(44, 7, '2026-02-02', 'approved', NULL, '2026-02-02 07:06:20', '2026-02-02 07:06:20'),
(45, 7, '2026-02-01', 'approved', NULL, '2026-02-01 07:06:20', '2026-02-01 07:06:20'),
(46, 7, '2026-01-31', 'approved', NULL, '2026-01-31 07:06:20', '2026-01-31 07:06:20'),
(47, 7, '2026-01-30', 'approved', NULL, '2026-01-30 07:06:20', '2026-01-30 07:06:20'),
(48, 7, '2026-01-29', 'approved', NULL, '2026-01-29 07:06:20', '2026-01-29 07:06:20'),
(49, 7, '2026-01-28', 'approved', NULL, '2026-01-28 07:06:20', '2026-01-28 07:06:20'),
(50, 7, '2026-01-27', 'approved', NULL, '2026-01-27 07:06:20', '2026-01-27 07:06:20'),
(51, 8, '2026-02-10', 'approved', NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(52, 8, '2026-02-09', 'approved', NULL, '2026-02-09 07:06:20', '2026-02-09 07:06:20'),
(53, 8, '2026-02-08', 'approved', NULL, '2026-02-08 07:06:20', '2026-02-08 07:06:20'),
(54, 8, '2026-02-07', 'approved', NULL, '2026-02-07 07:06:20', '2026-02-07 07:06:20'),
(55, 8, '2026-02-06', 'approved', NULL, '2026-02-06 07:06:20', '2026-02-06 07:06:20'),
(56, 8, '2026-02-05', 'approved', NULL, '2026-02-05 07:06:20', '2026-02-05 07:06:20'),
(57, 8, '2026-02-04', 'approved', NULL, '2026-02-04 07:06:20', '2026-02-04 07:06:20'),
(58, 8, '2026-02-03', 'approved', NULL, '2026-02-03 07:06:20', '2026-02-03 07:06:20'),
(59, 8, '2026-02-02', 'approved', NULL, '2026-02-02 07:06:20', '2026-02-02 07:06:20'),
(60, 8, '2026-02-01', 'approved', NULL, '2026-02-01 07:06:20', '2026-02-01 07:06:20'),
(61, 8, '2026-01-31', 'approved', NULL, '2026-01-31 07:06:20', '2026-01-31 07:06:20'),
(62, 8, '2026-01-30', 'approved', NULL, '2026-01-30 07:06:20', '2026-01-30 07:06:20'),
(63, 9, '2026-02-10', 'approved', NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(64, 9, '2026-02-09', 'approved', NULL, '2026-02-09 07:06:20', '2026-02-09 07:06:20'),
(65, 9, '2026-02-08', 'approved', NULL, '2026-02-08 07:06:20', '2026-02-08 07:06:20'),
(66, 9, '2026-02-07', 'approved', NULL, '2026-02-07 07:06:20', '2026-02-07 07:06:20'),
(67, 9, '2026-02-06', 'approved', NULL, '2026-02-06 07:06:20', '2026-02-06 07:06:20'),
(68, 9, '2026-02-05', 'approved', NULL, '2026-02-05 07:06:20', '2026-02-05 07:06:20'),
(69, 9, '2026-02-04', 'approved', NULL, '2026-02-04 07:06:20', '2026-02-04 07:06:20'),
(70, 9, '2026-02-03', 'approved', NULL, '2026-02-03 07:06:20', '2026-02-03 07:06:20'),
(71, 9, '2026-02-02', 'approved', NULL, '2026-02-02 07:06:20', '2026-02-02 07:06:20'),
(72, 9, '2026-02-01', 'approved', NULL, '2026-02-01 07:06:20', '2026-02-01 07:06:20'),
(73, 9, '2026-01-31', 'approved', NULL, '2026-01-31 07:06:20', '2026-01-31 07:06:20'),
(74, 10, '2026-02-10', 'approved', NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(75, 10, '2026-02-09', 'approved', NULL, '2026-02-09 07:06:20', '2026-02-09 07:06:20'),
(76, 10, '2026-02-08', 'approved', NULL, '2026-02-08 07:06:20', '2026-02-08 07:06:20'),
(77, 10, '2026-02-07', 'approved', NULL, '2026-02-07 07:06:20', '2026-02-07 07:06:20'),
(78, 10, '2026-02-06', 'approved', NULL, '2026-02-06 07:06:20', '2026-02-06 07:06:20'),
(79, 10, '2026-02-05', 'approved', NULL, '2026-02-05 07:06:20', '2026-02-05 07:06:20'),
(80, 10, '2026-02-04', 'approved', NULL, '2026-02-04 07:06:20', '2026-02-04 07:06:20'),
(81, 10, '2026-02-03', 'approved', NULL, '2026-02-03 07:06:20', '2026-02-03 07:06:20'),
(82, 10, '2026-02-02', 'approved', NULL, '2026-02-02 07:06:20', '2026-02-02 07:06:20'),
(83, 10, '2026-02-01', 'approved', NULL, '2026-02-01 07:06:20', '2026-02-01 07:06:20'),
(84, 10, '2026-01-31', 'approved', NULL, '2026-01-31 07:06:20', '2026-01-31 07:06:20'),
(85, 10, '2026-01-30', 'approved', NULL, '2026-01-30 07:06:20', '2026-01-30 07:06:20'),
(86, 11, '2026-02-10', 'approved', NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(87, 11, '2026-02-09', 'approved', NULL, '2026-02-09 07:06:20', '2026-02-09 07:06:20'),
(88, 11, '2026-02-08', 'approved', NULL, '2026-02-08 07:06:20', '2026-02-08 07:06:20'),
(89, 11, '2026-02-07', 'approved', NULL, '2026-02-07 07:06:20', '2026-02-07 07:06:20'),
(90, 11, '2026-02-06', 'approved', NULL, '2026-02-06 07:06:20', '2026-02-06 07:06:20'),
(91, 11, '2026-02-05', 'approved', NULL, '2026-02-05 07:06:20', '2026-02-05 07:06:20'),
(92, 11, '2026-02-04', 'approved', NULL, '2026-02-04 07:06:20', '2026-02-04 07:06:20'),
(93, 11, '2026-02-03', 'approved', NULL, '2026-02-03 07:06:20', '2026-02-03 07:06:20'),
(94, 11, '2026-02-02', 'approved', NULL, '2026-02-02 07:06:20', '2026-02-02 07:06:20'),
(95, 11, '2026-02-01', 'approved', NULL, '2026-02-01 07:06:20', '2026-02-01 07:06:20'),
(96, 12, '2026-02-10', 'approved', NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(97, 12, '2026-02-09', 'approved', NULL, '2026-02-09 07:06:20', '2026-02-09 07:06:20'),
(98, 12, '2026-02-08', 'approved', NULL, '2026-02-08 07:06:20', '2026-02-08 07:06:20'),
(99, 12, '2026-02-07', 'approved', NULL, '2026-02-07 07:06:20', '2026-02-07 07:06:20'),
(100, 12, '2026-02-06', 'approved', NULL, '2026-02-06 07:06:20', '2026-02-06 07:06:20'),
(101, 12, '2026-02-05', 'approved', NULL, '2026-02-05 07:06:20', '2026-02-05 07:06:20'),
(102, 12, '2026-02-04', 'approved', NULL, '2026-02-04 07:06:20', '2026-02-04 07:06:20'),
(103, 12, '2026-02-03', 'approved', NULL, '2026-02-03 07:06:20', '2026-02-03 07:06:20'),
(104, 12, '2026-02-02', 'approved', NULL, '2026-02-02 07:06:20', '2026-02-02 07:06:20'),
(105, 12, '2026-02-01', 'approved', NULL, '2026-02-01 07:06:20', '2026-02-01 07:06:20'),
(106, 12, '2026-01-31', 'approved', NULL, '2026-01-31 07:06:20', '2026-01-31 07:06:20'),
(107, 12, '2026-01-30', 'approved', NULL, '2026-01-30 07:06:20', '2026-01-30 07:06:20'),
(108, 12, '2026-01-29', 'approved', NULL, '2026-01-29 07:06:20', '2026-01-29 07:06:20'),
(109, 12, '2026-01-28', 'approved', NULL, '2026-01-28 07:06:20', '2026-01-28 07:06:20'),
(110, 13, '2026-02-10', 'approved', NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(111, 13, '2026-02-09', 'approved', NULL, '2026-02-09 07:06:20', '2026-02-09 07:06:20'),
(112, 13, '2026-02-08', 'approved', NULL, '2026-02-08 07:06:20', '2026-02-08 07:06:20'),
(113, 13, '2026-02-07', 'approved', NULL, '2026-02-07 07:06:20', '2026-02-07 07:06:20'),
(114, 13, '2026-02-06', 'approved', NULL, '2026-02-06 07:06:20', '2026-02-06 07:06:20'),
(115, 13, '2026-02-05', 'approved', NULL, '2026-02-05 07:06:20', '2026-02-05 07:06:20'),
(116, 13, '2026-02-04', 'approved', NULL, '2026-02-04 07:06:20', '2026-02-04 07:06:20'),
(117, 13, '2026-02-03', 'approved', NULL, '2026-02-03 07:06:20', '2026-02-03 07:06:20'),
(118, 13, '2026-02-02', 'approved', NULL, '2026-02-02 07:06:20', '2026-02-02 07:06:20'),
(119, 13, '2026-02-01', 'approved', NULL, '2026-02-01 07:06:20', '2026-02-01 07:06:20'),
(120, 13, '2026-01-31', 'approved', NULL, '2026-01-31 07:06:20', '2026-01-31 07:06:20'),
(121, 13, '2026-01-30', 'approved', NULL, '2026-01-30 07:06:20', '2026-01-30 07:06:20'),
(122, 13, '2026-01-29', 'approved', NULL, '2026-01-29 07:06:20', '2026-01-29 07:06:20'),
(123, 14, '2026-02-10', 'approved', NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(124, 14, '2026-02-09', 'approved', NULL, '2026-02-09 07:06:20', '2026-02-09 07:06:20'),
(125, 14, '2026-02-08', 'approved', NULL, '2026-02-08 07:06:20', '2026-02-08 07:06:20'),
(126, 14, '2026-02-07', 'approved', NULL, '2026-02-07 07:06:20', '2026-02-07 07:06:20'),
(127, 14, '2026-02-06', 'approved', NULL, '2026-02-06 07:06:20', '2026-02-06 07:06:20'),
(128, 14, '2026-02-05', 'approved', NULL, '2026-02-05 07:06:20', '2026-02-05 07:06:20'),
(129, 14, '2026-02-04', 'approved', NULL, '2026-02-04 07:06:20', '2026-02-04 07:06:20'),
(130, 14, '2026-02-03', 'approved', NULL, '2026-02-03 07:06:20', '2026-02-03 07:06:20'),
(131, 14, '2026-02-02', 'approved', NULL, '2026-02-02 07:06:20', '2026-02-02 07:06:20'),
(132, 14, '2026-02-01', 'approved', NULL, '2026-02-01 07:06:20', '2026-02-01 07:06:20'),
(133, 14, '2026-01-31', 'approved', NULL, '2026-01-31 07:06:20', '2026-01-31 07:06:20'),
(134, 14, '2026-01-30', 'approved', NULL, '2026-01-30 07:06:20', '2026-01-30 07:06:20'),
(135, 14, '2026-01-29', 'approved', NULL, '2026-01-29 07:06:20', '2026-01-29 07:06:20'),
(136, 14, '2026-01-28', 'approved', NULL, '2026-01-28 07:06:20', '2026-01-28 07:06:20'),
(137, 14, '2026-01-27', 'approved', NULL, '2026-01-27 07:06:20', '2026-01-27 07:06:20'),
(138, 15, '2026-02-10', 'approved', NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(139, 15, '2026-02-09', 'approved', NULL, '2026-02-09 07:06:20', '2026-02-09 07:06:20'),
(140, 15, '2026-02-08', 'approved', NULL, '2026-02-08 07:06:20', '2026-02-08 07:06:20'),
(141, 15, '2026-02-07', 'approved', NULL, '2026-02-07 07:06:20', '2026-02-07 07:06:20'),
(142, 15, '2026-02-06', 'approved', NULL, '2026-02-06 07:06:20', '2026-02-06 07:06:20'),
(143, 15, '2026-02-05', 'approved', NULL, '2026-02-05 07:06:20', '2026-02-05 07:06:20'),
(144, 15, '2026-02-04', 'approved', NULL, '2026-02-04 07:06:20', '2026-02-04 07:06:20'),
(145, 15, '2026-02-03', 'approved', NULL, '2026-02-03 07:06:20', '2026-02-03 07:06:20'),
(146, 15, '2026-02-02', 'approved', NULL, '2026-02-02 07:06:20', '2026-02-02 07:06:20'),
(147, 15, '2026-02-01', 'approved', NULL, '2026-02-01 07:06:20', '2026-02-01 07:06:20'),
(148, 15, '2026-01-31', 'approved', NULL, '2026-01-31 07:06:20', '2026-01-31 07:06:20'),
(149, 15, '2026-01-30', 'approved', NULL, '2026-01-30 07:06:20', '2026-01-30 07:06:20'),
(150, 15, '2026-01-29', 'approved', NULL, '2026-01-29 07:06:20', '2026-01-29 07:06:20'),
(151, 15, '2026-01-28', 'approved', NULL, '2026-01-28 07:06:20', '2026-01-28 07:06:20'),
(152, 15, '2026-01-27', 'approved', NULL, '2026-01-27 07:06:20', '2026-01-27 07:06:20'),
(153, 10, '2026-02-10', 'approved', NULL, '2026-02-10 01:53:00', '2026-02-10 01:53:12'),
(154, 10, '2026-02-11', 'pending', NULL, '2026-02-10 23:54:11', '2026-02-10 23:54:11');

-- --------------------------------------------------------

--
-- Table structure for table `divisi`
--

CREATE TABLE `divisi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_divisi` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `divisi`
--

INSERT INTO `divisi` (`id`, `nama_divisi`, `created_at`, `updated_at`) VALUES
(1, 'TAC', '2026-02-07 22:12:34', '2026-02-07 22:12:34'),
(2, 'Infrastructure', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kegiatan_detail`
--

CREATE TABLE `kegiatan_detail` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `daily_report_id` bigint(20) UNSIGNED NOT NULL,
  `variabel_kpi_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tipe_kegiatan` enum('case','activity') NOT NULL DEFAULT 'case',
  `kategori` enum('Network','CCTV','GPS','Lainnya') DEFAULT NULL,
  `deskripsi_kegiatan` text NOT NULL,
  `value_raw` varchar(255) DEFAULT NULL,
  `temuan_sendiri` tinyint(1) NOT NULL DEFAULT 0,
  `is_mandiri` tinyint(1) NOT NULL DEFAULT 1,
  `pic_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kegiatan_detail`
--

INSERT INTO `kegiatan_detail` (`id`, `daily_report_id`, `variabel_kpi_id`, `tipe_kegiatan`, `kategori`, `deskripsi_kegiatan`, `value_raw`, `temuan_sendiri`, `is_mandiri`, `pic_name`, `created_at`, `updated_at`) VALUES
(26, 11, 1, 'case', NULL, 'jsksjsbsjs', '7', 0, 1, NULL, '2026-02-08 21:26:32', '2026-02-08 21:33:20'),
(27, 12, 1, 'case', NULL, 'hwhshsh', '3', 0, 1, NULL, '2026-02-08 21:35:06', '2026-02-08 21:35:24'),
(28, 12, 1, 'case', NULL, 'hjjjjjjj', '2', 0, 1, NULL, '2026-02-08 21:35:06', '2026-02-08 21:35:24'),
(29, 14, NULL, 'activity', NULL, 'membuat laporan', NULL, 0, 1, NULL, '2026-02-09 20:20:20', '2026-02-09 20:20:20'),
(30, 15, 1, 'case', NULL, 'jsksjsbsjs', '0', 1, 1, NULL, '2026-02-09 20:20:53', '2026-02-09 20:20:53'),
(31, 15, NULL, 'activity', NULL, 'membuat laporan', NULL, 0, 1, NULL, '2026-02-09 20:20:53', '2026-02-09 20:20:53'),
(32, 17, NULL, 'activity', NULL, '[Network] reffdd: vxfdfd', NULL, 0, 1, NULL, '2026-02-09 21:20:47', '2026-02-09 21:20:47'),
(33, 18, NULL, 'activity', NULL, '[Network] geheheheh: hdjsehs', NULL, 0, 1, NULL, '2026-02-09 23:34:18', '2026-02-09 23:34:18'),
(34, 19, NULL, 'activity', 'Network', 'ggyyuu: hhhhhh', NULL, 0, 1, NULL, '2026-02-09 23:54:44', '2026-02-09 23:54:44'),
(35, 20, NULL, 'activity', 'Network', 'ggyyuu: hhhhhh', NULL, 0, 1, NULL, '2026-02-09 23:54:45', '2026-02-09 23:54:45'),
(36, 21, NULL, 'activity', 'CCTV', 'tess: tess', NULL, 0, 1, NULL, '2026-02-09 23:55:32', '2026-02-09 23:55:32'),
(37, 22, NULL, 'activity', 'Lainnya', 'Monitoring rutin harian pada sistem B', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(38, 23, NULL, 'activity', 'Lainnya', 'Monitoring rutin harian pada sistem B', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(39, 23, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '4', 1, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(40, 24, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem B', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(41, 24, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '9', 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(42, 25, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem B', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(43, 26, NULL, 'activity', 'CCTV', 'Monitoring rutin harian pada sistem C', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(44, 26, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '2', 1, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(45, 27, NULL, 'activity', 'GPS', 'Monitoring rutin harian pada sistem C', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(46, 28, NULL, 'activity', 'GPS', 'Monitoring rutin harian pada sistem C', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(47, 29, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem C', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(48, 30, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem B', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(49, 31, NULL, 'activity', 'CCTV', 'Monitoring rutin harian pada sistem B', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(50, 32, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem B', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(51, 33, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(52, 33, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '4', 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(53, 34, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(54, 35, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(55, 36, NULL, 'activity', 'CCTV', 'Monitoring rutin harian pada sistem B', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(56, 36, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '1', 1, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(57, 37, NULL, 'activity', 'GPS', 'Monitoring rutin harian pada sistem B', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(58, 37, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '5', 1, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(59, 38, NULL, 'activity', 'GPS', 'Monitoring rutin harian pada sistem C', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(60, 38, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '1', 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(61, 39, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem C', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(62, 40, NULL, 'activity', 'GPS', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(63, 40, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '9', 1, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(64, 41, NULL, 'activity', 'CCTV', 'Monitoring rutin harian pada sistem C', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(65, 41, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '7', 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(66, 42, NULL, 'activity', 'GPS', 'Monitoring rutin harian pada sistem C', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(67, 43, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem C', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(68, 43, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '2', 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(69, 44, NULL, 'activity', 'GPS', 'Monitoring rutin harian pada sistem B', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(70, 45, NULL, 'activity', 'CCTV', 'Monitoring rutin harian pada sistem C', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(71, 45, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '3', 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(72, 46, NULL, 'activity', 'CCTV', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(73, 46, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '0', 1, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(74, 47, NULL, 'activity', 'CCTV', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(75, 47, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '1', 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(76, 48, NULL, 'activity', 'CCTV', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(77, 48, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '3', 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(78, 49, NULL, 'activity', 'GPS', 'Monitoring rutin harian pada sistem C', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(79, 49, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '1', 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(80, 50, NULL, 'activity', 'CCTV', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(81, 51, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(82, 52, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(83, 52, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '2', 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(84, 53, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem B', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(85, 54, NULL, 'activity', 'CCTV', 'Monitoring rutin harian pada sistem B', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(86, 55, NULL, 'activity', 'CCTV', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(87, 56, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem B', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(88, 56, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '2', 1, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(89, 57, NULL, 'activity', 'CCTV', 'Monitoring rutin harian pada sistem C', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(90, 57, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '5', 1, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(91, 58, NULL, 'activity', 'GPS', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(92, 58, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '5', 1, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(93, 59, NULL, 'activity', 'GPS', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(94, 59, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '8', 1, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(95, 60, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(96, 60, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '5', 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(97, 61, NULL, 'activity', 'GPS', 'Monitoring rutin harian pada sistem C', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(98, 62, NULL, 'activity', 'CCTV', 'Monitoring rutin harian pada sistem C', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(99, 63, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem C', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(100, 64, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(101, 65, NULL, 'activity', 'GPS', 'Monitoring rutin harian pada sistem C', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(102, 66, NULL, 'activity', 'CCTV', 'Monitoring rutin harian pada sistem C', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(103, 66, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '5', 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(104, 67, NULL, 'activity', 'GPS', 'Monitoring rutin harian pada sistem B', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(105, 68, NULL, 'activity', 'CCTV', 'Monitoring rutin harian pada sistem B', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(106, 69, NULL, 'activity', 'GPS', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(107, 70, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem B', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(108, 70, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '4', 1, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(109, 71, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem C', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(110, 71, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '4', 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(111, 72, NULL, 'activity', 'CCTV', 'Monitoring rutin harian pada sistem B', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(112, 73, NULL, 'activity', 'CCTV', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(113, 73, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '7', 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(114, 74, NULL, 'activity', 'CCTV', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(115, 74, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '7', 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(116, 75, NULL, 'activity', 'CCTV', 'Monitoring rutin harian pada sistem B', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(117, 75, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '8', 1, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(118, 76, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(119, 77, NULL, 'activity', 'GPS', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(120, 77, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '9', 1, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(121, 78, NULL, 'activity', 'GPS', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(122, 79, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(123, 80, NULL, 'activity', 'GPS', 'Monitoring rutin harian pada sistem B', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(124, 80, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '8', 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(125, 81, NULL, 'activity', 'GPS', 'Monitoring rutin harian pada sistem B', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(126, 81, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '0', 1, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(127, 82, NULL, 'activity', 'GPS', 'Monitoring rutin harian pada sistem B', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(128, 83, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem C', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(129, 83, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '5', 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(130, 84, NULL, 'activity', 'GPS', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(131, 85, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem C', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(132, 85, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '8', 1, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(133, 86, NULL, 'activity', 'CCTV', 'Monitoring rutin harian pada sistem B', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(134, 87, NULL, 'activity', 'CCTV', 'Monitoring rutin harian pada sistem C', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(135, 87, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '6', 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(136, 88, NULL, 'activity', 'CCTV', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(137, 88, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '5', 1, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(138, 89, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem C', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(139, 89, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '3', 1, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(140, 90, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem C', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(141, 90, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '2', 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(142, 91, NULL, 'activity', 'GPS', 'Monitoring rutin harian pada sistem B', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(143, 92, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem C', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(144, 93, NULL, 'activity', 'CCTV', 'Monitoring rutin harian pada sistem C', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(145, 94, NULL, 'activity', 'CCTV', 'Monitoring rutin harian pada sistem B', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(146, 95, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem C', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(147, 96, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem C', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(148, 97, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem B', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(149, 97, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '5', 1, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(150, 98, NULL, 'activity', 'GPS', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(151, 98, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '1', 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(152, 99, NULL, 'activity', 'CCTV', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(153, 99, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '9', 1, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(154, 100, NULL, 'activity', 'GPS', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(155, 101, NULL, 'activity', 'GPS', 'Monitoring rutin harian pada sistem C', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(156, 102, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(157, 103, NULL, 'activity', 'GPS', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(158, 103, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '0', 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(159, 104, NULL, 'activity', 'CCTV', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(160, 104, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '5', 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(161, 105, NULL, 'activity', 'CCTV', 'Monitoring rutin harian pada sistem B', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(162, 105, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '4', 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(163, 106, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem C', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(164, 106, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '8', 1, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(165, 107, NULL, 'activity', 'CCTV', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(166, 108, NULL, 'activity', 'GPS', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(167, 109, NULL, 'activity', 'GPS', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(168, 110, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem C', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(169, 111, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem B', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(170, 112, NULL, 'activity', 'CCTV', 'Monitoring rutin harian pada sistem C', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(171, 113, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(172, 113, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '6', 1, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(173, 114, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem B', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(174, 114, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '1', 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(175, 115, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(176, 116, NULL, 'activity', 'CCTV', 'Monitoring rutin harian pada sistem B', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(177, 117, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem C', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(178, 117, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '0', 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(179, 118, NULL, 'activity', 'GPS', 'Monitoring rutin harian pada sistem B', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(180, 118, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '4', 1, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(181, 119, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(182, 120, NULL, 'activity', 'CCTV', 'Monitoring rutin harian pada sistem B', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(183, 121, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem B', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(184, 122, NULL, 'activity', 'CCTV', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(185, 123, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(186, 124, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(187, 125, NULL, 'activity', 'CCTV', 'Monitoring rutin harian pada sistem B', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(188, 125, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '5', 1, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(189, 126, NULL, 'activity', 'GPS', 'Monitoring rutin harian pada sistem C', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(190, 127, NULL, 'activity', 'GPS', 'Monitoring rutin harian pada sistem B', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(191, 128, NULL, 'activity', 'GPS', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(192, 129, NULL, 'activity', 'CCTV', 'Monitoring rutin harian pada sistem C', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(193, 129, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '2', 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(194, 130, NULL, 'activity', 'CCTV', 'Monitoring rutin harian pada sistem B', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(195, 130, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '6', 1, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(196, 131, NULL, 'activity', 'GPS', 'Monitoring rutin harian pada sistem C', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(197, 131, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '4', 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(198, 132, NULL, 'activity', 'GPS', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(199, 133, NULL, 'activity', 'CCTV', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(200, 133, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '4', 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(201, 134, NULL, 'activity', 'CCTV', 'Monitoring rutin harian pada sistem C', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(202, 135, NULL, 'activity', 'GPS', 'Monitoring rutin harian pada sistem B', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(203, 135, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '6', 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(204, 136, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem C', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(205, 136, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '8', 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(206, 137, NULL, 'activity', 'CCTV', 'Monitoring rutin harian pada sistem C', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(207, 137, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '6', 1, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(208, 138, NULL, 'activity', 'CCTV', 'Monitoring rutin harian pada sistem C', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(209, 139, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem C', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(210, 139, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '4', 1, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(211, 140, NULL, 'activity', 'GPS', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(212, 140, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '2', 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(213, 141, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem B', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(214, 141, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '8', 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(215, 142, NULL, 'activity', 'GPS', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(216, 142, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '0', 1, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(217, 143, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(218, 143, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '6', 1, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(219, 144, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(220, 145, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem B', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(221, 146, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem C', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(222, 146, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '8', 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(223, 147, NULL, 'activity', 'GPS', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(224, 147, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '6', 1, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(225, 148, NULL, 'activity', 'CCTV', 'Monitoring rutin harian pada sistem B', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(226, 148, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '0', 1, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(227, 149, NULL, 'activity', 'Network', 'Monitoring rutin harian pada sistem B', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(228, 150, NULL, 'activity', 'GPS', 'Monitoring rutin harian pada sistem C', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(229, 150, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '6', 1, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(230, 151, NULL, 'activity', 'Lainnya', 'Monitoring rutin harian pada sistem A', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(231, 152, NULL, 'activity', 'CCTV', 'Monitoring rutin harian pada sistem C', NULL, 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(232, 152, 1, 'case', NULL, 'Perbaikan kendala koneksi di titik User', '4', 0, 1, NULL, '2026-02-10 07:06:20', '2026-02-10 07:06:20'),
(233, 153, NULL, 'activity', 'Lainnya', 'Uji coba: Uji coba', NULL, 0, 1, NULL, '2026-02-10 01:53:00', '2026-02-10 01:53:00'),
(234, 154, NULL, 'activity', 'Lainnya', 'jshsjshsj: bsjshsjw', NULL, 0, 1, NULL, '2026-02-10 23:54:11', '2026-02-10 23:54:11');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000001_create_cache_table', 1),
(2, '0001_01_01_000002_create_jobs_table', 1),
(3, '2026_02_05_040647_create_sessions_table', 1),
(4, '2026_02_08_045538_create_divisis_table', 1),
(5, '2026_02_08_045553_create_users_table', 1),
(6, '2026_02_08_045626_create_variabel_kpis_table', 1),
(7, '2026_02_08_045652_create_daily_reports_table', 1),
(9, '2026_02_10_025832_add_tipe_kegiatan_to_kegiatan_detail_table', 2),
(10, '2026_02_10_031401_make_variabel_kpi_id_nullable_in_kegiatan_detail', 3);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('OXudtwUrPIqIrEoP9hKQHXLTuwItnjwcchItneZe', 10, '10.100.10.80', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiTmFkQ0NOa2lzb3o4QUozRnNEaWw5eE9Tc3dyelhHSDVvTlBUZVVFUyI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjQxOiJodHRwOi8vMTAuMTAwLjEwLjc2OjgwMDAvc3RhZmYvaW5wdXQtY2FzZSI7czo1OiJyb3V0ZSI7czoxMToic3RhZmYuaW5wdXQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxMDt9', 1770792851),
('yWMePXSS4peBGbNlXthEmSuF6tfVNgM2ohtxWWxr', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoidzNDT1FHYjJVeUtxbk1NNUwzdDR6RW4wSE90ZEtNd09sajhCa0tNTyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9tYW5hZ2VyL2Rhc2hib2FyZD9kaXZpc2lfaWQ9MSI7czo1OiJyb3V0ZSI7czoxNzoibWFuYWdlci5kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1770796484);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_lengkap` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('manager','staff') NOT NULL,
  `divisi_id` bigint(20) UNSIGNED NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama_lengkap`, `email`, `username`, `password`, `role`, `divisi_id`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Jay Manager', 'jay@mybolo.com', 'jay_manager', '$2y$12$rCj5ObrxBxiLXUYAHbA8qOKm9zQmn9jSYeg4Jx9I/Ltuo2SDFle/m', 'manager', 1, NULL, '2026-02-07 22:12:35', '2026-02-07 22:12:35'),
(5, 'Staff Tiga', 'staff-3@mybolo.com', 'staff-3', '$2y$12$BN2RfJModu0zCDOYpoF2CO79/zSmzQgOtpjA0ebv1u.SzBD3.ghMe', 'staff', 1, NULL, '2026-02-08 21:04:04', '2026-02-08 21:04:04'),
(7, 'Dimas Suhendra', 'dimassuhendra@mybolo.com', 'dimassuhendra', '$2y$12$5fUgxODz.u0SWaGr5tzxDueGj0Nxqlwk3YkSl6A2BgmOB8CbFW61K', 'staff', 1, NULL, '2026-02-09 20:23:19', '2026-02-09 20:23:19'),
(8, 'Makki', 'makki@myboo.com', 'makki', '$2y$12$PO8kXGuY5w/kTHuQ2WF4seNUzBthWMGAUg2YtytiB4ej01rLT8TO.', 'staff', 1, NULL, '2026-02-09 20:23:39', '2026-02-09 20:23:39'),
(9, 'Reynaldi', 'reynaldi@mybolo.com', 'reynaldi', '$2y$12$wPS5IHDRF2P11BwwXO2qVuvTEFZ1Wh5Jiwdk68MFLk5qP0cG2GMPy', 'staff', 1, NULL, '2026-02-09 20:24:00', '2026-02-09 20:24:00'),
(10, 'Haikal', 'haikal@mybolo.com', 'haikal', '$2y$12$Ujq7IehZGjXjRP5fLbzJ/u99Gy87m7t0bLBnK2DswP3B0yrlr9nu6', 'staff', 2, NULL, '2026-02-09 21:08:41', '2026-02-09 21:08:41'),
(11, 'Noval', 'noval@mybolo.com', 'noval', '$2y$12$S8D5ESHJ2XGdEBT3IqCLDes09SCYlh5p1kCrGxlVMvUjPuMA/BDsq', 'staff', 2, NULL, '2026-02-09 23:30:52', '2026-02-09 23:30:52'),
(12, 'Bang Abid', 'abid@mybolo.com', 'abid', '$2y$12$/yfUuzlly5gGa3ioqxsBO.KUJgyGQzF0x72u1UtRdnvaLLXQNgbCa', 'staff', 2, NULL, '2026-02-09 23:31:23', '2026-02-09 23:31:43'),
(13, 'Mas Chandra', 'chandra@mybolo.com', 'chandra', '$2y$12$zuIkMSj8BipbBzAviYoPWunJhx9U628YH5CLGfrP4pxCO3./d69QS', 'staff', 2, NULL, '2026-02-09 23:32:07', '2026-02-09 23:32:07'),
(14, 'Bang Geri', 'geri@mybolo.com', 'geri', '$2y$12$YScqQjdRQi0Snzy81EkzUuqVCJ6Eq51szb/HfCJsLUbHDIN1cZ7ea', 'staff', 2, NULL, '2026-02-09 23:32:38', '2026-02-09 23:32:38'),
(15, 'Mas Ridik', 'ridik@mybolo.com', 'ridik', '$2y$12$AjTn5XoCVae58hz2lFufYeNNQnDfueWyAYy.CZdHtLCorCt7gKLzu', 'staff', 2, NULL, '2026-02-09 23:33:04', '2026-02-09 23:33:04');

-- --------------------------------------------------------

--
-- Table structure for table `variabel_kpis`
--

CREATE TABLE `variabel_kpis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `divisi_id` bigint(20) UNSIGNED NOT NULL,
  `nama_variabel` varchar(255) NOT NULL,
  `input_type` enum('boolean','number','string') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `variabel_kpis`
--

INSERT INTO `variabel_kpis` (`id`, `divisi_id`, `nama_variabel`, `input_type`, `created_at`, `updated_at`) VALUES
(1, 1, 'Jumlah Case Harian', 'string', '2026-02-07 22:12:35', '2026-02-08 06:46:47'),
(2, 1, 'Durasi Response (Ambang Batas 15 Menit)', 'number', '2026-02-07 22:12:35', '2026-02-08 06:02:37'),
(3, 1, 'Case Ditemukan Sendiri', 'boolean', '2026-02-07 22:12:35', '2026-02-08 06:02:28'),
(4, 1, 'Penyelesaian Mandiri (Bonus)', 'boolean', '2026-02-07 22:12:35', '2026-02-08 06:02:15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `daily_reports`
--
ALTER TABLE `daily_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `daily_reports_user_id_foreign` (`user_id`);

--
-- Indexes for table `divisi`
--
ALTER TABLE `divisi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kegiatan_detail`
--
ALTER TABLE `kegiatan_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kegiatan_detail_daily_report_id_foreign` (`daily_report_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD KEY `users_divisi_id_foreign` (`divisi_id`);

--
-- Indexes for table `variabel_kpis`
--
ALTER TABLE `variabel_kpis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `variabel_kpis_divisi_id_foreign` (`divisi_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `daily_reports`
--
ALTER TABLE `daily_reports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=155;

--
-- AUTO_INCREMENT for table `divisi`
--
ALTER TABLE `divisi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kegiatan_detail`
--
ALTER TABLE `kegiatan_detail`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=235;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `variabel_kpis`
--
ALTER TABLE `variabel_kpis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `daily_reports`
--
ALTER TABLE `daily_reports`
  ADD CONSTRAINT `daily_reports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kegiatan_detail`
--
ALTER TABLE `kegiatan_detail`
  ADD CONSTRAINT `kegiatan_detail_daily_report_id_foreign` FOREIGN KEY (`daily_report_id`) REFERENCES `daily_reports` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_divisi_id_foreign` FOREIGN KEY (`divisi_id`) REFERENCES `divisi` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `variabel_kpis`
--
ALTER TABLE `variabel_kpis`
  ADD CONSTRAINT `variabel_kpis_divisi_id_foreign` FOREIGN KEY (`divisi_id`) REFERENCES `divisi` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
