<?php
require_once 'db_connection.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['user_name'], $_POST['parking_spot_id'])) {
        $userName = $_POST['user_name'];
        $spotId = intval($_POST['parking_spot_id']);

        // Insert into reservations table
        $stmt = $conn->prepare("INSERT INTO reservations (user_name, parking_spot_id) VALUES (?, ?)");
        $stmt->bind_param('si', $userName, $spotId);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Spot reserved successfully!"]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to reserve the spot."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Invalid data."]);
    }
}
?>
