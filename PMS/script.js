function distance_to_destination($destination) {

    $destination_lat = get_destination_latitude($destination);
    $destination_long = get_destination_longitude($destination);

    $distances = array();
    foreach ($parking_spots as $parking_spot) {
        $parking_spot_lat = $parking_spot['latitude'];
        $parking_spot_long = $parking_spot['longitude'];

        $distance = calculate_distance($destination_lat, $destination_long, $parking_spot_lat, $parking_spot_long);
        $distances[$parking_spot['id']] = $distance;
    }

    return $distances;
}

function calculate_distance($lat1, $long1, $lat2, $long2) {
    // Implement Haversine formula to calculate distance between two points on a sphere
    $earth_radius = 6371; // in kilometers

    $dlat = deg2rad($lat2 - $lat1);
    $dlong = deg2rad($long2 - $long1);

    $a = sin($dlat / 2) * sin($dlat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dlong / 2) * sin($dlong / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    $distance = $earth_radius * $c;

    return $distance;
}

function deg2rad($deg) {
    return $deg * pi() / 180;
}

function get_destination_latitude($destination) {
    // Implement logic to retrieve destination latitude from database or API
}

function get_destination_longitude($destination) {
    // Implement logic to retrieve destination longitude from database or API
}