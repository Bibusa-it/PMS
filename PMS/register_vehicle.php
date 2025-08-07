<?php
session_start();
require_once 'db_connection.php';

$error_message = ""; 
$success_message = ""; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $vehicle_number = trim($_POST['vehicle_number']);
    $vehicle_type = trim($_POST['vehicle_type']);

    if (!preg_match("/^[A-Z]{2}\s[0-9]{1,2}-[0-9]{1,4}\s?[A-Z]?$/", $vehicle_number)) {
        $error_message = "Vehicle number must follow the format: 'BA 1-2345' or 'GA 2-1234 C'.";
    } else {
        // Check if vehicle already exists
        $stmt = $conn->prepare("SELECT id FROM vehicles WHERE vehicle_number = ?");
        $stmt->bind_param("s", $vehicle_number);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error_message = "This vehicle is already registered!";
        } else {
            // Insert new vehicle
            $stmt = $conn->prepare("INSERT INTO vehicles (vehicle_number, vehicle_type) VALUES (?, ?)");
            $stmt->bind_param("ss", $vehicle_number, $vehicle_type);

            if ($stmt->execute()) {
                $success_message = "Vehicle registered successfully!";
            } else {
                $error_message = "Error registering vehicle: " . $stmt->error;
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
    <title>Register Vehicle - PMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 400px;
            margin-top: 50px;
            padding: 30px;
            border-radius: 10px;
            background-color: #fff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        h1 {
            text-align: center;
            color: green;
            margin-bottom: 30px;
        }
        .form-label {
            font-weight: bold;
            color: #343a40;
        }
        .form-control {
            border-radius: 5px;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid #ced4da;
        }
        .btn {
            width: 100%;
            padding: 12px;
            font-size: 16px;
        }
        .alert {
            text-align: center;
        }
        .error-message {
            color: red;
            font-size: 0.875em;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Register Vehicle</h1>

    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <form method="POST" onsubmit="return validateForm()">
        <div class="form-group mb-3">
            <label for="vehicle_number" class="form-label">Vehicle Number</label>
            <input type="text" name="vehicle_number" class="form-control" required>
            <?php if ($error_message): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>
        </div>
        <div class="form-group mb-3">
            <label for="vehicle_type" class="form-label">Vehicle Type</label>
            <select name="vehicle_type" class="form-control" required>
                <option value="" disabled selected>Select vehicle type</option>
                <option value="Car">Car</option>
                <option value="Bike">Bike</option>
                <option value="Scooter">Scooter</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Register Vehicle</button>
        <a href="register.php" class="btn btn-secondary mt-2">Proceed to User Registration</a>
    </form>
</div>

<script>
    function validateForm() {
        const vehicleNumber = document.querySelector('input[name="vehicle_number"]').value;
        const vehicleType = document.querySelector('select[name="vehicle_type"]').value;
        const vehicleNumberPattern = /^[A-Z]{2}\s[0-9]{1,2}-[0-9]{1,4}\s?[A-Z]?$/;

        if (!vehicleNumberPattern.test(vehicleNumber)) {
            alert("Vehicle number must follow the format: 'BA 1-2345' or 'GA 2-1234 C'.");
            return false;
        }

        if (vehicleType === "") {
            alert("Please select a vehicle type.");
            return false;
        }

        return true;
    }
</script>
</body>
</html>