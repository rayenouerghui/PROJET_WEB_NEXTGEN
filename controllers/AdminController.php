<?php
// controllers/AdminController.php
class AdminController {
    private $eventModel;
    private $reservationModel;
    
    public function __construct() {
        $this->eventModel = new EventModel();
        $this->reservationModel = new ReservationModel();
    }
    
    public function index() {
        header('Location: ?page=admin&action=events');
        exit;
    }
    
    public function events() {
        $events = $this->eventModel->getAllEvents();
        $categories = $this->eventModel->getAllCategories();
        $errors = [];
        $success = '';
        
        // ⭐⭐ GESTION AJOUT CATÉGORIE ⭐⭐
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_categorie'])) {
            $nom = trim($_POST['nom_categorie'] ?? '');
            $desc = trim($_POST['description_categorie'] ?? '');
            
            // Validation
            if (empty($nom)) {
                $errors[] = "Le nom de la catégorie est obligatoire";
            } elseif (mb_strlen($nom) < 2) {
                $errors[] = "Le nom de la catégorie doit contenir au moins 2 caractères";
            }
            
            if (empty($errors)) {
                try {
                    $result = $this->eventModel->addCategory($nom, $desc);
                    if ($result) {
                        $success = "✅ Catégorie '$nom' créée avec succès!";
                        $categories = $this->eventModel->getAllCategories(); // Recharger la liste
                    } else {
                        $errors[] = "❌ Erreur lors de la création de la catégorie";
                    }
                } catch (Exception $e) {
                    $errors[] = "❌ Erreur: " . $e->getMessage();
                }
            }
        }
        
        // GESTION AJOUT ÉVÉNEMENT
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_event'])) {
            $validation = $this->validateEvent($_POST);
            if (empty($validation['errors'])) {
                $result = $this->eventModel->createEvent($validation['data']);
                if ($result) {
                    $success = "✅ Événement créé avec succès!";
                    $events = $this->eventModel->getAllEvents(); // Recharger
                } else {
                    $errors[] = "❌ Erreur lors de la création de l'événement";
                }
            } else {
                $errors = $validation['errors'];
            }
        }
        
        // GESTION MODIFICATION ÉVÉNEMENT
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_event'])) {
            $id = (int)($_POST['id_evenement'] ?? 0);
            $validation = $this->validateEvent($_POST);
            if (empty($validation['errors']) && $id > 0) {
                $result = $this->eventModel->updateEvent($id, $validation['data']);
                if ($result) {
                    $success = "✅ Événement modifié avec succès!";
                    $events = $this->eventModel->getAllEvents(); // Recharger
                } else {
                    $errors[] = "❌ Erreur lors de la modification de l'événement";
                }
            } else {
                $errors = $validation['errors'];
            }
        }
        
        // GESTION SUPPRESSION ÉVÉNEMENT
        if (isset($_GET['delete'])) {
            $id = (int)$_GET['delete'];
            if ($id > 0) {
                $result = $this->eventModel->deleteEvent($id);
                if ($result) {
                    $success = "✅ Événement supprimé avec succès!";
                    $events = $this->eventModel->getAllEvents(); // Recharger
                } else {
                    $errors[] = "❌ Erreur lors de la suppression de l'événement";
                }
            }
        }
        
        // ÉVÉNEMENT À MODIFIER
        $eventToEdit = null;
        if (isset($_GET['edit'])) {
            $eventToEdit = $this->eventModel->getEventById((int)$_GET['edit']);
        }
        
        require 'views/admin/events.php';
    }
    
    public function reservations() {
        $reservations = $this->reservationModel->getAll();
        require 'views/admin/reservations.php';
    }
    
    private function validateEvent($data) {
        $errors = [];
        $validated = [];
        
        // Validation titre
        $titre = trim($data['titre'] ?? '');
        if (strlen($titre) < 3) {
            $errors[] = "Le titre doit contenir au moins 3 caractères";
        }
        $validated['titre'] = $titre;
        
        // Validation description
        $description = trim($data['description'] ?? '');
        $validated['description'] = $description;
        
        // Validation date
        $date = trim($data['date_evenement'] ?? '');
        if (empty($date)) {
            $errors[] = "La date est obligatoire";
        }
        $validated['date_evenement'] = $date;
        
        // Validation lieu
        $lieu = trim($data['lieu'] ?? '');
        if (empty($lieu)) {
            $errors[] = "Le lieu est obligatoire";
        }
        $validated['lieu'] = $lieu;
        
        // Validation catégorie
        $categorie = (int)($data['id_categorie'] ?? 0);
        if ($categorie <= 0) {
            $errors[] = "La catégorie est obligatoire";
        }
        $validated['id_categorie'] = $categorie;
        
        return ['data' => $validated, 'errors' => $errors];
    }
}
?>