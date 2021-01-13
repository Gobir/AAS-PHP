-- phpMyAdmin SQL Dump
-- version 4.7.9
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 13, 2021 at 01:10 PM
-- Server version: 10.2.36-MariaDB
-- PHP Version: 7.2.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `devouss_face`
--

-- --------------------------------------------------------

--
-- Table structure for table `absent_leave`
--

CREATE TABLE `absent_leave` (
  `id` int(11) UNSIGNED NOT NULL,
  `sick_days` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `leave_days` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `normal_start_time` varchar(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT '08:00',
  `normal_end_time` varchar(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT '17:00',
  `deduction_hour` decimal(10,2) NOT NULL,
  `basic_salary` decimal(10,2) NOT NULL,
  `bonus` decimal(10,2) NOT NULL,
  `currency` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  `tax` decimal(4,4) NOT NULL,
  `pension` decimal(4,4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) UNSIGNED NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `recovery_token` text DEFAULT NULL,
  `expiry_token` varchar(255) DEFAULT NULL,
  `used_token` varchar(1) DEFAULT 'N',
  `role` varchar(255) NOT NULL DEFAULT 'admin',
  `admin_quota` int(11) NOT NULL,
  `admin_subscription` varchar(4) NOT NULL DEFAULT 'Free',
  `user_price` decimal(10,2) NOT NULL,
  `latitude` decimal(10,8) NOT NULL DEFAULT 0.00000000,
  `longitude` decimal(11,8) NOT NULL DEFAULT 0.00000000,
  `premises` decimal(8,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `app_errors`
--

CREATE TABLE `app_errors` (
  `id` int(11) NOT NULL,
  `data` longtext NOT NULL,
  `email` varchar(255) NOT NULL,
  `time` varchar(255) NOT NULL,
  `date` varchar(255) NOT NULL,
  `timezone` varchar(255) NOT NULL,
  `errorType` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `faces`
--

CREATE TABLE `faces` (
  `id` int(11) NOT NULL,
  `image_path` text NOT NULL,
  `email` varchar(255) NOT NULL,
  `light_version_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `light`
--

CREATE TABLE `light` (
  `id` int(11) NOT NULL,
  `last_action_date` datetime NOT NULL DEFAULT current_timestamp(),
  `last_action` varchar(3) NOT NULL,
  `light_version_id` varchar(255) NOT NULL,
  `date_limit` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `order_status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `order_reference_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `order_price` decimal(10,2) NOT NULL,
  `order_currency` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'USD',
  `order_payee_email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `order_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `order_qte` decimal(10,2) NOT NULL,
  `order_payer_firstname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `order_payer_lastname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `order_intent` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `order_create_time` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `order_raw_json` longtext COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `payment_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `payment_status` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `payment_reference_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `payment_full_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `payment_address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `payment_amount` decimal(10,2) DEFAULT NULL,
  `payment_captures_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `payment_captures_status` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `payment_captures_reason` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `payment_currency_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `payment_seller_protection` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `payment_create_time` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `payment_update_time` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `payment_raw_json` longtext COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `paypal_keys`
--

CREATE TABLE `paypal_keys` (
  `id` int(11) UNSIGNED NOT NULL,
  `sandbox_client_id` varchar(255) NOT NULL,
  `sandbox_secret_id` varchar(255) NOT NULL,
  `livebox_client_id` varchar(255) NOT NULL,
  `livebox_secret_id` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tracking`
--

CREATE TABLE `tracking` (
  `id` int(11) UNSIGNED NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `time` varchar(255) DEFAULT NULL,
  `date` varchar(255) DEFAULT NULL,
  `timestamp` int(11) DEFAULT NULL,
  `timezone` varchar(255) DEFAULT NULL,
  `action` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `profile_img` text DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `tracking_time_interval` int(3) DEFAULT 10,
  `created_by` varchar(255) NOT NULL,
  `light_version_user` varchar(3) NOT NULL DEFAULT 'No'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `absent_leave`
--
ALTER TABLE `absent_leave`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `app_errors`
--
ALTER TABLE `app_errors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faces`
--
ALTER TABLE `faces`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `light`
--
ALTER TABLE `light`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `paypal_keys`
--
ALTER TABLE `paypal_keys`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tracking`
--
ALTER TABLE `tracking`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `absent_leave`
--
ALTER TABLE `absent_leave`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `app_errors`
--
ALTER TABLE `app_errors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `faces`
--
ALTER TABLE `faces`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `light`
--
ALTER TABLE `light`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `paypal_keys`
--
ALTER TABLE `paypal_keys`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tracking`
--
ALTER TABLE `tracking`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=689;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
