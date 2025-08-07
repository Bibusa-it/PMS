<?php
require_once '../db_connection.php';
$spotId = intval($_GET['spotId']);
$query = "SELECT id, name, capacities, availability FROM parking_spots WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $spotId);
$stmt->execute();
$spot = $stmt->get_result()->fetch_assoc();

if (!$spot) {
    die("Parking spot not found.");
}
$capacities = json_decode($spot['capacities'], true);
$availability = json_decode($spot['availability'], true);

$availability = array_merge(['car' => 0, 'bike' => 0, 'scooter' => 0], $availability);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Parking Spot</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            padding: 20px;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 600px;
            margin: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Manage Spot: <?= htmlspecialchars($spot['name']) ?></h2>
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Vehicle Type</th>
                    <th>Total Capacity</th>
                    <th>Used Spots</th>
                    <th>Assign Spots</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (['car', 'bike', 'scooter'] as $vehicleType): ?>
                    <tr>
                        <td><?= ucfirst($vehicleType) ?></td>
                        <td><?= $capacities[$vehicleType] ?></td>
                        <td><?= $availability[$vehicleType] ?></td>
                        <td>
                            <button class="btn btn-primary assign-btn" data-vehicle="<?= $vehicleType ?>">Assign</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div aria-live="polite" aria-atomic="true" style="position: fixed; top: 1rem; right: 1rem; z-index: 1050;">
        <div class="toast" id="toastNotification" role="alert" aria-live="assertive" aria-atomic="true" data-delay="3000">
            <div class="toast-header">
                <strong class="mr-auto">Notification</strong>
                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="toast-body">
            </div>
        </div>
    </div>

    <script>
        function showToast(message, isSuccess = true) {
            const toast = document.getElementById('toastNotification');
            const toastBody = toast.querySelector('.toast-body');

            toastBody.textContent = message;
            toast.classList.remove('bg-success', 'bg-danger');
            toast.classList.add(isSuccess ? 'bg-success' : 'bg-danger');
            $(toast).toast('show');
        }

        document.querySelectorAll('.assign-btn').forEach(button => {
            button.addEventListener('click', function () {
                const vehicleType = this.getAttribute('data-vehicle');
                const quantity = prompt(`Enter quantity for ${vehicleType}:`, "0");

                if (quantity !== null && !isNaN(quantity)) {
                    const formData = new FormData();
                    formData.append("spotId", <?= $spotId ?>);
                    formData.append("vehicleType", vehicleType);
                    formData.append("quantity", quantity);

                    fetch('assign_spot.php', {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => response.json())
                        .then(data => {
                            showToast(data.message, data.success);
                            if (data.success) location.reload();
                        })
                        .catch(error => {
                            showToast('Error: ' + error.message, false);
                        });
                }
            });
        });
    </script>
</body>
</html>
