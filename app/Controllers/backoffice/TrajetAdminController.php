<?php

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../Services/TrackingService.php';

class TrajetAdminController {
    private PDO $db;
    private TrackingService $tracking;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->tracking = new TrackingService();
    }

    public function afficherPage(): void {
        $message = null;
        $messageType = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            [$message, $messageType] = $this->handleAction($_POST);
        }

        $trajets = $this->findAllWithLivraisons();

        $data = [
            'message' => $message,
            'messageType' => $messageType,
            'trajets' => $trajets,
        ];

        extract($data);
        require __DIR__ . '/../../Views/backoffice/trajets.php';
    }

    private function handleAction(array $payload): array {
        switch ($payload['action']) {
            case 'sync_trajet':
                return $this->syncTrajet((int)$payload['id_trajet']);
            case 'delete_trajet':
                return $this->deleteTrajet((int)$payload['id_trajet']);
            default:
                return ['Action inconnue', 'error'];
        }
    }

    private function findAllWithLivraisons(): array {
        $sql = "
            SELECT t.*, 
                   l.adresse_complete, 
                   l.ville, 
                   l.code_postal, 
                   l.statut as statut_livraison
            FROM trajet t
            JOIN livraison l ON t.id_livraison = l.id_livraison
            ORDER BY t.derniere_mise_a_jour DESC
        ";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    private function findById(int $id) {
        $stmt = $this->db->prepare("SELECT * FROM trajet WHERE id_trajet = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    private function findLivraisonById(int $id) {
        $stmt = $this->db->prepare("SELECT * FROM livraison WHERE id_livraison = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    private function updateTrajetPosition(int $id, array $data): bool {
        $sql = "UPDATE trajets SET statut_realtime = :statut_realtime, position_lat = :position_lat, position_lng = :position_lng, derniere_mise_a_jour = NOW() WHERE id_trajet = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':statut_realtime' => $data['statut_realtime'],
            ':position_lat' => $data['position_lat'],
            ':position_lng' => $data['position_lng'],
            ':id' => $id
        ]);
    }

    private function syncTrajet(int $idTrajet): array {
        if ($idTrajet <= 0) {
            return ['Identifiant invalide', 'error'];
        }

        $trajet = $this->findById($idTrajet);
        if (!$trajet) {
            return ['Trajet introuvable', 'error'];
        }

        $livraison = $this->findLivraisonById((int)$trajet['id_livraison']);
        if (!$livraison) {
            return ['Livraison introuvable pour ce trajet', 'error'];
        }

        $liveData = $this->tracking->simulateProgress($livraison, $trajet);
        if (!$liveData) {
            return ['Impossible de joindre l'API de tracking', 'error'];
        }

        $this->updateTrajetPosition($idTrajet, [
            'statut_realtime' => $liveData['statut'],
            'position_lat' => $liveData['latitude'],
            'position_lng' => $liveData['longitude'],
        ]);

        return ['Trajet synchronisé', 'success'];
    }

    private function deleteTrajet(int $idTrajet): array {
        if ($idTrajet <= 0) {
            return ['Identifiant invalide', 'error'];
        }

        $success = $this->deleteTrajetData($idTrajet);
        return [$success ? 'Trajet supprimé' : 'Suppression impossible', $success ? 'success' : 'error'];
    }

    private function deleteTrajetData(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM trajet WHERE id_trajet = :id");
        return $stmt->execute([':id' => $id]);
    }
}



