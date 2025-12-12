<?php

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../Models/Trajet.php';
require_once __DIR__ . '/../../Services/TrackingService.php';

class TrajetApiController {
    private PDO $db;
    private TrackingService $tracking;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->tracking = new TrackingService();
    }

    public function getTrackingData(int $idLivraison): array {
        $livraison = $this->findLivraison($idLivraison);
        
        if (!$livraison) {
            http_response_code(404);
            return ['error' => 'Livraison introuvable'];
        }

        $destOk = isset($livraison['position_lat'], $livraison['position_lng'])
            && $livraison['position_lat'] !== null && $livraison['position_lng'] !== null
            && $livraison['position_lat'] >= -90 && $livraison['position_lat'] <= 90
            && $livraison['position_lng'] >= -180 && $livraison['position_lng'] <= 180;
        
        if (!$destOk) {
            http_response_code(400);
            return ['error' => 'Coordonnées livraison invalides'];
        }

        $trajet = $this->findOrCreateTrajet($idLivraison);
        
        $liveData = $this->tracking->simulateProgress($livraison, $trajet);

        if ($liveData) {
            $this->updateTrajet($trajet['id_trajet'], $liveData);
            
            if (isset($liveData['has_arrived']) && $liveData['has_arrived'] === true) {
                if ($livraison['statut'] !== 'livrée') {
                    $this->markAsDelivered($livraison['id_livraison']);
                    $livraison['statut'] = 'livrée';
                    error_log("✅ Delivery #{$livraison['id_livraison']} automatically marked as 'livrée' (arrived at destination)");
                }
            }
            
            $trajet = $this->findTrajet($idLivraison);
        }

        $origin = $liveData ? $liveData['origin'] : $this->tracking->getOrigin();
        $destination = [
            'lat' => (float)$livraison['position_lat'],
            'lng' => (float)$livraison['position_lng'],
        ];
        $current = [
            'lat' => $liveData ? $liveData['latitude'] : (float)$trajet['position_lat'],
            'lng' => $liveData ? $liveData['longitude'] : (float)$trajet['position_lng'],
        ];
        $progress = $this->tracking->progressData($origin, $destination, $current);
        $loc = $this->tracking->reverseGeocode($current['lat'], $current['lng']);

        $route = [];
        if ($liveData && isset($liveData['route'])) {
            $route = $liveData['route'];
        } elseif ($trajet && !empty($trajet['route_json'])) {
            $route = json_decode($trajet['route_json'], true) ?: [];
        }

        return [
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
        ];
    }

    private function findLivraison(int $id) {
        $stmt = $this->db->prepare("SELECT * FROM livraison WHERE id_livraison = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    private function findTrajet(int $idLivraison) {
        $stmt = $this->db->prepare("SELECT * FROM trajet WHERE id_livraison = :id");
        $stmt->execute([':id' => $idLivraison]);
        return $stmt->fetch();
    }

    private function findOrCreateTrajet(int $idLivraison) {
        $trajet = $this->findTrajet($idLivraison);
        
        if (!$trajet) {
            $trajetData = [
                'id_livraison' => $idLivraison,
                'fournisseur_api' => 'iss_demo',
                'identifiant_suivi' => 'API-' . $idLivraison . '-' . time(),
                'statut_realtime' => 'initialisation',
                'position_lat' => null,
                'position_lng' => null,
            ];
            
            $sql = "INSERT INTO trajets (id_livraison, fournisseur_api, identifiant_suivi, statut_realtime, position_lat, position_lng) 
                    VALUES (:id_livraison, :fournisseur_api, :identifiant_suivi, :statut_realtime, :position_lat, :position_lng)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($trajetData);
            $trajetId = $this->db->lastInsertId();

            $stmt = $this->db->prepare("SELECT * FROM trajet WHERE id_trajet = :id");
            $stmt->execute([':id' => $trajetId]);
            $trajet = $stmt->fetch();
        }
        
        return $trajet;
    }

    private function updateTrajet(int $id, array $liveData): void {
        $updateSql = "UPDATE trajets 
                      SET position_lat = :lat, 
                          position_lng = :lng, 
                          statut_realtime = :statut,
                          route_json = :route_json,
                          current_index = :current_index,
                          derniere_mise_a_jour = NOW()
                      WHERE id_trajet = :id";
        $updateStmt = $this->db->prepare($updateSql);
        $updateStmt->execute([
            ':lat' => $liveData['latitude'],
            ':lng' => $liveData['longitude'],
            ':statut' => $liveData['statut'],
            ':route_json' => $liveData['route_json'],
            ':current_index' => $liveData['current_index'],
            ':id' => $id
        ]);
    }

    private function markAsDelivered(int $idLivraison): void {
        $completeSql = "UPDATE livraison 
                       SET statut = 'livrée',
                           date_livraison = CURDATE()
                       WHERE id_livraison = :id";
        $completeStmt = $this->db->prepare($completeSql);
        $completeStmt->execute([':id' => $idLivraison]);
    }
}


