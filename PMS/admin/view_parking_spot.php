<?php
require_once '../db_connection.php';

// Fetch parking spots data
$parkingSpots = [];
$query = "SELECT id, name, latitude, longitude, capacities, availability FROM parking_spots";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    // Decode JSON values for capacities and availability
    $row['capacities'] = json_decode($row['capacities'], true);
    $row['availability'] = json_decode($row['availability'], true);
    
    // Merge any missing values in availability, defaulting to 0 for all vehicle types
    $row['availability'] = array_merge([
        'car' => 0,
        'bike' => 0,
        'scooter' => 0
    ], $row['availability']);

    $parkingSpots[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parking Spot Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f6;
        }
        .container {
            margin-top: 30px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .table th, .table td {
            text-align: center;
            vertical-align: middle;
        }
        .table {
            margin-top: 30px;
        }
        .action-btns button {
            padding: 10px;
            font-size: 16px;
            border-radius: 50%;
            width: 45px;
            height: 45px;
            border: none;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        .action-btns .btn-success {
            background-color: #28a745;
        }
        .action-btns .btn-danger {
            background-color: #dc3545;
        }
        .action-btns .btn-warning {
            background-color: #ffc107;
        }
        .action-btns button:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Parking Spot Management</h2>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th>Car Capacity</th>
                    <th>Bike Capacity</th>
                    <th>Scooter Capacity</th>
                    <th>Car Used</th>
                    <th>Bike Used</th>
                    <th>Scooter Used</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($parkingSpots as $spot): ?>
                    <tr>
                        <td><?= $spot['id'] ?></td>
                        <td><?= htmlspecialchars($spot['name']) ?></td>
                        <td><?= $spot['latitude'] ?></td>
                        <td><?= $spot['longitude'] ?></td>
                        <td><?= $spot['capacities']['car'] ?></td>
                        <td><?= $spot['capacities']['bike'] ?></td>
                        <td><?= $spot['capacities']['scooter'] ?></td>
                        <td><?= $spot['availability']['car'] ?></td>
                        <td><?= $spot['availability']['bike'] ?></td>
                        <td><?= $spot['availability']['scooter'] ?></td>
                        <td class="action-btns">
                            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#assignModal" data-spot-id="<?= $spot['id'] ?>" data-spot-name="<?= $spot['name'] ?>">
                                <i class="bi bi-plus-circle"></i>
                            </button>

                            <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#unassignModal" data-spot-id="<?= $spot['id'] ?>" data-spot-name="<?= $spot['name'] ?>">
                                <i class="bi bi-dash-circle"></i>
                            </button>

                            <button class="btn btn-danger" onclick="deleteSpot(<?= $spot['id'] ?>)">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Assign Modal -->
    <div class="modal fade" id="assignModal" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignModalLabel">Assign Vehicle to Parking Spot</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="assignForm">
                        <div class="mb-3">
                            <label for="vehicleType" class="form-label">Select Vehicle Type</label>
                            <select class="form-select" id="vehicleType" required>
                                <option value="car">Car</option>
                                <option value="bike">Bike</option>
                                <option value="scooter">Scooter</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Assign</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Unassign Modal -->
    <div class="modal fade" id="unassignModal" tabindex="-1" aria-labelledby="unassignModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="unassignModalLabel">Unassign Vehicle from Parking Spot</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="unassignForm">
                        <div class="mb-3">
                            <label for="vehicleTypeUnassign" class="form-label">Select Vehicle Type</label>
                            <select class="form-select" id="vehicleTypeUnassign" required>
                                <option value="car">Car</option>
                                <option value="bike">Bike</option>
                                <option value="scooter">Scooter</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-warning">Unassign</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentSpotId = null;

        // Assign Button Modal (Set the current spot)
        $('#assignModal').on('show.bs.modal', function (e) {
            const spotId = $(e.relatedTarget).data('spot-id');
            currentSpotId = spotId;
        });

        // Handle Assign Vehicle Form Submit
        $('#assignForm').submit(function(e) {
            e.preventDefault();
            const vehicleType = $('#vehicleType').val();
            const vehicleId = 'USER_VEHICLE_ID'; // Replace with actual vehicle/user ID dynamically

            fetch('assign_spot.php', {
                method: 'POST',
                body: JSON.stringify({ spotId: currentSpotId, vehicleType: vehicleType, vehicleId: vehicleId }),
                headers: { 'Content-Type': 'application/json' }
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });

        // Unassign Button Modal (Set the current spot)
        $('#unassignModal').on('show.bs.modal', function (e) {
            const spotId = $(e.relatedTarget).data('spot-id');
            currentSpotId = spotId;
        });

        // Handle Unassign Vehicle Form Submit
        $('#unassignForm').submit(function(e) {
            e.preventDefault();
            const vehicleType = $('#vehicleTypeUnassign').val();
            const vehicleId = 'USER_VEHICLE_ID'; // Replace with actual vehicle/user ID dynamically

            fetch('unassign_spot.php', {
                method: 'POST',
                body: JSON.stringify({ spotId: currentSpotId, vehicleType: vehicleType, vehicleId: vehicleId }),
                headers: { 'Content-Type': 'application/json' }
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });

        // Handle Delete Parking Spot
        function deleteSpot(spotId) {
            if (confirm('Are you sure you want to delete this parking spot?')) {
                fetch(`delete_spot.php?id=${spotId}`, {
                    method: 'GET',
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    location.reload();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
            }
        }
    </script>
</body>
</html>
