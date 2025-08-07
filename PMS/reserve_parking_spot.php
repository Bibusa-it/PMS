<?php
require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $spot_id = intval($_POST['spot_id']);
    
    $stmt = $conn->prepare("UPDATE parking_spots SET is_reserved = 1 WHERE id = ?");
    $stmt->bind_param("i", $spot_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit;
}
?>
