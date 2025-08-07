<?php
require_once '../db_connection.php';

// Read the input data
$input = json_decode(file_get_contents('php://input'), true);
$spotId = $input['spotId'];
$vehicleType = $input['vehicleType'];
$vehicleId = $input['vehicleId'];

// Fetch the parking spot data to get the current availability
$query = "SELECT availability FROM parking_spots WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $spotId);
$stmt->execute();
$result = $stmt->get_result();
$spot = $result->fetch_assoc();

// Decode the JSON values for availability
$availability = json_decode($spot['availability'], true);

// Check if the spot is currently assigned to this vehicle type
if ($availability[$vehicleType] <= 0) {
    // No vehicle assigned for this type
    echo json_encode(['success' => false, 'message' => 'No vehicle assigned to this spot for the selected type.']);
    exit;
}

// Update the availability for the vehicle type
$availability[$vehicleType] -= 1;
$availabilityJson = json_encode($availability);

// Update the database with the new availability
$query = "UPDATE parking_spots SET availability = ? WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $availabilityJson, $spotId);
$stmt->execute();

// Respond with a success message
echo json_encode(['success' => true, 'message' => 'Vehicle unassigned successfully.']);
?>
