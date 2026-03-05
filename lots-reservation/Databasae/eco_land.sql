-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 14, 2026 at 06:13 AM
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
-- Database: `eco_land`
--

-- --------------------------------------------------------

--
-- Table structure for table `lots`
--

CREATE TABLE `lots` (
  `id` int(11) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `phase_id` int(11) DEFAULT NULL,
  `block_no` varchar(10) DEFAULT NULL,
  `lot_no` varchar(10) DEFAULT NULL,
  `area` decimal(10,2) DEFAULT NULL,
  `price_per_sqm` decimal(10,2) DEFAULT NULL,
  `total_price` decimal(12,2) DEFAULT NULL,
  `status` enum('AVAILABLE','RESERVED','SOLD') DEFAULT 'AVAILABLE',
  `property_overview` text DEFAULT NULL,
  `coordinates` varchar(50) DEFAULT NULL,
  `lot_image` varchar(255) DEFAULT 'default_lot.jpg',
  `property_type` enum('Subdivision','Lot','Land','Farm','Shop','Business') DEFAULT 'Subdivision',
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lots`
--

INSERT INTO `lots` (`id`, `location`, `phase_id`, `block_no`, `lot_no`, `area`, `price_per_sqm`, `total_price`, `status`, `property_overview`, `coordinates`, `lot_image`, `property_type`, `latitude`, `longitude`) VALUES
(1, NULL, 1, 'A', '1', 100.00, 5000.00, 500000.00, 'RESERVED', NULL, NULL, 'default_lot.jpg', 'Subdivision', NULL, NULL),
(8, 'ergt', NULL, '12', '132', 13.00, 123.00, 1599.00, 'RESERVED', 'qswdefrgthyjukikhmnbvcx', NULL, '1770977471_back.png', '', 14.96326134, 120.63754721);

-- --------------------------------------------------------

--
-- Table structure for table `lot_gallery`
--

CREATE TABLE `lot_gallery` (
  `id` int(11) NOT NULL,
  `lot_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lot_gallery`
--

INSERT INTO `lot_gallery` (`id`, `lot_id`, `image_path`) VALUES
(1, 8, '1770977471_0_618915627_909738964940314_969639640789004154_n.jpg'),
(2, 8, '1770977471_1_621145074_1385022632744250_3609463222790889375_n.jpg'),
(3, 8, '1770977471_2_Gemini_Generated_Image_4i5kcd4i5kcd4i5k.png');

-- --------------------------------------------------------

--
-- Table structure for table `phases`
--

CREATE TABLE `phases` (
  `id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `phases`
--

INSERT INTO `phases` (`id`, `name`) VALUES
(1, 'Guagua (Pampanga)'),
(2, 'Minalin (Pampanga)'),
(3, 'Porac'),
(4, 'Lubao'),
(5, 'San Fernando'),
(6, 'Angeles City');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `lot_id` int(11) DEFAULT NULL,
  `reservation_date` datetime DEFAULT current_timestamp(),
  `status` enum('PENDING','APPROVED','CANCELLED') DEFAULT 'PENDING',
  `payment_proof` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `buyer_address` text DEFAULT NULL,
  `valid_id_file` varchar(255) DEFAULT NULL,
  `selfie_with_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `user_id`, `contact_number`, `email`, `birth_date`, `lot_id`, `reservation_date`, `status`, `payment_proof`, `notes`, `buyer_address`, `valid_id_file`, `selfie_with_id`) VALUES
(2, 1, '09667785843', NULL, '2001-06-14', 8, '2026-02-13 18:35:35', 'PENDING', '1770978935_621145074_1385022632744250_3609463222790889375_n.jpg', NULL, 'Talang pulungmasle', '1770978935_618915627_909738964940314_969639640789004154_n.jpg', '1770978935_Gemini_Generated_Image_4i5kcd4i5kcd4i5k.png');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('ADMIN','AGENT','BUYER') DEFAULT 'BUYER',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `password`, `phone`, `role`, `created_at`) VALUES
(1, 'Super Admin', 'admin@test.com', '0192023a7bbd73250516f069df18b500', NULL, 'ADMIN', '2026-02-10 12:14:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `lots`
--
ALTER TABLE `lots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `phase_id` (`phase_id`);

--
-- Indexes for table `lot_gallery`
--
ALTER TABLE `lot_gallery`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lot_id` (`lot_id`);

--
-- Indexes for table `phases`
--
ALTER TABLE `phases`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `lot_id` (`lot_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `lots`
--
ALTER TABLE `lots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `lot_gallery`
--
ALTER TABLE `lot_gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `phases`
--
ALTER TABLE `phases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `lots`
--
ALTER TABLE `lots`
  ADD CONSTRAINT `lots_ibfk_1` FOREIGN KEY (`phase_id`) REFERENCES `phases` (`id`);

--
-- Constraints for table `lot_gallery`
--
ALTER TABLE `lot_gallery`
  ADD CONSTRAINT `lot_gallery_ibfk_1` FOREIGN KEY (`lot_id`) REFERENCES `lots` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`lot_id`) REFERENCES `lots` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
