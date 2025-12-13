<?php
require_once 'config/database.php';
require_once 'models/Categorie.php';
require_once 'models/Evenement.php';
require_once 'models/Reservation.php';
require_once 'controllers/Controller.php';
require_once 'controllers/CategorieC.php';
require_once 'controllers/EvenementC.php';
require_once 'controllers/ReservationC.php';

class AdminC extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function dashboard()
    {
        try {
            // Utiliser les contrôleurs pour récupérer les données
            $categorieC = new CategorieC();
            $evenementC = new EvenementC();
            $reservationC = new ReservationC();

            $stats = [
                'categories' => $categorieC->countCategories(),
                'evenements' => $evenementC->countEvenements(),
                'reservations' => $reservationC->countReservations(),
                'reservations_today' => $reservationC->countReservationsToday()
            ];

            // Dernières réservations
            $dernieres_reservations = $reservationC->getRecentReservations(5);

            // Événements à venir
            $evenements_prochains = $evenementC->getUpcomingEvenements(5);

            // Événements passés
            $evenements_passes = $evenementC->getPastEvenements(5);

            // Statistiques par catégorie
            $categories = $categorieC->getAllCategories();
            $stats_categories = [];
            foreach ($categories as $cat) {
                $db = Database::getInstance();
                $result = $db->query("SELECT COUNT(*) as total FROM evenement WHERE id_categorie = ?", [$cat->getIdCategoriev()]);
                $stats_categories[] = [
                    'nom' => $cat->getNomCategoriev(),
                    'total' => $result->fetch()['total']
                ];
            }

            // Statistiques des réservations des 7 derniers jours
            $db = Database::getInstance();
            $reservations_7jours = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-$i days"));
                $result = $db->query("SELECT COUNT(*) as total FROM reservation WHERE DATE(date_reservation) = ?", [$date]);
                $reservations_7jours[] = [
                    'date' => date('d/m', strtotime($date)),
                    'total' => $result->fetch()['total']
                ];
            }

            $data = [
                'stats' => $stats,
                'dernieres_reservations' => $dernieres_reservations,
                'evenements_prochains' => $evenements_prochains,
                'evenements_passes' => $evenements_passes,
                'stats_categories' => $stats_categories,
                'reservations_7jours' => $reservations_7jours
            ];
            
            $this->render('admin/dashboard', $data);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }
}
?>
