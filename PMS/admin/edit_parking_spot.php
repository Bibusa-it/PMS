<?php
require_once '../db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = (int)$_POST["id"];
    $name = $_POST["name"];
    $vehicle_type = $_POST["vehicle_type"];
    $latitude = $_POST["latitude"];
    $longitude = $_POST["longitude"];
    $is_available = isset($_POST["is_available"]) ? (int)$_POST["is_available"] : 1; // Default to available
    $is_reserved = isset($_POST["is_reserved"]) ? (int)$_POST["is_reserved"] : 0; // Default to not reserved

    // SQL query to update the parking spot
    $sql = "UPDATE parking_spots SET name = ?, vehicle_type = ?, latitude = ?, longitude = ?, is_available = ?, is_reserved = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssiiii", $name, $vehicle_type, $latitude, $longitude, $is_available, $is_reserved, $id);

    if ($stmt->execute()) {
        header("Location: view_parking_spot.php?update=1");
    } else {
        echo "Error updating parking spot: " . $stmt->error;
    }
    $stmt->close();
} elseif (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    // Query to fetch the details of the parking spot
    $sql = "SELECT * FROM parking_spots WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $spot = $result->fetch_assoc();
    $stmt->close();
}

// Get all predefined parking spots to populate the dropdown
$spots_query = "SELECT id, name, latitude, longitude FROM parking_spots";
$spots_result = $conn->query($spots_query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Parking Spot</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        h2 {
            text-align: center;
            margin-top: 20px;
        }

        form {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #fff;
        }

        label {
            font-weight: bold;
            margin-bottom: 8px;
            display: block;
        }

        input[type="text"], select, button {
            padding: 10px;
            font-size: 14px;
            width: 100%;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            background-color: blue;
            color: white;
            cursor: pointer;
        }

        button:hover {
            background-color: blue;
        }
    </style>
    <script>
        // Function to update latitude and longitude based on selected parking spot
        function updateCoordinates() {
            var spotId = document.getElementById("name").value;
            var latitudeField = document.getElementById("latitude");
            var longitudeField = document.getElementById("longitude");

            // Fetch coordinates for the selected parking spot
            fetch('get_coordinates.php?id=' + spotId)
                .then(response => response.json())
                .then(data => {
                    if (data.latitude && data.longitude) {
                        latitudeField.value = data.latitude;
                        longitudeField.value = data.longitude;
                    }
                })
                .catch(error => {
                    console.error('Error fetching coordinates:', error);
                });
        }
    </script>
</head>
<body>
    <h2>Edit Parking Spot</h2>
    <form action="" method="post">
        <input type="hidden" name="id" value="<?php echo $spot['id']; ?>">

        <label for="name">Name:</label>
        <select name="name" id="name" required onchange="updateCoordinates()">
            <?php while ($row = $spots_result->fetch_assoc()): ?>
                <option value="<?php echo $row['name']; ?>" <?php if ($spot['name'] == $row['name']) echo 'selected'; ?>>
                    <?php echo $row['name']; ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="vehicle_type">Vehicle Type:</label>
        <select name="vehicle_type" id="vehicle_type" required>
            <option value="Car" <?php if ($spot['vehicle_type'] == 'Car') echo 'selected'; ?>>Car</option>
            <option value="Bike" <?php if ($spot['vehicle_type'] == 'Bike') echo 'selected'; ?>>Bike</option>
            <option value="Scooter" <?php if ($spot['vehicle_type'] == 'Scooter') echo 'selected'; ?>>Scooter</option>
        </select>

        <label for="latitude">Latitude:</label>
        <input type="text" name="latitude" id="latitude" value="<?php echo $spot['latitude']; ?>" required>

        <label for="longitude">Longitude:</label>
        <input type="text" name="longitude" id="longitude" value="<?php echo $spot['longitude']; ?>" required>

        <label for="is_available">Is Available:</label>
        <select name="is_available" id="is_available">
            <option value="1" <?php if ($spot['is_available'] == 1) echo 'selected'; ?>>Yes</option>
            <option value="0" <?php if ($spot['is_available'] == 0) echo 'selected'; ?>>No</option>
        </select>

        <label for="is_reserved">Is Reserved:</label>
        <select name="is_reserved" id="is_reserved">
            <option value="0" <?php if (!$spot['is_reserved']) echo 'selected'; ?>>No</option>
            <option value="1" <?php if ($spot['is_reserved']) echo 'selected'; ?>>Yes</option>
        </select>

        <button type="submit">Update</button>
    </form>
</body>
</html>
