<?php

class TrackingService {
    private $originLat;
    private $originLng;
    private $originLabel;

    public function __construct() {
        $configPath = __DIR__ . '/../../config/tracking.php';
        $config = file_exists($configPath) ? require $configPath : [];
        $origin = $config['origin'] ?? ['latitude' => 36.8667, 'longitude' => 10.1647, 'label' => 'Ariana, Tunisie'];

        $this->originLat = (float)($origin['latitude'] ?? 36.8667);
        $this->originLng = (float)($origin['longitude'] ?? 10.1647);
        $this->originLabel = $origin['label'] ?? 'Ariana, Tunisie';
    }

    /**
     * SIMPLE INDEX-BASED TRACKING - foolproof, always forward
     * Car moves from point 0 -> 1 -> 2 -> 3 ... along stored route
     */
    public function simulateProgress(array $livraison, ?array $trajet = null): ?array {
        if (!isset($livraison['position_lat'], $livraison['position_lng'])) {
            return null;
        }

        $destination = [
            'lat' => (float)$livraison['position_lat'],
            'lng' => (float)$livraison['position_lng'],
        ];
        $origin = ['lat' => $this->originLat, 'lng' => $this->originLng];

        $route = null;
        $currentIndex = 0;

        if ($trajet && !empty($trajet['route_json'])) {
            $route = json_decode($trajet['route_json'], true);
            $currentIndex = (int)($trajet['current_index'] ?? 0);
        }

        if (!$route || empty($route)) {
            $routeData = $this->getOSRMRoute($origin, $destination);
            if ($routeData && !empty($routeData['coordinates'])) {
                $route = $routeData['coordinates'];
                error_log("✅ OSRM route created with " . count($route) . " points (real roads)");
            } else {
                error_log("❌ OSRM route creation failed - cannot proceed without real roads");
                return null;
            }
        }

        if (empty($route)) {
            error_log("❌ No valid route available");
            return null;
        }

        $totalPoints = count($route);

        $increment = mt_rand(3, 5);
        $newIndex = $currentIndex + $increment;
        
        if ($newIndex >= $totalPoints) {
            $newIndex = $totalPoints - 1;
        }

        $hasArrived = $newIndex >= $totalPoints - 1;

        if (($livraison['statut'] ?? '') === 'livrée') {
            $newIndex = $totalPoints - 1;
            $hasArrived = true;
        }

        $coords = $route[$newIndex];
        $progress = ($newIndex / max(1, $totalPoints - 1)) * 100;

        $status = $hasArrived
            ? 'Arrivée au point de livraison'
            : 'En chemin (' . (int)round($progress) . '%)';

        return [
            'latitude' => $coords['lat'],
            'longitude' => $coords['lng'],
            'statut' => $status,
            'origin' => $origin,
            'destination' => $destination,
            'route' => $route,
            'route_json' => json_encode($route),
            'current_index' => $newIndex,
            'total_points' => $totalPoints,
            'has_arrived' => $hasArrived,
        ];
    }

    private function getOSRMRoute(array $origin, array $destination): ?array {
        $url = sprintf(
            'https://router.project-osrm.org/route/v1/driving/%f,%f;%f,%f?overview=full&geometries=geojson&steps=true&annotations=true',
            $origin['lng'], $origin['lat'],
            $destination['lng'], $destination['lat']
        );

        $context = stream_context_create([
            'http' => [
                'header' => "User-Agent: NextGenDelivery/2.0\r\n",
                'timeout' => 15,
                'ignore_errors' => true
            ]
        ]);

        $response = @file_get_contents($url, false, $context);
        
        if ($response === false) {
            error_log("OSRM fetch failed for URL: $url");
            return null;
        }

        $data = json_decode($response, true);
        
        if (!isset($data['routes'][0]['geometry']['coordinates'])) {
            error_log("OSRM response missing geometry: " . json_encode($data));
            return null;
        }

        $coordinates = [];
        foreach ($data['routes'][0]['geometry']['coordinates'] as $point) {
            $coordinates[] = ['lat' => $point[1], 'lng' => $point[0]];
        }

        if (count($coordinates) < 10) {
            error_log("OSRM returned too few points: " . count($coordinates));
            return null;
        }

        return [
            'coordinates' => $coordinates,
            'distance' => $data['routes'][0]['distance'] ?? 0,
            'duration' => $data['routes'][0]['duration'] ?? 0,
        ];
    }

    private function createLinearRoute(array $origin, array $destination, int $numPoints = 100): array {
        $route = [];
        for ($i = 0; $i <= $numPoints; $i++) {
            $progress = $i / $numPoints;
            $route[] = [
                'lat' => $origin['lat'] + ($destination['lat'] - $origin['lat']) * $progress,
                'lng' => $origin['lng'] + ($destination['lng'] - $origin['lng']) * $progress,
            ];
        }
        return $route;
    }

    public function getOrigin(): array {
        return [
            'lat' => $this->originLat,
            'lng' => $this->originLng,
            'label' => $this->originLabel,
        ];
    }

    public function haversine(array $a, array $b): float {
        $R = 6371.0;
        $lat1 = deg2rad($a['lat']);
        $lat2 = deg2rad($b['lat']);
        $dlat = deg2rad($b['lat'] - $a['lat']);
        $dlng = deg2rad($b['lng'] - $a['lng']);
        $h = sin($dlat/2) * sin($dlat/2) + cos($lat1) * cos($lat2) * sin($dlng/2) * sin($dlng/2);
        $c = 2 * atan2(sqrt($h), sqrt(1-$h));
        return $R * $c;
    }

    public function progressData(array $origin, array $destination, array $current): array {
        $total = max(0.0, $this->haversine($origin, $destination));
        $covered = max(0.0, $this->haversine($origin, $current));
        $pct = $total > 0 ? max(0.0, min(100.0, ($covered / $total) * 100)) : 100.0;
        return [
            'total_km' => $total,
            'covered_km' => $covered,
            'progress_pct' => $pct,
        ];
    }

    public function reverseGeocode(float $lat, float $lng): ?array {
        $url = 'https://nominatim.openstreetmap.org/reverse?format=json&lat=' . urlencode((string)$lat) . '&lon=' . urlencode((string)$lng);
        $context = stream_context_create(['http' => ['header' => "User-Agent: NextGenLivraison/1.0\r\n", 'timeout' => 5]]);
        $response = @file_get_contents($url, false, $context);
        if ($response === false) {
            return null;
        }
        $data = json_decode($response, true);
        if (!$data) {
            return null;
        }
        $addr = $data['address'] ?? [];
        return [
            'display_name' => $data['display_name'] ?? null,
            'city' => $addr['city'] ?? $addr['town'] ?? $addr['village'] ?? null,
            'state' => $addr['state'] ?? null,
            'country' => $addr['country'] ?? null,
        ];
    }
}
