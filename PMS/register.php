<?php
session_start();
require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $vehicle_type = trim($_POST['vehicle_type']);
    $vehicle_number = trim($_POST['vehicle_number']);

    // Validate form fields
    if (empty($first_name) || empty($last_name) || empty($username) || empty($password) || empty($confirm_password) || empty($vehicle_type) || empty($vehicle_number)) {
        $error = 'All fields are required!';
    } elseif (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        $error = 'Password must be at least 8 characters long, include at least one number and one symbol!';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match!';
    } else {
        // Check if username already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = 'Username already exists! Please choose a different username.';
        } else {
            // Check if vehicle number exists in 'vehicles' table and matches the selected vehicle type
            $stmt = $conn->prepare("SELECT * FROM vehicles WHERE vehicle_number = ? AND vehicle_type = ?");
            $stmt->bind_param("ss", $vehicle_number, $vehicle_type);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                $error = 'Vehicle not registered or type mismatch. Please register your vehicle.';
            } else {
                // Check if the vehicle is already linked to another user
                $stmt = $conn->prepare("SELECT id FROM users WHERE vehicle_number = ?");
                $stmt->bind_param("s", $vehicle_number);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $error = 'Vehicle number already linked to an account!';
                } else {
                    // Secure password hashing
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    // Insert new user into the users table
                    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, username, password, role, vehicle_type, vehicle_number) VALUES (?, ?, ?, ?, 'user', ?, ?)");
                    $stmt->bind_param("ssssss", $first_name, $last_name, $username, $hashed_password, $vehicle_type, $vehicle_number);

                    if ($stmt->execute()) {
                        // Store the vehicle type in the session after successful registration
                        $_SESSION['vehicle_type'] = $vehicle_type;

                        $_SESSION['success'] = 'Registration successful! You can now log in.';
                        header('Location: index.php'); // Redirect to login page
                        exit();
                    } else {
                        $error = 'Error registering user: ' . $stmt->error;
                    }
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PMS - User Registration</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="CSS/registerform.css">
    <style>
        body {
            background-color: #e9ecef;
            font-family: 'Arial', sans-serif;
        }
        .registration-container {
            margin-top: 16px;
            padding: 20px;
            border: 1px solid #ced4da;
            border-radius: 10px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }
        h3 {
            font-size: 1.8rem;
            color: green;
            margin-bottom: 16px;
        }
        h4 {
            color: blue;
            font-size: 1.2rem;
            margin-bottom: 20px;
        }
        label {
            font-size: 0.9rem;
        }
        input, select {
            font-size: 0.9rem;
            margin-bottom: 10px;
        }
        button {
            font-size: 1rem;
            margin-top: 6px;
        }
        .login-button {
            margin-top: 8px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 registration-container">
                <h3 class="text-center">Parking Management System</h3>
                <h4 class="text-center mb-4">Register Here</h4>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="row mb-3">
                        <div class="col">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" name="first_name" id="first_name" class="form-control" required>
                        </div>
                        <div class="col">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" name="last_name" id="last_name" class="form-control" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" name="username" id="username" class="form-control" required>
                        </div>
                        <div class="col">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                        </div>
                        <div class="col">
                            <label for="vehicle_type" class="form-label">Vehicle Type</label>
                            <select name="vehicle_type" id="vehicle_type" class="form-select" required>
                                <option value="">Select Vehicle Type</option>
                                <option value="car">Car</option>
                                <option value="bike">Bike</option>
                                <option value="scooter">Scooter</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="vehicle_number" class="form-label">Vehicle Number</label>
                        <input type="text" name="vehicle_number" id="vehicle_number" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <button type="submit" class="btn btn-primary w-100">Register</button>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn btn-secondary w-100" onclick="location.href='register_vehicle.php'">Register Vehicle</button>
                        </div>
                    </div>
                </form>
                <div class="login-button">
                    <button type="button" class="btn btn-link" onclick="location.href='index.php'">Already have an account? Login here</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>