<?php
// controllers/FrontController.php
class FrontController {
    private $eventModel;
    private $reservationModel;
    
    public function __construct() {
        $this->eventModel = new EventModel();
        $this->reservationModel = new ReservationModel();
    }
    
    public function index() {
        $categories = $this->eventModel->getAllCategories();
        require 'views/front/categories.php';
    }
    
    public function events() {
        $categoryId = (int)($_GET['category_id'] ?? 0);
        
        if ($categoryId <= 0) {
            header('Location: ?page=front&action=index');
            exit;
        }
        
        $events = $this->eventModel->getEventsByCategory($categoryId);
        $category = $this->eventModel->getCategoryById($categoryId);
        
        require 'views/front/events.php';
    }
    
    public function reservation() {
        $eventId = (int)($_GET['event_id'] ?? 0);
        $errors = [];
        
        if ($eventId <= 0) {
            header('Location: ?page=front&action=index');
            exit;
        }
        
        $event = $this->eventModel->getEventById($eventId);
        
        if (!$event) {
            header('Location: ?page=front&action=index');
            exit;
        }
        
        // Traitement du formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $validation = $this->validateReservation($_POST);
            
            if (empty($validation['errors'])) {
                $data = $validation['data'];
                $data['id_evenement'] = $eventId;
                
                $success = $this->reservationModel->create($data);
                if ($success) {
                    // SUCCÈS - Redirection vers la page de confirmation
                    header('Location: ?page=front&action=reservationSuccess');
                    exit;
                } else {
                    // ÉCHEC - On retourne avec un message d'erreur
                    $errors[] = "Erreur lors de l'enregistrement de la réservation";
                    // On retourne à la page des événements avec les erreurs
                    $_SESSION['reservation_errors'] = $errors;
                    header('Location: ?page=front&action=events&category_id=' . $event['id_categorie']);
                    exit;
                }
            } else {
                // ERREURS DE VALIDATION - On retourne avec les erreurs
                $errors = $validation['errors'];
                $_SESSION['reservation_errors'] = $errors;
                $_SESSION['reservation_data'] = $_POST;
                header('Location: ?page=front&action=events&category_id=' . $event['id_categorie']);
                exit;
            }
        }
        
        // SI ON ARRIVE ICI, C'EST UNE REQUÊTE GET - On redirige vers les événements
        header('Location: ?page=front&action=events&category_id=' . $event['id_categorie']);
        exit;
    }
    
    public function reservationSuccess() {
        echo "<!DOCTYPE html>
        <html lang='fr'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Réservation Confirmée - NEXTGEN</title>
            <link rel='stylesheet' href='/projet/public/css/style.css'>
            <style>
                .success-container {
                    max-width: 600px;
                    margin: 100px auto;
                    text-align: center;
                    background: white;
                    padding: 50px;
                    border-radius: 10px;
                    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                }
                .success-icon {
                    font-size: 4rem;
                    margin-bottom: 20px;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='success-container'>
                    <div class='success-icon'>✅</div>
                    <h1>Réservation Confirmée !</h1>
                    <p>Votre réservation a été enregistrée avec succès.</p>
                    <p>Vous recevrez un email de confirmation sous peu.</p>
                    <a href='?page=front&action=index' class='btn btn-primary' style='margin-top: 20px;'>
                        ← Retour à l'accueil
                    </a>
                </div>
            </div>
        </body>
        </html>";
    }
    
    private function validateReservation($data) {
        $errors = [];
        $validated = [];
        
        // Validation nom
        $nom = trim($data['nom_complet'] ?? '');
        if (strlen($nom) < 2) {
            $errors[] = "Le nom doit contenir au moins 2 caractères";
        }
        $validated['nom_complet'] = $nom;
        
        // Validation email
        $email = trim($data['email'] ?? '');
        if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
            $errors[] = "Email invalide";
        }
        $validated['email'] = $email;
        
        // Validation téléphone
        $tel = trim($data['telephone'] ?? '');
        $validated['telephone'] = $tel;
        
        // Validation places
        $places = (int)($data['nombre_places'] ?? 0);
        if ($places < 1 || $places > 10) {
            $errors[] = "Nombre de places invalide (1-10)";
        }
        $validated['nombre_places'] = $places;
        
        // Validation message
        $message = trim($data['message'] ?? '');
        $validated['message'] = $message;
        
        return ['data' => $validated, 'errors' => $errors];
    }
}
?>