<?php
require_once '//db_connection.php';

// Update parking spot
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];
    $available = $_POST["available"];

    updateParkingSpot($conn, $id, $available);
}
?>