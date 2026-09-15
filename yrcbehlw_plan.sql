-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 15, 2026 at 07:10 AM
-- Server version: 10.6.27-MariaDB
-- PHP Version: 8.4.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `yrcbehlw_plan`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `action` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
('laravel-cache-admin@gmail.com|127.0.0.1', 'i:1;', 1788338556),
('laravel-cache-admin@gmail.com|127.0.0.1:timer', 'i:1788338556;', 1788338556),
('yc-content-planning-cache-admin@gmail.com|116.68.205.66', 'i:1;', 1789376013),
('yc-content-planning-cache-admin@gmail.com|116.68.205.66:timer', 'i:1789376013;', 1789376013),
('yc-content-planning-cache-app_live_url', 's:23:\"https://plan.yrc-bd.com\";', 2104816139);

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
-- Table structure for table `calendar_events`
--

CREATE TABLE `calendar_events` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `team_type` varchar(255) NOT NULL,
  `event_date` date NOT NULL,
  `content_title` varchar(255) DEFAULT NULL,
  `aipe_pillar` varchar(255) DEFAULT NULL,
  `content_objective` text DEFAULT NULL,
  `format` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `boosting_budget` varchar(255) DEFAULT NULL,
  `financial_budget` varchar(111) DEFAULT NULL,
  `drive_link` varchar(255) DEFAULT NULL,
  `shoot_date` date DEFAULT NULL,
  `color_concern` varchar(255) DEFAULT NULL,
  `platform` varchar(255) DEFAULT NULL,
  `product` varchar(255) DEFAULT NULL,
  `post_no` varchar(255) DEFAULT NULL,
  `product_focus` varchar(255) DEFAULT NULL,
  `status` enum('done','not_done') DEFAULT 'not_done',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `calendar_events`
--

