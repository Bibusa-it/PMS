<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

require_once '../db_connection.php';

$total_users_sql = "SELECT COUNT(*) AS total_users FROM users";
$total_users_result = $conn->query($total_users_sql);
$total_users = $total_users_result->fetch_assoc()['total_users'] ?? 0;

$total_vehicles_sql = "SELECT COUNT(*) AS total_vehicles FROM vehicles";
$total_vehicles_result = $conn->query($total_vehicles_sql);
$total_vehicles = $total_vehicles_result->fetch_assoc()['total_vehicles'] ?? 0;

$parking_spots_sql = "
    SELECT COUNT(*) AS total_spots, 
           SUM(is_available) AS available_spots,
           SUM(is_reserved) AS reserved_spots
    FROM parking_spots";
$parking_spots_result = $conn->query($parking_spots_sql);

if ($parking_spots_result) {
    $parking_spots = $parking_spots_result->fetch_assoc();
    $total_spots = $parking_spots['total_spots'] ?? 0;
    $available_spots = $parking_spots['available_spots'] ?? 0;
    $reserved_spots = $parking_spots['reserved_spots'] ?? 0;
    $occupied_spots = $total_spots - $available_spots - $reserved_spots;
} else {
    $total_spots = $available_spots = $reserved_spots = $occupied_spots = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="CSS1/dashboard.css">
</head>
<body>

<div class="sidebar">
    <h4>Admin Dashboard</h4>
    <a href="admin_dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
    
    <a href="#" id="parkingManagementLink"><i class="bi bi-car-front"></i> Manage Parking Spots</a>
    <div class="submenu" id="parkingManagementSubmenu" style="display: none;">
        <a href="add_parking_spot.php"><i class="bi bi-plus-circle"></i> Add Parking Spot</a>
        <a href="view_parking_spot.php"><i class="bi bi-eye"></i> View Parking Spots</a>
        <a href="view_usage.php"><i class="bi bi-bar-chart"></i> View Usage</a>
    </div>

    <a href="#" id="userManagementLink"><i class="bi bi-people"></i> User Management</a>
    <div class="submenu" id="userManagementSubmenu" style="display: none;">
        <a href="manage_users.php"><i class="bi bi-person"></i> Manage Users</a>
        <a href="add_users.php"><i class="bi bi-person-plus"></i> Add User</a>
    </div>
    <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<div class="main-content">
    <h1>Welcome Admin, <?php echo $_SESSION['username']; ?>!</h1>
    <div class="row">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <p class="card-text"><?php echo $total_users; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Parking Spots</h5>
                    <p class="card-text"><?php echo $total_spots; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Vehicles</h5>
                    <p class="card-text"><?php echo $total_vehicles; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info mb-3">
                <div class="card-body">
                    <h5 class="card-title">Available Spots</h5>
                    <p class="card-text"><?php echo $available_spots; ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="usersubmenu.js"></script>
<script src="parkingsubmenu.js"></script>
</body>
</html>
