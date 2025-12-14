<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Reservation.php';
require_once __DIR__ . '/Controller.php';

class ReservationC extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    // ==================== MÉTHODES BASE DE DONNÉES ====================

    private function getAll()
    {
        $db = Database::getInstance();
        $result = $db->query("
            SELECT r.*, e.titre as evenement_titre 
            FROM reservation r 
            LEFT JOIN evenement e ON r.id_evenement = e.id_evenement 
            ORDER BY r.date_reservation DESC
        ");
        $reservations = [];
        while ($row = $result->fetch()) {
                $res = new Reservation(
                    $row['id_evenement'],
                    $row['nom_complet'],
                    $row['email'],
                    $row['telephone'],
                    $row['nombre_places'],
                    $row['message'],
                    $row['id_reservation'],
                    $row['date_reservation']
                );
                // hydrate points if present in the row
                if (isset($row['points_generes'])) {
                    $res->setPointsGeneres(intval($row['points_generes']));
                }
            $reservations[] = $this->toArray($res, $row['evenement_titre'] ?? '');
        }
        return $reservations;
    }

    private function getById($id)
    {
        $db = Database::getInstance();
        $result = $db->query("SELECT * FROM reservation WHERE id_reservation = ?", [$id]);
        $row = $result->fetch();
        if ($row) {
            $res = new Reservation(
                $row['id_evenement'],
                $row['nom_complet'],
                $row['email'],
                $row['telephone'],
                $row['nombre_places'],
                $row['message'],
                $row['id_reservation'],
                $row['date_reservation']
            );
            if (isset($row['points_generes'])) {
                $res->setPointsGeneres(intval($row['points_generes']));
            }
            return $res;
        }
        return null;
    }

    private function save($reservation)
    {
        $db = Database::getInstance();
        if ($reservation->getIdReservation() > 0) {
            // Update
            try {
                $db->query(
                    "UPDATE reservation SET id_evenement = ?, nom_complet = ?, email = ?, telephone = ?, nombre_places = ?, message = ?, points_generes = ? WHERE id_reservation = ?",
                    [$reservation->getIdEvenement(), $reservation->getNomComplet(), $reservation->getEmail(), $reservation->getTelephone(), $reservation->getNombrePlaces(), $reservation->getMessage(), $reservation->getPointsGeneres(), $reservation->getIdReservation()]
                );
            } catch (Exception $e) {
                // Fallback for databases without points_generes column: update without that field
                $db->query(
                    "UPDATE reservation SET id_evenement = ?, nom_complet = ?, email = ?, telephone = ?, nombre_places = ?, message = ? WHERE id_reservation = ?",
                    [$reservation->getIdEvenement(), $reservation->getNomComplet(), $reservation->getEmail(), $reservation->getTelephone(), $reservation->getNombrePlaces(), $reservation->getMessage(), $reservation->getIdReservation()]
                );
            }
        } else {
            // Insert
            try {
                $db->query(
                    "INSERT INTO reservation (id_evenement, nom_complet, email, telephone, nombre_places, message, date_reservation, points_generes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                    [$reservation->getIdEvenement(), $reservation->getNomComplet(), $reservation->getEmail(), $reservation->getTelephone(), $reservation->getNombrePlaces(), $reservation->getMessage(), $reservation->getDateReservation(), $reservation->getPointsGeneres()]
                );
                $reservation->setIdReservation($db->getConnection()->lastInsertId());
            } catch (Exception $e) {
                // Fallback for DB without points_generes column: insert without that field
                $db->query(
                    "INSERT INTO reservation (id_evenement, nom_complet, email, telephone, nombre_places, message, date_reservation) VALUES (?, ?, ?, ?, ?, ?, ?)",
                    [$reservation->getIdEvenement(), $reservation->getNomComplet(), $reservation->getEmail(), $reservation->getTelephone(), $reservation->getNombrePlaces(), $reservation->getMessage(), $reservation->getDateReservation()]
                );
                $reservation->setIdReservation($db->getConnection()->lastInsertId());
            }
        }
        return $reservation;
    }

    private function deleteById($id)
    {
        if ($id > 0) {
            $db = Database::getInstance();
            $db->query("DELETE FROM reservation WHERE id_reservation = ?", [$id]);
            return true;
        }
        return false;
    }

    private function count()
    {
        $db = Database::getInstance();
        $result = $db->query("SELECT COUNT(*) as total FROM reservation");
        return $result->fetch()['total'];
    }

    private function countToday()
    {
        $db = Database::getInstance();
        $result = $db->query("SELECT COUNT(*) as total FROM reservation WHERE DATE(date_reservation) = CURDATE()");
        return $result->fetch()['total'];
    }

    private function getRecent($limit = 5)
    {
        $db = Database::getInstance();
        $result = $db->query("
            SELECT r.*, e.titre as evenement_titre 
            FROM reservation r 
            LEFT JOIN evenement e ON r.id_evenement = e.id_evenement 
            ORDER BY r.date_reservation DESC 
            LIMIT ?
        ", [$limit]);
        $reservations = [];
        while ($row = $result->fetch()) {
            $res = new Reservation(
                $row['id_evenement'],
                $row['nom_complet'],
                $row['email'],
                $row['telephone'],
                $row['nombre_places'],
                $row['message'],
                $row['id_reservation'],
                $row['date_reservation']
            );
            $reservations[] = $this->toArray($res, $row['evenement_titre'] ?? '');
        }
        return $reservations;
    }

    private function toArray($reservation, $evenement_titre = '')
    {
        return [
            'id_reservation' => $reservation->getIdReservation(),
            'id_evenement' => $reservation->getIdEvenement(),
            'nom_complet' => $reservation->getNomComplet(),
            'email' => $reservation->getEmail(),
            'telephone' => $reservation->getTelephone(),
            'nombre_places' => $reservation->getNombrePlaces(),
                'points_generes' => $reservation->getPointsGeneres(),
            'message' => $reservation->getMessage(),
            'date_reservation' => $reservation->getDateReservation(),
            'evenement_titre' => $evenement_titre
        ];
    }

    // ==================== ACTIONS ====================

    public function index()
    {
        try {
            $reservations = $this->getAll();
            $data = ['reservations' => $reservations];
            $this->render('reservations', $data);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // Admin: create reservation form + handler
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id_evenement = intval($_POST['id_evenement'] ?? 0);
                $nom_complet = trim(strip_tags($_POST['nom_complet'] ?? ''));
                $email = trim(strip_tags($_POST['email'] ?? ''));
                $telephone = trim(strip_tags($_POST['telephone'] ?? ''));
                $nombre_places = intval($_POST['nombre_places'] ?? 0);
                $message = trim(strip_tags($_POST['message'] ?? ''));

                $errors = [];
                if (empty($nom_complet)) $errors['nom_complet'] = 'Le nom complet est obligatoire.';
                if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Veuillez saisir un email valide.';
                if (empty($telephone) || !preg_match('/^[0-9+()\-\s]{6,}$/', $telephone)) $errors['telephone'] = 'Veuillez saisir un numéro de téléphone valide.';
                if ($id_evenement <= 0) $errors['id_evenement'] = 'Veuillez sélectionner un événement.';
                if ($nombre_places <= 0) $errors['nombre_places'] = 'Le nombre de places doit être un nombre positif.';

                if (!empty($errors)) {
                    // Re-render form with errors and previous input
                    $evenementC = new EvenementC();
                    $evenements = $evenementC->getAllEvenements();
                    $data = [
                        'evenements' => $evenements,
                        'old' => [
                            'id_evenement' => $id_evenement,
                            'nom_complet' => $nom_complet,
                            'email' => $email,
                            'telephone' => $telephone,
                            'nombre_places' => $nombre_places,
                            'message' => $message
                        ],
                        'errors' => $errors
                    ];
                    $this->render('reservation_create', $data);
                    return;
                }

                $reservation = new Reservation($id_evenement, $nom_complet, $email, $telephone, $nombre_places, $message);
                // allow admin to set points generated for this reservation (total points)
                $points = intval($_POST['points_generes'] ?? 0);
                $reservation->setPointsGeneres($points);
                $this->saveReservation($reservation);
                $this->setFlash('success', 'Réservation créée avec succès.');
                $this->redirect(WEB_ROOT . '/index.php?c=reservation&a=index');
            } catch (Exception $e) {
                $this->setFlash('error', 'Erreur lors de la création: ' . $e->getMessage());
                $this->redirect(WEB_ROOT . '/index.php?c=reservation&a=index');
            }
        } else {
            try {
                $evenementC = new EvenementC();
                $evenements = $evenementC->getAllEvenements();
                $data = ['evenements' => $evenements];
                $this->render('reservation_create', $data);
            } catch (Exception $e) {
                die('Erreur: ' . $e->getMessage());
            }
        }
    }

    public function delete()
    {
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            $this->redirect(WEB_ROOT . '/index.php?c=reservation&a=index');
        }

        try {
            $reservation = $this->getById($id);
            if ($reservation) {
                $this->deleteById($id);
                $this->setFlash('success', 'Réservation supprimée avec succès.');
            } else {
                $this->setFlash('error', 'Réservation non trouvée.');
            }
        } catch (Exception $e) {
            $this->setFlash('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }

        $this->redirect(WEB_ROOT . '/index.php?c=reservation&a=index');
    }

    // Méthodes publiques pour être utilisées par d'autres contrôleurs
    public function getReservationById($id)
    {
        return $this->getById($id);
    }

    public function saveReservation($reservation)
    {
        return $this->save($reservation);
    }

    public function countReservations()
    {
        return $this->count();
    }

    public function countReservationsToday()
    {
        return $this->countToday();
    }

    public function getRecentReservations($limit = 5)
    {
        return $this->getRecent($limit);
    }
}
?>
