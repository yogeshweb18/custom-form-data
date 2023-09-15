-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 15, 2023 at 05:22 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wp-houzeo`
--

-- --------------------------------------------------------

--
-- Table structure for table `wp_custom_form_data`
--

CREATE TABLE `wp_custom_form_data` (
  `id` mediumint(9) NOT NULL,
  `name` text NOT NULL,
  `email` text NOT NULL,
  `phone` text NOT NULL,
  `comment` text NOT NULL,
  `submission_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `wp_custom_form_data`
--

INSERT INTO `wp_custom_form_data` (`id`, `name`, `email`, `phone`, `comment`, `submission_date`) VALUES
(1, 'yogeshweb18', 'de@gmail.com', '(231) 232-2334', 'sdfsfd', '2023-09-14 22:49:59'),
(2, 'yogeshweb18', 'de@gmail.com', '(231) 232-2334', 'sfgsgs', '2023-09-14 23:08:24'),
(3, 'yogeshweb18', 'sada@gmail.com', '(231) 232-2334', 'asfaf', '2023-09-15 08:45:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `wp_custom_form_data`
--
ALTER TABLE `wp_custom_form_data`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `wp_custom_form_data`
--
ALTER TABLE `wp_custom_form_data`
  MODIFY `id` mediumint(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
