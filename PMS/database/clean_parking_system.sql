-- Parking Management System - Clean Database Setup
-- This file will create a fresh database structure

-- Disable foreign key checks temporarily
SET FOREIGN_KEY_CHECKS = 0;

-- Drop existing tables if they exist (in correct order)
DROP TABLE IF EXISTS `usage`;
DROP TABLE IF EXISTS `parking_routes`;
DROP TABLE IF EXISTS `parking_spots`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `vehicles`;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- Create users table
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `vehicle_number` varchar(50) DEFAULT NULL,
  `vehicle_type` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create vehicles table
CREATE TABLE `vehicles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vehicle_type` varchar(50) NOT NULL,
  `vehicle_number` varchar(50) NOT NULL,
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `vehicle_number` (`vehicle_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create parking_spots table
CREATE TABLE `parking_spots` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `vehicle_type` enum('car','bike','scooter') DEFAULT NULL,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_reserved` tinyint(1) NOT NULL DEFAULT 0,
  `capacities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`capacities`)),
  `availability` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`availability`)),
  PRIMARY KEY (`id`),
  KEY `idx_name` (`name`),
  KEY `idx_vehicle_type` (`vehicle_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create parking_routes table
CREATE TABLE `parking_routes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `start_node` int(11) NOT NULL,
  `end_node` int(11) NOT NULL,
  `distance` double NOT NULL,
  PRIMARY KEY (`id`),
  KEY `start_node` (`start_node`),
  KEY `end_node` (`end_node`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create usage table
CREATE TABLE `usage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parking_spot_id` int(11) DEFAULT NULL,
  `is_occupied` tinyint(1) DEFAULT 0,
  `usage_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `parking_spot_id` (`parking_spot_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default admin user
INSERT INTO `users` (`username`, `first_name`, `last_name`, `password`, `role`) VALUES
('admin', 'Admin', 'User', '0192023a7bbd73250516f069df18b500', 'admin');

-- Insert default test user
INSERT INTO `users` (`username`, `first_name`, `last_name`, `password`, `role`) VALUES
('user', 'Test', 'User', '6ad14ba9986e3615423dfca256d04e3f', 'user');

-- Insert sample parking spots
INSERT INTO `parking_spots` (`name`, `vehicle_type`, `latitude`, `longitude`, `is_available`, `is_reserved`, `capacities`, `availability`) VALUES
('New Road Complex', NULL, 27.7017, 85.3103, 1, 0, '{"car":10,"bike":15,"scooter":20}', '{"car":0,"bike":0,"scooter":0}'),
('RB Complex', NULL, 27.7021, 85.3097, 1, 1, '{"car":12,"bike":20,"scooter":16}', '{"car":0,"bike":0,"scooter":0}'),
('Ranjana Complex', NULL, 27.703, 85.311, 1, 0, '{"car":15,"bike":20,"scooter":20}', '{"car":0,"bike":0,"scooter":0}');

-- Insert sample parking routes
INSERT INTO `parking_routes` (`start_node`, `end_node`, `distance`) VALUES
(1, 2, 0.5),
(1, 3, 1.8),
(2, 3, 2);

-- Insert sample vehicle
INSERT INTO `vehicles` (`vehicle_type`, `vehicle_number`) VALUES
('Bike', 'BA 3-3454'); 