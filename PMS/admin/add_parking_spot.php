<?php
require_once '../db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"]) && $_POST["action"] === "add_spot") {
    header('Content-Type: application/json');
    

    $name = trim($_POST["name"]);
    $latitude = floatval(trim($_POST["latitude"]));
    $longitude = floatval(trim($_POST["longitude"]));
    $total_car_capacity = intval(trim($_POST["total_car_capacity"]));
    $total_bike_capacity = intval(trim($_POST["total_bike_capacity"]));
    $total_scooter_capacity = intval(trim($_POST["total_scooter_capacity"]));
    
    $initialAvailability = json_encode([
        'car' => 0, 
        'bike' => 0,  
        'scooter' => 0  
    ]);

    // Validate inputs
    if (empty($name) || !$latitude || !$longitude || $total_car_capacity < 1 || $total_bike_capacity < 1 || $total_scooter_capacity < 1) {
        echo json_encode(["success" => false, "message" => "All fields are required, and capacities must be positive numbers."]);
        exit;
    }

    try {
        // Check if the parking spot already exists
        $checkQuery = "SELECT COUNT(*) AS count FROM parking_spots WHERE name = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if ($result['count'] > 0) {
            echo json_encode(["success" => false, "message" => "Parking spot with this name already exists."]);
            exit;
        }

        // Insert new parking spot into the database
        $sql = "INSERT INTO parking_spots (name, latitude, longitude, capacities, availability, is_available, is_reserved)
                VALUES (?, ?, ?, ?, ?, 1, 0)";
        $capacities = json_encode([
            'car' => $total_car_capacity,
            'bike' => $total_bike_capacity,
            'scooter' => $total_scooter_capacity
        ]);
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $name, $latitude, $longitude, $capacities, $initialAvailability);
        $stmt->execute();

        echo json_encode(["success" => true, "message" => "Parking spot added successfully."]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Failed to add parking spot. Error: " . $e->getMessage()]);
    } finally {
        $stmt->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Parking Spot</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }
        h2 {
            text-align: center;
            color: #007bff;
        }
        label {
            font-weight: bold;
            margin-top: 10px;
            display: block;
        }
        input, select, button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #007bff;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        #map {
            height: 300px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add Parking Spot</h2>
        <form id="addSpotForm">
            <input type="hidden" name="action" value="add_spot" />

            <!-- Parking Spot Name -->
            <label for="name">Parking Spot Name:</label>
            <select id="name" name="name" required>
                <option value="">Select Parking Spot</option>
                <option value="New Road Complex" data-lat="27.7017" data-lng="85.3103">New Road Complex</option>
                <option value="RB Complex" data-lat="27.7021" data-lng="85.3097">RB Complex</option>
                <option value="Ranjana Complex" data-lat="27.7030" data-lng="85.3110">Ranjana Complex</option>
            </select>

            <!-- Latitude and Longitude -->
            <label for="latitude">Latitude:</label>
            <input type="text" id="latitude" name="latitude" readonly required />

            <label for="longitude">Longitude:</label>
            <input type="text" id="longitude" name="longitude" readonly required />

            <!-- Total Capacities -->
            <label for="total_car_capacity">Total Car Capacity:</label>
            <input type="number" id="total_car_capacity" name="total_car_capacity" min="1" required />

            <label for="total_bike_capacity">Total Bike Capacity:</label>
            <input type="number" id="total_bike_capacity" name="total_bike_capacity" min="1" required />

            <label for="total_scooter_capacity">Total Scooter Capacity:</label>
            <input type="number" id="total_scooter_capacity" name="total_scooter_capacity" min="1" required />

            <!-- Submit Button -->
            <button type="submit">Add Spot</button>
        </form>

        <div id="map"></div>
    </div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        const map = L.map('map').setView([27.7172, 85.3240], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        document.getElementById('name').addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const latitude = selectedOption.getAttribute('data-lat');
            const longitude = selectedOption.getAttribute('data-lng');

            if (latitude && longitude) {
                document.getElementById('latitude').value = latitude;
                document.getElementById('longitude').value = longitude;

                map.setView([latitude, longitude], 16);
                L.marker([latitude, longitude]).addTo(map)
                    .bindPopup(`<b>${this.value}</b>`).openPopup();
            }
        });

        document.getElementById('addSpotForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('add_parking_spot.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => alert(data.message))
            .catch(error => alert('Error: ' + error.message));
        });
    </script>
</body>
</html>
