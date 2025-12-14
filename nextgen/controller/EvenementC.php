<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Evenement.php';
require_once __DIR__ . '/../models/CategorieV.php';
require_once __DIR__ . '/Controller.php';

class EvenementC extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    // ==================== MÉTHODES BASE DE DONNÉES ====================

    private function getAll()
    {
        $db = Database::getInstance();
        $result = $db->query("SELECT e.*, c.nom_categorie FROM evenement e LEFT JOIN categoriev c ON e.id_categorie = c.id_categorie ORDER BY e.date_evenement DESC");
        $evenements = [];
        while ($row = $result->fetch()) {
            $evt = new Evenement(
                $row['titre'],
                $row['description'],
                $row['date_evenement'],
                $row['lieu'],
                $row['id_categorie'],
                $row['id_evenement'],
                $row['places_disponibles'] ?? 0
            );
            $evenements[] = $this->toArray($evt, $row['nom_categorie'] ?? '');
        }
        return $evenements;
    }

    private function getById($id)
    {
        $db = Database::getInstance();
        $result = $db->query("SELECT * FROM evenement WHERE id_evenement = ?", [$id]);
        $row = $result->fetch();
        if ($row) {
            return new Evenement(
                $row['titre'],
                $row['description'],
                $row['date_evenement'],
                $row['lieu'],
                $row['id_categorie'],
                $row['id_evenement'],
                $row['places_disponibles'] ?? 0
            );
        }
        return null;
    }

    private function save($evenement)
    {
        $db = Database::getInstance();
        if ($evenement->getIdEvenement() > 0) {
            $db->query(
                "UPDATE evenement SET titre = ?, description = ?, date_evenement = ?, lieu = ?, id_categorie = ?, places_disponibles = ? WHERE id_evenement = ?",
                [$evenement->getTitre(), $evenement->getDescription(), $evenement->getDateEvenement(), $evenement->getLieu(), $evenement->getIdCategorie(), $evenement->getPlacesDisponibles(), $evenement->getIdEvenement()]
            );
        } else {
            $db->query(
                "INSERT INTO evenement (titre, description, date_evenement, lieu, id_categorie, places_disponibles) VALUES (?, ?, ?, ?, ?, ?)",
                [$evenement->getTitre(), $evenement->getDescription(), $evenement->getDateEvenement(), $evenement->getLieu(), $evenement->getIdCategorie(), $evenement->getPlacesDisponibles()]
            );
            $evenement->setIdEvenement($db->getConnection()->lastInsertId());
        }
        return $evenement;
    }

    public function deleteById($id)
    {
        if ($id > 0) {
            $db = Database::getInstance();
            $db->query("DELETE FROM evenement WHERE id_evenement = ?", [$id]);
            return true;
        }
        return false;
    }

    private function count()
    {
        $db = Database::getInstance();
        $result = $db->query("SELECT COUNT(*) as total FROM evenement");
        return $result->fetch()['total'];
    }

    private function toArray($evenement, $nom_categorie = '')
    {
        return [
            'id_evenement' => $evenement->getIdEvenement(),
            'titre' => $evenement->getTitre(),
            'description' => $evenement->getDescription(),
            'date_evenement' => $evenement->getDateEvenement(),
            'lieu' => $evenement->getLieu(),
            'id_categorie' => $evenement->getIdCategorie(),
            'places_disponibles' => $evenement->getPlacesDisponibles(),
            'nom_categorie' => $nom_categorie
        ];
    }

    // ==================== ACTIONS ====================

    public function index()
    {
        try {
            $evenements = $this->getAll();
            $evenements = array_map(function ($evt) {
                return $evt;
            }, $evenements);
            $data = ['evenements' => $evenements];
            $this->render('events', $data);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $titre = trim(strip_tags($_POST['titre'] ?? ''));
                $description = trim(strip_tags($_POST['description'] ?? ''));
                $date_evenement = trim(strip_tags($_POST['date_evenement'] ?? ''));
                $lieu = trim(strip_tags($_POST['lieu'] ?? ''));
                $id_categorie = intval($_POST['id_categorie'] ?? 0);
                $places = intval($_POST['places_disponibles'] ?? 0);

                if (empty($titre) || empty($date_evenement)) {
                    $this->setFlash('error', 'Le formulaire contient des erreurs.');
                    $this->redirect(WEB_ROOT . '/index.php?c=evenement&a=index');
                    return;
                }

                $evenement = new Evenement($titre, $description, $date_evenement, $lieu, $id_categorie, 0, $places);
                $this->save($evenement);

                $this->setFlash('success', 'Événement créé avec succès.');
                $this->redirect(WEB_ROOT . '/index.php?c=evenement&a=index');
            } catch (Exception $e) {
                $this->setFlash('error', 'Erreur lors de la création: ' . $e->getMessage());
                $this->redirect(WEB_ROOT . '/index.php?c=evenement&a=index');
            }
        } else {
            // Serve the create form (so AJAX modal can fetch the form fragment)
            try {
                $db = Database::getInstance();
                $result = $db->query("SELECT id_categorie, nom_categorie FROM categoriev ORDER BY nom_categorie ASC");
                $categories = [];
                while ($row = $result->fetch()) {
                    $categories[] = $row;
                }
                $data = ['categories' => $categories, 'errors' => [], 'old' => []];
                $this->render('event_create', $data);
            } catch (Exception $e) {
                $this->setFlash('error', 'Erreur: ' . $e->getMessage());
                $this->redirect(WEB_ROOT . '/index.php?c=evenement&a=index');
            }
        }
    }

    public function edit()
    {
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            $this->redirect(WEB_ROOT . '/index.php?c=evenement&a=index');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $titre = trim(strip_tags($_POST['titre'] ?? ''));
                $description = trim(strip_tags($_POST['description'] ?? ''));
                $date_evenement = trim(strip_tags($_POST['date_evenement'] ?? ''));
                $lieu = trim(strip_tags($_POST['lieu'] ?? ''));
                $id_categorie = intval($_POST['id_categorie'] ?? 0);
                $places = intval($_POST['places_disponibles'] ?? 0);

                if (empty($titre) || empty($date_evenement)) {
                    $this->setFlash('error', 'Le formulaire contient des erreurs.');
                    $this->redirect(WEB_ROOT . '/index.php?c=evenement&a=edit&id=' . urlencode($id));
                    return;
                }

                $evenement = $this->getById($id);
                if (!$evenement) {
                    $this->setFlash('error', 'Événement non trouvé.');
                    $this->redirect(WEB_ROOT . '/index.php?c=evenement&a=index');
                    return;
                }

                $evenement->setTitre($titre);
                $evenement->setDescription($description);
                $evenement->setDateEvenement($date_evenement);
                $evenement->setLieu($lieu);
                $evenement->setIdCategorie($id_categorie);
                $evenement->setPlacesDisponibles($places);

                $this->save($evenement);

                $this->setFlash('success', 'Événement modifié avec succès.');
                $this->redirect(WEB_ROOT . '/index.php?c=evenement&a=index');
            } catch (Exception $e) {
                $this->setFlash('error', 'Erreur lors de la modification: ' . $e->getMessage());
                $this->redirect(WEB_ROOT . '/index.php?c=evenement&a=edit&id=' . urlencode($id));
            }
        } else {
            try {
                $evenement = $this->getById($id);
                if (!$evenement) {
                    $this->redirect(WEB_ROOT . '/index.php?c=evenement&a=index');
                }
                $data = ['evenement' => $this->toArray($evenement)];
                $this->render('event_edit', $data);
            } catch (Exception $e) {
                die('Erreur: ' . $e->getMessage());
            }
        }
    }

    public function delete()
    {
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            $this->redirect(WEB_ROOT . '/index.php?c=evenement&a=index');
        }

        try {
            $evenement = $this->getById($id);
            if ($evenement) {
                $this->deleteById($id);
                $this->setFlash('success', 'Événement supprimé avec succès.');
            } else {
                $this->setFlash('error', 'Événement non trouvé.');
            }
        } catch (Exception $e) {
            $this->setFlash('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }

        $this->redirect(WEB_ROOT . '/index.php?c=evenement&a=index');
    }

    // Méthodes publiques utiles pour le front-end
    public function getAllEvenements()
    {
        return $this->getAll();
    }

    public function getEvenementById($id)
    {
        return $this->getById($id);
    }

    public function countEvenements()
    {
        return $this->count();
    }

    public function deleteEvenement($id)
    {
        return $this->deleteById($id);
    }

    // Public wrapper to save an Evenement object (used by other controllers)
    public function saveEvenement($evenement)
    {
        return $this->save($evenement);
    }

    // Retourne les événements à venir (public pour le front-end)
    public function getUpcomingEvenements($limit = 5)
    {
        return $this->getUpcoming($limit);
    }

    // Requête interne pour récupérer les événements futurs
    private function getUpcoming($limit = 5)
    {
        $db = Database::getInstance();
        $sql = "SELECT e.*, c.nom_categorie FROM evenement e LEFT JOIN categoriev c ON e.id_categorie = c.id_categorie WHERE e.date_evenement >= CURDATE() ORDER BY e.date_evenement ASC";
        if ($limit) {
            $result = $db->query($sql . " LIMIT ?", [$limit]);
        } else {
            $result = $db->query($sql);
        }
        $evenements = [];
        while ($row = $result->fetch()) {
            $evt = new Evenement(
                $row['titre'],
                $row['description'],
                $row['date_evenement'],
                $row['lieu'],
                $row['id_categorie'],
                $row['id_evenement'],
                $row['places_disponibles'] ?? 0
            );
            $evenements[] = $this->toArray($evt, $row['nom_categorie'] ?? '');
        }
        return $evenements;
    }

    // Retourne les événements passés (public wrapper)
    public function getPastEvenements($limit = null)
    {
        return $this->getPast($limit);
    }

    // Requête interne pour récupérer les événements passés
    private function getPast($limit = null)
    {
        $db = Database::getInstance();
        $sql = "SELECT e.*, c.nom_categorie FROM evenement e LEFT JOIN categoriev c ON e.id_categorie = c.id_categorie WHERE e.date_evenement < CURDATE() ORDER BY e.date_evenement DESC";
        if ($limit) {
            $result = $db->query($sql . " LIMIT ?", [$limit]);
        } else {
            $result = $db->query($sql);
        }
        $evenements = [];
        while ($row = $result->fetch()) {
            $evt = new Evenement(
                $row['titre'],
                $row['description'],
                $row['date_evenement'],
                $row['lieu'],
                $row['id_categorie'],
                $row['id_evenement'],
                $row['places_disponibles'] ?? 0
            );
            $evenements[] = $this->toArray($evt, $row['nom_categorie'] ?? '');
        }
        return $evenements;
    }
}
?>
