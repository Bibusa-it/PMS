-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 29, 2024 at 01:00 AM
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
-- Database: `parking_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `parking_routes`
--

CREATE TABLE `parking_routes` (
  `id` int(11) NOT NULL,
  `start_node` int(11) NOT NULL,
  `end_node` int(11) NOT NULL,
  `distance` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parking_routes`
--

INSERT INTO `parking_routes` (`id`, `start_node`, `end_node`, `distance`) VALUES
(1, 1, 2, 0.5),
(2, 1, 3, 1.8),
(3, 2, 3, 2);

-- --------------------------------------------------------

--
-- Table structure for table `parking_spots`
--

CREATE TABLE `parking_spots` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `vehicle_type` enum('car','bike','scooter') DEFAULT NULL,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_reserved` tinyint(1) NOT NULL DEFAULT 0,
  `capacities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`capacities`)),
  `availability` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`availability`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parking_spots`
--

INSERT INTO `parking_spots` (`id`, `name`, `vehicle_type`, `latitude`, `longitude`, `is_available`, `created_at`, `updated_at`, `is_reserved`, `capacities`, `availability`) VALUES
(1, 'New Road Complex', NULL, 27.7017, 85.3103, 1, '2024-11-28 02:15:58', '2024-11-28 02:15:58', 0, '{\"car\":10,\"bike\":15,\"scooter\":20}', '{\"car\":0,\"bike\":0,\"scooter\":0}'),
(2, 'RB Complex', NULL, 27.7021, 85.3097, 1, '2024-11-28 02:17:31', '2024-11-28 16:04:49', 1, '{\"car\":12,\"bike\":20,\"scooter\":16}', '{\"car\":0,\"bike\":0,\"scooter\":0}'),
(3, 'Ranjana Complex', NULL, 27.703, 85.311, 1, '2024-11-28 02:17:51', '2024-11-28 02:17:51', 0, '{\"car\":15,\"bike\":20,\"scooter\":20}', '{\"car\":0,\"bike\":0,\"scooter\":0}');

-- --------------------------------------------------------

--
-- Table structure for table `usage`
--

CREATE TABLE `usage` (
  `id` int(11) NOT NULL,
  `parking_spot_id` int(11) DEFAULT NULL,
  `is_occupied` tinyint(1) DEFAULT 0,
  `usage_timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `vehicle_number` varchar(50) DEFAULT NULL,
  `vehicle_type` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `first_name`, `last_name`, `password`, `role`, `created_at`, `updated_at`, `vehicle_number`, `vehicle_type`) VALUES
(1, 'admin', '', '', '0192023a7bbd73250516f069df18b500', 'admin', '2024-11-17 16:11:32', '2024-11-17 16:11:32', NULL, NULL),
(2, 'user', '', '', '6ad14ba9986e3615423dfca256d04e3f', 'user', '2024-11-17 16:11:32', '2024-11-17 16:11:32', NULL, NULL),
(18, 'user1', 'Manoj', 'Magar', '$2y$10$uJRrSgMo.estzoSPdqLEpOc2c9OeAT2moiP3wYh2EQBVWAOTn1C9S', 'user', '2024-11-24 13:33:29', '2024-11-24 13:33:29', 'BA 3-3454', 'bike');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` int(11) NOT NULL,
  `vehicle_type` varchar(50) NOT NULL,
  `vehicle_number` varchar(50) NOT NULL,
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `vehicle_type`, `vehicle_number`, `registration_date`, `updated_at`) VALUES
(1, 'Bike', 'BA 3-3454', '2024-11-24 13:32:37', '2024-11-24 13:32:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `parking_routes`
--
ALTER TABLE `parking_routes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `start_node` (`start_node`),
  ADD KEY `end_node` (`end_node`);

--
-- Indexes for table `parking_spots`
--
ALTER TABLE `parking_spots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_name` (`name`),
  ADD KEY `idx_vehicle_type` (`vehicle_type`);

--
-- Indexes for table `usage`
--
ALTER TABLE `usage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parking_spot_id` (`parking_spot_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vehicle_number` (`vehicle_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `parking_routes`
--
ALTER TABLE `parking_routes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `parking_spots`
--
ALTER TABLE `parking_spots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `usage`
--
ALTER TABLE `usage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `parking_routes`
--
ALTER TABLE `parking_routes`
  ADD CONSTRAINT `parking_routes_ibfk_1` FOREIGN KEY (`start_node`) REFERENCES `parking_spots` (`id`),
  ADD CONSTRAINT `parking_routes_ibfk_2` FOREIGN KEY (`end_node`) REFERENCES `parking_spots` (`id`);

--
-- Constraints for table `usage`
--
ALTER TABLE `usage`
  ADD CONSTRAINT `usage_ibfk_1` FOREIGN KEY (`parking_spot_id`) REFERENCES `parking_spots` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
