<?php
require_once '../db_connection.php';

// Read the input data
$input = json_decode(file_get_contents('php://input'), true);
$spotId = $input['spotId'];
$vehicleType = $input['vehicleType'];
$vehicleId = $input['vehicleId'];

// Fetch the parking spot data to get the current availability and capacity
$query = "SELECT capacities, availability FROM parking_spots WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $spotId);
$stmt->execute();
$result = $stmt->get_result();
$spot = $result->fetch_assoc();

// Decode the JSON values for capacities and availability
$capacities = json_decode($spot['capacities'], true);
$availability = json_decode($spot['availability'], true);

// Check if there is availability for the selected vehicle type
if ($availability[$vehicleType] >= $capacities[$vehicleType]) {
    // No more spots available for this vehicle type
    echo json_encode(['success' => false, 'message' => 'No more spots available for this vehicle type.']);
    exit;
}

// Update the availability for the vehicle type
$availability[$vehicleType] += 1;
$availabilityJson = json_encode($availability);

// Update the database with the new availability
$query = "UPDATE parking_spots SET availability = ? WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $availabilityJson, $spotId);
$stmt->execute();

// Respond with a success message
echo json_encode(['success' => true, 'message' => 'Vehicle assigned successfully.']);
?>
