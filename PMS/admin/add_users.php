<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: index.php");
    exit();
}

require_once '../db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = md5($_POST['password']); // Hash the password
    $role = $_POST['role'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $vehicle_number = $_POST['vehicle_number'];
    $vehicle_type = $_POST['vehicle_type'];

    // Validate vehicle number format for Nepal
    if (!preg_match('/^[0-9]{1,4}-[A-Za-z]{1,2}-[0-9]{1,4}$/', $vehicle_number)) {
        die("Invalid vehicle number format. Please use the format: XXXX-XX-XXXX");
    }

    // Insert new user into the database
    $sql = "INSERT INTO users (username, password, role, first_name, last_name, vehicle_number, vehicle_type) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssssss', $username, $password, $role, $first_name, $last_name, $vehicle_number, $vehicle_type);
    $stmt->execute();

    // Redirect to manage users page after adding
    header("Location: manage_users.php?status=success");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User - PMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: lightgray;
            font-family: 'Arial', sans-serif;
        }
        .container {
            margin-top: 30px;
            max-width: 400px; /* Set a smaller maximum width for the container */
            border-radius: 8px;
            background-color: #ffffff;
            padding: 16px; /* Keep padding moderate */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            margin-left: auto; 
            margin-right: auto; 
            margin-bottom: 30px; 
        }
        h3 {
            color: #343a40;
            margin-bottom: 12px;
            font-weight: bold;
            text-align: center; 
        }
        .btn {
            margin-top: 10px;
        }
        .mb-3 {
            margin-bottom: 8px; /* Reduce bottom margin for form groups */
        }
        .form-control {
            height: 38px; /* Adjust height to make inputs smaller */
        }
    </style>
</head>
<body>
    <div class="container">
        <h3>Add User</h3>
        <form action="" method="post">
            <div class="mb-3">
                <label for="first_name" class="form-label">First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" required>
            </div>
            <div class="mb-3">
                <label for="last_name" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" required>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select class=" form-select" id="role" name="role" required>
                    <option value="user">User </option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="vehicle_number" class="form-label">Vehicle Number</label>
                <input type="text" class="form-control" id="vehicle_number" name="vehicle_number" required>
            </div>
            <div class="mb-3">
                <label for="vehicle_type" class="form-label">Vehicle Type</label>
                <select class="form-select" id="vehicle_type" name="vehicle_type" required>
                    <option value="car">Car</option>
                    <option value="motorcycle">Motorcycle</option>
                    <option value="truck">Truck</option>
                    <option value="van">Van</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add User</button>
            <a href="manage_users.php" class="btn btn-secondary">Back to Manage Users</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>