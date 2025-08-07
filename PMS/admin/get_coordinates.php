<?php
require_once '../db_connection.php';

if (isset($_GET['id'])) {
    $spot_id = (int)$_GET['id'];
    $sql = "SELECT latitude, longitude FROM parking_spots WHERE name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $spot_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode($row); // Return latitude and longitude as JSON
    } else {
        echo json_encode(['latitude' => '', 'longitude' => '']);
    }

    $stmt->close();
}
?>
