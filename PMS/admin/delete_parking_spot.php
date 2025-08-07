<?php
require_once '../db_connection.php';

// Get the spot ID from the URL
$spotId = $_GET['id'];

// Check if the parking spot exists
$query = "SELECT id FROM parking_spots WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $spotId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // Spot not found
    echo json_encode(['success' => false, 'message' => 'Parking spot not found.']);
    exit;
}

// Delete the parking spot
$query = "DELETE FROM parking_spots WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $spotId);
$stmt->execute();

// Respond with a success message
echo json_encode(['success' => true, 'message' => 'Parking spot deleted successfully.']);
?>
