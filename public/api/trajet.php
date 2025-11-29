<?php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../app/Models/Trajet.php';
require_once __DIR__ . '/../../app/Services/TrackingService.php';

$id = isset($_GET['id_livraison']) ? (int)$_GET['id_livraison'] : 0;
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'id_livraison invalide']);
    exit;
}

$db = Database::getInstance()->getConnection();
$tracking = new TrackingService();

// Fetch Livraison
$stmt = $db->prepare("SELECT * FROM livraison WHERE id_livraison = :id");
$stmt->execute([':id' => $id]);
$livraison = $stmt->fetch();

if (!$livraison) {
    http_response_code(404);
    echo json_encode(['error' => 'Livraison introuvable']);
    exit;
}

// Valide la destination
$destOk = isset($livraison['position_lat'], $livraison['position_lng'])
    && $livraison['position_lat'] !== null && $livraison['position_lng'] !== null
    && $livraison['position_lat'] >= -90 && $livraison['position_lat'] <= 90
    && $livraison['position_lng'] >= -180 && $livraison['position_lng'] <= 180;
if (!$destOk) {
    http_response_code(400);
    echo json_encode(['error' => 'Coordonnées livraison invalides']);
    exit;
}

// Fetch Trajet
$stmt = $db->prepare("SELECT * FROM trajet WHERE id_livraison = :id");
$stmt->execute([':id' => $id]);
$trajet = $stmt->fetch();

if (!$trajet) {
    $trajetData = [
        'id_livraison' => $id,
        'fournisseur_api' => 'iss_demo',
        'identifiant_suivi' => 'API-' . $id . '-' . time(),
        'statut_realtime' => 'initialisation',
        'position_lat' => null,
        'position_lng' => null,
    ];
    
    $sql = "INSERT INTO trajet (id_livraison, fournisseur_api, identifiant_suivi, statut_realtime, position_lat, position_lng) 
            VALUES (:id_livraison, :fournisseur_api, :identifiant_suivi, :statut_realtime, :position_lat, :position_lng)";
    $stmt = $db->prepare($sql);
    $stmt->execute($trajetData);
    $trajetId = $db->lastInsertId();

    $stmt = $db->prepare("SELECT * FROM trajet WHERE id_trajet = :id");
    $stmt->execute([':id' => $trajetId]);
    $trajet = $stmt->fetch();
}

$liveData = $tracking->simulateProgress($livraison, $trajet);

// Update trajet with new position and route data (CRITICAL: prevents glitching!)
if ($liveData) {
    $updateSql = "UPDATE trajet 
                  SET position_lat = :lat, 
                      position_lng = :lng, 
                      statut_realtime = :statut,
                      route_json = :route_json,
                      current_index = :current_index,
                      derniere_mise_a_jour = NOW()
                  WHERE id_trajet = :id";
    $updateStmt = $db->prepare($updateSql);
    $updateStmt->execute([
        ':lat' => $liveData['latitude'],
        ':lng' => $liveData['longitude'],
        ':statut' => $liveData['statut'],
        ':route_json' => $liveData['route_json'],
        ':current_index' => $liveData['current_index'],
        ':id' => $trajet['id_trajet']
    ]);
    
    // AUTO-COMPLETE: If arrived at destination, mark delivery as completed
    if (isset($liveData['has_arrived']) && $liveData['has_arrived'] === true) {
        if ($livraison['statut'] !== 'livrée') {
            $completeSql = "UPDATE livraison 
                           SET statut = 'livrée',
                               date_livraison = CURDATE()
                           WHERE id_livraison = :id";
            $completeStmt = $db->prepare($completeSql);
            $completeStmt->execute([':id' => $livraison['id_livraison']]);
            
            // Update local livraison data
            $livraison['statut'] = 'livrée';
            
            error_log("✅ Delivery #{$livraison['id_livraison']} automatically marked as 'livrée' (arrived at destination)");
        }
    }
    
    // Refresh trajet data after update
    $stmt = $db->prepare("SELECT * FROM trajet WHERE id_trajet = :id");
    $stmt->execute([':id' => $trajet['id_trajet']]);
    $trajet = $stmt->fetch();
}

$origin = $liveData ? $liveData['origin'] : $tracking->getOrigin();
$destination = [
    'lat' => (float)$livraison['position_lat'],
    'lng' => (float)$livraison['position_lng'],
];
$current = [
    'lat' => $liveData ? $liveData['latitude'] : (float)$trajet['position_lat'],
    'lng' => $liveData ? $liveData['longitude'] : (float)$trajet['position_lng'],
];
$progress = $tracking->progressData($origin, $destination, $current);
$loc = $tracking->reverseGeocode($current['lat'], $current['lng']);

// Extract route coordinates if available
$route = [];
if ($liveData && isset($liveData['route'])) {
    $route = $liveData['route'];
} elseif ($trajet && !empty($trajet['route_json'])) {
    $route = json_decode($trajet['route_json'], true) ?: [];
}

echo json_encode([
    'livraison' => [
        'id' => (int)$livraison['id_livraison'],
        'statut' => $livraison['statut'],
        'mode' => $livraison['mode_livraison'],
        'prix' => (float)$livraison['prix_livraison'],
    ],
    'trajet' => [
        'id' => (int)$trajet['id_trajet'],
        'statut_realtime' => $liveData ? $liveData['statut'] : $trajet['statut_realtime'],
        'position_lat' => $current['lat'],
        'position_lng' => $current['lng'],
        'current_index' => (int)($trajet['current_index'] ?? 0),
    ],
    'origin' => $origin,
    'destination' => $destination,
    'route' => $route,
    'progress' => $progress,
    'location' => $loc,
]);
?>