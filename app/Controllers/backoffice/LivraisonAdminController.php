<?php

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../Services/TrackingService.php';
require_once __DIR__ . '/../../Models/Livraison.php';
require_once __DIR__ . '/../../Models/Trajet.php';

class LivraisonAdminController {
    private $db;
    private $tracking;
    private $statuts = ['preparée', 'en_route', 'livrée', 'annulée'];
    private $deliveryModes = [
        'standard' => ['label' => 'Standard (3-5j)', 'price' => 0],
        'express' => ['label' => 'Express (24h)', 'price' => 9.99],
        'super_fast' => ['label' => 'Super Fast (4h)', 'price' => 19.99],
    ];
    private $transportTypes = [
        'camion' => 'Camion longue distance',
        'fourgon' => 'Fourgon urbain',
        'moto' => 'Moto express',
        'velo' => 'Vélo cargo',
        'drone' => 'Drone rapide'
    ];

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->tracking = new TrackingService();
    }

    public function afficherPage(): void {
        $message = null;
        $messageType = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            [$message, $messageType] = $this->dispatchAction($_POST);
        }

        $stats = $this->countByStatut();
        $livraisons = $this->findAllWithRelations();
        $commandes = $this->findAllCommandesDetailed();
        $origin = $this->tracking->getOrigin();

        foreach ($livraisons as &$livraison) {
            $livraison['trajet'] = $this->findTrajetByLivraison((int)$livraison['id_livraison']);
        }

        $data = [
            'message' => $message,
            'messageType' => $messageType,
            'statuts' => $this->statuts,
            'livraisons' => $livraisons,
            'stats' => $stats,
            'commandes' => $commandes,
            'deliveryModes' => $this->deliveryModes,
            'transportTypes' => $this->transportTypes,
            'origin' => $origin,
        ];

        extract($data);
        require __DIR__ . '/../../Views/backoffice/livraisons.php';
    }

    private function dispatchAction(array $payload): array {
        switch ($payload['action']) {
            case 'create_livraison':
                return $this->createLivraison($payload);
            case 'update_statut':
                return $this->updateLivraison($payload);
            case 'delete_livraison':
                return $this->deleteLivraison($payload);
            case 'refresh_trajet':
                return $this->refreshTrajet($payload);
            case 'confirm_livraison':
                return $this->confirmLivraison($payload);
            default:
                return ['Action inconnue', 'error'];
        }
    }

    private function findAllWithRelations(): array {
        $sql = "
            SELECT l.*, 
                   c.numero_commande, 
                   u.nom as nom_utilisateur, 
                   u.prenom as prenom_utilisateur,
                   j.titre as nom_jeu
            FROM livraison l
            LEFT JOIN jeu_achete c ON l.id_commande = c.id_achat
            LEFT JOIN utilisateur u ON c.user_id = u.id_user
            LEFT JOIN jeu_jeu j ON c.jeu_id = j.id_jeu
            ORDER BY l.created_at DESC
        ";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    private function countByStatut(): array {
        $sql = "SELECT statut, COUNT(*) as count FROM livraison GROUP BY statut";
        $stmt = $this->db->query($sql);
        $results = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        $stats = [];
        foreach ($this->statuts as $statut) {
            $stats[$statut] = $results[$statut] ?? 0;
        }
        return $stats;
    }

    private function findAllCommandesDetailed(): array {
        $sql = "
            SELECT c.id_achat as id_commande, 
                   c.numero_commande, 
                   c.date_achat, 
                   u.nom as nom_utilisateur, 
                   u.prenom as prenom_utilisateur,
                   j.titre as nom_jeu
            FROM jeu_achete c
            LEFT JOIN utilisateur u ON c.user_id = u.id_user
            LEFT JOIN jeu_jeu j ON c.jeu_id = j.id_jeu
            LEFT JOIN livraison l ON c.id_achat = l.id_commande
            WHERE l.id_livraison IS NULL
            ORDER BY c.date_achat DESC
        ";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    private function findById(int $id) {
        $stmt = $this->db->prepare("SELECT * FROM livraison WHERE id_livraison = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    private function findCommandeById(int $id) {
        $stmt = $this->db->prepare("SELECT * FROM jeu_achete WHERE id_achat = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    private function findTrajetByLivraison(int $idLivraison) {
        $stmt = $this->db->prepare("SELECT * FROM trajet WHERE id_livraison = :id");
        $stmt->execute([':id' => $idLivraison]);
        return $stmt->fetch();
    }

    private function findTrajetById(int $id) {
        $stmt = $this->db->prepare("SELECT * FROM trajet WHERE id_trajet = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    private function insertLivraison(array $data): bool {
        $sql = "INSERT INTO livraison (id_commande, adresse_complete, ville, code_postal, date_livraison, mode_livraison, prix_livraison, transport_type, statut, notes_client, position_lat, position_lng) 
                VALUES (:id_commande, :adresse_complete, :ville, :code_postal, :date_livraison, :mode_livraison, :prix_livraison, :transport_type, :statut, :notes_client, :position_lat, :position_lng)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    private function updateLivraisonData(int $id, array $data): bool {
        $fields = [];
        $params = [':id' => $id];
        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
            $params[":$key"] = $value;
        }
        if (empty($fields)) return false;

        $sql = "UPDATE livraison SET " . implode(', ', $fields) . " WHERE id_livraison = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    private function deleteLivraisonData(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM livraison WHERE id_livraison = :id");
        return $stmt->execute([':id' => $id]);
    }

    private function deleteTrajetByLivraison(int $idLivraison): bool {
        $stmt = $this->db->prepare("DELETE FROM trajet WHERE id_livraison = :id");
        return $stmt->execute([':id' => $idLivraison]);
    }

    private function insertTrajet(array $data): int {
        $sql = "INSERT INTO trajets (id_livraison, fournisseur_api, identifiant_suivi, statut_realtime, position_lat, position_lng) 
                VALUES (:id_livraison, :fournisseur_api, :identifiant_suivi, :statut_realtime, :position_lat, :position_lng)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        return (int)$this->db->lastInsertId();
    }

    private function updateTrajetPosition(int $id, array $data): bool {
        $sql = "UPDATE trajets SET 
                statut_realtime = :statut_realtime, 
                position_lat = :position_lat, 
                position_lng = :position_lng, 
                route_json = :route_json,
                current_index = :current_index,
                total_points = :total_points,
                derniere_mise_a_jour = NOW() 
                WHERE id_trajet = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':statut_realtime' => $data['statut_realtime'],
            ':position_lat' => $data['position_lat'],
            ':position_lng' => $data['position_lng'],
            ':route_json' => $data['route_json'] ?? null,
            ':current_index' => $data['current_index'] ?? null,
            ':total_points' => $data['total_points'] ?? null,
            ':id' => $id
        ]);
    }

    private function createLivraison(array $payload): array {
        $idCommande = isset($payload['id_commande']) ? (int)$payload['id_commande'] : 0;
        $adresse = trim($payload['adresse_complete'] ?? '');
        $ville = trim($payload['ville'] ?? '');
        $codePostal = trim($payload['code_postal'] ?? '');
        $dateLivraison = trim($payload['date_livraison'] ?? '');
        $notes = trim($payload['notes_client'] ?? '');
        $mode = $payload['mode_livraison'] ?? 'standard';
        $transport = $payload['transport_type'] ?? 'fourgon';
        $prix = isset($payload['prix_livraison']) && $payload['prix_livraison'] !== '' ? (float)$payload['prix_livraison'] : null;
        $positionLat = isset($payload['position_lat']) && $payload['position_lat'] !== '' ? (float)$payload['position_lat'] : null;
        $positionLng = isset($payload['position_lng']) && $payload['position_lng'] !== '' ? (float)$payload['position_lng'] : null;

        if ($idCommande <= 0 || empty($adresse) || empty($ville) || empty($codePostal) || empty($dateLivraison)) {
            return ['Champs manquants pour créer la livraison', 'error'];
        }

        $commande = $this->findCommandeById($idCommande);
        if (!$commande) {
            return ['Commande introuvable', 'error'];
        }

        if (!isset($this->deliveryModes[$mode])) {
            $mode = 'standard';
        }

        if (!isset($this->transportTypes[$transport])) {
            $transport = 'fourgon';
        }

        if ($prix === null) {
            $prix = $this->deliveryModes[$mode]['price'];
        }

        $data = [
            'id_commande' => $idCommande,
            'adresse_complete' => $adresse,
            'ville' => $ville,
            'code_postal' => $codePostal,
            'date_livraison' => $dateLivraison,
            'mode_livraison' => $mode,
            'prix_livraison' => $prix,
            'transport_type' => $transport,
            'statut' => 'preparée',
            'notes_client' => $notes,
            'position_lat' => $positionLat,
            'position_lng' => $positionLng,
        ];

        $this->insertLivraison($data);

        return ['Livraison créée par l’administrateur', 'success'];
    }

    private function updateLivraison(array $payload): array {
        $id = isset($payload['id_livraison']) ? (int)$payload['id_livraison'] : 0;
        if ($id <= 0) {
            return ['Livraison invalide', 'error'];
        }

        $data = [];

        if (!empty($payload['statut']) && in_array($payload['statut'], $this->statuts, true)) {
            $data['statut'] = $payload['statut'];
        }

        if (!empty($payload['date_livraison'])) {
            $data['date_livraison'] = $payload['date_livraison'];
        }

        if (isset($payload['notes_client'])) {
            $data['notes_client'] = trim($payload['notes_client']);
        }

        if (!empty($payload['mode_livraison']) && isset($this->deliveryModes[$payload['mode_livraison']])) {
            $data['mode_livraison'] = $payload['mode_livraison'];
        }

        if (!empty($payload['transport_type']) && isset($this->transportTypes[$payload['transport_type']])) {
            $data['transport_type'] = $payload['transport_type'];
        }

        if (isset($payload['prix_livraison']) && $payload['prix_livraison'] !== '') {
            $data['prix_livraison'] = (float)$payload['prix_livraison'];
        }

        if (isset($payload['position_lat']) && $payload['position_lat'] !== '' && isset($payload['position_lng']) && $payload['position_lng'] !== '') {
            $data['position_lat'] = (float)$payload['position_lat'];
            $data['position_lng'] = (float)$payload['position_lng'];
        }

        if (empty($data)) {
            return ['Aucune modification détectée', 'warning'];
        }

        $existing = $this->findById($id);
        if (!$existing) {
            return ['Livraison introuvable', 'error'];
        }
        
        $success = $this->updateLivraisonData($id, $data);
        return [$success ? 'Livraison mise à jour' : 'Erreur lors de la mise à jour', $success ? 'success' : 'error'];
    }

    private function deleteLivraison(array $payload): array {
        $id = isset($payload['id_livraison']) ? (int)$payload['id_livraison'] : 0;
        if ($id <= 0) {
            return ['Identifiant invalide', 'error'];
        }

        try {
            $this->db->beginTransaction();

            $this->deleteTrajetByLivraison($id);

            $success = $this->deleteLivraisonData($id);

            if ($success) {
                $this->db->commit();
                return ['Livraison supprimée définitivement', 'success'];
            } else {
                $this->db->rollBack();
                return ['Impossible de supprimer la livraison', 'error'];
            }
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['Erreur technique lors de la suppression: ' . $e->getMessage(), 'error'];
        }
    }

    private function refreshTrajet(array $payload): array {
        $id = isset($payload['id_livraison']) ? (int)$payload['id_livraison'] : 0;
        if ($id <= 0) {
            return ['Livraison invalide', 'error'];
        }

        $livraison = $this->findById($id);
        if (!$livraison) {
            return ['Livraison introuvable', 'error'];
        }

        $trajet = $this->findTrajetByLivraison($id);
        if (!$trajet) {
            $trajetData = [
                'id_livraison' => $id,
                'fournisseur_api' => 'iss_demo',
                'identifiant_suivi' => 'ADM-' . $id . '-' . time(),
                'statut_realtime' => 'initialisation',
                'position_lat' => null,
                'position_lng' => null,
            ];
            
            $trajetId = $this->insertTrajet($trajetData);
            $trajet = $this->findTrajetById((int)$trajetId);
        }

        $liveData = $this->tracking->simulateProgress($livraison, $trajet);
        if (!$liveData) {
            return ['Service de tracking indisponible', 'error'];
        }

        $this->updateTrajetPosition($trajet['id_trajet'], [
            'statut_realtime' => $liveData['statut'],
            'position_lat' => $liveData['latitude'],
            'position_lng' => $liveData['longitude'],
            'route_json' => $liveData['route_json'] ?? null,
            'current_index' => $liveData['current_index'] ?? null,
            'total_points' => $liveData['total_points'] ?? null,
        ]);

        return ['Position rafraîchie', 'success'];
    }

    private function confirmLivraison(array $payload): array {
        $id = isset($payload['id_livraison']) ? (int)$payload['id_livraison'] : 0;
        if ($id <= 0) {
            return ['Livraison invalide', 'error'];
        }

        $livraison = $this->findById($id);
        if (!$livraison) {
            return ['Livraison introuvable', 'error'];
        }

        if (!isset($livraison['position_lat'], $livraison['position_lng']) || $livraison['position_lat'] === null || $livraison['position_lng'] === null) {
            return ['Ajoutez les coordonnées de livraison avant confirmation', 'error'];
        }

        $updateOk = $this->updateLivraisonData($id, ['statut' => 'en_route']);
        if (!$updateOk) {
            return ['Impossible de confirmer la livraison', 'error'];
        }

        $trajet = $this->findTrajetByLivraison($id);
        if (!$trajet) {
            $trajetData = [
                'id_livraison' => $id,
                'fournisseur_api' => 'iss_demo',
                'identifiant_suivi' => 'ADM-' . $id . '-' . time(),
                'statut_realtime' => 'initialisation',
                'position_lat' => null,
                'position_lng' => null,
            ];
            
            $trajetId = $this->insertTrajet($trajetData);
            $trajet = $this->findTrajetById((int)$trajetId);
        }

        $liveData = $this->tracking->simulateProgress($livraison, $trajet);
        if ($liveData) {
            $this->updateTrajetPosition($trajet['id_trajet'], [
                'statut_realtime' => $liveData['statut'],
                'position_lat' => $liveData['latitude'],
                'position_lng' => $liveData['longitude'],
                'route_json' => $liveData['route_json'] ?? null,
                'current_index' => $liveData['current_index'] ?? null,
                'total_points' => $liveData['total_points'] ?? null,
            ]);
        }

        return ['Livraison confirmée et suivi initialisé', 'success'];
    }
}



