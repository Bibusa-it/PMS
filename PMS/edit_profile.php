<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: dashboard.php");
    exit();
}

require_once 'db_connection.php';

// Fetch user data
$stmt = $conn->prepare("SELECT first_name, last_name, username, vehicle_type, vehicle_number FROM users WHERE username = ?");
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $vehicle_type = trim($_POST['vehicle_type']);
    $vehicle_number = trim($_POST['vehicle_number']);

    $errors = [];

    // Validation
    if (empty($first_name)) {
        $errors[] = "First name is required.";
    }

    if (empty($last_name)) {
        $errors[] = "Last name is required.";
    }

    if (empty($username)) {
        $errors[] = "Username is required.";
    }

    // Password validation
    if (!empty($password)) {
        if (strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters long.";
        }
        if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[\W_]/', $password)) {
            $errors[] = "Password must contain at least one letter, one number, and one special character.";
        }
    }

    // Vehicle type validation
    if (empty($vehicle_type)) {
        $errors[] = "Vehicle type is required.";
    }

    // Vehicle number validation
    if (empty($vehicle_number) || !preg_match('/^[A-Z]{2} [0-9]{1}-[0-9]{4}$/', $vehicle_number)) {
        $errors[] = "Vehicle number must be in the format 'XX 0-0000'.";
    }

    // Check if vehicle number already exists in the database (optional)
    $check_vehicle_stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE vehicle_number = ? AND username != ?");
    $check_vehicle_stmt->bind_param("ss", $vehicle_number, $_SESSION['username']);
    $check_vehicle_stmt->execute();
    $check_vehicle_stmt->bind_result($count);
    $check_vehicle_stmt->fetch();
    
    if ($count > 0) {
        $errors[] = "Vehicle number already exists. Please choose a different one.";
    }

    // If no errors, update user info
    if (empty($errors)) {
        $hashed_password = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : null;

        $update_stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, username = ?, vehicle_type = ?, vehicle_number = ?" . ($hashed_password ? ", password = ?" : "") . " WHERE username = ?");
        if ($hashed_password) {
            $update_stmt->bind_param("sssss", $first_name, $last_name, $username, $vehicle_type, $vehicle_number, $_SESSION['username']);
        } else {
            $update_stmt->bind_param("sssss", $first_name, $last_name, $username, $vehicle_type, $vehicle_number);
        }

        if ($update_stmt->execute()) {
            $_SESSION['username'] = $username; // Update session username
            header("Location: dashboard.php");
            exit();
        } else {
            $errors[] = "Failed to update profile.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: lightgray;
        }
        .container {
            margin-top: 40px;
            margin-bottom: 30px;
            max-width: 600px;
            padding: 20px;
            background-color:rgb(106, 90, 205);
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .error {
            color: red;
        }
        .form-control {
            position: relative;
        }
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #007bff;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center">Edit Profile</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label for="first_name" class="form-label">First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Enter your first name" value="<?php echo htmlspecialchars($user_data['first_name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="last_name" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Enter your last name" value="<?php echo htmlspecialchars($user_data['last_name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" value="<?php echo htmlspecialchars($user_data['username']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Edit Password (leave blank to keep current)</label>
            <div class="input-group">
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter new password">
                <span class="toggle-password" onclick="togglePasswordVisibility()">
                    <i class="fas fa-eye" id="toggle-icon"></i>
                </span>
            </div>
        </div>
        <div class="mb-3">
            <label for="vehicle_type" class="form-label">Vehicle Type</label>
            <select class="form-select" id="vehicle_type" name="vehicle_type" required>
                <option value="" disabled>Select vehicle type</option>
                <option value="Car" <?php echo ($user_data['vehicle_type'] === 'Car') ? 'selected' : ''; ?>>Car</option>
                <option value="Bike" <?php echo ($user_data['vehicle_type'] === 'Bike') ? 'selected' : ''; ?>>Bike</option>
                <option value="Scooter" <?php echo ($user_data['vehicle_type'] === 'Scooter') ? 'selected' : ''; ?>>Scooter</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="vehicle_number" class="form-label">Vehicle Number</label>
            <input type="text" class="form-control" id="vehicle_number" name="vehicle_number" placeholder="Enter vehicle number (e.g., AB 1-2345)" value="<?php echo htmlspecialchars($user_data['vehicle_number']); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function togglePasswordVisibility() {
        const passwordField = document.getElementById('password');
        const toggleIcon = document.getElementById('toggle-icon');
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);
        toggleIcon.classList.toggle('fa-eye');
        toggleIcon.classList.toggle('fa-eye-slash');
    }
</script>
</body>
</html>