INSERT INTO `calendar_events` (`id`, `user_id`, `team_type`, `event_date`, `content_title`, `aipe_pillar`, `content_objective`, `format`, `remarks`, `boosting_budget`, `financial_budget`, `drive_link`, `shoot_date`, `color_concern`, `platform`, `product`, `post_no`, `product_focus`, `status`, `created_at`, `updated_at`) VALUES
(7, 1, 'global_team', '2026-09-05', 'International Day of Charity', NULL, 'Global observance', NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'not_done', '2026-08-23 07:10:54', '2026-08-30 02:11:56'),
(8, 1, 'global_team', '2026-09-06', 'San Marino GP', NULL, 'Global observance', NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'not_done', '2026-08-23 07:10:54', '2026-08-30 02:11:56'),
(9, 1, 'global_team', '2026-09-07', 'Madhu Purnima and Mahalaya', NULL, 'Global observance', NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'not_done', '2026-08-23 07:10:54', '2026-08-30 02:11:56'),
(10, 1, 'global_team', '2026-09-12', 'Saluto UBS Launching', NULL, 'Global observance', NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'not_done', '2026-08-23 07:10:54', '2026-09-14 07:12:13'),
(12, 1, 'global_team', '2026-10-01', 'Ashtami', NULL, 'Global observance', NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'not_done', '2026-08-23 07:10:54', '2026-08-30 02:11:56'),
(13, 1, 'global_team', '2026-10-04', 'Japanese GP', NULL, 'Global observance', NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'not_done', '2026-08-23 07:10:54', '2026-08-30 02:11:56'),
(14, 1, 'global_team', '2026-10-18', 'Durga Puja', NULL, 'Global observance', NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'not_done', '2026-08-23 07:10:54', '2026-08-30 02:11:56'),
(15, 1, 'global_team', '2026-11-01', 'Halloween', NULL, 'Global observance', NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'not_done', '2026-08-23 07:10:54', '2026-08-30 02:11:56'),
(16, 1, 'global_team', '2026-11-17', 'World Diabetes Day', NULL, 'Global observance', NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'not_done', '2026-08-23 07:10:54', '2026-08-30 02:11:56'),
(17, 1, 'global_team', '2026-12-05', 'World AIDS Day', NULL, 'Global observance', NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'not_done', '2026-08-23 07:10:54', '2026-08-30 02:11:56'),
(18, 1, 'global_team', '2026-12-25', 'Christmas Day', NULL, 'Global observance', NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'not_done', '2026-08-23 07:10:54', '2026-08-30 02:11:56'),
(47, 3, 'digital_team', '2026-09-17', 'Post #1: FZS V4', 'Lifestyle Aspiration', 'Premium urban lifestyle story positioning the FZS V4 as a confident everyday companion from workdays to evening rides.', 'Reel', '', '0', NULL, '', NULL, NULL, NULL, NULL, '1', 'FZS V4', 'not_done', '2026-08-30 01:51:16', '2026-08-30 02:11:56'),
(50, 3, 'digital_team', '2026-09-21', 'Post #1: FZS Hybrid', 'Interest Generation', 'Explain Smart Motor Generator and Stop & Start System benefits through a simple everyday traffic scenario.', 'Motion', '', '0', NULL, '', NULL, NULL, NULL, NULL, '1', 'FZS Hybrid', 'not_done', '2026-08-30 01:51:16', '2026-08-30 02:11:56'),
(54, 3, 'digital_team', '2026-09-27', 'Post #1: FZ-X', 'Lifestyle Aspiration', 'World Tourism Day journey story celebrating responsible discovery of Bangladesh through a relaxed, culturally curious touring perspective.', 'Reel', '', '0', NULL, '', NULL, NULL, NULL, NULL, '1', 'FZ-X', 'not_done', '2026-08-30 01:51:16', '2026-08-30 02:11:56'),
(57, 3, 'digital_team', '2026-09-30', 'Post #1: FZS Hybrid', 'Customer Experience', 'Month-closing customer story demonstrating practical hybrid ownership across a realistic weekday routine.', 'Reel', '', '0', NULL, '', NULL, NULL, NULL, NULL, '1', 'FZS Hybrid', 'not_done', '2026-08-30 01:51:16', '2026-08-30 02:11:56'),
(520, 3, 'digital_team', '2026-09-01', 'Month-opening offer announcement', 'Interest', 'Month-opening offer announcement. One clean static/motion carousel revealing September price and EMI schemes across all six priority models in one place, so a browsing customer can shop the whole range from a single post. CTA: \'Visit your nearest showroom this September.\' Sets the tone that this is a purchase-forward month.', NULL, 'Content Type: Static', '400', NULL, 'https://drive.google.com/drive/folders/1ycC_iuAnzjbOLCaDNJoj9cNkSuf57GaL', NULL, 'Month Kick-off', '', NULL, '1', NULL, 'done', '2026-09-02 03:32:36', '2026-09-14 06:53:25'),
(521, 3, 'digital_team', '2026-09-04', 'FZS FI Hybrid Lifestyle Trend', 'Interest', 'Multiple Product Lifestyle', 'Reels', 'Content Type: Short-form Video', '70', NULL, 'https://www.facebook.com/share/v/1ESypHWM9z/', NULL, 'Cyan Mettalic', '', NULL, '3', NULL, 'done', '2026-09-02 03:32:36', '2026-09-14 06:53:56'),
(523, 3, 'digital_team', '2026-09-05', 'Customer Success Story Documentary Featuring a Yamaha Bike', 'Interest', 'Interest Generation Content  Straightforward conversion content for the second half of the bread-and-butter pair.', 'Reels', 'Content Type: Short-form Video', '60', '0', 'https://drive.google.com/file/d/1wV5mjttaBbFEUj6iZiw5MKGpmVIOYNc-/view?usp=drive_link', NULL, 'Dark Knight', '', NULL, '4', 'FZS V4', 'not_done', '2026-09-02 03:32:36', '2026-09-14 06:55:24'),
(524, 3, 'digital_team', '2026-09-06', 'FZ 25 Lifestyle OVC', 'Experience', 'FZ 25 Lifestyle OVC. Showcasing real customer story in a cinematic storytelling way', NULL, 'No content provided', '0', NULL, NULL, NULL, 'Racing Blue', '', NULL, '5', NULL, 'not_done', '2026-09-02 03:32:36', '2026-09-13 12:10:42'),
(526, 3, 'digital_team', '2026-09-07', 'FZX Rajshahi Couple Video', 'Experience', 'FZX Rajshahi Couple Video. Portray 62% of FZ-X target demographic, who are married with emotional content.', 'Reel', 'Content Type: Long-form Video', '0', NULL, '', NULL, 'Matte Blue', '', NULL, '7', 'FZ-X', 'not_done', '2026-09-02 03:32:36', '2026-09-02 03:32:36'),
(528, 3, 'digital_team', '2026-09-11', 'Feature communication of the TFT meter and multimedia functionality and its real life benefits', 'Interest', 'Feature communication of the TFT meter and multimedia functionality and its real life benefits.', 'Reels', 'Content Type: Long-form Video', '60', '0', 'https://www.facebook.com/share/r/1EuNUYpSCc/', NULL, 'Cyan Blue', '', NULL, '9', NULL, 'done', '2026-09-02 03:32:36', '2026-09-14 07:09:38'),
(531, 3, 'digital_team', '2026-09-14', 'FZ V2 Customer Lifestyle Review', 'Experience', 'FZ V2 Customer Lifestyle Review. To showcase an authentic customer experience with Yamaha FZS Version 2', 'Long-form Video', 'Content Type: Long-form Video', '0', NULL, '', NULL, 'Black Metallic', '', NULL, '12', 'FZS V2 (DD)', 'not_done', '2026-09-02 03:32:36', '2026-09-02 03:32:36'),
(533, 3, 'digital_team', '2026-09-16', 'MT 15 \"The Moment That Move Us\" \"Build an always-on customer engagement platform that encourages R15...', 'Experience', 'MT 15 \"The Moment That Move Us\" \"Build an always-on customer engagement platform that encourages R15 and MT15 riders to share their photos, reels, riding moments and personal memories.\n\"', 'Reel', 'Content Type: Long-form Video', '0', NULL, '', NULL, 'Metallic Black', '', NULL, '14', 'MT15-V2', 'not_done', '2026-09-02 03:32:36', '2026-09-02 03:32:36'),
(535, 3, 'digital_team', '2026-09-19', 'AI VIDEO ON THE TECHNOLOGY AND AESTHETIC OF THE DARK KNIGHT', 'Interest', 'AI VIDEO ON THE TECHNOLOGY AND AESTHETIC OF THE DARK KNIGHT', 'Reel', 'Content Type: Short-form Video', '0', NULL, '', NULL, 'Metallic Black', '', NULL, '16', 'MT15-V2', 'not_done', '2026-09-02 03:32:36', '2026-09-02 03:32:36'),
(536, 3, 'digital_team', '2026-09-20', 'Feature content on fuel economy and touring comfort — the two things Fazer owners consistently value', 'Interest', 'Feature content on fuel economy and touring comfort — the two things Fazer owners consistently value. Core purpose: follow the Sep 8 awareness post with a concrete reason to consider, before any offer messaging.', 'Motion Video', 'Content Type: Short-form Video', '0', NULL, '', NULL, 'World Cleanup Day', '', NULL, '17', 'Multiple Model', 'not_done', '2026-09-02 03:32:36', '2026-09-02 03:32:36'),
(538, 3, 'digital_team', '2026-09-23', 'Theme Based MT15 and Product shoot content by AI, AI-generated thematic visual content cost-effectiv...', 'Purchase', 'Theme Based MT15 and Product shoot content by AI, AI-generated thematic visual content cost-effectively delivers studio-quality aesthetic shoots for the R15 and MT-15, tailored to capture the visual preferences of Gen Z and young riders. This strategy drives targeted brand awareness, elevates product desire, and boosts digital engagement without the heavy production expenses of traditional shoots. Want to make that content by DM team.', 'Reel', 'Content Type: Short-form Video', '0', NULL, '', NULL, 'Metallic Grey', '', NULL, '19', 'R15 Series', 'not_done', '2026-09-02 03:32:36', '2026-09-02 03:32:36'),
(539, 3, 'digital_team', '2026-09-24', 'CUSTOMER REVIEW (distinct from the Sep 14 ride review) — a real owner talks about day-to-day ownersh...', 'Experience', 'CUSTOMER REVIEW (distinct from the Sep 14 ride review) — a real owner talks about day-to-day ownership: service experience, resale confidence, how the bike has held up. Core purpose: trust and ownership confidence rather than performance — this is the proof a hesitant buyer needs.', 'Reel', 'Content Type: Short-form Video', '0', NULL, '', NULL, 'Dark Knight Red', '', NULL, '20', 'FZS V2 (DD)', 'not_done', '2026-09-02 03:32:36', '2026-09-02 03:32:36'),
(540, 3, 'digital_team', '2026-09-25', 'FZX Lifestyle Review, Showcasing FZX as a lifestyle product', 'Experience', 'FZX Lifestyle Review, Showcasing FZX as a lifestyle product', 'Reel', 'Content Type: Long-form Video', '0', NULL, '', NULL, 'Matte Titan', '', NULL, '21', 'FZX', 'not_done', '2026-09-02 03:32:36', '2026-09-02 03:32:36'),
(541, 3, 'digital_team', '2026-09-26', 'September offer CTA — the close of a three-post FZ25 arc this month (Awareness on Sep 5, Interest on...', 'Purchase', 'September offer CTA — the close of a three-post FZ25 arc this month (Awareness on Sep 5, Interest on Sep 15, Purchase here). Core purpose: convert the visibility rebuilt earlier in the month rather than opening with price.', 'Reel', 'Content Type: Short-form Video', '0', NULL, '', NULL, 'Racing Blue', '', NULL, '22', 'FZ25', 'not_done', '2026-09-02 03:32:36', '2026-09-02 03:32:36'),
(542, 3, 'digital_team', '2026-09-27', 'World Tourism Day tie-in', 'Interest', 'World Tourism Day tie-in. Connects FZ-X\'s long-route, retro-modern identity to responsible discovery and local journeys — emotional/lifestyle register, not a sales post. Core purpose: reinforce FZ-X\'s touring identity on a day the audience is already primed for travel content.', 'Reel', 'Content Type: Short-form Video', '0', NULL, '', NULL, 'World Tourism Day', '', NULL, '23', 'Multiple Model', 'not_done', '2026-09-02 03:32:36', '2026-09-02 03:32:36'),
(543, 3, 'digital_team', '2026-09-28', 'Racing Perfectionist Meet Up - Cultivate brand loyalty and performance culture among R-Series owners...', 'Experience', 'Racing Perfectionist Meet Up - Cultivate brand loyalty and performance culture among R-Series owners by creating an exclusive platform that strengthens community connection and generates organic user advocacy.', 'Reel', 'Content Type: Long-form Video', '0', NULL, '', NULL, 'Metallic Grey', '', NULL, '24', 'R15 Series', 'not_done', '2026-09-02 03:32:36', '2026-09-02 03:32:36'),
(544, 3, 'digital_team', '2026-09-29', 'CUSTOMER REVIEW — an owner\'s honest take on long-term ownership (control, comfort on daily commutes,...', 'Experience', 'CUSTOMER REVIEW — an owner\'s honest take on long-term ownership (control, comfort on daily commutes, confidence in the ABS system). Core purpose: proof-of-ownership content to sit alongside the month\'s purchase pushes.', 'Reel', 'Content Type: Short-form Video', '0', NULL, '', NULL, 'Matte Black', '', NULL, '25', 'FZS V4', 'not_done', '2026-09-02 03:32:36', '2026-09-02 03:32:36'),
(597, 3, 'digital_team', '2026-09-15', 'Post #1: FZ25', 'Interest Generation', 'Performance-led feature content focused on 249cc character, Dual Channel ABS and comfortable upright ergonomics.', 'Motion', '', '0', NULL, '', NULL, NULL, NULL, NULL, '1', 'FZ25', 'not_done', '2026-09-02 03:32:37', '2026-09-02 03:32:37'),
(599, 1, 'digital_team', '2026-09-02', NULL, NULL, 'Month-opening offer announcement. One clean static/motion carousel revealing September price and EMI schemes across all six priority models in one place, so a browsing customer can shop the whole range from a single post. CTA: \'Visit your nearest showroom this September.\' Sets the tone that this is a purchase-forward month.', 'Reels', NULL, '400', NULL, 'https://www.facebook.com/share/v/1MFqrccJbd/', NULL, NULL, NULL, NULL, '2', NULL, 'done', '2026-09-13 12:05:58', '2026-09-14 06:53:41'),
(601, 1, 'digital_team', '2026-09-06', NULL, 'Interest', 'MT15 INTEREST GENERATION CONTENT', 'Reels', NULL, '60', '0', 'https://www.facebook.com/share/v/19TgajzWjr/', NULL, NULL, NULL, NULL, '5', 'MT', 'done', '2026-09-14 06:59:33', '2026-09-14 06:59:46'),
(602, 1, 'digital_team', '2026-09-07', NULL, 'Interest', '80s Trend Catch up', 'Special Content', NULL, '80', '0', 'https://www.facebook.com/share/p/14tQPS87Gfa/', NULL, NULL, NULL, NULL, NULL, 'FZX', 'done', '2026-09-14 07:00:56', '2026-09-14 07:01:20'),
(603, 1, 'digital_team', '2026-09-08', NULL, NULL, 'Trend Catchup Video', 'Reels', NULL, '100', '0', 'https://www.facebook.com/share/r/1DqexoSqrG/', NULL, NULL, NULL, NULL, 'Trend Catchup Video', 'FZS V4', 'done', '2026-09-14 07:02:50', '2026-09-14 07:05:01'),
(604, 1, 'digital_team', '2026-09-08', NULL, 'Experience', 'R15M Delivery Celebration', 'Special Content', NULL, '100', '0', 'https://www.facebook.com/share/v/1DjsvQh5fU/', NULL, NULL, NULL, NULL, 'R15M Delivery Celebration', 'R15', 'done', '2026-09-14 07:03:59', '2026-09-14 07:04:42'),
(605, 1, 'digital_team', '2026-09-09', NULL, 'Experience', 'Kizuna', 'Reels', NULL, '80', '0', 'https://www.facebook.com/share/r/19kAgjFoZ1/', NULL, NULL, NULL, NULL, 'Kizuna Ep 1', 'Fazer', 'done', '2026-09-14 07:07:25', '2026-09-14 07:07:31'),
(606, 1, 'digital_team', '2026-09-10', NULL, NULL, 'Exchange Communication for purchase', NULL, 'Statics', '60', '0', 'https://www.facebook.com/share/p/18P7uAGo58/', NULL, NULL, NULL, NULL, 'exchange communication', NULL, 'done', '2026-09-14 07:08:42', '2026-09-14 07:08:50'),
(607, 1, 'digital_team', '2026-09-12', NULL, NULL, 'Hybrid Price communication', 'Reels', NULL, '60', '0', 'https://www.facebook.com/share/v/1bkmvtgGbT/', NULL, NULL, NULL, NULL, 'Hybrid Price communication', 'FZS FI Hybrid', 'not_done', '2026-09-14 07:11:11', '2026-09-14 07:11:11'),
(608, 1, 'digital_team', '2026-09-13', NULL, 'Awareness', 'No.1 Market Leader', 'Special Content', NULL, '60', '0', 'https://www.facebook.com/share/p/1HWuunX9S1/', NULL, NULL, NULL, NULL, 'No.1 Market Leader', NULL, 'done', '2026-09-14 07:14:56', '2026-09-14 07:15:05'),
(609, 1, 'digital_team', '2026-09-18', NULL, 'Experience', 'Customer Review hybrid', NULL, NULL, '0', '0', NULL, NULL, NULL, NULL, NULL, 'Customer Review', 'FZS FI Hybrid', 'not_done', '2026-09-14 07:17:03', '2026-09-14 07:17:03'),
(610, 1, 'digital_team', '2026-09-22', NULL, 'Experience', 'Hybrid Customer review', 'Reels', NULL, '0', '0', NULL, NULL, NULL, NULL, NULL, 'Hybrid Customer review', 'FZS FI Hybrid', 'not_done', '2026-09-14 07:18:52', '2026-09-14 07:18:52'),
(611, 1, 'product_team', '2026-09-19', 'Rev Your Pulse', NULL, 'Rev your pulse', NULL, '\"shadin motors barishal 19sep Bike express jamalpur 19 sep Arish Motors noagaon 19 sep Royal motors Rajshahi 19 sep\"', '0', '0', NULL, NULL, NULL, NULL, 'FZS V2', NULL, NULL, 'not_done', '2026-09-14 08:26:28', '2026-09-14 08:26:28'),
(612, 1, 'product_team', '2026-09-26', 'Rev Your Pulse', NULL, 'Rev Your Pulse', NULL, 'Dipu Enterprise Chuadanga 26 sep\"', '0', '0', NULL, NULL, NULL, NULL, 'FZS V2', NULL, NULL, 'not_done', '2026-09-14 08:27:17', '2026-09-14 08:27:17'),
(613, 1, 'product_team', '2026-09-18', 'Hybrid G2G', NULL, 'Hybrid G2G', NULL, 'Rangpur, Cox\'s Bazar and Sylhet', '0', '0', NULL, NULL, NULL, NULL, 'FZS FI Hybrid', NULL, NULL, 'not_done', '2026-09-14 08:31:01', '2026-09-14 08:31:01');

