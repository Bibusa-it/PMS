<?php
require_once '../db_connection.php';

$sql = "SELECT * FROM parking_spots";
$result = $conn->query($sql);

$total_spots = 0;
$available_spots = 0;
$occupied_spots = 0;
$reserved_spots = 0;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $total_spots++;
        if ($row['is_available'] == 1) {
            $available_spots++;
        } else if ($row['is_reserved'] == 1) { 
            $reserved_spots++;
        } else {
            $occupied_spots++;
        }
    }
}
$free_spots = $total_spots - $reserved_spots - $occupied_spots;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Parking Spot Usage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" />
    <style>
        body{
            background-color:lightgray;
        }
        h2{
            margin-top: 20px;
            margin-bottom: 20px;
            text-align: center;
            color: blue;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Parking Spot Usage</h2>
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Parking Spots</h5>
                        <p class="card-text"><?php echo $total_spots; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Available Parking Spots</h5>
                        <p class="card-text"><?php echo $available_spots; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Reserved Parking Spots</h5>
                        <p class="card-text"><?php echo $reserved_spots; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Free Parking Spots</h5>
                        <p class="card-text"><?php echo $free_spots; ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-12">
                <canvas id="usageChart" style="max-width: 400px; margin: 0 auto;"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('usageChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Available', 'Occupied', 'Reserved', 'Free'],
                datasets: [{
                    label: 'Parking Spot Usage',
                    data: [
                        <?php echo $available_spots; ?>, 
                        <?php echo $occupied_spots; ?>, 
                        <?php echo $reserved_spots; ?>, 
                        <?php echo $free_spots; ?>
                    ],
                    backgroundColor: [
                        'rgba(0, 255, 0, 0.2)', // Available
                        'rgba(255, 0, 0, 0.2)', // Occupied
                        'rgba(0, 0, 255, 0.2)', // Reserved
                        'rgba(255, 255, 0, 0.2)' // Free
                    ],
                    borderColor: [
                        'rgba(0, 255, 0, 1)',
                        'rgba(255, 0, 0, 1)',
                        'rgba(0, 0, 255, 1)',
                        'rgba(255, 255, 0, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                size: 14
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
