-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 12, 2025 at 06:56 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wattway`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_admin`
--

CREATE TABLE `tbl_admin` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_admin`
--

INSERT INTO `tbl_admin` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2a$12$p7fijCIN5QhQLr1QsW6P8.aBxNYkvbxMwXDjqmMslUfwjEovaY0sK');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_availability`
--

CREATE TABLE `tbl_availability` (
  `availability_id` int(11) NOT NULL,
  `station_id` int(11) DEFAULT NULL,
  `is_available` enum('Yes','No') DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `assigned_driver_id` int(11) DEFAULT NULL,
  `otp` varchar(10) DEFAULT NULL,
  `slot_time` time DEFAULT NULL,
  `visit_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_bookings`
--

CREATE TABLE `tbl_bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `station_id` int(11) NOT NULL,
  `booking_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `visit_date` date NOT NULL,
  `visit_time` time DEFAULT NULL,
  `payment_status` varchar(20) DEFAULT 'Pending',
  `payment_method` varchar(20) DEFAULT NULL,
  `otp` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_drivers`
--

CREATE TABLE `tbl_drivers` (
  `driver_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_drivers`
--

INSERT INTO `tbl_drivers` (`driver_id`, `name`, `email`, `phone`, `password`, `created_at`) VALUES
(101, 'faheem', 'faheem@gmail.com', '7346836938', 'faheem', '2025-07-20 18:30:00'),
(104, 'advaid', 'advaid@gmail.com', '8129466660', '$2y$10$JZipvIPBXmMIr2Ja.TgXR.T2NG/mznv8s6GcX/wu890a2ay.jxnAK', '2025-07-28 09:54:30'),
(105, 'Ament', 'ament@gmail.com', '9876556789', '$2y$10$YnqAZWwwDCNOC0jlSRjJ5ervsQLz7KpaqiPkHNP.ZelrG8h9eQHjy', '2025-08-25 10:15:31'),
(107, 'Rajappan', 'rajappan@gmail.com', NULL, '$2y$10$4eDTygpgdATucglKFCLZEuq4y0f0bhRiHlvTYmjzQbYs6B89UL97.', '2025-09-11 09:58:12'),
(108, 'Benny', 'benny@gmail.com', NULL, '$2y$10$rFLEzRF.9wcjR/M06D/Xb.EhUAXI5V//p/aGddq6ObaupJ/mPkKRu', '2025-09-11 10:02:16'),
(110, 'adeem', 'adeem@g.c', NULL, '$2y$10$nTeeQUyUZvfdtnEs7a2SDuvUOvNnLoQCETL1.w92opfwpmeIFhAby', '2025-10-01 04:33:30'),
(111, 'faruk', 'shahnawazfaheem378@gmail.com', NULL, '$2y$10$p1KpWCzXwYHYAnnbCL4dUejkNl8RlelY2hJ6jnPPEHwbeHxXuqxde', '2025-10-09 05:38:07');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_feedback`
--

CREATE TABLE `tbl_feedback` (
  `feedback_id` int(11) NOT NULL,
  `station_id` int(11) NOT NULL,
  `driver_id` int(11) DEFAULT NULL,
  `rating` int(1) NOT NULL DEFAULT 0,
  `feedback_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_feedback`
--

INSERT INTO `tbl_feedback` (`feedback_id`, `station_id`, `driver_id`, `rating`, `feedback_text`, `created_at`) VALUES
(1, 8, NULL, 0, 'Good', '2025-10-09 04:03:45'),
(2, 9, NULL, 0, 'Good', '2025-10-09 04:03:57'),
(3, 9, 110, 0, 'Gooodgood', '2025-10-09 04:17:20'),
(4, 9, 110, 4, 'GoodGood', '2025-10-09 04:35:18');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_stations`
--

CREATE TABLE `tbl_stations` (
  `station_id` int(11) NOT NULL,
  `station_name` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `status` enum('pending','active','inactive','rejected') NOT NULL DEFAULT 'pending',
  `plug_type` enum('Type1','Type2','CCS','CHAdeMO','GB/T') DEFAULT NULL,
  `charging_speed` enum('slow','fast','rapid') DEFAULT NULL,
  `pricing` decimal(6,2) DEFAULT NULL COMMENT 'Price per kWh in ₹',
  `opening_time` time DEFAULT NULL,
  `closing_time` time DEFAULT NULL,
  `ratings_avg` float DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_stations`
--

INSERT INTO `tbl_stations` (`station_id`, `station_name`, `address`, `latitude`, `longitude`, `contact_number`, `status`, `plug_type`, `charging_speed`, `pricing`, `opening_time`, `closing_time`, `ratings_avg`, `added_by`, `created_at`) VALUES
(8, 'asssss', 'ba', 12.22246400, 75.12391680, '2345678901', 'active', 'CCS', 'rapid', 234.00, '11:54:00', '23:54:00', NULL, 2, '2025-09-28 23:54:29'),
(9, 'uppala', 'uppala', 12.20280320, 75.16651520, NULL, 'active', 'CCS', 'fast', 150.00, NULL, NULL, NULL, 2, '2025-10-01 16:25:41'),
(10, 'uppalass', 'ny', 12.20280320, 75.16651520, NULL, 'active', 'Type1', 'rapid', 1000.00, NULL, NULL, NULL, 2, '2025-10-01 16:43:45');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_station_operators`
--

CREATE TABLE `tbl_station_operators` (
  `operator_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `identity_proof` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_station_operators`
--

INSERT INTO `tbl_station_operators` (`operator_id`, `name`, `email`, `password`, `contact_number`, `photo`, `identity_proof`) VALUES
(1, 'ashik', 'ashik@gmail.com', '$2y$10$cw.EpBV4hz.0FkzKqsvKOOTnDhOJWgM3HrkOo54rmsVijcJyMYeBK', '8129466660', '1754904565_photo_hero.jpeg', '1754904565_id_admin.jpeg'),
(2, 'faizal', 'faizal@gmail.com', '$2y$10$j1St0ts2UkOTtKOdbJ5TQuoMemtE720xvoqopq6qzhLyPv3IbPa7u', '2345678901', 'uploads/1759051756_GOT 2019 season 8  (The pack survives).jpeg', 'uploads/1759051756_ML Programs.pdf');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_availability`
--
ALTER TABLE `tbl_availability`
  ADD PRIMARY KEY (`availability_id`),
  ADD KEY `fk_availability_station` (`station_id`),
  ADD KEY `fk_availability_user` (`updated_by`);

--
-- Indexes for table `tbl_bookings`
--
ALTER TABLE `tbl_bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `station_id` (`station_id`);

--
-- Indexes for table `tbl_drivers`
--
ALTER TABLE `tbl_drivers`
  ADD PRIMARY KEY (`driver_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `tbl_feedback`
--
ALTER TABLE `tbl_feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `station_id` (`station_id`),
  ADD KEY `driver_id` (`driver_id`);

--
-- Indexes for table `tbl_stations`
--
ALTER TABLE `tbl_stations`
  ADD PRIMARY KEY (`station_id`),
  ADD KEY `fk_name` (`added_by`);

--
-- Indexes for table `tbl_station_operators`
--
ALTER TABLE `tbl_station_operators`
  ADD PRIMARY KEY (`operator_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_availability`
--
ALTER TABLE `tbl_availability`
  MODIFY `availability_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `tbl_bookings`
--
ALTER TABLE `tbl_bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tbl_drivers`
--
ALTER TABLE `tbl_drivers`
  MODIFY `driver_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT for table `tbl_feedback`
--
ALTER TABLE `tbl_feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_stations`
--
ALTER TABLE `tbl_stations`
  MODIFY `station_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tbl_station_operators`
--
ALTER TABLE `tbl_station_operators`
  MODIFY `operator_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_availability`
--
ALTER TABLE `tbl_availability`
  ADD CONSTRAINT `fk_availability_station` FOREIGN KEY (`station_id`) REFERENCES `tbl_stations` (`station_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_availability_user` FOREIGN KEY (`updated_by`) REFERENCES `tbl_station_operators` (`operator_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `tbl_bookings`
--
ALTER TABLE `tbl_bookings`
  ADD CONSTRAINT `tbl_bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbl_drivers` (`driver_id`),
  ADD CONSTRAINT `tbl_bookings_ibfk_2` FOREIGN KEY (`station_id`) REFERENCES `tbl_stations` (`station_id`);

--
-- Constraints for table `tbl_feedback`
--
ALTER TABLE `tbl_feedback`
  ADD CONSTRAINT `tbl_feedback_ibfk_1` FOREIGN KEY (`station_id`) REFERENCES `tbl_stations` (`station_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_feedback_ibfk_2` FOREIGN KEY (`driver_id`) REFERENCES `tbl_drivers` (`driver_id`) ON DELETE SET NULL;

--
-- Constraints for table `tbl_stations`
--
ALTER TABLE `tbl_stations`
  ADD CONSTRAINT `fk_name` FOREIGN KEY (`added_by`) REFERENCES `tbl_station_operators` (`operator_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