-- --------------------------------------------------------

--
-- Table structure for table `content_plan_logics`
--

CREATE TABLE `content_plan_logics` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `period` varchar(255) NOT NULL,
  `row_type` varchar(255) NOT NULL DEFAULT 'allocation',
  `product` varchar(255) DEFAULT NULL,
  `units` varchar(255) DEFAULT NULL,
  `share` varchar(255) DEFAULT NULL,
  `share_shift` varchar(255) DEFAULT NULL,
  `previous_retail` varchar(255) DEFAULT NULL,
  `forecast` varchar(255) DEFAULT NULL,
  `posts_planned` int(10) UNSIGNED DEFAULT NULL,
  `pillar_split` varchar(255) DEFAULT NULL,
  `rationale` text DEFAULT NULL,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `content_plan_logics`
--

INSERT INTO `content_plan_logics` (`id`, `period`, `row_type`, `product`, `units`, `share`, `share_shift`, `previous_retail`, `forecast`, `posts_planned`, `pillar_split`, `rationale`, `sort_order`, `created_at`, `updated_at`) VALUES
(40, 'September 2026', 'allocation', 'FZS V2 (DD)', '3,950', '50.8%', '+4.8 pts YoY', '2739', '~4,210', 4, '1 Purchase / 3 Experience', 'Volume engine, still gaining share. No awareness/interest needed — content is pure conversion + proof.', 0, '2026-09-02 03:32:37', '2026-09-02 03:32:37'),
(41, 'September 2026', 'allocation', 'FZS V4', '1,968', '25.3%', '-4.2 pts YoY', '1311', '~2,210', 3, '1 Purchase / 2  Experience', 'Still top-two by volume but losing share to V2 and Hybrid. Keeps a light interest thread to defend its TCS/ABS differentiation.', 1, '2026-09-02 03:32:37', '2026-09-02 03:32:37'),
(42, 'September 2026', 'allocation', 'FZS Hybrid', '761', '9.8%', '+9.8 pts YoY (new)', '683', '~800-950', 5, '2 Interest/Awareness / 1 Purchase / 2 Experience', 'Fastest-growing model in the lineup; +17% MoM Jun->Jul momentum. Heaviest Interest weight this month to clear the technology-comprehension barrier (no external charging) before the September purchase peak.', 2, '2026-09-02 03:32:37', '2026-09-02 03:32:37'),
(43, 'September 2026', 'allocation', 'FZ25', '73', '0.9%', '-0.7 pts YoY (units -34%)', '58', '~100', 3, '1 Awareness / 1 Interest / 1 Purchase', 'Steepest decline in the lineup outside Fazer. Given the brief\'s aggressive/speed positioning, the funnel gap is above Interest, so Awareness leads this month\'s arc.', 3, '2026-09-02 03:32:37', '2026-09-02 03:32:37'),
(44, 'September 2026', 'allocation', 'Fazer', '229', '2.9%', '-4.5 pts YoY (units -54%)', '', '~235', 1, '1 Experience', 'Steepest YoY decline of any model. Deliberately no Purchase post this month — rebuilding consideration comes first.', 4, '2026-09-02 03:32:37', '2026-09-02 03:32:37'),
(45, 'September 2026', 'allocation', 'FZ-X', '272', '3.5%', '-0.9 pts YoY', '', '~300', 3, '1 Awareness-Interest / 1 Purchase / 1 Experience', 'Softer decline than Fazer, so it can carry one purchase post alongside a lifestyle/identity post.', 5, '2026-09-02 03:32:37', '2026-09-02 03:32:37'),
(46, 'September 2026', 'allocation', 'MT15', '81', '1.0%', '+0.1 pts YoY (units +37%)', '', '~80', 2, '1 Interest / 1 Purchase', 'Genuine YoY grower on a lifestyle-led approach — one UGC-style post keeps the format going without over-investing budget.', 6, '2026-09-02 03:32:37', '2026-09-02 03:32:37'),
(47, 'September 2026', 'allocation', 'R15 Series', '130', '1.7%', '-0.2 pts YoY (flat)', '', '~170', 2, '1 Interest / 1 Experience', 'Flat despite continued lifestyle investment — this month tests a heavier purchase push against the usual lifestyle content (see Nov 28 for the lifestyle counterpart to compare).', 7, '2026-09-02 03:32:37', '2026-09-02 03:32:37'),
(48, 'September 2026', 'note', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Methodology & data notes', 0, '2026-09-02 03:32:37', '2026-09-02 03:32:37'),
(49, 'September 2026', 'note', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '•  September is forecast as the seasonal purchase peak for nearly every model (see Section 4 of the market trend report) — every core model historically dips in August and peaks in September. Content this month is deliberately purchase-heavy relative to August\'s funnel-building posture.', 1, '2026-09-02 03:32:37', '2026-09-02 03:32:37'),
(50, 'September 2026', 'note', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '•  Total market forecast for September 2026: ~8,400 units, essentially flat vs. September 2025\'s actual 8,355 — this is a share-capture month, not a category-growth month. Every unit gained comes from a competitor.', 2, '2026-09-02 03:32:37', '2026-09-02 03:32:37'),
(51, 'September 2026', 'note', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '•  The brief\'s instruction to \'push slower products\' in September is reflected in FZ25 and Fazer each getting an Awareness-led arc rather than jumping straight to offers, since their declines are steep enough that a purchase post alone would have little to convert against.', 3, '2026-09-02 03:32:37', '2026-09-02 03:32:37'),
(52, 'September 2026', 'source', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Source: Product-wise retail sell-out data, Yamaha Bangladesh Retail Sales Reports (Aug\'25, Sep\'25, Oct\'25, May\'26, Jun\'26, Jul\'26), aggregated in the Market Trend Analysis & Q3 FY26 Content Strategy report.', 4, '2026-09-02 03:32:37', '2026-09-02 03:32:37');

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
-- Table structure for table `master_data`
--

CREATE TABLE `master_data` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `master_data`
--

INSERT INTO `master_data` (`id`, `category`, `value`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'platform', 'Facebook', 1, '2026-08-24 23:33:44', '2026-08-24 23:33:44'),
(2, 'platform', 'Instagram', 1, '2026-08-24 23:33:44', '2026-08-24 23:33:44'),
(3, 'platform', 'Tiktok', 1, '2026-08-24 23:33:44', '2026-08-24 23:33:44'),
(4, 'platform', 'LinkedIn', 1, '2026-08-24 23:33:44', '2026-08-24 23:33:44'),
(5, 'platform', 'Youtube', 1, '2026-08-24 23:33:44', '2026-08-24 23:33:44'),
(6, 'platform', 'YRC Page', 1, '2026-08-24 23:33:44', '2026-08-24 23:33:44'),
(7, 'platform', 'Yamaha Lovers BD', 1, '2026-08-24 23:33:44', '2026-08-24 23:33:44'),
(8, 'format', 'Product Review', 1, '2026-08-24 23:33:44', '2026-08-24 23:33:44'),
(9, 'format', 'OVC', 1, '2026-08-24 23:33:44', '2026-08-24 23:33:44'),
(10, 'format', 'Special Content', 1, '2026-08-24 23:33:44', '2026-08-24 23:33:44'),
(11, 'format', 'Get Together', 1, '2026-08-24 23:33:44', '2026-08-24 23:33:44'),
(12, 'format', 'Reels', 1, '2026-08-24 23:33:44', '2026-08-24 23:33:44'),
(13, 'aipe_pillar', 'Awareness', 1, '2026-08-24 23:33:44', '2026-08-24 23:33:44'),
(14, 'aipe_pillar', 'Awareness+Interest', 1, '2026-08-24 23:33:44', '2026-08-24 23:33:44'),
(15, 'aipe_pillar', 'Interest', 1, '2026-08-24 23:33:44', '2026-08-24 23:33:44'),
(16, 'aipe_pillar', 'Interest+Experience', 1, '2026-08-24 23:33:44', '2026-08-24 23:33:44'),
(17, 'aipe_pillar', 'Experience', 1, '2026-08-24 23:33:44', '2026-08-24 23:33:44'),
(18, 'product', 'FZS V2', 1, '2026-08-24 23:33:44', '2026-08-24 23:33:44'),
(19, 'product', 'FZS V4', 1, '2026-08-24 23:33:44', '2026-08-24 23:33:44'),
(20, 'product', 'FZS FI Hybrid', 1, '2026-08-24 23:33:44', '2026-08-24 23:33:44'),
(21, 'product', 'FZX', 1, '2026-08-24 23:33:44', '2026-08-24 23:33:44'),
(22, 'product', 'Fazer', 1, '2026-08-24 23:33:44', '2026-08-24 23:33:44'),
(23, 'product', 'FZ 25', 1, '2026-08-24 23:33:44', '2026-08-24 23:33:44'),
(24, 'product', 'MT', 1, '2026-08-24 23:33:44', '2026-08-24 23:33:44'),
(25, 'product', 'R15', 1, '2026-08-24 23:33:44', '2026-08-24 23:33:44');

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
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_08_23_122710_create_calendar_events_table', 1),
(5, '2026_08_25_052150_create_master_data_table', 2),
(6, '2026_08_25_052217_create_activity_logs_table', 2),
(7, '2026_09_02_091622_create_content_plan_logics_table', 3),
(8, '2026_09_02_091623_add_staff_fields_to_users_table', 3);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
('cOdMyIPRHHzwvIhoQ2ShPf2F4ZEoHs4lKwtzjrrY', NULL, '65.109.236.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZDVNcDI1WDIzUEtRRUlJN0hYTzNLZHl3Slc0Qng5UFZNeXFSQ3NRTyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjM6Imh0dHBzOi8vcGxhbi55cmMtYmQuY29tIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1789441603),
('gLiUk2ze6JpjOTppzUGwVeuueBIyOvJkQmX92ZWk', NULL, '116.68.205.66', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWHRTdWxFQk1ianU1MkZLTXNwSVpjRDZ5MGU4Tm5LcHprS3h6SWhxMCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDI6Imh0dHBzOi8vcGxhbi55cmMtYmQuY29tL2Nyb24vZXZlbnRzLW5vdGlmeSI7czo1OiJyb3V0ZSI7czoxODoiY3Jvbi5ldmVudHMubm90aWZ5Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1789456139),
('IUolIUglIDaYl66lOnleWxmIVBVN3sklckSCCvs8', NULL, '116.68.205.66', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoib2UxRHowMWdpZGdwRHZWREZRQVZnVnV6anUzQldpa3pSeGRPYVcwdCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjM6Imh0dHBzOi8vcGxhbi55cmMtYmQuY29tIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1789456139),
('MjlF2gosEcdynO83ZMyMYFzCjSbX8OA94WF7AiF9', NULL, '119.15.154.84', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.6 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMkM3RXdadDN4d3FodmpGd0g4Q0ZQM016eURvaEVqN2JqaDZPQW5rMCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjM6Imh0dHBzOi8vcGxhbi55cmMtYmQuY29tIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1789407844),
('NKdnyTnBwb4bujz47EU7PIxReLcyCsIKh3A7ekXs', NULL, '119.15.154.84', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.6 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMUJTVWl6SVNiUFpkWnF4UVVjNmhHNHM4T2dDNTJYMGdmYkxHN1pPRyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjM6Imh0dHBzOi8vcGxhbi55cmMtYmQuY29tIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1789407847),
('nTMcwqsy8neeXmDIYLRXJWNIp9yxMbRJv3RICc0H', NULL, '34.21.224.227', 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMHRSY1pqYkh6ZVY0MHVnWGV3dllzeWFIYlBRZDNzaThPQ3J5TFNDbyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjM6Imh0dHBzOi8vcGxhbi55cmMtYmQuY29tIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1789401574),
('Phg8EWJ4rMZRvunrTskP6yZXjXVBZv1gLet2BP2N', NULL, '34.35.51.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVnlaRW5sQmZNRTgyYlBEdGtDNXdLQzVQSktGYlA3ZTNVMXJhc29mciI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjM6Imh0dHBzOi8vcGxhbi55cmMtYmQuY29tIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1789432569),
('qTeEGWWwT5eozsZFrvsssbt4KkBKp7AMeyrXBb7G', NULL, '34.35.51.138', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZVh0bTJ5QXZZSWE1REdhS0RDQWJhb0w0UzdZRWdCRkIxMGpkRVA1biI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjM6Imh0dHBzOi8vcGxhbi55cmMtYmQuY29tIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1789432695),
('ZYheEw6rWrB0SyshLNokVzbwV1aavljSmMrGP7Df', NULL, '85.204.70.104', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRzNrVTk4UkJFcHhtalowQkpXbURXYUNMMmJ2U0NwY000dFlRUktSQiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjI6Imh0dHA6Ly9wbGFuLnlyYy1iZC5jb20iO3M6NToicm91dGUiO047fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1789428250);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`key`, `value`, `created_at`, `updated_at`) VALUES
('MAIL_CC_ADDRESS', 'mirajul@aci-bd.com,richard@aci-bd.com,adhikary@aci-bd.com,efaz@aci-bd.com,Sourav.Bikash@aci-bd.com,acijubairislamdaief@gmail.com,Swagata@aci-bd.com,arnob@aci-bd.com,Nabil.Sarker@aci-bd.com,Abu.siddik@aci-bd.com,priasa@aci-bd.com,azmyen@aci-bd.com,Ashif.Ahmed@aci-bd.com,oshin@aci-bd.com,zahidul@aci-bd.com', '2026-09-14 00:43:18', '2026-09-14 06:08:09'),
('MAIL_FROM_ADDRESS', 'planning@yrc-bd.com', '2026-09-14 00:43:18', '2026-09-14 03:23:17'),
('MAIL_HOST', 'sandbox.smtp.mailtrap.io', '2026-09-14 00:43:18', '2026-09-14 06:15:12'),
('MAIL_MAILER', 'smtp', '2026-09-14 00:43:18', '2026-09-14 00:43:18'),
('MAIL_PASSWORD', '80bf0d8911a570', '2026-09-14 00:43:18', '2026-09-14 06:15:12'),
('MAIL_PORT', '2525', '2026-09-14 00:43:18', '2026-09-14 06:15:12'),
('MAIL_USERNAME', 'fecdbec6edde94', '2026-09-14 00:43:18', '2026-09-14 06:15:12');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `staff_id` varchar(255) DEFAULT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'digital_team',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `staff_id`, `designation`, `role`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'admin@test.com', NULL, NULL, 'super_admin', '2026-08-23 07:10:52', '$2y$12$7ATC19cFf.NLX2/v7B0SBOU8MlFXFIxlZ1yEWIc1Yg8SEJoUdQ8KG', '1cjLHnZIK6NXOo1RtgqUBf9RqWYgT29QbL2kn5AIkbfnIgPsnESSD5hMvOxZ', '2026-08-23 07:10:53', '2026-09-02 04:08:03'),
(2, 'Product Team User', 'product@test.com', NULL, NULL, 'product_team', '2026-08-23 07:10:53', '$2y$12$7ATC19cFf.NLX2/v7B0SBOU8MlFXFIxlZ1yEWIc1Yg8SEJoUdQ8KG', 'RWQzDF5yiolKr35IuFzCAC5OdOxnEY3S5zBBAfrusHKveLRP5eCrRyLfcgl6', '2026-08-23 07:10:53', '2026-09-02 04:08:03'),
(3, 'Digital Team User', 'digital@test.com', NULL, NULL, 'digital_team', '2026-08-23 07:10:54', '$2y$12$7ATC19cFf.NLX2/v7B0SBOU8MlFXFIxlZ1yEWIc1Yg8SEJoUdQ8KG', 'kdnIOTB6U62k3QHojbWvBb8c6L1POUmqgNuA9ACUzzF5bLNN4zHQu5s7vyfL', '2026-08-23 07:10:54', '2026-09-02 04:08:03'),
(94, 'Hossain Mohammad Option', 'option@aci-bd.com', '11465', 'BM,Yamaha', 'product_team', '2026-09-02 03:36:50', '$2y$12$7ATC19cFf.NLX2/v7B0SBOU8MlFXFIxlZ1yEWIc1Yg8SEJoUdQ8KG', NULL, '2026-09-02 03:32:38', '2026-09-02 04:08:03'),
(95, 'Mirajul Alam', 'mirajul@aci-bd.com', '18415', 'DGM', 'product_team', '2026-09-02 03:36:50', '$2y$12$7ATC19cFf.NLX2/v7B0SBOU8MlFXFIxlZ1yEWIc1Yg8SEJoUdQ8KG', NULL, '2026-09-02 03:32:39', '2026-09-02 04:08:03'),
(96, 'Md. Zahidul Islam', 'zahidul@aci-bd.com', '14814', 'AGM', 'product_team', '2026-09-02 03:36:50', '$2y$12$7ATC19cFf.NLX2/v7B0SBOU8MlFXFIxlZ1yEWIc1Yg8SEJoUdQ8KG', NULL, '2026-09-02 03:32:40', '2026-09-02 04:08:03'),
(97, 'Rizwan UZ Zaman', 'rizwan@aci-bd.com', '23689', 'Manager', 'product_team', '2026-09-02 03:36:50', '$2y$12$7ATC19cFf.NLX2/v7B0SBOU8MlFXFIxlZ1yEWIc1Yg8SEJoUdQ8KG', NULL, '2026-09-02 03:32:41', '2026-09-02 04:08:03'),
(98, 'Nabanita Islam', 'nabanita@aci-bd.com', '29842', 'APM,Yamaha', 'product_team', '2026-09-02 03:36:50', '$2y$12$7ATC19cFf.NLX2/v7B0SBOU8MlFXFIxlZ1yEWIc1Yg8SEJoUdQ8KG', NULL, '2026-09-02 03:32:41', '2026-09-02 04:08:03'),
(99, 'Richard Chakma', 'richard@aci-bd.com', '24082', 'APM,Yamaha', 'product_team', '2026-09-02 03:36:50', '$2y$12$7ATC19cFf.NLX2/v7B0SBOU8MlFXFIxlZ1yEWIc1Yg8SEJoUdQ8KG', NULL, '2026-09-02 03:32:42', '2026-09-02 04:08:03'),
(100, 'Sudipta Adhikary', 'adhikary@aci-bdcom', '24784', 'APM,Yamaha', 'product_team', '2026-09-02 03:36:50', '$2y$12$7ATC19cFf.NLX2/v7B0SBOU8MlFXFIxlZ1yEWIc1Yg8SEJoUdQ8KG', NULL, '2026-09-02 03:32:43', '2026-09-02 04:08:03'),
(101, 'Kamrul Islam Anik', 'kamrul.anik@aci-bd.com', '32320', 'AM', 'product_team', '2026-09-02 03:36:50', '$2y$12$7ATC19cFf.NLX2/v7B0SBOU8MlFXFIxlZ1yEWIc1Yg8SEJoUdQ8KG', NULL, '2026-09-02 03:32:44', '2026-09-02 04:08:03'),
(102, 'Amit Sarker', 'amitsarker@aci-bd.com', '27614', 'Sr.Executive', 'product_team', '2026-09-02 03:36:50', '$2y$12$7ATC19cFf.NLX2/v7B0SBOU8MlFXFIxlZ1yEWIc1Yg8SEJoUdQ8KG', NULL, '2026-09-02 03:32:45', '2026-09-02 04:08:03'),
(103, 'Efaz Ahmed', 'efaz@aci-bd.com', '42993', 'Sr.Executive', 'product_team', '2026-09-02 03:36:50', '$2y$12$7ATC19cFf.NLX2/v7B0SBOU8MlFXFIxlZ1yEWIc1Yg8SEJoUdQ8KG', NULL, '2026-09-02 03:32:45', '2026-09-02 04:08:03'),
(104, 'Marzia Tabassum Fariha', 'tabassum@aci-bd.com', '17618', 'Sr.Executive', 'product_team', '2026-09-02 03:36:50', '$2y$12$7ATC19cFf.NLX2/v7B0SBOU8MlFXFIxlZ1yEWIc1Yg8SEJoUdQ8KG', NULL, '2026-09-02 03:32:46', '2026-09-02 04:08:03'),
(105, 'Nazia Bintay Ashraf', 'nazia.ashraf@aci-bd.com', '32182', 'Sr. Ex-DM', 'product_team', '2026-09-02 03:36:50', '$2y$12$7ATC19cFf.NLX2/v7B0SBOU8MlFXFIxlZ1yEWIc1Yg8SEJoUdQ8KG', NULL, '2026-09-02 03:32:47', '2026-09-02 04:08:03'),
(106, 'Sourav Bikash Mojumder', 'sourav.bikash@aci-bd.com', '38629', 'Sr.PE', 'product_team', '2026-09-02 03:36:50', '$2y$12$7ATC19cFf.NLX2/v7B0SBOU8MlFXFIxlZ1yEWIc1Yg8SEJoUdQ8KG', NULL, '2026-09-02 03:32:48', '2026-09-02 04:08:03'),
(107, 'Kazi Rifat Mahmud', 'rifat.mahmud@aci-bd.com', '34790', 'Sr.BE', 'product_team', '2026-09-02 03:36:50', '$2y$12$7ATC19cFf.NLX2/v7B0SBOU8MlFXFIxlZ1yEWIc1Yg8SEJoUdQ8KG', NULL, '2026-09-02 03:32:49', '2026-09-02 04:08:03'),
(108, 'Md. Robin', 'robin@aci-bd.com', '32310', 'GD', 'product_team', '2026-09-02 03:36:50', '$2y$12$7ATC19cFf.NLX2/v7B0SBOU8MlFXFIxlZ1yEWIc1Yg8SEJoUdQ8KG', NULL, '2026-09-02 03:32:49', '2026-09-02 04:08:03'),
(109, 'AlMamun', 'mamun.gd@aci-bd.com', '37586', 'GD', 'product_team', '2026-09-02 03:36:50', '$2y$12$7ATC19cFf.NLX2/v7B0SBOU8MlFXFIxlZ1yEWIc1Yg8SEJoUdQ8KG', NULL, '2026-09-02 03:32:50', '2026-09-02 04:08:03'),
(110, 'Mir Sabbir Ahammed Chowdhury', 'sabbir.ahammed@aci-bd.com', '38352', 'PE', 'product_team', '2026-09-02 03:36:50', '$2y$12$7ATC19cFf.NLX2/v7B0SBOU8MlFXFIxlZ1yEWIc1Yg8SEJoUdQ8KG', NULL, '2026-09-02 03:32:51', '2026-09-02 04:08:03'),
(111, 'Mahabubur Rahman', 'motor.mahbub@aci-bd.com', '39048', 'Aactivity Executive', 'product_team', '2026-09-02 03:36:50', '$2y$12$7ATC19cFf.NLX2/v7B0SBOU8MlFXFIxlZ1yEWIc1Yg8SEJoUdQ8KG', NULL, '2026-09-02 03:32:52', '2026-09-02 04:08:03'),
(112, 'Nasrin Sultana Nishi', 'sultana.nishi@aci-bd.com', '40664', 'PE', 'product_team', '2026-09-02 03:36:50', '$2y$12$7ATC19cFf.NLX2/v7B0SBOU8MlFXFIxlZ1yEWIc1Yg8SEJoUdQ8KG', NULL, '2026-09-02 03:32:52', '2026-09-02 04:08:03'),
(113, 'Swagata Kumar Saha', 'swagata@aci-bd.com', '42994', 'PE', 'product_team', '2026-09-02 03:36:50', '$2y$12$7ATC19cFf.NLX2/v7B0SBOU8MlFXFIxlZ1yEWIc1Yg8SEJoUdQ8KG', NULL, '2026-09-02 03:32:53', '2026-09-02 04:08:03'),
(114, 'Samiun Nabi', 'samiun@aci-bd.com', '40663', 'Executive', 'product_team', '2026-09-02 03:36:50', '$2y$12$7ATC19cFf.NLX2/v7B0SBOU8MlFXFIxlZ1yEWIc1Yg8SEJoUdQ8KG', NULL, '2026-09-02 03:32:54', '2026-09-02 04:08:03'),
(115, 'Istiak Md. Anam Arnob', 'arnob@aci-bd.com', '41150', 'Executive-Dm', 'product_team', '2026-09-02 03:36:50', '$2y$12$7ATC19cFf.NLX2/v7B0SBOU8MlFXFIxlZ1yEWIc1Yg8SEJoUdQ8KG', NULL, '2026-09-02 03:32:54', '2026-09-02 04:08:03'),
(116, 'SM Abu Nasim Shakil', 'nasim.shakil@aci-bd.com', '44040', 'Executive', 'product_team', '2026-09-02 03:36:50', '$2y$12$7ATC19cFf.NLX2/v7B0SBOU8MlFXFIxlZ1yEWIc1Yg8SEJoUdQ8KG', NULL, '2026-09-02 03:32:54', '2026-09-02 04:08:03'),
(117, 'Ruckmana Zaman', 'ruckmana@aci-bd.com', '37587', 'Executiver- QM', 'product_team', '2026-09-02 03:36:50', '$2y$12$7ATC19cFf.NLX2/v7B0SBOU8MlFXFIxlZ1yEWIc1Yg8SEJoUdQ8KG', NULL, '2026-09-02 03:32:55', '2026-09-02 04:08:03'),
(118, 'Md. Nabil Uddin Sarker', 'nabil.sarker@aci-bd.com', '41351', 'Executive-DM', 'product_team', '2026-09-02 03:36:50', '$2y$12$7ATC19cFf.NLX2/v7B0SBOU8MlFXFIxlZ1yEWIc1Yg8SEJoUdQ8KG', NULL, '2026-09-02 03:32:55', '2026-09-02 04:08:03'),
(119, 'MD Abu Bakkar Siddik', 'abu.siddik@aci-bd.com', '46090', 'PE', 'product_team', '2026-09-02 03:36:50', '$2y$12$7ATC19cFf.NLX2/v7B0SBOU8MlFXFIxlZ1yEWIc1Yg8SEJoUdQ8KG', NULL, '2026-09-02 03:32:55', '2026-09-02 04:08:03'),
(120, 'Mithila Priasa', 'priasa@aci-bd.com', '44610', 'PE', 'product_team', '2026-09-02 03:36:50', '$2y$12$7ATC19cFf.NLX2/v7B0SBOU8MlFXFIxlZ1yEWIc1Yg8SEJoUdQ8KG', NULL, '2026-09-02 03:32:56', '2026-09-02 04:08:03'),
(121, 'Azmyen Mustafa Chowdhury', 'azmyen@aci-bd.com', '44249', 'PE', 'product_team', '2026-09-02 03:36:50', '$2y$12$7ATC19cFf.NLX2/v7B0SBOU8MlFXFIxlZ1yEWIc1Yg8SEJoUdQ8KG', NULL, '2026-09-02 03:32:56', '2026-09-02 04:08:03'),
(122, 'Mirza Arafin', 'arafin@aci-bd.com', '43325', 'GM', 'product_team', '2026-09-02 03:36:50', '$2y$12$7ATC19cFf.NLX2/v7B0SBOU8MlFXFIxlZ1yEWIc1Yg8SEJoUdQ8KG', NULL, '2026-09-02 03:32:56', '2026-09-02 04:08:03'),
(123, 'Ashif Ahmed (Niloy)', 'ashif.ahmed@aci-bd.com', '46219', 'Deputy Manager,', 'product_team', '2026-09-02 03:36:50', '$2y$12$7ATC19cFf.NLX2/v7B0SBOU8MlFXFIxlZ1yEWIc1Yg8SEJoUdQ8KG', NULL, '2026-09-02 03:32:56', '2026-09-02 04:08:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_logs_user_id_foreign` (`user_id`);

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
-- Indexes for table `calendar_events`
--
ALTER TABLE `calendar_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `calendar_events_user_id_foreign` (`user_id`);

--
-- Indexes for table `content_plan_logics`
--
ALTER TABLE `content_plan_logics`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `content_plan_logics_period_row_type_sort_order_unique` (`period`,`row_type`,`sort_order`),
  ADD KEY `content_plan_logics_period_index` (`period`);

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
-- Indexes for table `master_data`
--
ALTER TABLE `master_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD UNIQUE KEY `settings_key_unique` (`key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_staff_id_unique` (`staff_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `calendar_events`
--
ALTER TABLE `calendar_events`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=614;

--
-- AUTO_INCREMENT for table `content_plan_logics`
--
ALTER TABLE `content_plan_logics`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

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
-- AUTO_INCREMENT for table `master_data`
--
ALTER TABLE `master_data`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=124;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `calendar_events`
--
ALTER TABLE `calendar_events`
  ADD CONSTRAINT `calendar_events_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
