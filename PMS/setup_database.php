<?php
// Database setup script
echo "<h2>Database Setup Script</h2>";

// Connect to database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "parking_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "✓ Connected to database<br>";

// Disable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// Drop existing tables
$tables = ['usage', 'parking_routes', 'parking_spots', 'users', 'vehicles'];
foreach ($tables as $table) {
    $conn->query("DROP TABLE IF EXISTS `$table`");
    echo "✓ Dropped table: $table<br>";
}

// Re-enable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

// Create users table
$sql = "CREATE TABLE `users` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

if ($conn->query($sql) === TRUE) {
    echo "✓ Users table created successfully<br>";
} else {
    echo "✗ Error creating users table: " . $conn->error . "<br>";
}

// Create other tables
$sql = "CREATE TABLE `vehicles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vehicle_type` varchar(50) NOT NULL,
  `vehicle_number` varchar(50) NOT NULL,
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `vehicle_number` (`vehicle_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

if ($conn->query($sql) === TRUE) {
    echo "✓ Vehicles table created successfully<br>";
} else {
    echo "✗ Error creating vehicles table: " . $conn->error . "<br>";
}

$sql = "CREATE TABLE `parking_spots` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `vehicle_type` enum('car','bike','scooter') DEFAULT NULL,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_reserved` tinyint(1) NOT NULL DEFAULT 0,
  `capacities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `availability` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_name` (`name`),
  KEY `idx_vehicle_type` (`vehicle_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

if ($conn->query($sql) === TRUE) {
    echo "✓ Parking spots table created successfully<br>";
} else {
    echo "✗ Error creating parking spots table: " . $conn->error . "<br>";
}

// Insert default users
$admin_password = md5('admin123');
$user_password = md5('user123');

$sql = "INSERT INTO users (username, first_name, last_name, password, role) VALUES 
('admin', 'Admin', 'User', '$admin_password', 'admin'),
('user', 'Test', 'User', '$user_password', 'user')";

if ($conn->query($sql) === TRUE) {
    echo "✓ Default users created successfully<br>";
} else {
    echo "✗ Error creating users: " . $conn->error . "<br>";
}

// Insert sample data
$sql = "INSERT INTO parking_spots (name, vehicle_type, latitude, longitude, is_available, is_reserved, capacities, availability) VALUES 
('New Road Complex', NULL, 27.7017, 85.3103, 1, 0, '{\"car\":10,\"bike\":15,\"scooter\":20}', '{\"car\":0,\"bike\":0,\"scooter\":0}'),
('RB Complex', NULL, 27.7021, 85.3097, 1, 1, '{\"car\":12,\"bike\":20,\"scooter\":16}', '{\"car\":0,\"bike\":0,\"scooter\":0}'),
('Ranjana Complex', NULL, 27.703, 85.311, 1, 0, '{\"car\":15,\"bike\":20,\"scooter\":20}', '{\"car\":0,\"bike\":0,\"scooter\":0}')";

if ($conn->query($sql) === TRUE) {
    echo "✓ Sample parking spots created successfully<br>";
} else {
    echo "✗ Error creating parking spots: " . $conn->error . "<br>";
}

$sql = "INSERT INTO vehicles (vehicle_type, vehicle_number) VALUES ('Bike', 'BA 3-3454')";

if ($conn->query($sql) === TRUE) {
    echo "✓ Sample vehicle created successfully<br>";
} else {
    echo "✗ Error creating vehicle: " . $conn->error . "<br>";
}

// Verify setup
echo "<h3>Verification:</h3>";
$result = $conn->query("SELECT username, role FROM users");
if ($result) {
    echo "✓ Users in database:<br>";
    while ($row = $result->fetch_assoc()) {
        echo "- " . $row['username'] . " (" . $row['role'] . ")<br>";
    }
}

$result = $conn->query("SELECT COUNT(*) as count FROM parking_spots");
if ($result) {
    $row = $result->fetch_assoc();
    echo "✓ Parking spots: " . $row['count'] . "<br>";
}

$result = $conn->query("SELECT COUNT(*) as count FROM vehicles");
if ($result) {
    $row = $result->fetch_assoc();
    echo "✓ Vehicles: " . $row['count'] . "<br>";
}

$conn->close();

echo "<hr>";
echo "<h3>Setup Complete!</h3>";
echo "<p><a href='simple_login.php' class='btn btn-primary'>Test Login</a></p>";
echo "<p><a href='index.php' class='btn btn-secondary'>Go to Main Login</a></p>";
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
.btn { display: inline-block; padding: 10px 20px; margin: 5px; text-decoration: none; border-radius: 5px; }
.btn-primary { background-color: #007bff; color: white; }
.btn-secondary { background-color: #6c757d; color: white; }
</style> 