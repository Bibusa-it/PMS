<?php
// Define the graph with parking spot names as nodes and distances as weights
$graph = [
    'New Road Complex' => ['RB Complex' => 1.5, 'Ranjana Complex' => 2.0],
    'RB Complex' => ['New Road Complex' => 1.5, 'City Center' => 3.0],
    'Ranjana Complex' => ['New Road Complex' => 2.0, 'City Center' => 1.2],
    'City Center' => ['RB Complex' => 3.0, 'Ranjana Complex' => 1.2]
];

function findShortestPath($graph, $start, $end) {
    $distances = [];
    $previous = [];
    $queue = [];

    foreach ($graph as $node => $edges) {
        $distances[$node] = INF;
        $previous[$node] = null;
        $queue[$node] = INF;
    }
    $distances[$start] = 0;
    $queue[$start] = 0;

    while (!empty($queue)) {
        $current = array_search(min($queue), $queue);
        unset($queue[$current]);

        if ($current === $end) break;

        foreach ($graph[$current] as $neighbor => $distance) {
            $alt = $distances[$current] + $distance;
            if ($alt < $distances[$neighbor]) {
                $distances[$neighbor] = $alt;
                $previous[$neighbor] = $current;
                $queue[$neighbor] = $alt;
            }
        }
    }

    $path = [];
    for ($node = $end; $node !== null; $node = $previous[$node]) {
        array_unshift($path, $node);
    }

    return ['distance' => $distances[$end], 'path' => $path];
}
?>
