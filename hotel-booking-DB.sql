-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 24, 2026 at 12:31 PM
-- Server version: 8.0.45-0ubuntu0.24.04.1
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hotel-booking`
--

-- --------------------------------------------------------

--
-- Table structure for table `amenities`
--

CREATE TABLE `amenities` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amenityable_id` bigint UNSIGNED NOT NULL,
  `amenityable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `amenities`
--

INSERT INTO `amenities` (`id`, `title`, `icon`, `amenityable_id`, `amenityable_type`, `created_at`, `updated_at`) VALUES
(1, 'Free Wi-Fi', 'bi-wifi', 11, 'App\\Models\\Hotel', '2026-04-23 05:23:15', '2026-04-23 05:23:15'),
(2, 'Air Conditioning', 'bi-snow', 11, 'App\\Models\\Hotel', '2026-04-23 05:23:15', '2026-04-23 05:23:15'),
(3, 'Swimming Pool', 'bi-water', 11, 'App\\Models\\Hotel', '2026-04-23 05:23:15', '2026-04-23 05:23:15'),
(4, 'Bar / Lounge', 'bi-glass-cocktail', 11, 'App\\Models\\Hotel', '2026-04-23 05:23:15', '2026-04-23 05:23:15'),
(5, 'Free Parking', 'bi-p-circle', 11, 'App\\Models\\Hotel', '2026-04-23 05:23:15', '2026-04-23 05:23:15'),
(6, '24/7 Front Desk', 'bi-person-badge', 11, 'App\\Models\\Hotel', '2026-04-23 05:23:15', '2026-04-23 05:23:15'),
(7, 'Laundry Service', 'bi-tsunami', 11, 'App\\Models\\Hotel', '2026-04-23 05:23:15', '2026-04-23 05:23:15'),
(8, 'Free Wi-Fi', 'bi-wifi', 1, 'App\\Models\\Hotel', '2026-04-23 05:24:19', '2026-04-23 05:24:19'),
(9, 'Air Conditioning', 'bi-snow', 1, 'App\\Models\\Hotel', '2026-04-23 05:24:19', '2026-04-23 05:24:19'),
(10, 'Restaurant', 'bi-cup-hot', 1, 'App\\Models\\Hotel', '2026-04-23 05:24:19', '2026-04-23 05:24:19'),
(11, 'Room Service', 'bi-bell', 1, 'App\\Models\\Hotel', '2026-04-23 05:24:20', '2026-04-23 05:24:20'),
(12, 'Laundry Service', 'bi-tsunami', 1, 'App\\Models\\Hotel', '2026-04-23 05:24:20', '2026-04-23 05:24:20'),
(13, 'Conference Room', 'bi-people', 1, 'App\\Models\\Hotel', '2026-04-23 05:24:20', '2026-04-23 05:24:20'),
(14, 'Swimming Pool', 'bi-water', 2, 'App\\Models\\Hotel', '2026-04-23 05:24:42', '2026-04-23 05:24:42'),
(15, 'Fitness Center', 'bi-bicycle', 2, 'App\\Models\\Hotel', '2026-04-23 05:24:42', '2026-04-23 05:24:42'),
(16, 'Free Parking', 'bi-p-circle', 2, 'App\\Models\\Hotel', '2026-04-23 05:24:42', '2026-04-23 05:24:42'),
(17, '24/7 Front Desk', 'bi-person-badge', 2, 'App\\Models\\Hotel', '2026-04-23 05:24:42', '2026-04-23 05:24:42'),
(18, 'Airport Shuttle', 'bi-bus-front', 2, 'App\\Models\\Hotel', '2026-04-23 05:24:42', '2026-04-23 05:24:42'),
(19, 'Free Wi-Fi', 'bi-wifi', 3, 'App\\Models\\Hotel', '2026-04-23 05:24:49', '2026-04-23 05:24:49'),
(20, 'Spa & Wellness', 'bi-magic', 3, 'App\\Models\\Hotel', '2026-04-23 05:24:49', '2026-04-23 05:24:49'),
(21, 'Free Parking', 'bi-p-circle', 3, 'App\\Models\\Hotel', '2026-04-23 05:24:49', '2026-04-23 05:24:49'),
(22, '24/7 Front Desk', 'bi-person-badge', 3, 'App\\Models\\Hotel', '2026-04-23 05:24:49', '2026-04-23 05:24:49'),
(23, 'Free Wi-Fi', 'bi-wifi', 4, 'App\\Models\\Hotel', '2026-04-23 05:24:56', '2026-04-23 05:24:56'),
(24, 'Restaurant', 'bi-cup-hot', 4, 'App\\Models\\Hotel', '2026-04-23 05:24:56', '2026-04-23 05:24:56'),
(25, 'Bar / Lounge', 'bi-glass-cocktail', 4, 'App\\Models\\Hotel', '2026-04-23 05:24:56', '2026-04-23 05:24:56'),
(26, 'Pet Friendly', 'bi-heart', 4, 'App\\Models\\Hotel', '2026-04-23 05:24:56', '2026-04-23 05:24:56');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `hotel_id` bigint UNSIGNED NOT NULL,
  `status` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `reference_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_id` bigint UNSIGNED DEFAULT NULL,
  `discount_amount` decimal(12,2) NOT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `instructions` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sub_amount` decimal(12,2) NOT NULL,
  `currency` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `guest_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guest_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guest_phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `arrival` datetime DEFAULT NULL,
  `leaved` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `hotel_id`, `status`, `reference_number`, `discount_id`, `discount_amount`, `total_amount`, `created_at`, `updated_at`, `instructions`, `sub_amount`, `currency`, `guest_name`, `guest_email`, `guest_phone`, `arrival`, `leaved`) VALUES
(1, 1, 1, 1, '1776847410LW730001', NULL, 0.00, 8598.00, '2026-04-22 08:43:30', '2026-04-22 08:43:31', NULL, 8598.00, 'INR', 'Ibrahim Garana', 'ibrahim.g@texhxperts.co.in', '9725847556', NULL, NULL),
(2, 1, 10, 1, '1776929182ZADO0001', 1, 4.00, 36.00, '2026-04-23 07:26:22', '2026-04-23 10:53:14', NULL, 40.00, 'SAR', 'Ibrahim Garana', 'ibrahim@gmail.com', '9725847556', '2026-04-23 16:23:14', NULL),
(3, 10, 7, 5, '1776942017AKQP0010', 1, 4.50, 40.50, '2026-04-23 11:00:17', '2026-04-23 11:02:14', NULL, 45.00, 'EUR', 'Neoma Little', 'ashlee57@example.com', '958546542', NULL, NULL),
(8, 10, 2, 2, '1776947372QLCG0010', 1, 580.00, 5220.00, '2026-04-23 12:29:32', '2026-04-24 12:29:33', NULL, 5800.00, 'INR', 'Neoma Little', 'ashlee57@example.com', '987586589', NULL, NULL),
(14, 10, 8, 2, '1777007957VHFX0010', 1, 12.00, 108.00, '2026-04-24 05:19:17', '2026-04-24 05:49:17', NULL, 120.00, 'USD', 'Neoma Little', 'ashlee57@example.com', '985784589', NULL, NULL),
(15, 10, 11, 2, '1777029282WH4H0010', 8, 3040.00, 160.00, '2026-04-24 11:14:42', '2026-04-24 11:44:43', NULL, 3200.00, 'INR', 'Neoma Little', 'ashlee57@example.com', '32453235', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `booking_items`
--

CREATE TABLE `booking_items` (
  `id` bigint UNSIGNED NOT NULL,
  `booking_id` bigint UNSIGNED NOT NULL,
  `room_id` bigint UNSIGNED NOT NULL,
  `check_in` datetime NOT NULL,
  `check_out` datetime NOT NULL,
  `price_at_booking` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `booking_items`
--

INSERT INTO `booking_items` (`id`, `booking_id`, `room_id`, `check_in`, `check_out`, `price_at_booking`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2026-04-22 00:00:00', '2026-04-24 00:00:00', 4299.00, '2026-04-22 08:43:30', '2026-04-22 08:43:30'),
(2, 2, 221, '2026-04-23 00:00:00', '2026-04-24 00:00:00', 40.00, '2026-04-23 07:26:22', '2026-04-23 07:26:22'),
(3, 3, 181, '2026-04-24 00:00:00', '2026-04-25 00:00:00', 45.00, '2026-04-23 11:00:17', '2026-04-23 11:00:17'),
(8, 8, 121, '2026-04-23 00:00:00', '2026-04-24 00:00:00', 5800.00, '2026-04-23 12:29:32', '2026-04-23 12:29:32'),
(19, 14, 191, '2026-04-25 00:00:00', '2026-04-26 00:00:00', 60.00, '2026-04-24 05:19:17', '2026-04-24 05:19:17'),
(20, 14, 191, '2026-04-25 00:00:00', '2026-04-26 00:00:00', 60.00, '2026-04-24 05:19:17', '2026-04-24 05:19:17'),
(21, 15, 241, '2026-04-25 00:00:00', '2026-04-26 00:00:00', 3200.00, '2026-04-24 11:14:42', '2026-04-24 11:14:42');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id` bigint UNSIGNED NOT NULL,
  `state_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `state_id`, `name`) VALUES
(1, 7, 'Ahmedabad'),
(2, 7, 'Amreli'),
(3, 7, 'Anand'),
(4, 7, 'Aravalli'),
(5, 7, 'Banaskantha'),
(6, 7, 'Bharuch'),
(7, 7, 'Bhavnagar'),
(8, 7, 'Botad'),
(9, 7, 'Chhota Udaipur'),
(10, 7, 'Dahod'),
(11, 7, 'Dang'),
(12, 7, 'Devbhoomi Dwarka'),
(13, 7, 'Gandhinagar'),
(14, 7, 'Gir Somnath'),
(15, 7, 'Jamnagar'),
(16, 7, 'Junagadh'),
(17, 7, 'Kheda'),
(18, 7, 'Kutch'),
(19, 7, 'Mahisagar'),
(20, 7, 'Mehsana'),
(21, 7, 'Morbi'),
(22, 7, 'Narmada'),
(23, 7, 'Navsari'),
(24, 7, 'Panchmahal'),
(25, 7, 'Patan'),
(26, 7, 'Porbandar'),
(27, 7, 'Rajkot'),
(28, 7, 'Sabarkantha'),
(29, 7, 'Surat'),
(30, 7, 'Surendranagar'),
(31, 7, 'Tapi'),
(32, 7, 'Vadodara'),
(33, 7, 'Valsad'),
(34, 1, 'Visakhapatnam'),
(35, 1, 'Vijayawada'),
(36, 2, 'Itanagar'),
(37, 3, 'Guwahati'),
(38, 4, 'Patna'),
(39, 5, 'Raipur'),
(40, 6, 'Panaji'),
(41, 8, 'Gurgaon'),
(42, 8, 'Faridabad'),
(43, 9, 'Shimla'),
(44, 10, 'Ranchi'),
(45, 11, 'Bangalore'),
(46, 11, 'Mysore'),
(47, 12, 'Kochi'),
(48, 12, 'Thiruvananthapuram'),
(49, 13, 'Indore'),
(50, 13, 'Bhopal'),
(51, 14, 'Mumbai'),
(52, 14, 'Pune'),
(53, 14, 'Nagpur'),
(54, 15, 'Imphal'),
(55, 16, 'Shillong'),
(56, 17, 'Aizawl'),
(57, 18, 'Kohima'),
(58, 19, 'Bhubaneswar'),
(59, 20, 'Amritsar'),
(60, 20, 'Ludhiana'),
(61, 21, 'Jaipur'),
(62, 21, 'Udaipur'),
(63, 21, 'Jodhpur'),
(64, 22, 'Gangtok'),
(65, 23, 'Chennai'),
(66, 23, 'Coimbatore'),
(67, 23, 'Madurai'),
(68, 24, 'Hyderabad'),
(69, 25, 'Agartala'),
(70, 26, 'Lucknow'),
(71, 26, 'Varanasi'),
(72, 26, 'Agra'),
(73, 27, 'Dehradun'),
(74, 28, 'Kolkata'),
(75, 29, 'Port Blair'),
(76, 30, 'Chandigarh'),
(77, 31, 'Daman'),
(78, 32, 'New Delhi'),
(79, 33, 'Srinagar'),
(80, 33, 'Jammu'),
(81, 34, 'Leh'),
(82, 35, 'Kavaratti'),
(83, 36, 'Puducherry'),
(84, 197, 'Los Angeles'),
(85, 197, 'San Francisco'),
(86, 198, 'Houston'),
(87, 199, 'Miami'),
(88, 200, 'New York City'),
(89, 201, 'London'),
(90, 202, 'Edinburgh'),
(91, 203, 'Cardiff'),
(92, 204, 'Belfast'),
(93, 205, 'Toronto'),
(94, 206, 'Montreal'),
(95, 207, 'Vancouver'),
(96, 208, 'Calgary'),
(97, 209, 'Sydney'),
(98, 210, 'Melbourne'),
(99, 211, 'Brisbane'),
(100, 212, 'Perth'),
(101, 213, 'Munich'),
(102, 214, 'Berlin'),
(103, 215, 'Hamburg'),
(104, 216, 'Frankfurt'),
(105, 217, 'Paris'),
(106, 218, 'Marseille'),
(107, 219, 'Toulouse'),
(108, 220, 'Bordeaux'),
(109, 221, 'Guangzhou'),
(110, 222, 'Beijing'),
(111, 223, 'Shanghai'),
(112, 224, 'Chengdu'),
(113, 225, 'Tokyo'),
(114, 226, 'Osaka'),
(115, 227, 'Sapporo'),
(116, 228, 'Kyoto'),
(117, 229, 'São Paulo'),
(118, 230, 'Rio de Janeiro'),
(119, 231, 'Salvador'),
(120, 232, 'Belo Horizonte'),
(121, 233, 'Moscow'),
(122, 234, 'Saint Petersburg'),
(123, 235, 'Kazan'),
(124, 236, 'Novosibirsk'),
(125, 237, 'Guadalajara'),
(126, 238, 'Monterrey'),
(127, 239, 'Puebla City'),
(128, 240, 'Merida'),
(129, 241, 'Milan'),
(130, 242, 'Rome'),
(131, 243, 'Florence'),
(132, 244, 'Palermo'),
(133, 245, 'Barcelona'),
(134, 246, 'Madrid'),
(135, 247, 'Seville'),
(136, 248, 'Valencia'),
(137, 249, 'Istanbul'),
(138, 250, 'Ankara'),
(139, 251, 'Izmir'),
(140, 252, 'Antalya'),
(141, 253, 'Riyadh'),
(142, 254, 'Mecca'),
(143, 255, 'Dammam'),
(144, 256, 'Abha'),
(145, 257, 'Dubai'),
(146, 258, 'Abu Dhabi'),
(147, 259, 'Sharjah'),
(148, 260, 'Ajman'),
(149, 261, 'Lahore'),
(150, 262, 'Karachi'),
(151, 263, 'Peshawar'),
(152, 264, 'Quetta'),
(153, 265, 'Dhaka'),
(154, 266, 'Chattogram'),
(155, 267, 'Khulna'),
(156, 268, 'Rajshahi'),
(157, 269, 'Jakarta'),
(158, 270, 'Medan'),
(159, 271, 'Denpasar'),
(160, 272, 'Balikpapan'),
(161, 273, 'Johannesburg'),
(162, 274, 'Cape Town'),
(163, 275, 'Durban'),
(164, 276, 'Port Elizabeth');

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `iso_code` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency_code` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency_symbol` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `name`, `iso_code`, `currency_code`, `currency_symbol`) VALUES
(1, 'Afghanistan', 'AF', 'AFN', '؋'),
(2, 'Albania', 'AL', 'ALL', 'L'),
(3, 'Algeria', 'DZ', 'DZD', 'د.ج'),
(4, 'Andorra', 'AD', 'EUR', '€'),
(5, 'Angola', 'AO', 'AOA', 'Kz'),
(6, 'Argentina', 'AR', 'ARS', '$'),
(7, 'Armenia', 'AM', 'AMD', '֏'),
(8, 'Australia', 'AU', 'AUD', '$'),
(9, 'Austria', 'AT', 'EUR', '€'),
(10, 'Azerbaijan', 'AZ', 'AZN', '₼'),
(11, 'Bahamas', 'BS', 'BSD', '$'),
(12, 'Bahrain', 'BH', 'BHD', '.د.ب'),
(13, 'Bangladesh', 'BD', 'BDT', '৳'),
(14, 'Barbados', 'BB', 'BBD', '$'),
(15, 'Belarus', 'BY', 'BYN', 'Br'),
(16, 'Belgium', 'BE', 'EUR', '€'),
(17, 'Belize', 'BZ', 'BZD', '$'),
(18, 'Benin', 'BJ', 'XOF', 'CFA'),
(19, 'Bhutan', 'BT', 'BTN', 'Nu.'),
(20, 'Bolivia', 'BO', 'BOB', 'Bs.'),
(21, 'Bosnia and Herzegovina', 'BA', 'BAM', 'KM'),
(22, 'Botswana', 'BW', 'BWP', 'P'),
(23, 'Brazil', 'BR', 'BRL', 'R$'),
(24, 'Brunei', 'BN', 'BND', '$'),
(25, 'Bulgaria', 'BG', 'BGN', 'лв'),
(26, 'Burkina Faso', 'BF', 'XOF', 'CFA'),
(27, 'Burundi', 'BI', 'BIF', 'FBu'),
(28, 'Cambodia', 'KH', 'KHR', '៛'),
(29, 'Cameroon', 'CM', 'XAF', 'CFA'),
(30, 'Canada', 'CA', 'CAD', '$'),
(31, 'Cape Verde', 'CV', 'CVE', '$'),
(32, 'Central African Republic', 'CF', 'XAF', 'CFA'),
(33, 'Chad', 'TD', 'XAF', 'CFA'),
(34, 'Chile', 'CL', 'CLP', '$'),
(35, 'China', 'CN', 'CNY', '¥'),
(36, 'Colombia', 'CO', 'COP', '$'),
(37, 'Comoros', 'KM', 'KMF', 'CF'),
(38, 'Congo', 'CG', 'XAF', 'CFA'),
(39, 'Costa Rica', 'CR', 'CRC', '₡'),
(40, 'Croatia', 'HR', 'EUR', '€'),
(41, 'Cuba', 'CU', 'CUP', '$'),
(42, 'Cyprus', 'CY', 'EUR', '€'),
(43, 'Czech Republic', 'CZ', 'CZK', 'Kč'),
(44, 'Denmark', 'DK', 'DKK', 'kr'),
(45, 'Djibouti', 'DJ', 'DJF', 'Fdj'),
(46, 'Dominica', 'DM', 'XCD', '$'),
(47, 'Dominican Republic', 'DO', 'DOP', '$'),
(48, 'Ecuador', 'EC', 'USD', '$'),
(49, 'Egypt', 'EG', 'EGP', '£'),
(50, 'El Salvador', 'SV', 'USD', '$'),
(51, 'Estonia', 'EE', 'EUR', '€'),
(52, 'Eswatini', 'SZ', 'SZL', 'L'),
(53, 'Ethiopia', 'ET', 'ETB', 'Br'),
(54, 'Fiji', 'FJ', 'FJD', '$'),
(55, 'Finland', 'FI', 'EUR', '€'),
(56, 'France', 'FR', 'EUR', '€'),
(57, 'Gabon', 'GA', 'XAF', 'CFA'),
(58, 'Gambia', 'GM', 'GMD', 'D'),
(59, 'Georgia', 'GE', 'GEL', '₾'),
(60, 'Germany', 'DE', 'EUR', '€'),
(61, 'Ghana', 'GH', 'GHS', '₵'),
(62, 'Greece', 'GR', 'EUR', '€'),
(63, 'Guatemala', 'GT', 'GTQ', 'Q'),
(64, 'Guinea', 'GN', 'GNF', 'FG'),
(65, 'Guyana', 'GY', 'GYD', '$'),
(66, 'Haiti', 'HT', 'HTG', 'G'),
(67, 'Honduras', 'HN', 'HNL', 'L'),
(68, 'Hungary', 'HU', 'HUF', 'Ft'),
(69, 'Iceland', 'IS', 'ISK', 'kr'),
(70, 'India', 'IN', 'INR', '₹'),
(71, 'Indonesia', 'ID', 'IDR', 'Rp'),
(72, 'Iran', 'IR', 'IRR', '﷼'),
(73, 'Iraq', 'IQ', 'IQD', 'ع.د'),
(74, 'Ireland', 'IE', 'EUR', '€'),
(75, 'Israel', 'IL', 'ILS', '₪'),
(76, 'Italy', 'IT', 'EUR', '€'),
(77, 'Jamaica', 'JM', 'JMD', '$'),
(78, 'Japan', 'JP', 'JPY', '¥'),
(79, 'Jordan', 'JO', 'JOD', 'JD'),
(80, 'Kazakhstan', 'KZ', 'KZT', '₸'),
(81, 'Kenya', 'KE', 'KES', 'KSh'),
(82, 'Kuwait', 'KW', 'KWD', 'KD'),
(83, 'Kyrgyzstan', 'KG', 'KGS', 'с'),
(84, 'Laos', 'LA', 'LAK', '₭'),
(85, 'Latvia', 'LV', 'EUR', '€'),
(86, 'Lebanon', 'LB', 'LBP', '£'),
(87, 'Lesotho', 'LS', 'LSL', 'L'),
(88, 'Liberia', 'LR', 'LRD', '$'),
(89, 'Libya', 'LY', 'LYD', 'LD'),
(90, 'Lithuania', 'LT', 'EUR', '€'),
(91, 'Luxembourg', 'LU', 'EUR', '€'),
(92, 'Madagascar', 'MG', 'MGA', 'Ar'),
(93, 'Malawi', 'MW', 'MWK', 'MK'),
(94, 'Malaysia', 'MY', 'MYR', 'RM'),
(95, 'Maldives', 'MV', 'MVR', 'Rf'),
(96, 'Mali', 'ML', 'XOF', 'CFA'),
(97, 'Malta', 'MT', 'EUR', '€'),
(98, 'Mauritania', 'MR', 'MRU', 'UM'),
(99, 'Mauritius', 'MU', 'MUR', '₨'),
(100, 'Mexico', 'MX', 'MXN', '$'),
(101, 'Moldova', 'MD', 'MDL', 'L'),
(102, 'Mongolia', 'MN', 'MNT', '₮'),
(103, 'Montenegro', 'ME', 'EUR', '€'),
(104, 'Morocco', 'MA', 'MAD', 'DH'),
(105, 'Mozambique', 'MZ', 'MZN', 'MT'),
(106, 'Myanmar', 'MM', 'MMK', 'K'),
(107, 'Namibia', 'NA', 'NAD', '$'),
(108, 'Nepal', 'NP', 'NPR', '₨'),
(109, 'Netherlands', 'NL', 'EUR', '€'),
(110, 'New Zealand', 'NZ', 'NZD', '$'),
(111, 'Nicaragua', 'NI', 'NIO', 'C$'),
(112, 'Niger', 'NE', 'XOF', 'CFA'),
(113, 'Nigeria', 'NG', 'NGN', '₦'),
(114, 'North Korea', 'KP', 'KPW', '₩'),
(115, 'Norway', 'NO', 'NOK', 'kr'),
(116, 'Oman', 'OM', 'OMR', 'ر.ع.'),
(117, 'Pakistan', 'PK', 'PKR', '₨'),
(118, 'Panama', 'PA', 'PAB', '$'),
(119, 'Paraguay', 'PY', 'PYG', '₲'),
(120, 'Peru', 'PE', 'PEN', 'S/'),
(121, 'Philippines', 'PH', 'PHP', '₱'),
(122, 'Poland', 'PL', 'PLN', 'zł'),
(123, 'Portugal', 'PT', 'EUR', '€'),
(124, 'Qatar', 'QA', 'QAR', 'ر.ق'),
(125, 'Romania', 'RO', 'RON', 'lei'),
(126, 'Russia', 'RU', 'RUB', '₽'),
(127, 'Rwanda', 'RW', 'RWF', 'FRw'),
(128, 'Saudi Arabia', 'SA', 'SAR', '﷼'),
(129, 'Senegal', 'SN', 'XOF', 'CFA'),
(130, 'Serbia', 'RS', 'RSD', 'дин'),
(131, 'Singapore', 'SG', 'SGD', '$'),
(132, 'Slovakia', 'SK', 'EUR', '€'),
(133, 'Slovenia', 'SI', 'EUR', '€'),
(134, 'Somalia', 'SO', 'SOS', 'S'),
(135, 'South Africa', 'ZA', 'ZAR', 'R'),
(136, 'South Korea', 'KR', 'KRW', '₩'),
(137, 'Spain', 'ES', 'EUR', '€'),
(138, 'Sri Lanka', 'LK', 'LKR', '₨'),
(139, 'Sudan', 'SD', 'SDG', 'ج.س.'),
(140, 'Sweden', 'SE', 'SEK', 'kr'),
(141, 'Switzerland', 'CH', 'CHF', 'CHF'),
(142, 'Syria', 'SY', 'SYP', '£'),
(143, 'Taiwan', 'TW', 'TWD', '$'),
(144, 'Tanzania', 'TZ', 'TZS', 'Sh'),
(145, 'Thailand', 'TH', 'THB', '฿'),
(146, 'Tunisia', 'TN', 'TND', 'DT'),
(147, 'Turkey', 'TR', 'TRY', '₺'),
(148, 'Uganda', 'UG', 'UGX', 'USh'),
(149, 'Ukraine', 'UA', 'UAH', '₴'),
(150, 'United Arab Emirates', 'AE', 'AED', 'د.إ'),
(151, 'United Kingdom', 'GB', 'GBP', '£'),
(152, 'United States', 'US', 'USD', '$'),
(153, 'Uruguay', 'UY', 'UYU', '$'),
(154, 'Uzbekistan', 'UZ', 'UZS', 'soʻm'),
(155, 'Venezuela', 'VE', 'VES', 'Bs'),
(156, 'Vietnam', 'VN', 'VND', '₫'),
(157, 'Yemen', 'YE', 'YER', '﷼'),
(158, 'Zambia', 'ZM', 'ZMW', 'ZK'),
(159, 'Zimbabwe', 'ZW', 'ZWL', '$');

-- --------------------------------------------------------

--
-- Table structure for table `discounts`
--

CREATE TABLE `discounts` (
  `id` bigint UNSIGNED NOT NULL,
  `coupen_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('percentage','fixed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` decimal(8,2) NOT NULL,
  `required_code` tinyint(1) NOT NULL DEFAULT '1',
  `message` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hotel_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active_status` tinyint(1) NOT NULL DEFAULT '1',
  `starts_from` datetime NOT NULL DEFAULT '2026-04-02 00:00:00',
  `ends_at` datetime NOT NULL,
  `min_nights` int NOT NULL DEFAULT '0',
  `usage_limit` int DEFAULT NULL,
  `used_count` int NOT NULL DEFAULT '0',
  `user_limit` int DEFAULT '1',
  `min_amount` int DEFAULT '0',
  `max_discount` decimal(8,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `country_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `discounts`
--

INSERT INTO `discounts` (`id`, `coupen_code`, `type`, `value`, `required_code`, `message`, `hotel_id`, `active_status`, `starts_from`, `ends_at`, `min_nights`, `usage_limit`, `used_count`, `user_limit`, `min_amount`, `max_discount`, `created_at`, `updated_at`, `country_id`) VALUES
(1, 'LIMITED OFFER', 'percentage', 10.00, 0, 'Limited Time Discount, Grab it Faster', NULL, 1, '2026-04-22 00:00:00', '2026-05-31 00:00:00', 1, 100, 2, 10, 1, 1000.00, '2026-04-22 10:14:09', '2026-04-23 11:00:26', 70),
(2, 'TEST OFFER', 'percentage', 20.00, 0, 'This is Test Offer, Grab it Fast', NULL, 0, '2026-04-22 00:00:00', '2027-01-01 00:00:00', 1, 1000, 0, 100, 1, 5000.00, '2026-04-22 11:55:36', '2026-04-23 04:45:39', NULL),
(3, 'all10', 'percentage', 10.00, 1, NULL, NULL, 1, '2026-04-23 00:00:00', '2026-07-31 00:00:00', 1, 1000, 0, 20, NULL, 1000.00, '2026-04-23 12:00:06', '2026-04-23 12:00:06', NULL),
(4, 'Test20', 'fixed', 2000.00, 1, NULL, NULL, 1, '2026-04-23 00:00:00', '2026-04-24 00:00:00', 1, 20, 0, 5, 100, 2000.00, '2026-04-23 13:14:26', '2026-04-23 13:14:28', 70),
(5, '12', 'percentage', 50.00, 1, NULL, NULL, 0, '2026-04-23 00:00:00', '2026-04-30 00:00:00', 1, NULL, 0, NULL, NULL, 5000.00, '2026-04-23 13:19:28', '2026-04-23 13:19:28', NULL),
(7, 'TEST90', 'fixed', 900.00, 0, NULL, NULL, 1, '2026-04-23 00:00:00', '2026-04-25 00:00:00', 1, NULL, 0, NULL, NULL, NULL, '2026-04-23 13:22:59', '2026-04-24 07:19:09', NULL),
(8, 'OFFER-95', 'percentage', 95.00, 0, 'This is Test Offer, Grab it Fast', '11', 1, '2026-04-23 00:00:00', '2026-04-30 00:00:00', 1, NULL, 0, NULL, 1, NULL, '2026-04-23 13:35:03', '2026-04-24 06:48:25', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hotels`
--

CREATE TABLE `hotels` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `city_id` bigint UNSIGNED DEFAULT NULL,
  `pincode` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `cancellation_charge` decimal(10,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hotels`
--

INSERT INTO `hotels` (`id`, `name`, `description`, `address`, `city_id`, `pincode`, `created_at`, `updated_at`, `cancellation_charge`) VALUES
(1, 'The Taj Mahal Palace', 'Luxury heritage hotel overlooking the Arabian Sea, offering world-class hospitality and iconic architecture.', 'Ahmedabad, Gujarat, India', 1, '400001', '2026-04-22 05:23:19', '2026-04-22 05:30:40', 0.00),
(2, 'The Leela Palace New Delhi', 'Opulent hotel featuring royal Indian design, fine dining, and premium spa services in the heart of Delhi.', 'New Delhi, India', 78, '110023', '2026-04-22 05:23:19', '2026-04-22 06:00:05', 100.00),
(3, 'The Oberoi Bangalore', 'Elegant luxury hotel set amidst lush gardens with premium rooms and award-winning dining.', 'Bangalore, Karnataka, India', 45, '560001', '2026-04-22 05:23:19', '2026-04-22 05:32:10', 0.00),
(4, 'ITC Rajputana', 'Rajasthani-style luxury hotel offering cultural ambiance with modern comfort in Jaipur.', 'Jaipur, Rajasthan, India', 61, '302006', '2026-04-22 05:23:19', '2026-04-22 06:00:14', 150.00),
(5, 'Taj Resort & Convention Centre Goa', 'Beachside resort with scenic views, infinity pool, and relaxing coastal experience.', 'West Beach, Panaji', 40, '403516', '2026-04-22 05:23:19', '2026-04-22 05:33:54', 0.00),
(6, 'Burj Al Arab Jumeirah', 'Iconic 7-star luxury hotel in Dubai known for its sail-shaped design and ultra-premium services.', 'Jumeirah St, Dubai, UAE', 145, '00000', '2026-04-22 05:23:19', '2026-04-22 06:00:43', 5.00),
(7, 'Hôtel Ritz Paris', 'Historic palace hotel in central Paris offering timeless elegance and luxury suites.', '15 Place Vendôme, Paris, France', 105, '75001', '2026-04-22 05:23:19', '2026-04-22 05:38:08', 0.00),
(8, 'The Beverly Hills Hotel', 'Legendary luxury hotel in Los Angeles known for Hollywood glamour and premium hospitality.', '9641 Sunset Blvd, Beverly Hills, CA', 84, '90210', '2026-04-22 05:23:19', '2026-04-22 06:00:54', 10.00),
(9, 'The Ritz-Carlton Riyadh', 'Grand luxury hotel featuring palatial architecture and premium services in Saudi Arabia.', 'Al Hada Area, Riyadh, Saudi Arabia', 141, '11493', '2026-04-22 05:23:19', '2026-04-22 05:39:15', 0.00),
(10, 'Jeddah Hilton', 'Modern waterfront hotel offering Red Sea views and premium accommodations in Jeddah.', 'North Corniche Rd, Jeddah, Saudi Arabia', 143, '23511', '2026-04-22 05:23:19', '2026-04-22 06:01:02', 18.00),
(11, 'Hotel The Grand Regency', 'A modern hotel in the heart of Rajkot offering comfortable rooms, fast WiFi, and in-house dining. Ideal for both business and leisure stays with easy access to major city attractions.', 'Dr. Yagnik Road, Near Race Course Ring Road', 27, '360001', '2026-04-22 05:42:22', '2026-04-22 05:42:22', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `id` bigint UNSIGNED NOT NULL,
  `imageable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `imageable_id` bigint UNSIGNED NOT NULL,
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `images`
--

INSERT INTO `images` (`id`, `imageable_type`, `imageable_id`, `path`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\Hotel', 1, 'assets/hotel/X2uQEWxxIwY3FXlQzrLY1MCRCGsg6ZCGeJxaCWFf.jpg', '2026-04-22 05:30:40', '2026-04-22 05:30:40'),
(2, 'App\\Models\\Hotel', 2, 'assets/hotel/dCsXHl8zmK3OCwM8U7yLEqLazcoc43gxDP29tGmo.jpg', '2026-04-22 05:31:28', '2026-04-22 05:31:28'),
(3, 'App\\Models\\Hotel', 3, 'assets/hotel/Zxmp5tMbgNTjByxg9NHtLGgfukQwVFcBsqa4Lp7e.jpg', '2026-04-22 05:32:10', '2026-04-22 05:32:10'),
(4, 'App\\Models\\Hotel', 4, 'assets/hotel/n9RWAkoneQPtJTYZgAOHKDHPW9k08OoUEHoKsjMY.jpg', '2026-04-22 05:33:05', '2026-04-22 05:33:05'),
(5, 'App\\Models\\Hotel', 5, 'assets/hotel/2R4PtKRaWiHlc6ZJX4cvotvHVZUpvWeO8sI6oJkn.jpg', '2026-04-22 05:33:54', '2026-04-22 05:33:54'),
(6, 'App\\Models\\Hotel', 6, 'assets/hotel/5EcCtI2yHC0b09Kj92KTQUWnPWnuUajHDvcpp6no.jpg', '2026-04-22 05:37:50', '2026-04-22 05:37:50'),
(7, 'App\\Models\\Hotel', 7, 'assets/hotel/z5Z0oSIfdLzmCf8BY83LArwhdDuJyO9Ef5yxyeKG.jpg', '2026-04-22 05:38:08', '2026-04-22 05:38:08'),
(8, 'App\\Models\\Hotel', 8, 'assets/hotel/GB3WmKxqt3ysVGRYLPBSXcJXVYubYUdCnFkrE6uy.jpg', '2026-04-22 05:38:50', '2026-04-22 05:38:50'),
(9, 'App\\Models\\Hotel', 9, 'assets/hotel/3DjjAq4UiMsAF1eMOXcdJFA0vZFf7i3bTVRLSowJ.jpg', '2026-04-22 05:39:15', '2026-04-22 05:39:15'),
(10, 'App\\Models\\Hotel', 10, 'assets/hotel/N3NL6O13pyTNbmVZgNRBQREFTskwH0mwAEfLckd8.jpg', '2026-04-22 05:39:49', '2026-04-22 05:39:49'),
(11, 'App\\Models\\Hotel', 11, 'assets/hotel/TJDf7tuREkPGty5UDs54q7HhxbV2HVjBkEtjv6ik.jpg', '2026-04-22 05:42:22', '2026-04-22 05:42:22'),
(12, 'App\\Models\\RoomDetail', 1, 'assets/rooms/LzZg5cMDBL60YBHS8pXZgfVtUdLhZBHxPyU3n6Pc.jpg', '2026-04-22 06:44:21', '2026-04-22 06:44:21'),
(13, 'App\\Models\\Hotel', 1, 'assets/hotel/AtoZeEWG5KlxPP0uldelIfZUmpoyngbg7hKiyFMQ.jpg', '2026-04-22 07:21:02', '2026-04-22 07:21:02'),
(14, 'App\\Models\\Hotel', 1, 'assets/hotel/3VhmbvuA9uULfv90H5nBxnuPtFRnRH0r69p1kihE.jpg', '2026-04-22 07:21:02', '2026-04-22 07:21:02'),
(15, 'App\\Models\\Hotel', 1, 'assets/hotel/6MH4XWJUAGmfzRqgbX9FP1eZVWI4o5l2fz284AEe.jpg', '2026-04-22 07:21:02', '2026-04-22 07:21:02'),
(16, 'App\\Models\\RoomDetail', 2, 'assets/rooms/Mte0YaoYMPiMCvxJJgpgoNjaUTjHjgLCdWLWoqvu.jpg', '2026-04-22 07:35:19', '2026-04-22 07:35:19'),
(17, 'App\\Models\\RoomDetail', 3, 'assets/rooms/856q4qezAexu298JBr4QnPvIbsmXMGK0UPE6fTqa.jpg', '2026-04-22 07:38:07', '2026-04-22 07:38:07'),
(18, 'App\\Models\\RoomDetail', 4, 'assets/rooms/bXjIxOl01EJ2aHsjb3p8HaT13CWcezFdstOQdV1P.jpg', '2026-04-22 07:46:48', '2026-04-22 07:46:48'),
(19, 'App\\Models\\RoomDetail', 5, 'assets/rooms/oGILXG0cEKCR516BvUOwtpn2WJlM8MZZGSAZSAVI.jpg', '2026-04-22 07:59:13', '2026-04-22 07:59:13'),
(20, 'App\\Models\\RoomDetail', 37, 'assets/rooms/a3DGKpzhXq5WmeIkJSpe4wN3KclU1T3mEwm5Wd1v.jpg', '2026-04-22 08:19:32', '2026-04-22 08:19:32'),
(21, 'App\\Models\\RoomDetail', 38, 'assets/rooms/JoTnWPoGXGVtjvqQgT7IHWXGr62AcuUeYEQXFArr.jpg', '2026-04-22 08:21:12', '2026-04-22 08:21:12'),
(22, 'App\\Models\\RoomDetail', 36, 'assets/rooms/28ScvdjIR8IWv2L3PihH79gZS2Aulaa2UIF5aKAL.jpg', '2026-04-22 08:21:28', '2026-04-22 08:21:28'),
(23, 'App\\Models\\RoomDetail', 35, 'assets/rooms/tcNDS9bmzloJJ4NZTVfB8vkwftjAkgX7R6Rr8CVF.jpg', '2026-04-22 08:21:41', '2026-04-22 08:21:41'),
(24, 'App\\Models\\RoomDetail', 34, 'assets/rooms/wqV7VdZkRcJnRWXKKCULjuuozRmWKGFQAPcc3dQx.jpg', '2026-04-22 08:23:03', '2026-04-22 08:23:03'),
(25, 'App\\Models\\RoomDetail', 9, 'assets/rooms/92FECN4ylp10mlRxLjDPrBXzhrfqhoeOoM9evObZ.jpg', '2026-04-22 08:23:58', '2026-04-22 08:23:58'),
(26, 'App\\Models\\RoomDetail', 8, 'assets/rooms/bveZ4QrwJbPrx04P9XpxnBAfH5XiqBS4UiKsb0qi.jpg', '2026-04-22 08:24:36', '2026-04-22 08:24:36'),
(27, 'App\\Models\\RoomDetail', 7, 'assets/rooms/FGVsLa0HKRAdtrqHTaTM4WGpYbyVp9iByOTXMO9i.jpg', '2026-04-22 08:25:13', '2026-04-22 08:25:13'),
(28, 'App\\Models\\RoomDetail', 6, 'assets/rooms/1H0w1PGy53icBMQ5zUw8O8lAA3rhWSJKpJuO0jG5.jpg', '2026-04-22 08:25:24', '2026-04-22 08:25:24'),
(29, 'App\\Models\\RoomDetail', 13, 'assets/rooms/ZfxwlaahMmTv3tgvMxySJZzXVUJPrwuJTo0uBlOL.jpg', '2026-04-22 08:26:50', '2026-04-22 08:26:50'),
(30, 'App\\Models\\RoomDetail', 12, 'assets/rooms/NYBUMdbh3r3GxEzbSAHrBqaP4vcOADsHZuaPNILP.jpg', '2026-04-22 08:27:03', '2026-04-22 08:27:03'),
(31, 'App\\Models\\RoomDetail', 11, 'assets/rooms/eG3ZJSaT2406faoZY2gXr1izuyOjJEY3cwtENMYw.jpg', '2026-04-22 08:27:14', '2026-04-22 08:27:14'),
(32, 'App\\Models\\RoomDetail', 10, 'assets/rooms/n96BKrOw0FRZyobkNFE8Yh3ahAYB3bcAgRevGdtu.jpg', '2026-04-22 08:28:00', '2026-04-22 08:28:00'),
(33, 'App\\Models\\RoomDetail', 17, 'assets/rooms/jXVd4sR1TwfI6WuN1utXLaivxShquka7s1znqjK2.jpg', '2026-04-22 08:28:31', '2026-04-22 08:28:31'),
(34, 'App\\Models\\RoomDetail', 16, 'assets/rooms/DCxvtFRAmgCDrNR21jprzvWlYuRTwENiYsVeJNEQ.jpg', '2026-04-22 08:28:39', '2026-04-22 08:28:39'),
(35, 'App\\Models\\RoomDetail', 15, 'assets/rooms/yFl9twWsPABxte1feRwbaYepvj3MwKR5P1oEZJcM.jpg', '2026-04-22 08:28:53', '2026-04-22 08:28:53'),
(36, 'App\\Models\\RoomDetail', 14, 'assets/rooms/cmccztYe6iQDtCkFdcWijir1tV46aX2DNotjGVTN.jpg', '2026-04-22 08:29:05', '2026-04-22 08:29:05'),
(37, 'App\\Models\\RoomDetail', 21, 'assets/rooms/9BPLeCko2gXq2mWv1hfoQkm1DKbBp3n1jYETeK2h.jpg', '2026-04-22 08:30:59', '2026-04-22 08:30:59'),
(38, 'App\\Models\\RoomDetail', 20, 'assets/rooms/LAKKqKj5BOlX7ydt3qAkHLgQq136CqvfudV1tFeu.jpg', '2026-04-22 08:31:16', '2026-04-22 08:31:16'),
(39, 'App\\Models\\RoomDetail', 19, 'assets/rooms/rsK2xm3B83MD1jXLzODeCLi1BqjHZkyQKNWbnQWl.png', '2026-04-22 08:32:20', '2026-04-22 08:32:20'),
(40, 'App\\Models\\RoomDetail', 18, 'assets/rooms/Rjjc9sI6GplsIr5XV58ZUaeAnuhlV084EstjJmdF.jpg', '2026-04-22 08:33:00', '2026-04-22 08:33:00'),
(41, 'App\\Models\\RoomDetail', 25, 'assets/rooms/33izZd1rdfOraSECkmPLakugAgUcSgIqHCklVUkm.jpg', '2026-04-22 08:35:14', '2026-04-22 08:35:14'),
(43, 'App\\Models\\RoomDetail', 23, 'assets/rooms/tkEhgbm5TAyjga7H9Fa7A0bmeiR8KWozDJOhZIWh.jpg', '2026-04-22 08:36:22', '2026-04-22 08:36:22'),
(44, 'App\\Models\\RoomDetail', 24, 'assets/rooms/He2QrG61sDsh5I8NTnP6DTeokBwaofs8wPPYGdtn.jpg', '2026-04-22 08:36:43', '2026-04-22 08:36:43'),
(45, 'App\\Models\\RoomDetail', 22, 'assets/rooms/fk8N15OFMY9Y9QVhYd2RhGF4RMwDWba7SPZjE3VA.jpg', '2026-04-22 08:37:03', '2026-04-22 08:37:03'),
(46, 'App\\Models\\RoomDetail', 29, 'assets/rooms/tEYEd6tQeosNgFwtdT18LKETSDsNJcwrYOtRZ7B6.jpg', '2026-04-22 08:51:20', '2026-04-22 08:51:20'),
(48, 'App\\Models\\RoomDetail', 27, 'assets/rooms/2kDeOCEnAZRPi4rDqNpAfAxccikShGE0DkdUdSD7.jpg', '2026-04-22 08:52:09', '2026-04-22 08:52:09'),
(49, 'App\\Models\\RoomDetail', 28, 'assets/rooms/HR7R5v7EqSPQpiriK34XcZJw1KLejHkORTZNkB3m.jpg', '2026-04-22 08:52:28', '2026-04-22 08:52:28'),
(50, 'App\\Models\\RoomDetail', 26, 'assets/rooms/9TpwwhXfgHAX3J6zrOPiXx05WSxoz3kN3TqfZ0hn.jpg', '2026-04-22 08:53:36', '2026-04-22 08:53:36'),
(51, 'App\\Models\\RoomDetail', 33, 'assets/rooms/AO0kGLpqQ6jmRV8wScns1bW82CW6EcTnfllpshZ2.jpg', '2026-04-22 08:54:15', '2026-04-22 08:54:15'),
(52, 'App\\Models\\RoomDetail', 32, 'assets/rooms/eDr6baRsvp7NXiypKmxVQPLXq7Olxt9u5sgqKLdw.jpg', '2026-04-22 08:54:34', '2026-04-22 08:54:34'),
(53, 'App\\Models\\RoomDetail', 31, 'assets/rooms/bfJGOTOhFoBh3Pnxgx1OecwsOyE7QmBJsQwz5l1o.jpg', '2026-04-22 08:54:48', '2026-04-22 08:54:48'),
(54, 'App\\Models\\RoomDetail', 30, 'assets/rooms/7au7hNbV1ywtWJd7ba0iPAVGMtSMY8Ka77fUtECK.jpg', '2026-04-22 08:55:10', '2026-04-22 08:55:10'),
(55, 'App\\Models\\RoomDetail', 45, 'assets/rooms/h029uEEDTHnWurqNKaCHMPMPDmseLSZ7MVD1s9tX.jpg', '2026-04-22 08:56:01', '2026-04-22 08:56:01'),
(56, 'App\\Models\\RoomDetail', 44, 'assets/rooms/aYxErWFBTP11m6aBeDi4r2r9GWlLsDwdMkw3OFt0.jpg', '2026-04-22 08:56:20', '2026-04-22 08:56:20'),
(57, 'App\\Models\\RoomDetail', 43, 'assets/rooms/0ZgZEsaRHWowQ1KFUo0skFHiFYO2JmIqXHACyIx7.jpg', '2026-04-22 08:56:36', '2026-04-22 08:56:36'),
(58, 'App\\Models\\RoomDetail', 42, 'assets/rooms/L0VKVNAdLaL1grf7oF885I1X5KhNWMH02BEtyMft.jpg', '2026-04-22 08:56:50', '2026-04-22 08:56:50'),
(59, 'App\\Models\\RoomDetail', 41, 'assets/rooms/Sd6ymV2tnedTe4pvwrSPVz1U8C3w4wN0ZQAFe7Nf.jpg', '2026-04-22 08:57:12', '2026-04-22 08:57:12'),
(60, 'App\\Models\\RoomDetail', 40, 'assets/rooms/DaKByt9Jb3FjtbvntU8lMubTlPxiVqIE9k4DT7T6.jpg', '2026-04-22 08:57:25', '2026-04-22 08:57:25'),
(61, 'App\\Models\\RoomDetail', 39, 'assets/rooms/NTYmmXRSKl1Hkjo2aooKM1rdJdz88hPeit1LsNVv.jpg', '2026-04-22 08:57:36', '2026-04-22 08:57:36'),
(66, 'App\\Models\\RoomDetail', 47, 'assets/rooms/s0wHD68ECcU0od7IsxPejMfTBLJso3dV0dlrdORU.jpg', '2026-04-23 06:00:54', '2026-04-23 06:00:54'),
(67, 'App\\Models\\RoomDetail', 48, 'assets/rooms/qgNl4zIrVRbpY7wg6xIOLvgtoP7yhi7clzAEZXVV.jpg', '2026-04-23 06:17:14', '2026-04-23 06:17:14'),
(68, 'App\\Models\\RoomDetail', 49, 'assets/rooms/X8VPZ8NS0dVwLY4vIobNWKbtLNfzFdEf5qr9MSHz.jpg', '2026-04-23 06:25:03', '2026-04-23 06:25:03'),
(70, 'App\\Models\\RoomDetail', 50, 'assets/rooms/bjQgQZQIno9N2brHOfZgFMJstKkZyWNwhFEX1ulK.jpg', '2026-04-23 06:40:30', '2026-04-23 06:40:30');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_03_19_045720_create_images_table', 1),
(5, '2026_03_19_045819_create_hotels_table', 1),
(6, '2026_03_19_045852_create_rooms_table', 1),
(7, '2026_03_19_045853_create_rooms_table', 2),
(8, '2026_03_19_045854_create_rooms_table', 3),
(11, '2026_03_26_101111_create_room_details_table', 4),
(12, '2026_03_26_102501_alter_room_table', 5),
(13, '2026_03_27_043342_create_rooms_table_again', 6),
(14, '2026_03_27_110628_add_currency_column_to_room_details', 7),
(15, '2026_03_30_051611_create_bookings_table', 7),
(16, '2026_03_31_042544_create_payments_table', 7),
(17, '2026_03_31_044413_adding_instruction_column_to_bookings_table', 7),
(18, '2026_03_31_044444_making_status_enum_in_rooms_table', 7),
(19, '2026_03_31_052341_create_discounts_table', 8),
(21, '2026_04_01_074027_changes_in_bookings_table', 9),
(22, '2026_04_01_080649_create_booking_items_table', 9),
(23, '2026_04_01_122905_create_personal_access_tokens_table', 10),
(24, '2026_04_02_064501_update_payments_table', 10),
(25, '2026_04_02_104735_update_discounts_table', 11),
(26, '2026_04_02_110640_update_bookings_table', 12),
(27, '2026_04_02_104745_update_discounts_table', 13),
(28, '2026_04_03_061159_add_currency_column_in_bookings_table', 14),
(33, '2026_04_03_064906_create_countries_states_cities_table', 15),
(34, '2026_04_03_073602_update_discounts_table', 15),
(35, '2026_04_03_120556_update_hotels_table', 16),
(37, '2026_04_03_120557_update_hotels_table', 17),
(40, '2026_04_03_120558_update_hotels_table', 18),
(44, '2026_04_09_062130_create_user_profiles_table', 19),
(57, '2026_04_13_111414_adding_payment_intent_id_column_to_payments_table', 20),
(58, '2026_04_17_105114_update_discounts_table', 21),
(59, '2026_04_17_162852_update_room_details_table', 21),
(60, '2026_04_21_100002_update_discounts_table_for_min_amount', 21),
(61, '2026_04_21_164634_update_bookings_table', 21),
(62, '2026_04_21_170236_create_amenities_table', 21),
(63, '2026_04_21_174229_update_hotels_table', 21),
(64, '2026_04_23_142657_update_bookings_table_to_add_arrival_and_leave_feilds', 22);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint UNSIGNED NOT NULL,
  `booking_id` bigint UNSIGNED NOT NULL,
  `gateway` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'stripe',
  `amount` decimal(15,2) NOT NULL,
  `converted_amount` decimal(15,2) DEFAULT NULL,
  `paid_currency` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `exchange_rate` decimal(12,8) DEFAULT NULL COMMENT 'from user currency to hotel currency',
  `currency` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `status` tinyint NOT NULL DEFAULT '0' COMMENT '0:pending, 1:success, 2:failed, 3:processing',
  `session_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_intent_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `booking_id`, `gateway`, `amount`, `converted_amount`, `paid_currency`, `exchange_rate`, `currency`, `status`, `session_id`, `payment_intent_id`, `created_at`, `updated_at`) VALUES
(1, 1, 'Stripe', 8598.00, 338.93, 'AED', 0.03942000, 'INR', 1, 'cs_test_a1VEZ3ked2FyESwAkn0aydHrcStw0QwjowXeh1flLa84KtBWwhJW5989FT', NULL, '2026-04-22 08:43:31', '2026-04-22 08:43:31'),
(2, 2, 'Stripe', 36.00, 901.05, 'INR', 25.02930000, 'SAR', 1, 'cs_test_a1S2WKf5GTjAniy5GRUazI4dbCkbB7mx51clnpr2ya8b4KE89JOlxWFVVg', 'pi_3TPHedLOdhBME3Ik03qzbLNM', '2026-04-23 07:26:24', '2026-04-23 07:26:32'),
(3, 3, 'Stripe', 40.50, 4451.40, 'INR', 109.91120000, 'EUR', 1, 'cs_test_a1cygtbbOy11cI0YDxO6WbHNYtQijkXAvW3vHmaNktK8fe7iUI1HsoGdiS', 'pi_3TPKzcLOdhBME3Ik0ISCtzmY', '2026-04-23 11:00:18', '2026-04-23 11:00:26'),
(4, 8, 'Stripe', 5220.00, 5220.00, 'INR', 1.00000000, 'INR', 2, 'cs_test_a13ZeZMBqAyKTTJCf7OFoNuYercHveP1X39VlNVIISNjnl3QHgIoMTLBUX', NULL, '2026-04-23 12:29:33', '2026-04-24 12:29:33'),
(5, 14, 'Stripe', 108.00, 10170.37, 'INR', 94.17010000, 'USD', 2, 'cs_test_b1LDnJq7axMnf7ZCTldnvGzVAGBwOUA9mgxgpZdbV85DADKz8L9uZZOisD', NULL, '2026-04-24 05:19:19', '2026-04-24 05:49:17'),
(6, 15, 'Stripe', 160.00, 160.00, 'INR', 1.00000000, 'INR', 2, 'cs_test_a1T2dIHz5mQZrEZOl847Tj4f6fkGbigsSZdVRrQPWLUfjpDpNM2uCoT76H', NULL, '2026-04-24 11:14:43', '2026-04-24 11:44:43');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` bigint UNSIGNED NOT NULL,
  `hotel_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `booking_id` bigint UNSIGNED DEFAULT NULL,
  `rating` tinyint UNSIGNED NOT NULL,
  `cleaning` tinyint UNSIGNED DEFAULT NULL,
  `services` tinyint UNSIGNED DEFAULT NULL,
  `food` tinyint UNSIGNED DEFAULT NULL,
  `hospitality` tinyint UNSIGNED DEFAULT NULL,
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` bigint UNSIGNED NOT NULL,
  `hotel_id` bigint UNSIGNED NOT NULL,
  `room_detail_id` bigint UNSIGNED NOT NULL,
  `room_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `hotel_id`, `room_detail_id`, `room_number`, `created_at`, `updated_at`, `status`) VALUES
(1, 1, 1, 'DD251', NULL, NULL, 1),
(2, 1, 1, 'DD252', NULL, NULL, 1),
(3, 1, 1, 'DD253', NULL, NULL, 1),
(4, 1, 1, 'DD254', NULL, NULL, 1),
(5, 1, 1, 'DD255', NULL, NULL, 1),
(6, 1, 1, 'DD256', NULL, NULL, 1),
(7, 1, 1, 'DD257', NULL, NULL, 1),
(8, 1, 1, 'DD258', NULL, NULL, 1),
(9, 1, 1, 'DD259', NULL, NULL, 1),
(10, 1, 1, 'DD260', NULL, NULL, 1),
(11, 1, 2, 'PF271', NULL, NULL, 1),
(12, 1, 2, 'PF272', NULL, NULL, 1),
(13, 1, 2, 'PF273', NULL, NULL, 1),
(14, 1, 2, 'PF274', NULL, NULL, 1),
(15, 1, 2, 'PF275', NULL, NULL, 1),
(16, 1, 3, 'SST385', NULL, NULL, 1),
(17, 1, 3, 'SST386', NULL, NULL, 1),
(18, 1, 3, 'SST387', NULL, NULL, 1),
(19, 1, 3, 'SST388', NULL, NULL, 1),
(20, 1, 3, 'SST389', NULL, NULL, 1),
(21, 1, 3, 'SST390', NULL, NULL, 1),
(22, 1, 3, 'SST391', NULL, NULL, 1),
(23, 1, 3, 'SST392', NULL, NULL, 1),
(24, 1, 3, 'SST393', NULL, NULL, 1),
(25, 1, 3, 'SST394', NULL, NULL, 1),
(26, 1, 3, 'SST395', NULL, NULL, 1),
(27, 1, 3, 'SST396', NULL, NULL, 1),
(28, 1, 3, 'SST397', NULL, NULL, 1),
(29, 1, 3, 'SST398', NULL, NULL, 1),
(30, 1, 3, 'SST399', NULL, NULL, 1),
(31, 1, 4, 'DT341', NULL, NULL, 1),
(32, 1, 4, 'DT342', NULL, NULL, 1),
(33, 1, 4, 'DT343', NULL, NULL, 1),
(34, 1, 4, 'DT344', NULL, NULL, 1),
(35, 1, 4, 'DT345', NULL, NULL, 1),
(36, 1, 4, 'DT346', NULL, NULL, 1),
(37, 1, 4, 'DT347', NULL, NULL, 1),
(38, 1, 4, 'DT348', NULL, NULL, 1),
(39, 1, 4, 'DT349', NULL, NULL, 1),
(40, 1, 4, 'DT350', NULL, NULL, 1),
(41, 1, 5, 'LD121', NULL, NULL, 1),
(42, 1, 5, 'LD122', NULL, NULL, 1),
(43, 1, 5, 'LD123', NULL, NULL, 1),
(44, 1, 5, 'LD124', NULL, NULL, 1),
(45, 1, 5, 'LD125', NULL, NULL, 1),
(46, 3, 6, 'SST501', NULL, NULL, 1),
(47, 3, 6, 'SST502', NULL, NULL, 1),
(48, 3, 6, 'SST503', NULL, NULL, 1),
(49, 3, 6, 'SST504', NULL, NULL, 1),
(50, 3, 6, 'SST505', NULL, NULL, 1),
(51, 3, 6, 'SST506', NULL, NULL, 1),
(52, 3, 6, 'SST507', NULL, NULL, 1),
(53, 3, 6, 'SST508', NULL, NULL, 1),
(54, 3, 6, 'SST509', NULL, NULL, 1),
(55, 3, 6, 'SST510', NULL, NULL, 1),
(56, 3, 7, 'DD401', NULL, NULL, 1),
(57, 3, 7, 'DD402', NULL, NULL, 1),
(58, 3, 7, 'DD403', NULL, NULL, 1),
(59, 3, 7, 'DD404', NULL, NULL, 1),
(60, 3, 7, 'DD405', NULL, NULL, 1),
(61, 3, 7, 'DD406', NULL, NULL, 1),
(62, 3, 7, 'DD407', NULL, NULL, 1),
(63, 3, 7, 'DD408', NULL, NULL, 1),
(64, 3, 7, 'DD409', NULL, NULL, 1),
(65, 3, 7, 'DD410', NULL, NULL, 1),
(66, 4, 13, 'PF251', NULL, NULL, 1),
(67, 4, 13, 'PF252', NULL, NULL, 1),
(68, 4, 13, 'PF253', NULL, NULL, 1),
(69, 4, 13, 'PF254', NULL, NULL, 1),
(70, 4, 13, 'PF255', NULL, NULL, 1),
(71, 4, 13, 'PF256', NULL, NULL, 1),
(72, 4, 13, 'PF257', NULL, NULL, 1),
(73, 4, 13, 'PF258', NULL, NULL, 1),
(74, 4, 13, 'PF259', NULL, NULL, 1),
(75, 4, 13, 'PF260', NULL, NULL, 1),
(76, 4, 12, 'DT211', NULL, NULL, 1),
(77, 4, 12, 'DT212', NULL, NULL, 1),
(78, 4, 12, 'DT213', NULL, NULL, 1),
(79, 4, 12, 'DT214', NULL, NULL, 1),
(80, 4, 12, 'DT215', NULL, NULL, 1),
(81, 4, 11, 'DD216', NULL, NULL, 1),
(82, 4, 11, 'DD217', NULL, NULL, 1),
(83, 4, 11, 'DD218', NULL, NULL, 1),
(84, 4, 11, 'DD219', NULL, NULL, 1),
(85, 4, 11, 'DD220', NULL, NULL, 1),
(86, 4, 10, 'SS221', NULL, NULL, 1),
(87, 4, 10, 'SS222', NULL, NULL, 1),
(88, 4, 10, 'SS223', NULL, NULL, 1),
(89, 4, 10, 'SS224', NULL, NULL, 1),
(90, 4, 10, 'SS225', NULL, NULL, 1),
(91, 3, 9, 'PF251', NULL, NULL, 1),
(92, 3, 9, 'PF252', NULL, NULL, 1),
(93, 3, 9, 'PF253', NULL, NULL, 1),
(94, 3, 9, 'PF254', NULL, NULL, 1),
(95, 3, 9, 'PF255', NULL, NULL, 1),
(96, 3, 8, 'DT301', NULL, NULL, 1),
(97, 3, 8, 'DT302', NULL, NULL, 1),
(98, 3, 8, 'DT303', NULL, NULL, 1),
(99, 3, 8, 'DT304', NULL, NULL, 1),
(100, 3, 8, 'DT305', NULL, NULL, 1),
(101, 3, 8, 'DT306', NULL, NULL, 1),
(102, 3, 8, 'DT307', NULL, NULL, 1),
(103, 3, 8, 'DT308', NULL, NULL, 1),
(104, 3, 8, 'DT309', NULL, NULL, 1),
(105, 3, 8, 'DT310', NULL, NULL, 1),
(106, 2, 37, 'PF101', NULL, NULL, 1),
(107, 2, 37, 'PF102', NULL, NULL, 1),
(108, 2, 37, 'PF103', NULL, NULL, 1),
(109, 2, 37, 'PF104', NULL, NULL, 1),
(110, 2, 37, 'PF105', NULL, NULL, 1),
(111, 2, 36, 'DT201', NULL, NULL, 1),
(112, 2, 36, 'DT202', NULL, NULL, 1),
(113, 2, 36, 'DT203', NULL, NULL, 1),
(114, 2, 36, 'DT204', NULL, NULL, 1),
(115, 2, 36, 'DT205', NULL, NULL, 1),
(116, 2, 36, 'DT206', NULL, NULL, 1),
(117, 2, 36, 'DT207', NULL, NULL, 1),
(118, 2, 36, 'DT208', NULL, NULL, 1),
(119, 2, 36, 'DT209', NULL, NULL, 1),
(120, 2, 36, 'DT210', NULL, NULL, 1),
(121, 2, 35, 'DD211', NULL, NULL, 1),
(122, 2, 35, 'DD212', NULL, NULL, 1),
(123, 2, 35, 'DD213', NULL, NULL, 1),
(124, 2, 35, 'DD214', NULL, NULL, 1),
(125, 2, 35, 'DD215', NULL, NULL, 1),
(126, 2, 35, 'DD216', NULL, NULL, 1),
(127, 2, 35, 'DD217', NULL, NULL, 1),
(128, 2, 35, 'DD218', NULL, NULL, 1),
(129, 2, 35, 'DD219', NULL, NULL, 1),
(130, 2, 35, 'DD220', NULL, NULL, 1),
(131, 2, 34, 'SS231', NULL, NULL, 1),
(132, 2, 34, 'SS232', NULL, NULL, 1),
(133, 2, 34, 'SS233', NULL, NULL, 1),
(134, 2, 34, 'SS234', NULL, NULL, 1),
(135, 2, 34, 'SS235', NULL, NULL, 1),
(136, 2, 34, 'SS236', NULL, NULL, 1),
(137, 2, 34, 'SS237', NULL, NULL, 1),
(138, 2, 34, 'SS238', NULL, NULL, 1),
(139, 2, 34, 'SS239', NULL, NULL, 1),
(140, 2, 34, 'SS240', NULL, NULL, 1),
(141, 5, 16, 'DT651', NULL, NULL, 1),
(142, 5, 16, 'DT652', NULL, NULL, 1),
(143, 5, 16, 'DT653', NULL, NULL, 1),
(144, 5, 16, 'DT654', NULL, NULL, 1),
(145, 5, 16, 'DT655', NULL, NULL, 1),
(146, 5, 16, 'DT656', NULL, NULL, 1),
(147, 5, 16, 'DT657', NULL, NULL, 1),
(148, 5, 16, 'DT658', NULL, NULL, 1),
(149, 5, 16, 'DT659', NULL, NULL, 1),
(150, 5, 16, 'DT660', NULL, NULL, 1),
(151, 5, 15, 'DD641', NULL, NULL, 1),
(152, 5, 15, 'DD642', NULL, NULL, 1),
(153, 5, 15, 'DD643', NULL, NULL, 1),
(154, 5, 15, 'DD644', NULL, NULL, 1),
(155, 5, 15, 'DD645', NULL, NULL, 1),
(156, 5, 15, 'DD646', NULL, NULL, 1),
(157, 5, 15, 'DD647', NULL, NULL, 1),
(158, 5, 15, 'DD648', NULL, NULL, 1),
(159, 5, 15, 'DD649', NULL, NULL, 1),
(160, 5, 15, 'DD650', NULL, NULL, 1),
(161, 6, 21, 'LF451', NULL, NULL, 1),
(162, 6, 21, 'LF452', NULL, NULL, 1),
(163, 6, 21, 'LF453', NULL, NULL, 1),
(164, 6, 21, 'LF454', NULL, NULL, 1),
(165, 6, 21, 'LF455', NULL, NULL, 1),
(166, 6, 21, 'LF456', NULL, NULL, 1),
(167, 6, 21, 'LF457', NULL, NULL, 1),
(168, 6, 21, 'LF458', NULL, NULL, 1),
(169, 6, 21, 'LF459', NULL, NULL, 1),
(170, 6, 21, 'LF460', NULL, NULL, 1),
(171, 6, 19, 'LD421', NULL, NULL, 1),
(172, 6, 19, 'LD422', NULL, NULL, 1),
(173, 6, 19, 'LD423', NULL, NULL, 1),
(174, 6, 19, 'LD424', NULL, NULL, 1),
(175, 6, 19, 'LD425', NULL, NULL, 1),
(176, 6, 19, 'LD426', NULL, NULL, 1),
(177, 6, 19, 'LD427', NULL, NULL, 1),
(178, 6, 19, 'LD428', NULL, NULL, 1),
(179, 6, 19, 'LD429', NULL, NULL, 1),
(180, 6, 19, 'LD430', NULL, NULL, 1),
(181, 7, 24, 'DT301', NULL, NULL, 1),
(182, 7, 24, 'DT302', NULL, NULL, 1),
(183, 7, 24, 'DT303', NULL, NULL, 1),
(184, 7, 24, 'DT304', NULL, NULL, 1),
(185, 7, 24, 'DT305', NULL, NULL, 1),
(186, 7, 24, 'DT306', NULL, NULL, 1),
(187, 7, 24, 'DT307', NULL, NULL, 1),
(188, 7, 24, 'DT308', NULL, NULL, 1),
(189, 7, 24, 'DT309', NULL, NULL, 1),
(190, 7, 24, 'DT310', NULL, NULL, 1),
(191, 8, 27, 'DD251', NULL, NULL, 1),
(192, 8, 27, 'DD252', NULL, NULL, 1),
(193, 8, 27, 'DD253', NULL, NULL, 1),
(194, 8, 27, 'DD254', NULL, NULL, 1),
(195, 8, 27, 'DD255', NULL, NULL, 1),
(196, 8, 27, 'DD256', NULL, NULL, 1),
(197, 8, 27, 'DD257', NULL, NULL, 1),
(198, 8, 27, 'DD258', NULL, NULL, 1),
(199, 8, 27, 'DD259', NULL, NULL, 1),
(200, 8, 27, 'DD260', NULL, NULL, 1),
(201, 9, 30, 'SS201', NULL, NULL, 1),
(202, 9, 30, 'SS202', NULL, NULL, 1),
(203, 9, 30, 'SS203', NULL, NULL, 1),
(204, 9, 30, 'SS204', NULL, NULL, 1),
(205, 9, 30, 'SS205', NULL, NULL, 1),
(206, 9, 30, 'SS206', NULL, NULL, 1),
(207, 9, 30, 'SS207', NULL, NULL, 1),
(208, 9, 30, 'SS208', NULL, NULL, 1),
(209, 9, 30, 'SS209', NULL, NULL, 1),
(210, 9, 30, 'SS210', NULL, NULL, 1),
(211, 9, 30, 'SS211', NULL, NULL, 1),
(212, 9, 30, 'SS212', NULL, NULL, 1),
(213, 9, 30, 'SS213', NULL, NULL, 1),
(214, 9, 30, 'SS214', NULL, NULL, 1),
(215, 9, 30, 'SS215', NULL, NULL, 1),
(216, 9, 30, 'SS216', NULL, NULL, 1),
(217, 9, 30, 'SS217', NULL, NULL, 1),
(218, 9, 30, 'SS218', NULL, NULL, 1),
(219, 9, 30, 'SS219', NULL, NULL, 1),
(220, 9, 30, 'SS220', NULL, NULL, 1),
(221, 10, 39, 'DD351', NULL, '2026-04-23 07:31:26', 1),
(222, 10, 39, 'DD352', NULL, NULL, 1),
(223, 10, 39, 'DD353', NULL, NULL, 1),
(224, 10, 39, 'DD354', NULL, NULL, 1),
(225, 10, 39, 'DD355', NULL, NULL, 1),
(226, 10, 39, 'DD356', NULL, NULL, 1),
(227, 10, 39, 'DD357', NULL, NULL, 1),
(228, 10, 39, 'DD358', NULL, NULL, 1),
(229, 10, 39, 'DD359', NULL, NULL, 1),
(230, 10, 39, 'DD360', NULL, NULL, 1),
(231, 11, 45, 'SF351', NULL, NULL, 1),
(232, 11, 45, 'SF352', NULL, NULL, 1),
(233, 11, 45, 'SF353', NULL, NULL, 1),
(234, 11, 45, 'SF354', NULL, NULL, 1),
(235, 11, 45, 'SF355', NULL, NULL, 1),
(236, 11, 45, 'SF356', NULL, NULL, 1),
(237, 11, 45, 'SF357', NULL, NULL, 1),
(238, 11, 45, 'SF358', NULL, NULL, 1),
(239, 11, 45, 'SF359', NULL, NULL, 1),
(240, 11, 45, 'SF360', NULL, NULL, 1),
(241, 11, 43, 'DD361', NULL, NULL, 1),
(242, 11, 43, 'DD362', NULL, NULL, 1),
(243, 11, 43, 'DD363', NULL, NULL, 1),
(244, 11, 43, 'DD364', NULL, NULL, 1),
(245, 11, 43, 'DD365', NULL, NULL, 1),
(246, 11, 43, 'DD366', NULL, NULL, 1),
(247, 11, 43, 'DD367', NULL, NULL, 1),
(248, 11, 43, 'DD368', NULL, NULL, 1),
(249, 11, 43, 'DD369', NULL, NULL, 1),
(250, 11, 43, 'DD370', NULL, NULL, 1),
(251, 6, 20, 'DT851', NULL, NULL, 1),
(252, 6, 20, 'DT852', NULL, NULL, 1),
(253, 6, 20, 'DT853', NULL, NULL, 1),
(254, 6, 20, 'DT854', NULL, NULL, 1),
(255, 6, 20, 'DT855', NULL, NULL, 1),
(256, 6, 20, 'DT856', NULL, NULL, 1),
(257, 6, 20, 'DT857', NULL, NULL, 1),
(258, 6, 20, 'DT858', NULL, NULL, 1),
(259, 6, 20, 'DT859', NULL, NULL, 1),
(260, 6, 20, 'DT860', NULL, NULL, 1),
(261, 6, 18, 'SSI981', NULL, NULL, 1),
(262, 6, 18, 'SSI982', NULL, NULL, 1),
(263, 6, 18, 'SSI983', NULL, NULL, 1),
(264, 6, 18, 'SSI984', NULL, NULL, 1),
(265, 6, 18, 'SSI985', NULL, NULL, 1),
(266, 6, 18, 'SSI986', NULL, NULL, 1),
(267, 6, 18, 'SSI987', NULL, NULL, 1),
(268, 6, 18, 'SSI988', NULL, NULL, 1),
(269, 6, 18, 'SSI989', NULL, NULL, 1),
(270, 6, 18, 'SSI990', NULL, NULL, 1),
(271, 6, 18, 'SSI991', NULL, NULL, 1),
(272, 6, 18, 'SSI992', NULL, NULL, 1),
(273, 6, 18, 'SSI993', NULL, NULL, 1),
(274, 6, 18, 'SSI994', NULL, NULL, 1),
(275, 6, 18, 'SSI995', NULL, NULL, 1),
(276, 6, 18, 'SSI996', NULL, NULL, 1),
(277, 6, 18, 'SSI997', NULL, NULL, 1),
(278, 6, 18, 'SSI998', NULL, NULL, 1),
(279, 6, 18, 'SSI999', NULL, NULL, 1),
(280, 6, 18, 'SSI1000', NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `room_details`
--

CREATE TABLE `room_details` (
  `id` bigint UNSIGNED NOT NULL,
  `hotel_id` bigint UNSIGNED NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` int UNSIGNED NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `max_adults` tinyint DEFAULT '2',
  `max_children` tinyint DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `room_details`
--

INSERT INTO `room_details` (`id`, `hotel_id`, `type`, `category`, `description`, `qty`, `price`, `created_at`, `updated_at`, `max_adults`, `max_children`) VALUES
(1, 1, 'double', 'deluxe', 'A luxurious five-star hotel offering elegant rooms, world-class dining, and premium hospitality. Located in the upscale area of Ahmedabad, it provides modern amenities, refined interiors, and a comfortable stay for both business and leisure travelers.', 10, 4299.00, '2026-04-22 06:44:21', '2026-04-22 06:44:21', 2, 1),
(2, 1, 'family', 'premium', 'Spacious family premium rooms designed for comfort and convenience, featuring multiple beds, modern amenities, and elegant interiors. Ideal for families, offering ample space, high-speed WiFi, and a relaxing ambiance for a pleasant stay.', 5, 6899.00, '2026-04-22 07:35:19', '2026-04-22 08:06:04', 4, 2),
(3, 1, 'single', 'standard', 'Comfortable single standard room ideal for solo travelers, featuring a cozy bed, essential amenities, and a clean, functional layout. Perfect for a simple and budget-friendly stay with all basic conveniences.', 15, 1299.00, '2026-04-22 07:38:07', '2026-04-22 08:05:53', 1, 0),
(4, 1, 'twin', 'deluxe', 'Comfortable twin deluxe room featuring two well-appointed beds, stylish interiors, and modern amenities. Ideal for friends or colleagues, offering extra space, high-speed WiFi, and a relaxing, premium stay experience.', 10, 2999.00, '2026-04-22 07:46:48', '2026-04-22 08:05:44', 2, 0),
(5, 1, 'double', 'luxury', 'Elegant double deluxe room featuring a spacious double bed, modern furnishings, and premium amenities. Designed for comfort and relaxation, it offers ample space, high-speed WiFi, and a refined stay experience for couples or business travelers.', 5, 7899.00, '2026-04-22 07:59:13', '2026-04-22 08:05:34', 2, 0),
(6, 3, 'single', 'standard', 'Comfortable single standard room designed for solo travelers with essential amenities and a cozy setup.', 10, 1200.00, '2026-04-22 08:16:16', '2026-04-22 08:59:59', 1, 0),
(7, 3, 'double', 'deluxe', 'Elegant double deluxe room with modern interiors, spacious bedding, and premium comfort for couples.', 10, 2200.00, '2026-04-22 08:16:16', '2026-04-22 09:01:02', 2, 1),
(8, 3, 'twin', 'deluxe', 'Well-designed twin deluxe room with two separate beds and contemporary amenities for shared stays.', 10, 2000.00, '2026-04-22 08:16:16', '2026-04-22 09:10:59', 2, 1),
(9, 3, 'family', 'premium', 'Spacious family premium room offering multiple beds and ample space for a comfortable family stay.', 5, 3500.00, '2026-04-22 08:16:16', '2026-04-22 09:10:34', 4, 2),
(10, 4, 'single', 'standard', 'Neat single standard room with a compact layout, ideal for solo guests seeking comfort and simplicity.', 5, 1300.00, '2026-04-22 08:16:16', '2026-04-22 09:10:13', 1, 0),
(11, 4, 'double', 'deluxe', 'Stylish double deluxe room featuring elegant décor and enhanced amenities for a relaxing experience.', 5, 2300.00, '2026-04-22 08:16:16', '2026-04-22 09:09:41', 2, 1),
(12, 4, 'twin', 'deluxe', 'Twin deluxe room with modern furnishings and two cozy beds, perfect for friends or colleagues.', 5, 2100.00, '2026-04-22 08:16:16', '2026-04-22 09:06:15', 2, 1),
(13, 4, 'family', 'premium', 'Large family premium room designed with extra space and comfort for group or family stays.', 10, 3600.00, '2026-04-22 08:16:16', '2026-04-22 09:05:28', 4, 2),
(14, 5, 'single', 'standard', 'Simple and comfortable single room with essential facilities and a peaceful ambiance.', 0, 1100.00, '2026-04-22 08:16:16', '2026-04-22 08:16:16', 1, 0),
(15, 5, 'double', 'deluxe', 'Modern double deluxe room with refined interiors and comfortable bedding for a premium stay.', 10, 2100.00, '2026-04-22 08:16:16', '2026-04-22 09:13:22', 2, 1),
(16, 5, 'twin', 'deluxe', 'Twin room offering two beds, clean interiors, and all necessary amenities for convenience.', 10, 1900.00, '2026-04-22 08:16:16', '2026-04-22 09:13:02', 2, 1),
(17, 5, 'family', 'premium', 'Family premium room with spacious layout and multiple beds, ideal for longer stays.', 0, 3300.00, '2026-04-22 08:16:16', '2026-04-22 08:16:16', 4, 2),
(18, 6, 'single', 'suite', 'Well-maintained single room with a cozy bed and functional setup for solo travelers.', 20, 2000.00, '2026-04-22 08:16:16', '2026-04-22 11:39:22', 1, 0),
(19, 6, 'double', 'luxury', 'Luxurious double deluxe room with high-end furnishings and premium facilities.', 10, 5000.00, '2026-04-22 08:16:16', '2026-04-22 09:14:13', 2, 1),
(20, 6, 'twin', 'deluxe', 'Premium twin room with stylish décor and comfortable bedding for a refined stay.', 10, 4500.00, '2026-04-22 08:16:16', '2026-04-22 11:39:01', 2, 1),
(21, 6, 'family', 'luxury', 'Large luxury family room with ample space and top-tier amenities.', 10, 8000.00, '2026-04-22 08:16:16', '2026-04-22 09:13:48', 4, 2),
(22, 7, 'single', 'suite', 'Classic single room with elegant interiors and essential modern comforts.', 0, 25.00, '2026-04-22 08:16:16', '2026-04-22 08:37:03', 1, 0),
(23, 7, 'double', 'deluxe', 'Sophisticated double deluxe room with premium bedding and luxurious ambiance.', 0, 30.00, '2026-04-22 08:16:16', '2026-04-22 08:36:22', 2, 1),
(24, 7, 'twin', 'deluxe', 'Elegant twin room offering two beds and a refined interior experience.', 10, 45.00, '2026-04-22 08:16:16', '2026-04-22 09:14:49', 2, 1),
(25, 7, 'family', 'standard', 'Spacious family suite with luxurious setup and comfortable multi-bed arrangement.', 0, 79.00, '2026-04-22 08:16:16', '2026-04-22 08:35:14', 4, 2),
(26, 8, 'single', 'standard', 'Compact single room with modern design and essential amenities for a pleasant stay.', 0, 25.00, '2026-04-22 08:16:16', '2026-04-22 08:53:36', 1, 0),
(27, 8, 'double', 'deluxe', 'Double deluxe room featuring premium interiors and a comfortable double bed.', 10, 60.00, '2026-04-22 08:16:16', '2026-04-22 09:15:07', 2, 1),
(28, 8, 'twin', 'deluxe', 'Twin deluxe room with contemporary style and two well-appointed beds.', 0, 55.00, '2026-04-22 08:16:16', '2026-04-22 08:51:47', 2, 1),
(29, 8, 'family', 'premium', 'Spacious family room with modern features and ample space for group comfort.', 0, 90.00, '2026-04-22 08:16:16', '2026-04-22 08:51:28', 4, 2),
(30, 9, 'single', 'standard', 'Comfortable single room with modern amenities and a relaxing atmosphere.', 20, 22.00, '2026-04-22 08:16:16', '2026-04-22 09:15:33', 1, 0),
(31, 9, 'double', 'deluxe', 'Premium double deluxe room with stylish interiors and enhanced comfort.', 0, 58.00, '2026-04-22 08:16:16', '2026-04-22 08:54:48', 2, 1),
(32, 9, 'twin', 'deluxe', 'Twin room with modern design and two cozy beds for shared accommodation.', 0, 52.00, '2026-04-22 08:16:16', '2026-04-22 08:54:34', 2, 1),
(33, 9, 'family', 'premium', 'Luxury family room with spacious design and high-end facilities.', 0, 95.00, '2026-04-22 08:16:16', '2026-04-22 08:54:15', 4, 2),
(34, 2, 'single', 'standard', 'Comfortable single room with modern amenities and a relaxing atmosphere.', 10, 2200.00, '2026-04-22 08:16:16', '2026-04-22 09:12:25', 1, 0),
(35, 2, 'double', 'deluxe', 'Premium double deluxe room with stylish interiors and enhanced comfort.', 10, 5800.00, '2026-04-22 08:16:16', '2026-04-22 09:12:09', 2, 1),
(36, 2, 'twin', 'deluxe', 'Twin room with modern design and two cozy beds for shared accommodation.', 10, 5200.00, '2026-04-22 08:16:16', '2026-04-22 09:11:51', 2, 1),
(37, 2, 'family', 'premium', 'Luxury family room with spacious design and high-end facilities.', 5, 9500.00, '2026-04-22 08:16:16', '2026-04-22 09:11:32', 4, 2),
(38, 10, 'single', 'standard', 'Simple single room with clean setup and essential facilities for short stays.', 0, 1800.00, '2026-04-22 08:16:16', '2026-04-22 08:16:16', 1, 0),
(39, 10, 'double', 'deluxe', 'Double deluxe room offering modern comfort and elegant interiors.', 15, 40.00, '2026-04-22 08:16:16', '2026-04-23 07:37:34', 2, 1),
(40, 10, 'twin', 'deluxe', 'Twin room with functional design and comfortable bedding for two guests.', 0, 34.00, '2026-04-22 08:16:16', '2026-04-22 08:57:24', 2, 1),
(41, 10, 'family', 'premium', 'Family room designed with extra space and multiple sleeping arrangements.', 0, 70.00, '2026-04-22 08:16:16', '2026-04-22 08:57:12', 4, 2),
(42, 11, 'single', 'standard', 'Comfortable single room with cozy interiors and essential amenities for solo travelers.', 0, 1800.00, '2026-04-22 08:16:16', '2026-04-22 08:56:50', 1, 0),
(43, 11, 'double', 'deluxe', 'Elegant double deluxe room with modern furnishings and a relaxing ambiance.', 10, 3200.00, '2026-04-22 08:16:16', '2026-04-22 09:16:39', 2, 1),
(44, 11, 'twin', 'deluxe', 'Twin deluxe room with two beds and modern amenities for a comfortable shared stay.', 0, 3000.00, '2026-04-22 08:16:16', '2026-04-22 08:56:20', 2, 1),
(45, 11, 'family', 'suite', 'Spacious family premium room with multiple beds and a comfortable environment for families.', 10, 3500.00, '2026-04-22 08:16:16', '2026-04-22 09:16:28', 4, 2),
(50, 3, 'double', 'deluxe', 'ddddddd', 0, 101.00, '2026-04-23 06:39:15', '2026-04-23 06:40:30', 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('ByaeqrzbjgbZGY7jvVrTvSaZVFKPFXS6MKJTB81p', 2, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoia0VDbDdWdVF3MEZqOXZQaWxwRXc3cDB5SmdxNEpFQmxTTWgxaHcwaSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9ob3RlbHMiO3M6NToicm91dGUiO3M6MTg6ImFkbWluLmhvdGVscy5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7czoxNToicGlja2VkX2Rpc2NvdW50IjtzOjY6IlRFU1Q5MCI7czoyODoicm9vbV9kZXRhaWxfYWRkX3ByZXZpb3VzX3VybCI7czo0NjoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2FkbWluL2NhdGVnb3JpZXMvNTAvZWRpdCI7fQ==', 1777031951),
('VAdMDTZra228TdfAgR35qOXBrwBufb2OcQxJHCvG', 10, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YToxMjp7czo2OiJfdG9rZW4iO3M6NDA6Ilpva3VaWnh5YXRIcGw1QkxlZ24wSTBhcGpwTHZPbmVBVXhvQnBROXAiO3M6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjEyMjoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL3Jvb21zP2FkdWx0cz0xJmNoZWNrX2luPTIwMjYtMDQtMjUmY2hlY2tfb3V0PTIwMjYtMDQtMjcmY2hpbGRyZW49MCZjaXR5X2lkPTE0MSZtYXhfcHJpY2U9Jm1pbl9wcmljZT0iO3M6NToicm91dGUiO3M6MTI6ImNsaWVudC5yb29tcyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6MzoidXJsIjthOjE6e3M6ODoiaW50ZW5kZWQiO3M6Mjk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9wcm9maWxlIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTA7czoxMzoidXNlcl9sb2NhdGlvbiI7YTo2OntzOjEwOiJjb3VudHJ5X2lkIjtzOjI6IjcwIjtzOjEyOiJjb3VudHJ5X2NvZGUiO3M6MjoiSU4iO3M6MTM6ImN1cnJlbmN5X2NvZGUiO3M6MzoiSU5SIjtzOjE1OiJjdXJyZW5jeV9zeW1ib2wiO3M6Mzoi4oK5IjtzOjk6ImNpdHlfbmFtZSI7czo2OiJSYWprb3QiO3M6NzoiY2l0eV9pZCI7czozOiIxNDEiO31zOjE2OiJib29raW5nX2NoZWNrX2luIjtzOjEwOiIyMDI2LTA0LTI1IjtzOjE3OiJib29raW5nX2NoZWNrX291dCI7czoxMDoiMjAyNi0wNC0yNyI7czoxMToiY2hhbmdlZFN0YXkiO2I6MTtzOjQ6InN0YXkiO2E6Nzp7czo1OiJpdGVtcyI7YToxOntpOjMwO2E6ODp7czoyOiJpZCI7aTozMDtzOjU6InRpdGxlIjtzOjE3OiJTdGFuZGFyZCAtIFNpbmdsZSI7czoxMDoiYmFzZV9wcmljZSI7czo1OiIyMi4wMCI7czoyMDoiY29udmVydGVkX2Jhc2VfcHJpY2UiO2Q6NTUzLjA1O3M6NToicHJpY2UiO2Q6MjI7czoxNToiY29udmVydGVkX3ByaWNlIjtkOjU1My4wNTtzOjg6InF1YW50aXR5IjtpOjE7czo1OiJpbWFnZSI7czo1NzoiYXNzZXRzL3Jvb21zLzdhdTdoTmJWMXl3dFdKZDdiYTBpUEFWR010U01ZOEthNzdmVXRFQ0suanBnIjt9fXM6ODoiaG90ZWxfaWQiO2k6OTtzOjExOiJkaXNjb3VudF9pZCI7TjtzOjEzOiJvZmZlcl9tZXNzYWdlIjtOO3M6MTU6ImN1cnJlbmN5X3N5bWJvbCI7czozOiLigrkiO3M6MTY6InRlbXBfZGlzY291bnRfaWQiO2k6NztzOjE5OiJsYXN0X2Rpc2NvdW50X2Vycm9yIjtzOjMyOiJDb3Vwb24gY2FuJ3QgYmUgQXBwbGllZCBHbG9iYWxseSI7fXM6MTY6ImJvb2tpbmdfaG90ZWxfaWQiO2k6OTtzOjE1OiJwaWNrZWRfZGlzY291bnQiO3M6NjoiVEVTVDkwIjt9', 1777033413);

-- --------------------------------------------------------

--
-- Table structure for table `states`
--

CREATE TABLE `states` (
  `id` bigint UNSIGNED NOT NULL,
  `country_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `states`
--

INSERT INTO `states` (`id`, `country_id`, `name`) VALUES
(1, 70, 'Andhra Pradesh'),
(2, 70, 'Arunachal Pradesh'),
(3, 70, 'Assam'),
(4, 70, 'Bihar'),
(5, 70, 'Chhattisgarh'),
(6, 70, 'Goa'),
(7, 70, 'Gujarat'),
(8, 70, 'Haryana'),
(9, 70, 'Himachal Pradesh'),
(10, 70, 'Jharkhand'),
(11, 70, 'Karnataka'),
(12, 70, 'Kerala'),
(13, 70, 'Madhya Pradesh'),
(14, 70, 'Maharashtra'),
(15, 70, 'Manipur'),
(16, 70, 'Meghalaya'),
(17, 70, 'Mizoram'),
(18, 70, 'Nagaland'),
(19, 70, 'Odisha'),
(20, 70, 'Punjab'),
(21, 70, 'Rajasthan'),
(22, 70, 'Sikkim'),
(23, 70, 'Tamil Nadu'),
(24, 70, 'Telangana'),
(25, 70, 'Tripura'),
(26, 70, 'Uttar Pradesh'),
(27, 70, 'Uttarakhand'),
(28, 70, 'West Bengal'),
(29, 70, 'Andaman and Nicobar Islands'),
(30, 70, 'Chandigarh'),
(31, 70, 'Dadra and Nagar Haveli and Daman and Diu'),
(32, 70, 'Delhi'),
(33, 70, 'Jammu and Kashmir'),
(34, 70, 'Ladakh'),
(35, 70, 'Lakshadweep'),
(36, 70, 'Puducherry'),
(197, 152, 'California'),
(198, 152, 'Texas'),
(199, 152, 'Florida'),
(200, 152, 'New York'),
(201, 151, 'England'),
(202, 151, 'Scotland'),
(203, 151, 'Wales'),
(204, 151, 'Northern Ireland'),
(205, 30, 'Ontario'),
(206, 30, 'Quebec'),
(207, 30, 'British Columbia'),
(208, 30, 'Alberta'),
(209, 8, 'New South Wales'),
(210, 8, 'Victoria'),
(211, 8, 'Queensland'),
(212, 8, 'Western Australia'),
(213, 60, 'Bavaria'),
(214, 60, 'Berlin'),
(215, 60, 'Hamburg'),
(216, 60, 'Hesse'),
(217, 56, 'Île-de-France'),
(218, 56, 'Provence-Alpes-Côte d\'Azur'),
(219, 56, 'Occitanie'),
(220, 56, 'Nouvelle-Aquitaine'),
(221, 35, 'Guangdong'),
(222, 35, 'Beijing'),
(223, 35, 'Shanghai'),
(224, 35, 'Sichuan'),
(225, 78, 'Tokyo'),
(226, 78, 'Osaka'),
(227, 78, 'Hokkaido'),
(228, 78, 'Kyoto'),
(229, 23, 'São Paulo'),
(230, 23, 'Rio de Janeiro'),
(231, 23, 'Bahia'),
(232, 23, 'Minas Gerais'),
(233, 126, 'Moscow'),
(234, 126, 'Saint Petersburg'),
(235, 126, 'Tatarstan'),
(236, 126, 'Novosibirsk'),
(237, 100, 'Jalisco'),
(238, 100, 'Nuevo León'),
(239, 100, 'Puebla'),
(240, 100, 'Yucatán'),
(241, 76, 'Lombardy'),
(242, 76, 'Lazio'),
(243, 76, 'Tuscany'),
(244, 76, 'Sicily'),
(245, 137, 'Catalonia'),
(246, 137, 'Madrid'),
(247, 137, 'Andalusia'),
(248, 137, 'Valencia'),
(249, 147, 'Istanbul'),
(250, 147, 'Ankara'),
(251, 147, 'Izmir'),
(252, 147, 'Antalya'),
(253, 128, 'Riyadh'),
(254, 128, 'Makkah'),
(255, 128, 'Eastern Province'),
(256, 128, 'Asir'),
(257, 150, 'Dubai'),
(258, 150, 'Abu Dhabi'),
(259, 150, 'Sharjah'),
(260, 150, 'Ajman'),
(261, 117, 'Punjab'),
(262, 117, 'Sindh'),
(263, 117, 'Khyber Pakhtunkhwa'),
(264, 117, 'Balochistan'),
(265, 13, 'Dhaka'),
(266, 13, 'Chittagong'),
(267, 13, 'Khulna'),
(268, 13, 'Rajshahi'),
(269, 71, 'Java'),
(270, 71, 'Sumatra'),
(271, 71, 'Bali'),
(272, 71, 'Kalimantan'),
(273, 135, 'Gauteng'),
(274, 135, 'Western Cape'),
(275, 135, 'KwaZulu-Natal'),
(276, 135, 'Eastern Cape');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('admin','manager','customer') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'customer',
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `phone`, `role`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Ibrahim Garana', 'ibrahim.g@techxperts.co.in', NULL, '$2y$12$l6WIo/grHKLaH.pgh/YLxeSfkJbLcdD7JfdV5zmytZwZ4Yq2KHjFS', '9725847556', 'customer', NULL, '2026-04-22 04:46:15', '2026-04-22 04:46:15', NULL),
(2, 'Admin of System', 'admin@example.com', NULL, '$2y$12$dcU9Zw29JHBakCGN2cO6pu1cz3eWp9eYLB8eE4P0ClA4OEgYoNOr2', '9100292900', 'admin', NULL, '2026-04-22 05:12:51', '2026-04-22 05:12:51', NULL),
(3, 'Manager of Hotel X', 'manager@example.com', NULL, '$2y$12$eg5r/OPYwpfQ4Mu53aipPu4FJoZeLAxOw9uNBQecQsZ5brNGr0Fpq', '9004587766', 'manager', NULL, '2026-04-22 05:15:14', '2026-04-22 05:15:14', NULL),
(4, 'Jeramy Larson', 'allison.cole@example.org', '2026-04-22 05:16:51', '$2y$12$iu/tmeLgGbLtR2b5x3O/Xe.Dvri6jT6OrbSL7HqFUON1PYH8N1Rj.', NULL, 'customer', 'qKlQZv065v', '2026-04-22 05:16:51', '2026-04-22 05:16:51', NULL),
(5, 'Toy Crist DVM', 'roxanne.mitchell@example.org', '2026-04-22 05:16:51', '$2y$12$iu/tmeLgGbLtR2b5x3O/Xe.Dvri6jT6OrbSL7HqFUON1PYH8N1Rj.', NULL, 'customer', 'p0VoxfuJif', '2026-04-22 05:16:51', '2026-04-22 05:16:51', NULL),
(6, 'Ruben Kling', 'lubowitz.emiliano@example.net', '2026-04-22 05:16:51', '$2y$12$iu/tmeLgGbLtR2b5x3O/Xe.Dvri6jT6OrbSL7HqFUON1PYH8N1Rj.', NULL, 'customer', '5SEmefSLst', '2026-04-22 05:16:51', '2026-04-22 05:16:51', NULL),
(7, 'Curt Hansen', 'xbahringer@example.com', '2026-04-22 05:16:51', '$2y$12$iu/tmeLgGbLtR2b5x3O/Xe.Dvri6jT6OrbSL7HqFUON1PYH8N1Rj.', NULL, 'customer', 'urzCdtiGEK', '2026-04-22 05:16:51', '2026-04-22 05:16:51', NULL),
(8, 'Mr. Kraig Frami', 'camryn.ohara@example.org', '2026-04-22 05:16:51', '$2y$12$iu/tmeLgGbLtR2b5x3O/Xe.Dvri6jT6OrbSL7HqFUON1PYH8N1Rj.', NULL, 'customer', 'BvQDURZDGd', '2026-04-22 05:16:51', '2026-04-22 05:16:51', NULL),
(9, 'Jaren Vandervort III', 'ggreen@example.com', '2026-04-22 05:16:51', '$2y$12$iu/tmeLgGbLtR2b5x3O/Xe.Dvri6jT6OrbSL7HqFUON1PYH8N1Rj.', NULL, 'customer', '0PIjtIGBP0', '2026-04-22 05:16:51', '2026-04-22 05:16:51', NULL),
(10, 'Neoma Little', 'ashlee57@example.com', '2026-04-22 05:16:51', '$2y$12$iu/tmeLgGbLtR2b5x3O/Xe.Dvri6jT6OrbSL7HqFUON1PYH8N1Rj.', NULL, 'customer', 'DDDUkmQUNP', '2026-04-22 05:16:51', '2026-04-22 05:16:51', NULL),
(11, 'Prof. Carson Schuppe', 'knicolas@example.com', '2026-04-22 05:16:51', '$2y$12$iu/tmeLgGbLtR2b5x3O/Xe.Dvri6jT6OrbSL7HqFUON1PYH8N1Rj.', NULL, 'customer', '17Sz6e2Z3v', '2026-04-22 05:16:51', '2026-04-22 05:16:51', NULL),
(12, 'Dr. Joany Pollich Jr.', 'hassie.okon@example.net', '2026-04-22 05:16:51', '$2y$12$iu/tmeLgGbLtR2b5x3O/Xe.Dvri6jT6OrbSL7HqFUON1PYH8N1Rj.', NULL, 'customer', 'MtfZDhq7t7', '2026-04-22 05:16:51', '2026-04-22 05:16:51', NULL),
(13, 'Michele Welch', 'alyce.abshire@example.org', '2026-04-22 05:16:51', '$2y$12$iu/tmeLgGbLtR2b5x3O/Xe.Dvri6jT6OrbSL7HqFUON1PYH8N1Rj.', NULL, 'customer', 'mjuf3EKIf1', '2026-04-22 05:16:51', '2026-04-22 05:16:51', NULL),
(14, 'Tyrell Schulist', 'lwaelchi@example.net', '2026-04-22 05:16:51', '$2y$12$iu/tmeLgGbLtR2b5x3O/Xe.Dvri6jT6OrbSL7HqFUON1PYH8N1Rj.', NULL, 'customer', 'dXxV0mAPME', '2026-04-22 05:16:51', '2026-04-22 05:16:51', NULL),
(15, 'Derek Tremblay', 'kaleb.bode@example.com', '2026-04-22 05:16:51', '$2y$12$iu/tmeLgGbLtR2b5x3O/Xe.Dvri6jT6OrbSL7HqFUON1PYH8N1Rj.', NULL, 'customer', 'Zd0suImCWG', '2026-04-22 05:16:51', '2026-04-22 05:16:51', NULL),
(16, 'Anabelle Hammes', 'rogahn.braden@example.net', '2026-04-22 05:16:51', '$2y$12$iu/tmeLgGbLtR2b5x3O/Xe.Dvri6jT6OrbSL7HqFUON1PYH8N1Rj.', NULL, 'customer', 'WVSTGsZaSk', '2026-04-22 05:16:52', '2026-04-22 05:16:52', NULL),
(17, 'Jess Purdy', 'marques.spencer@example.org', '2026-04-22 05:16:51', '$2y$12$iu/tmeLgGbLtR2b5x3O/Xe.Dvri6jT6OrbSL7HqFUON1PYH8N1Rj.', NULL, 'customer', 'ScxsAs8Y5G', '2026-04-22 05:16:52', '2026-04-22 05:16:52', NULL),
(18, 'Mrs. Trycia Lakin', 'herman51@example.com', '2026-04-22 05:16:51', '$2y$12$iu/tmeLgGbLtR2b5x3O/Xe.Dvri6jT6OrbSL7HqFUON1PYH8N1Rj.', NULL, 'customer', 'w4ISayzchV', '2026-04-22 05:16:52', '2026-04-22 05:16:52', NULL),
(19, 'Obie Kshlerin', 'braxton85@example.org', '2026-04-22 05:16:51', '$2y$12$iu/tmeLgGbLtR2b5x3O/Xe.Dvri6jT6OrbSL7HqFUON1PYH8N1Rj.', NULL, 'customer', 'e6OIsyGKi3', '2026-04-22 05:16:52', '2026-04-22 05:16:52', NULL),
(20, 'Armand Effertz', 'mortimer.breitenberg@example.net', '2026-04-22 05:16:51', '$2y$12$iu/tmeLgGbLtR2b5x3O/Xe.Dvri6jT6OrbSL7HqFUON1PYH8N1Rj.', NULL, 'customer', 'dBVreRxF6Z', '2026-04-22 05:16:52', '2026-04-22 05:16:52', NULL),
(21, 'Mr. Stewart Pagac II', 'pfeffer.leonie@example.net', '2026-04-22 05:16:51', '$2y$12$iu/tmeLgGbLtR2b5x3O/Xe.Dvri6jT6OrbSL7HqFUON1PYH8N1Rj.', NULL, 'customer', '6rqgpJWnex', '2026-04-22 05:16:52', '2026-04-22 05:16:52', NULL),
(22, 'Dr. Clement Marvin', 'teagan.lebsack@example.org', '2026-04-22 05:16:51', '$2y$12$iu/tmeLgGbLtR2b5x3O/Xe.Dvri6jT6OrbSL7HqFUON1PYH8N1Rj.', NULL, 'customer', 'vbnJolfwCp', '2026-04-22 05:16:52', '2026-04-22 05:16:52', NULL),
(23, 'Lorenz Daugherty DDS', 'hsmith@example.net', '2026-04-22 05:16:51', '$2y$12$iu/tmeLgGbLtR2b5x3O/Xe.Dvri6jT6OrbSL7HqFUON1PYH8N1Rj.', NULL, 'customer', '29zmEB8Ivh', '2026-04-22 05:16:52', '2026-04-22 05:16:52', NULL),
(24, 'test', 'user1@example.com', NULL, '$2y$12$Xo5dWhWKfKxvNBDPAx.yVuO.7umdgUZdX6.2Vg6Qt0jwaBBTtCNXK', '685461', 'customer', NULL, '2026-04-23 12:36:58', '2026-04-23 12:36:58', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

CREATE TABLE `user_profiles` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `gender` enum('male','female','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `city_id` bigint UNSIGNED DEFAULT NULL,
  `pincode` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_profiles`
--

INSERT INTO `user_profiles` (`id`, `user_id`, `gender`, `dob`, `address`, `city_id`, `pincode`, `id_type`, `id_number`, `created_at`, `updated_at`) VALUES
(1, 1, 'male', '2004-06-07', 'Jetpur', 27, '360370', 'passport', '2004005250056', '2026-04-22 05:08:20', '2026-04-22 05:08:20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `amenities`
--
ALTER TABLE `amenities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `booking_items`
--
ALTER TABLE `booking_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_items_booking_id_foreign` (`booking_id`),
  ADD KEY `booking_items_room_id_foreign` (`room_id`);

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
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cities_state_id_foreign` (`state_id`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `discounts`
--
ALTER TABLE `discounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `discounts_country_id_foreign` (`country_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `hotels`
--
ALTER TABLE `hotels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hotels_city_id_foreign` (`city_id`);

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `images_imageable_type_imageable_id_index` (`imageable_type`,`imageable_id`);

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
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payments_transaction_id_unique` (`session_id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reviews_hotel_id_foreign` (`hotel_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rooms_hotel_id_room_number_unique` (`hotel_id`,`room_number`),
  ADD KEY `rooms_room_detail_id_foreign` (`room_detail_id`);

--
-- Indexes for table `room_details`
--
ALTER TABLE `room_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_details_hotel_id_foreign` (`hotel_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `states`
--
ALTER TABLE `states`
  ADD PRIMARY KEY (`id`),
  ADD KEY `states_country_id_foreign` (`country_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_profiles_user_id_foreign` (`user_id`),
  ADD KEY `user_profiles_city_id_foreign` (`city_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `amenities`
--
ALTER TABLE `amenities`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `booking_items`
--
ALTER TABLE `booking_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=165;

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=160;

--
-- AUTO_INCREMENT for table `discounts`
--
ALTER TABLE `discounts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hotels`
--
ALTER TABLE `hotels`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `images`
--
ALTER TABLE `images`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=294;

--
-- AUTO_INCREMENT for table `room_details`
--
ALTER TABLE `room_details`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `states`
--
ALTER TABLE `states`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=277;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `user_profiles`
--
ALTER TABLE `user_profiles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking_items`
--
ALTER TABLE `booking_items`
  ADD CONSTRAINT `booking_items_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_items_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);

--
-- Constraints for table `cities`
--
ALTER TABLE `cities`
  ADD CONSTRAINT `cities_state_id_foreign` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `discounts`
--
ALTER TABLE `discounts`
  ADD CONSTRAINT `discounts_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hotels`
--
ALTER TABLE `hotels`
  ADD CONSTRAINT `hotels_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_hotel_id_foreign` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_hotel_id_foreign` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rooms_room_detail_id_foreign` FOREIGN KEY (`room_detail_id`) REFERENCES `room_details` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `room_details`
--
ALTER TABLE `room_details`
  ADD CONSTRAINT `room_details_hotel_id_foreign` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `states`
--
ALTER TABLE `states`
  ADD CONSTRAINT `states_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD CONSTRAINT `user_profiles_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`),
  ADD CONSTRAINT `user_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
