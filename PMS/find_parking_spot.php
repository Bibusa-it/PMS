<?php
require_once 'db_connection.php';
function calculateDistance($lat1, $lng1, $lat2, $lng2) {
    $earthRadius = 6371;

    $lat1 = deg2rad($lat1);
    $lng1 = deg2rad($lng1);
    $lat2 = deg2rad($lat2);
    $lng2 = deg2rad($lng2);

    $dlat = $lat2 - $lat1;
    $dlng = $lng2 - $lng1;

    $a = sin($dlat / 2) ** 2 +
         cos($lat1) * cos($lat2) * sin($dlng / 2) ** 2;

    return $earthRadius * (2 * atan2(sqrt($a), sqrt(1 - $a)));
}

function dijkstra($graph, $startNode) {
    $distances = [];
    $previous = [];
    $queue = new SplPriorityQueue();

    foreach ($graph as $node => $edges) {
        $distances[$node] = INF;
        $previous[$node] = null;
    }
    $distances[$startNode] = 0;
    $queue->insert($startNode, 0);

    while (!$queue->isEmpty()) {
        $current = $queue->extract();

        foreach ($graph[$current] as $neighbor => $distance) {
            $alt = $distances[$current] + $distance;
            if ($alt < $distances[$neighbor]) {
                $distances[$neighbor] = $alt;
                $previous[$neighbor] = $current;
                $queue->insert($neighbor, -$alt);
            }
        }
    }

    return $distances;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['latitude']) || !isset($_POST['longitude'])) {
        echo json_encode(["error" => "Latitude and Longitude not provided."]);
        exit;
    }

    $user_lat = filter_var($_POST['latitude'], FILTER_VALIDATE_FLOAT);
    $user_lng = filter_var($_POST['longitude'], FILTER_VALIDATE_FLOAT);

    if ($user_lat === false || $user_lng === false) {
        echo json_encode(["error" => "Invalid latitude or longitude values."]);
        exit;
    }

    $spotsStmt = $conn->prepare("SELECT id, name, latitude, longitude FROM parking_spots");
    $spotsStmt->execute();
    $spotsResult = $spotsStmt->get_result();

    $spots = [];
    while ($row = $spotsResult->fetch_assoc()) {
        $spots[$row['id']] = $row;
    }

    $routesStmt = $conn->prepare("SELECT start_node, end_node, distance FROM parking_routes");
    $routesStmt->execute();
    $routesResult = $routesStmt->get_result();

    $graph = [];
    while ($route = $routesResult->fetch_assoc()) {
        $graph[$route['start_node']][$route['end_node']] = $route['distance'];
        $graph[$route['end_node']][$route['start_node']] = $route['distance']; // Undirected graph
    }

    $userNode = 'user';
    $graph[$userNode] = [];
    foreach ($spots as $id => $spot) {
        $distance = calculateDistance($user_lat, $user_lng, $spot['latitude'], $spot['longitude']);
        $graph[$userNode][$id] = $distance;
        $graph[$id][$userNode] = $distance;
    }
    $distances = dijkstra($graph, $userNode);

    $nearestSpotId = null;
    $shortestDistance = INF;

    foreach ($spots as $id => $spot) {
        if (isset($distances[$id]) && $distances[$id] < $shortestDistance) {
            $nearestSpotId = $id;
            $shortestDistance = $distances[$id];
        }
    }

    if ($nearestSpotId !== null) {
        echo json_encode([
            "success" => true,
            "nearest_spot" => $spots[$nearestSpotId],
            "distance" => $shortestDistance
        ]);
    } else {
        echo json_encode(["error" => "No parking spots found."]);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find and Start Journey to Parking Spot</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css" />
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            max-width: 800px;
            width: 100%;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1 {
            color: #333;
            font-size: 24px;
        }

        .location-info {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .btn {
            padding: 12px 24px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 10px 0;
            font-size: 16px;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        #map {
            height: 400px;
            width: 100%;
            margin: 20px 0;
            border-radius: 8px;
        }

        .result {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .success {
            color: #28a745;
            font-weight: bold;
        }

        .error {
            color: #dc3545;
            font-weight: bold;
        }

        .distance {
            font-size: 16px;
            color: #333;
            margin-top: 10px;
        }

        #startJourneyButton {
            display: none;
        }

    </style>
</head>
<body>
    <div class="container">
        <h1>Find and Start Journey to Parking Spot</h1>
        <div id="locationInfo" class="location-info">
            <p><strong>Current Location:</strong> <span id="locationName">Not Available</span></p>
            <p><strong>Latitude:</strong> <span id="lat"></span></p>
            <p><strong>Longitude:</strong> <span id="lng"></span></p>
        </div>
        <button class="btn" onclick="getLocation()">Use Current Location</button>
        <button class="btn" onclick="findSpot()">Find Nearest Spot</button>
        <div id="result" class="result"></div>
        <button id="startJourneyButton" class="btn" style="background-color: #28a745;" onclick="startJourney()">Start Journey</button>
    </div>
    <div id="map"></div>

    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>
    <script>
        let map, userMarker, routeControl, userLat, userLng;
        let nearestSpot = null;

        map = L.map('map').setView([27.7172, 85.3240], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(success, error);
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        }

        function success(position) {
            userLat = position.coords.latitude;
            userLng = position.coords.longitude;

            fetch(`https://nominatim.openstreetmap.org/reverse?lat=${userLat}&lon=${userLng}&format=json`)
                .then(response => response.json())
                .then(data => {
                    const locationName = data.display_name || "Location not found";
                    document.getElementById("locationName").textContent = locationName;
                    document.getElementById("lat").textContent = userLat;
                    document.getElementById("lng").textContent = userLng;
                });

            map.setView([userLat, userLng], 13);

            if (userMarker) {
                map.removeLayer(userMarker);
            }

            userMarker = L.marker([userLat, userLng]).addTo(map).bindPopup("Your Location").openPopup();
        }

        function error() {
            document.getElementById("locationName").textContent = "Location not available";
            alert("Unable to retrieve your location.");
        }

        function findSpot() {
            fetch(`find_parking_spot.php`, {
                method: 'POST',
                body: new URLSearchParams({
                    latitude: userLat,
                    longitude: userLng
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    nearestSpot = data.nearest_spot;
                    const nearestSpotName = nearestSpot.name;
                    const nearestSpotLat = nearestSpot.latitude;
                    const nearestSpotLng = nearestSpot.longitude;

                    const distance = data.distance.toFixed(2);
                    document.getElementById("result").innerHTML = `
                        <p class="success">Nearest Parking Spot: ${nearestSpotName}</p>
                        <p class="distance">Distance: ${distance} km</p>
                    `;

                    document.getElementById("startJourneyButton").style.display = "block";

                    if (routeControl) {
                        routeControl.setWaypoints([L.latLng(userLat, userLng), L.latLng(nearestSpotLat, nearestSpotLng)]);
                    } else {
                        routeControl = L.Routing.control({
                            waypoints: [
                                L.latLng(userLat, userLng),
                                L.latLng(nearestSpotLat, nearestSpotLng)
                            ],
                            routeWhileDragging: true
                        }).addTo(map);
                    }
                } else {
                    document.getElementById("result").innerHTML = `<p class="error">${data.error}</p>`;
                }
            });
        }

        function startJourney() {
            if (nearestSpot) {
                alert(`Journey started to: ${nearestSpot.name}`);
                document.getElementById("startJourneyButton").style.display = "none";
            }
        }
    </script>
</body>
</html>
