<?php

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../Services/TrackingService.php';
require_once __DIR__ . '/../../Models/Livraison.php';
require_once __DIR__ . '/../../Models/Trajet.php';

class LivraisonController {
    private PDO $db;
    private TrackingService $trackingService;
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
    private $defaultUserId = 1;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->trackingService = new TrackingService();
    }

    public function afficherPage() {
        $message = '';
        $messageType = '';
        $requestedUserId = isset($_GET['id_utilisateur']) ? max(1, (int)$_GET['id_utilisateur']) : $this->defaultUserId;
        $activeUserId = $requestedUserId;

        $commandes = $this->fetchCommandesByUtilisateur($activeUserId);
        if (!isset($_GET['id_utilisateur']) && empty($commandes)) {
            $fallbackUserId = $this->findFirstUserIdWithCommande();
            if ($fallbackUserId && $fallbackUserId !== $activeUserId) {
                $activeUserId = $fallbackUserId;
                $commandes = $this->fetchCommandesByUtilisateur($activeUserId);
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            [$message, $messageType] = $this->gererAction($activeUserId, $_POST);
            $commandes = $this->fetchCommandesByUtilisateur($activeUserId);
        }

        $jeux = $this->fetchAllJeux();
        $livraisons = $this->fetchLivraisonsByUtilisateur($activeUserId);
        $profil = $this->findUserProfile($activeUserId);
        $origin = $this->trackingService->getOrigin();

        foreach ($livraisons as &$livraison) {
            $trajet = null;
            $peutSuivre = in_array($livraison['statut'], ['en_route', 'livrée'], true)
                && $livraison['position_lat'] !== null
                && $livraison['position_lng'] !== null;

            if ($peutSuivre) {
                $trajet = $this->synchroniserTrajetAutomatique($livraison);
            }

            if (!$trajet) {
                $trajet = $this->findTrajetByLivraison($livraison['id_livraison']);
            }

            if ($trajet) {
                $livraison['trajet'] = $trajet;
            }
        }

        $idsCommandesLivrees = array_column($livraisons, 'id_commande');

        $data = [
            'idUtilisateur' => $activeUserId,
            'commandes' => $commandes,
            'jeux' => $jeux,
            'livraisons' => $livraisons,
            'idsCommandesLivrees' => $idsCommandesLivrees,
            'statuts' => $this->statuts,
            'message' => $message,
            'messageType' => $messageType,
            'deliveryModes' => $this->deliveryModes,
            'transportTypes' => $this->transportTypes,
            'profil' => $profil,
            'origin' => $origin,
        ];

        extract($data);
        require_once __DIR__ . '/../../Views/frontoffice/livraison_view.php';
    }

    private function gererAction(int $idUtilisateur, array $postData): array {
        $action = $postData['action'];

        switch ($action) {
            case 'creer_livraison':
                return $this->creerLivraison($idUtilisateur, $postData);
            case 'creer_commande':
                return $this->creerCommande($idUtilisateur, $postData);
            case 'modifier_livraison':
                return $this->modifierLivraison($idUtilisateur, $postData);
            case 'supprimer_livraison':
                return $this->supprimerLivraison($idUtilisateur, $postData);
            case 'rafraichir_trajet':
                return $this->rafraichirTrajet((int)$postData['id_livraison']);
            default:
                return ['Action inconnue', 'error'];
        }
    }

    private function creerLivraison(int $idUtilisateur, array $postData): array {
        $idCommande = isset($postData['id_commande']) ? (int)$postData['id_commande'] : 0;
        $adresse = trim($postData['adresse_complete'] ?? '');
        $ville = trim($postData['ville'] ?? '');
        $codePostal = trim($postData['code_postal'] ?? '');
        $notes = trim($postData['notes_client'] ?? '');
        $mode = $postData['mode_livraison'] ?? 'standard';

        if ($idCommande <= 0) {
            return ['Commande invalide', 'error'];
        }

        if (!isset($this->deliveryModes[$mode])) {
            $mode = 'standard';
        }

        $dateLivraison = $this->calculerDateLivraison($mode);
        $positionLat = null;
        $positionLng = null;
        if (isset($postData['position_lat'], $postData['position_lng']) && $postData['position_lat'] !== '' && $postData['position_lng'] !== '') {
            $lat = (float)$postData['position_lat'];
            $lng = (float)$postData['position_lng'];
            if ($lat >= -90 && $lat <= 90 && $lng >= -180 && $lng <= 180) {
                $positionLat = $lat;
                $positionLng = $lng;
            }
        }
        if ($positionLat === null || $positionLng === null) {
            $coords = $this->geocodeAdresse($adresse, $codePostal, $ville);
            $positionLat = $coords['lat'] ?? null;
            $positionLng = $coords['lng'] ?? null;
        }

        if (($positionLat === null || $positionLng === null) && (empty($adresse) || empty($ville) || empty($codePostal))) {
            return ['Choisis la destination sur la carte', 'error'];
        }

        if ((empty($adresse) || empty($ville) || empty($codePostal)) && $positionLat !== null && $positionLng !== null) {
            $rev = $this->trackingService->reverseGeocode($positionLat, $positionLng);
            $adresse = $adresse ?: ($rev['display_name'] ?? '');
            $ville = $ville ?: ($rev['city'] ?? '');
            $codePostal = $codePostal ?: '';
        }

        if (!$this->utilisateurPossedeCommande($idUtilisateur, $idCommande)) {
            return ['Commande introuvable pour cet utilisateur', 'error'];
        }

        if ($this->commandeHasLivraison($idCommande)) {
            return ['Une livraison est déjà planifiée pour cette commande', 'warning'];
        }

        $stmt = $this->db->prepare('
            INSERT INTO livraison (
                id_commande, adresse_complete, ville, code_postal,
                date_livraison, mode_livraison, prix_livraison,
                transport_type, statut, notes_client, position_lat, position_lng
            ) VALUES (
                :id_commande, :adresse, :ville, :code_postal,
                :date_livraison, :mode_livraison, :prix_livraison,
                :transport_type, :statut, :notes_client, :position_lat, :position_lng
            )
        ');
        $ok = $stmt->execute([
            ':id_commande' => $idCommande,
            ':adresse' => $adresse,
            ':ville' => $ville,
            ':code_postal' => $codePostal,
            ':date_livraison' => $dateLivraison,
            ':mode_livraison' => $mode,
            ':prix_livraison' => $this->deliveryModes[$mode]['price'],
            ':transport_type' => null,
            ':statut' => 'preparée',
            ':notes_client' => $notes,
            ':position_lat' => $positionLat,
            ':position_lng' => $positionLng,
        ]);

        return $ok ? ['Livraison créée avec succès', 'success'] : ['Impossible de créer la livraison', 'error'];
    }

    private function modifierLivraison(int $idUtilisateur, array $postData): array {
        $idLivraison = isset($postData['id_livraison']) ? (int)$postData['id_livraison'] : 0;

        if ($idLivraison <= 0) {
            return ['Livraison invalide', 'error'];
        }

        $livraison = $this->findLivraisonById($idLivraison);
        if (!$livraison) {
            return ['Livraison introuvable', 'error'];
        }

        if (!$this->utilisateurPossedeCommande($idUtilisateur, (int)$livraison['id_commande'])) {
            return ['Livraison introuvable ou non autorisée', 'error'];
        }

        $data = [];
        if (!empty($postData['date_livraison'])) {
            $data['date_livraison'] = $postData['date_livraison'];
        }
        if (isset($postData['notes_client'])) {
            $data['notes_client'] = trim($postData['notes_client']);
        }

        if (empty($data)) {
            return ['Aucune modification reçue', 'warning'];
        }
        $success = $this->updateLivraison($idLivraison, $data);
        return $success ? ['Livraison mise à jour', 'success'] : ['Erreur lors de la mise à jour', 'error'];
    }

    private function supprimerLivraison(int $idUtilisateur, array $postData): array {
        $idLivraison = isset($postData['id_livraison']) ? (int)$postData['id_livraison'] : 0;

        if ($idLivraison <= 0) {
            return ['Identifiant de livraison invalide', 'error'];
        }

        $livraison = $this->findLivraisonById($idLivraison);
        if (!$livraison) {
            return ['Livraison introuvable', 'error'];
        }

        if (!$this->utilisateurPossedeCommande($idUtilisateur, (int)$livraison['id_commande'])) {
            return ['Livraison introuvable ou non autorisée', 'error'];
        }

        $this->deleteTrajetsByLivraison($idLivraison);
        $success = $this->deleteLivraison($idLivraison);

        return $success ? ['Livraison supprimée', 'success'] : ['Erreur lors de la suppression', 'error'];
    }

    private function rafraichirTrajet(int $idLivraison): array {
        if ($idLivraison <= 0) {
            return ['Livraison invalide', 'error'];
        }

        $livraison = $this->findLivraisonById($idLivraison);
        if (!$livraison) {
            return ['Livraison introuvable', 'error'];
        }

        if ($livraison['position_lat'] === null || $livraison['position_lng'] === null) {
            return ['Ajoute la destination pour lancer le suivi', 'error'];
        }

        $trajet = $this->synchroniserTrajetAutomatique($livraison);
        if (!$trajet) {
            return ['Impossible de synchroniser le trajet', 'error'];
        }

        return ['Position actualisée', 'success'];
    }

    private function synchroniserTrajetAutomatique(array $livraison): ?array {
        $idLivraison = (int)$livraison['id_livraison'];
        $trajet = $this->findTrajetByLivraison($idLivraison);

        if (!$trajet) {
            $trajetId = $this->insertTrajet([
                'id_livraison' => $idLivraison,
                'fournisseur_api' => 'iss_demo',
                'identifiant_suivi' => 'LIV-' . $idLivraison . '-' . time(),
                'statut_realtime' => 'initialisation',
                'position_lat' => null,
                'position_lng' => null,
                'route_json' => null,
                'current_index' => 0,
            ]);
            if (!$trajetId) {
                return null;
            }
            $trajet = $this->findTrajetById($trajetId);
        }

        $liveData = $this->trackingService->simulateProgress($livraison, $trajet);
        if (!$liveData) {
            return $trajet;
        }

        // Update with route persistence and index tracking
        $this->updateTrajetPosition($trajet['id_trajet'], [
            'statut_realtime' => $liveData['statut'] ?? 'inconnu',
            'position_lat' => $liveData['latitude'],
            'position_lng' => $liveData['longitude'],
            'route_json' => $liveData['route_json'] ?? $trajet['route_json'],
            'current_index' => $liveData['current_index'] ?? 0,
        ]);

        return $this->findTrajetById($trajet['id_trajet']);
    }

    private function creerCommande(int $idUtilisateur, array $postData): array {
        $idJeu = isset($postData['id_jeu']) ? (int)$postData['id_jeu'] : 0;
        
        $jeu = null;
        if ($idJeu > 0) {
            $jeu = $this->findJeuById($idJeu);
        }

        if (!$jeu) {
            $stmt = $this->db->query("SELECT * FROM jeu_jeu LIMIT 1");
            $jeu = $stmt->fetch();
        }

        if (!$jeu) {
            try {
                $stmt = $this->db->prepare("INSERT INTO jeu_jeu (titre, prix, description, src_img) VALUES (:titre, :prix, :desc, :img)");
                $stmt->execute([
                    ':titre' => 'Jeu de Démo', 
                    ':prix' => 49.99, 
                    ':desc' => 'Jeu généré pour le test', 
                    ':img' => 'https://placehold.co/600x400?text=Jeu+Demo'
                ]);
                $idJeu = (int)$this->db->lastInsertId();
                $jeu = $this->findJeuById($idJeu);
            } catch (Exception $e) {
                try {
                    $stmt = $this->db->prepare("INSERT INTO jeu_jeu (titre, prix) VALUES (:titre, :prix)");
                    $stmt->execute([':titre' => 'Jeu de Démo', ':prix' => 49.99]);
                    $idJeu = (int)$this->db->lastInsertId();
                    $jeu = $this->findJeuById($idJeu);
                } catch (Exception $ex) {
                    return ['Impossible de créer un jeu de démo (erreur DB)', 'error'];
                }
            }
        }

        if (!$jeu) {
            return ['Jeu introuvable et impossible à créer', 'error'];
        }
        
        $idJeu = (int)$jeu['id_jeu'];

        try {
            $numero = 'CMD-' . $idUtilisateur . '-' . strtoupper(bin2hex(random_bytes(3)));
        } catch (Exception $e) {
            $numero = 'CMD-' . $idUtilisateur . '-' . strtoupper(uniqid());
        }

        $stmt = $this->db->prepare('
            INSERT INTO jeu_achete (user_id, jeu_id, numero_commande, total, statut, date_achat)
            VALUES (:user, :jeu, :numero, :total, :statut, NOW())
        ');
        $stmt->execute([
            ':user' => $idUtilisateur,
            ':jeu' => $idJeu,
            ':numero' => $numero,
            ':total' => $jeu['prix'],
            ':statut' => 'payée',
        ]);

        return ['Commande fictive créée (#' . $numero . ')', 'success'];
    }

    private function calculerDateLivraison(string $mode): string {
        $now = new DateTime('now');
        switch ($mode) {
            case 'express':
                $now->modify('+12 hours');
                break;
            case 'super_fast':
                $now->modify('+4 hours');
                break;
            default:
                $now->modify('+4 days');
                break;
        }
        return $now->format('Y-m-d');
    }

    private function geocodeAdresse(string $adresse, string $codePostal, string $ville): array {
        $query = urlencode($adresse . ', ' . $codePostal . ' ' . $ville);
        $url = 'https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' . $query;

        $context = stream_context_create([
            'http' => [
                'header' => "User-Agent: NextGenLivraison/1.0\r\n",
                'timeout' => 5,
            ]
        ]);

        $response = @file_get_contents($url, false, $context);
        if ($response === false) {
            return [];
        }

        $data = json_decode($response, true);
        if (empty($data) || !isset($data[0]['lat'], $data[0]['lon'])) {
            return [];
        }

        return [
            'lat' => (float)$data[0]['lat'],
            'lng' => (float)$data[0]['lon'],
        ];
    }

    private function fetchCommandesByUtilisateur(int $id): array {
        $stmt = $this->db->prepare('
            SELECT 
                a.id_achat AS id_commande,
                a.user_id AS id_utilisateur,
                a.jeu_id AS id_jeu,
                a.numero_commande,
                a.total,
                a.statut,
                a.date_achat AS date_commande,
                j.titre AS nom_jeu,
                j.src_img AS image_jeu,
                j.prix
            FROM jeu_achete a
            LEFT JOIN jeu_jeu j ON a.jeu_id = j.id_jeu
            WHERE a.user_id = :id
            ORDER BY a.date_achat DESC
        ');
        $stmt->execute([':id' => $id]);
        return $stmt->fetchAll() ?: [];
    }

    private function findFirstUserIdWithCommande(): ?int {
        $stmt = $this->db->query('SELECT user_id FROM jeu_achete ORDER BY date_achat DESC LIMIT 1');
        $row = $stmt->fetch();
        return $row ? (int)$row['user_id'] : null;
    }

    private function fetchAllJeux(): array {
        $stmt = $this->db->query('SELECT id_jeu, titre, prix FROM jeu_jeu ORDER BY titre ASC');
        return $stmt->fetchAll() ?: [];
    }

    private function findJeuById(int $id): ?array {
        $stmt = $this->db->prepare('SELECT * FROM jeu_jeu WHERE id_jeu = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    private function fetchLivraisonsByUtilisateur(int $id): array {
        $stmt = $this->db->prepare('
            SELECT l.*, a.numero_commande, j.titre AS nom_jeu, j.src_img AS image_jeu
            FROM livraison l
            INNER JOIN jeu_achete a ON l.id_commande = a.id_achat
            LEFT JOIN jeu_jeu j ON a.jeu_id = j.id_jeu
            WHERE a.user_id = :id
            ORDER BY l.created_at DESC
        ');
        $stmt->execute([':id' => $id]);
        return $stmt->fetchAll() ?: [];
    }

    private function findLivraisonById(int $id): ?array {
        $stmt = $this->db->prepare('SELECT * FROM livraison WHERE id_livraison = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    private function updateLivraison(int $id, array $data): bool {
        if (empty($data)) {
            return false;
        }
        $set = [];
        $params = [':id' => $id];
        foreach ($data as $column => $value) {
            $set[] = "$column = :$column";
            $params[":$column"] = $value;
        }
        $sql = 'UPDATE livraison SET ' . implode(', ', $set) . ', updated_at = NOW() WHERE id_livraison = :id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    private function deleteLivraison(int $id): bool {
        $stmt = $this->db->prepare('DELETE FROM livraison WHERE id_livraison = :id');
        return $stmt->execute([':id' => $id]);
    }

    private function utilisateurPossedeCommande(int $idUtilisateur, int $idCommande): bool {
        $stmt = $this->db->prepare('
            SELECT COUNT(*) AS total
            FROM jeu_achete
            WHERE id_achat = :commande AND user_id = :user
        ');
        $stmt->execute([':commande' => $idCommande, ':user' => $idUtilisateur]);
        $row = $stmt->fetch();
        return !empty($row['total']);
    }

    private function deleteTrajetsByLivraison(int $idLivraison): void {
        $stmt = $this->db->prepare('DELETE FROM trajet WHERE id_livraison = :id');
        $stmt->execute([':id' => $idLivraison]);
    }

    private function findTrajetByLivraison(int $idLivraison): ?array {
        $stmt = $this->db->prepare('SELECT * FROM trajet WHERE id_livraison = :id LIMIT 1');
        $stmt->execute([':id' => $idLivraison]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    private function findTrajetById(int $id): ?array {
        $stmt = $this->db->prepare('SELECT * FROM trajet WHERE id_trajet = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    private function insertTrajet(array $data): ?int {
        $stmt = $this->db->prepare('
            INSERT INTO trajet (
                id_livraison, fournisseur_api, identifiant_suivi,
                statut_realtime, position_lat, position_lng, route_json, current_index
            ) VALUES (
                :id_livraison, :fournisseur_api, :identifiant_suivi,
                :statut_realtime, :position_lat, :position_lng, :route_json, :current_index
            )
        ');
        $ok = $stmt->execute([
            ':id_livraison' => $data['id_livraison'],
            ':fournisseur_api' => $data['fournisseur_api'],
            ':identifiant_suivi' => $data['identifiant_suivi'],
            ':statut_realtime' => $data['statut_realtime'],
            ':position_lat' => $data['position_lat'],
            ':position_lng' => $data['position_lng'],
            ':route_json' => $data['route_json'] ?? null,
            ':current_index' => $data['current_index'] ?? 0,
        ]);
        return $ok ? (int)$this->db->lastInsertId() : null;
    }

    private function updateTrajetPosition(int $id, array $data): void {
        $stmt = $this->db->prepare('
            UPDATE trajet
            SET statut_realtime = :statut,
                position_lat = :lat,
                position_lng = :lng,
                route_json = COALESCE(:route_json, route_json),
                current_index = :current_index,
                derniere_mise_a_jour = NOW()
            WHERE id_trajet = :id
        ');
        $stmt->execute([
            ':statut' => $data['statut_realtime'],
            ':lat' => $data['position_lat'],
            ':lng' => $data['position_lng'],
            ':route_json' => $data['route_json'] ?? null,
            ':current_index' => $data['current_index'] ?? 0,
            ':id' => $id,
        ]);
    }

    private function findUserProfile(int $idUtilisateur): ?array {
        $stmt = $this->db->prepare('SELECT id_user, nom, prenom, email FROM utilisateur WHERE id_user = :id');
        $stmt->execute([':id' => $idUtilisateur]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    private function commandeHasLivraison(int $idCommande): bool {
        $stmt = $this->db->prepare('SELECT COUNT(*) as count FROM livraison WHERE id_commande = :id');
        $stmt->execute([':id' => $idCommande]);
        $row = $stmt->fetch();
        return ($row['count'] ?? 0) > 0;
    }
}
