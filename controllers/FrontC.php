<?php
require_once 'config/database.php';
require_once 'models/CategorieV.php';
require_once 'models/Evenement.php';
require_once 'models/Reservation.php';
require_once 'controllers/Controller.php';
require_once 'controllers/CategorieC.php';
require_once 'controllers/EvenementC.php';
require_once 'controllers/ReservationC.php';

class FrontC extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function events()
    {
        try {
            $categorieC = new CategorieC();
            $evenementC = new EvenementC();
            
            $categories = $categorieC->getAllCategories();
            $evenements = $evenementC->getAllEvenements();
            
            // Convertir en format pour JavaScript et compter les événements par catégorie
            $counts = [];
            foreach ($evenements as $e) {
                $cid = $e['id_categorie'];
                if (!isset($counts[$cid])) $counts[$cid] = 0;
                $counts[$cid]++;
            }

            $categories_js = [];
            foreach ($categories as $cat) {
                $cid = $cat->getIdCategoriev();
                $categories_js[] = [
                    'id' => $cid,
                    'name' => $cat->getNomCategoriev(),
                    'description' => $cat->getDescriptionCategoriev(),
                    'count' => isset($counts[$cid]) ? $counts[$cid] : 0
                ];
            }
            
            // Si un filtre initial est fourni via GET (ex: ?cat=2), ne conserver que
            // les événements de cette catégorie afin que la page ouverte dans un
            // nouvel onglet affiche uniquement les événements demandés.
            $initial = $_GET['cat'] ?? 'all';
            if ($initial !== 'all' && $initial !== null && $initial !== '') {
                // Normaliser en int/string selon la structure des données
                $filterId = $initial;
                $evenements = array_filter($evenements, function ($ev) use ($filterId) {
                    return strval($ev['id_categorie']) === strval($filterId) || intval($ev['id_categorie']) === intval($filterId);
                });
                // Reindex array to be sequential for downstream code
                $evenements = array_values($evenements);
            }

            // Format de date en français
            setlocale(LC_TIME, 'fr_FR.UTF-8', 'fr_FR', 'french');
            $mois = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
            
            $evenements_js = [];
            foreach ($evenements as $evt) {
                $date_obj = new DateTime($evt['date_evenement']);
                $jour = $date_obj->format('d');
                $mois_nom = $mois[(int)$date_obj->format('m') - 1];
                $annee = $date_obj->format('Y');
                $date_formatee = $jour . ' ' . $mois_nom . ' ' . $annee;
                
                $evenements_js[] = [
                    'id' => $evt['id_evenement'],
                    'category' => $evt['id_categorie'],
                    'title' => $evt['titre'],
                    'date' => $date_formatee,
                    'lieu' => $evt['lieu'],
                    'description' => $evt['description'],
                    'places' => $evt['places_disponibles'],
                    'points' => 25 // Points par défaut, peut être modifié
                ];
            }
            
            $initial = $_GET['cat'] ?? 'all';
            $data = [
                'categories_js' => $categories_js,
                'evenements_js' => $evenements_js,
                'initial_filter' => $initial
            ];
            $this->render('front/events', $data);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // Affiche les catégories côté front et les événements correspondants lorsqu'on clique
    public function categories()
    {
        try {
            $categorieC = new CategorieC();
            $evenementC = new EvenementC();

            $categories = $categorieC->getAllCategories();
            $evenements = $evenementC->getAllEvenements();

            // Compter les événements par catégorie pour afficher le nombre sur la page catégories
            $counts = [];
            foreach ($evenements as $e) {
                $cid = $e['id_categorie'] ?? ($e['category'] ?? null);
                if ($cid === null) continue;
                if (!isset($counts[$cid])) $counts[$cid] = 0;
                $counts[$cid]++;
            }

            // Préparer pour JS
            $categories_js = [];
            foreach ($categories as $cat) {
                $cid = $cat->getIdCategoriev();
                $categories_js[] = [
                    'id' => $cid,
                    'name' => $cat->getNomCategoriev(),
                    'description' => $cat->getDescriptionCategoriev(),
                    'count' => isset($counts[$cid]) ? $counts[$cid] : 0
                ];
            }

            // Format de date en français
            setlocale(LC_TIME, 'fr_FR.UTF-8', 'fr_FR', 'french');
            $mois = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];

            $evenements_js = [];
            foreach ($evenements as $evt) {
                $date_obj = new DateTime($evt['date_evenement']);
                $jour = $date_obj->format('d');
                $mois_nom = $mois[(int)$date_obj->format('m') - 1];
                $annee = $date_obj->format('Y');
                $date_formatee = $jour . ' ' . $mois_nom . ' ' . $annee;

                $evenements_js[] = [
                    'id' => $evt['id_evenement'],
                    'category' => $evt['id_categorie'],
                    'title' => $evt['titre'],
                    'date' => $date_formatee,
                    'lieu' => $evt['lieu'],
                    'description' => $evt['description'],
                    'places' => $evt['places_disponibles'],
                    'points' => 25
                ];
            }

            $data = [
                'categories_js' => $categories_js,
                'evenements_js' => $evenements_js
            ];
            $this->render('front/categories', $data);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function reservation()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Méthode non autorisée'], 405);
        }

        try {
            $id_evenement = intval($_POST['id_evenement'] ?? 0);
            $nom_complet = trim($_POST['nom_complet'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $telephone = trim($_POST['telephone'] ?? '');
            $nombre_places = intval($_POST['nombre_places'] ?? 0);
            $message = trim($_POST['message'] ?? '');

            // Validation
            if (empty($nom_complet) || empty($email) || empty($telephone) || $nombre_places < 1 || $id_evenement < 1) {
                $this->jsonResponse(['success' => false, 'message' => 'Tous les champs obligatoires doivent être remplis'], 400);
            }

            // Vérifier que l'événement existe et a assez de places
            $evenementC = new EvenementC();
            $evenement = $evenementC->getEvenementById($id_evenement);
            if (!$evenement) {
                $this->jsonResponse(['success' => false, 'message' => 'Événement non trouvé'], 404);
            }

            if ($evenement->getPlacesDisponibles() < $nombre_places) {
                $this->jsonResponse(['success' => false, 'message' => 'Pas assez de places disponibles'], 400);
            }

            // Points générés pour cet événement (par place), envoyé par le formulaire client
            $points_per_place = intval($_POST['points_generes'] ?? 25);
            $total_points = $points_per_place * $nombre_places;

            // Créer la réservation et stocker les points générés pour cette réservation
            $reservation = new Reservation($id_evenement, $nom_complet, $email, $telephone, $nombre_places, $message);
            $reservation->setPointsGeneres($total_points);
            $reservationC = new ReservationC();
            $reservationC->saveReservation($reservation);

            // Mettre à jour les places disponibles
            $evenement->setPlacesDisponibles($evenement->getPlacesDisponibles() - $nombre_places);
            $evenementC->saveEvenement($evenement);

            // Envoi d'un email de confirmation via PHPMailer
            require_once __DIR__ . '/../vendor/PHPMailer/src/Exception.php';
            require_once __DIR__ . '/../vendor/PHPMailer/src/PHPMailer.php';
            require_once __DIR__ . '/../vendor/PHPMailer/src/SMTP.php';
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'bouzidiayoub87@gmail.com';
                $mail->Password = 'wxsv uyjf jqzu ezcb';
                $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('bouzidiayoub87@gmail.com', 'NextGen Events');
                $mail->addAddress($email, $nom_complet);
                $mail->isHTML(true);
                $mail->Subject = 'Confirmation de réservation';
                                $mail->Body    = '<div style="font-family:Arial,sans-serif;font-size:16px;color:#222;">
<h2 style="color:#4CAF50;">🎉 Merci pour votre réservation !</h2>
<p>Bonjour <strong>' . htmlspecialchars($nom_complet) . '</strong>,</p>
<p>Nous avons le plaisir de vous confirmer votre inscription à l\'événement&nbsp;:</p>
<ul style="background:#f7f7f7;padding:15px;border-radius:8px;list-style:none;">
    <li><strong>Événement :</strong> ' . htmlspecialchars($evenement->getTitre()) . '</li>
    <li><strong>Date :</strong> ' . htmlspecialchars($evenement->getDateEvenement()) . '</li>
    <li><strong>Lieu :</strong> ' . htmlspecialchars($evenement->getLieu()) . '</li>
    <li><strong>Nombre de places réservées :</strong> ' . intval($nombre_places) . '</li>
</ul>
<p style="margin-top:20px;">Nous avons hâte de vous retrouver pour partager ce moment solidaire et festif !<br>
N\'hésitez pas à nous contacter pour toute question.</p>
<p style="color:#888;font-size:14px;margin-top:30px;">Cet email est généré automatiquement, merci de ne pas y répondre.<br>À très bientôt sur <strong>NextGen Events</strong> !</p>
</div>';

                $mail->send();
            } catch (\Exception $e) {
                // Tu peux logger l'erreur si besoin : $e->getMessage()
            }

            // URL du certificat téléchargeable
            $certUrl = '/projet/index.php?c=front&amp;a=certificate&amp;id=' . $reservation->getIdReservation();

            $this->jsonResponse([
                'success' => true,
                'message' => 'Réservation enregistrée avec succès',
                'places_restantes' => $evenement->getPlacesDisponibles(),
                'points' => $total_points,
                'certificate_url' => $certUrl
            ]);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()], 500);
        }
    }

    public function index()
    {
        try {
            $categorieC = new CategorieC();
            $evenementC = new EvenementC();
            
            // Récupérer les événements à venir (3 derniers)
            $evenements_avenir = $evenementC->getUpcomingEvenements(3);
            // Pour afficher le nombre d'événements par catégorie sur l'accueil
            $all_evenements = $evenementC->getAllEvenements();
            $counts = [];
            foreach ($all_evenements as $e) {
                $cid = $e['id_categorie'];
                if (!isset($counts[$cid])) $counts[$cid] = 0;
                $counts[$cid]++;
            }
            
            // Récupérer les catégories
            $categories = $categorieC->getAllCategories();
            
            // Statistiques générales
            $stats = [
                'total_categories' => count($categories),
                'total_evenements' => $evenementC->countEvenements()
            ];
            
            $data = [
                'evenements_avenir' => $evenements_avenir,
                'categories' => $categories,
                'stats' => $stats,
                'category_counts' => $counts
            ];
            
            $this->render('front/index', $data);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // Affiche le classement des participants basé sur la table reservation
    public function leaderboard()
    {
        $this->render('front/leaderboard');
    }

    // Génère un certificat PNG pour une réservation
    public function certificate()
    {
        $id = intval($_GET['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            echo '<h2>ID de réservation non fourni</h2><p>Impossible de générer le certificat sans identifiant de réservation.</p>';
            exit;
        }

        $reservationC = new ReservationC();
        $evenementC = new EvenementC();

        $reservation = $reservationC->getReservationById($id);
        if (!$reservation) {
            http_response_code(404);
            echo '<h2>Réservation non trouvée</h2><p>L\'identifiant fourni ne correspond à aucune réservation.</p>';
            exit;
        }

        $evenement = $evenementC->getEvenementById($reservation->getIdEvenement());

        // Vérifier que GD est disponible
        if (!function_exists('imagecreatetruecolor')) {
            http_response_code(500);
            echo '<h2>Extension GD manquante</h2>';
            echo '<p>La librairie GD PHP n\'est pas activée sur ce serveur. Pour générer automatiquement les certificats (images), activez GD dans votre PHP (voir instructions ci-dessous).</p>';
            echo '<pre>1) Ouvrez le fichier <strong>c:\\xampp\\php\\php.ini</strong>\n2) Recherchez une ligne contenant <em>extension=gd</em> ou <em>extension=gd2</em> et enlevez le ";" au début (d%C3%A9commentez).\n3) Redémarrez Apache via le panneau XAMPP.</pre>';
            echo '<p>Après activation, réessayez le téléchargement du certificat.</p>';
            exit;
        }

        // Création d'une image simple (certificat) en PNG
        $width = 1200;
        $height = 800;
        $im = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($im, 255, 255, 255);
        $black = imagecolorallocate($im, 34, 34, 34);
        $green = imagecolorallocate($im, 76, 175, 80);
        imagefilledrectangle($im, 0, 0, $width, $height, $white);

        // Title
        $title = 'CERTIFICAT DE PARTICIPATION';
        imagestring($im, 5, 340, 80, $title, $green);

        // Event title
        $eventTitle = $evenement ? $evenement->getTitre() : 'Événement';
        imagestring($im, 5, 150, 200, 'Nom: ' . $reservation->getNomComplet(), $black);
        imagestring($im, 5, 150, 260, 'Événement: ' . $eventTitle, $black);
        imagestring($im, 5, 150, 320, 'Date: ' . ($evenement ? $evenement->getDateEvenement() : $reservation->getDateReservation()), $black);
        imagestring($im, 5, 150, 380, 'Nombre de places: ' . $reservation->getNombrePlaces(), $black);

        // Footer
        imagestring($im, 3, 150, 700, 'NextGen Events - Merci de votre participation', $black);

        // Envoi de l'image en téléchargement
        header('Content-Type: image/png');
        header('Content-Disposition: attachment; filename="certificate_' . $id . '.png"');
        imagepng($im);
        imagedestroy($im);
        exit;
    }

    public function historique()
    {
        try {
            $evenementC = new EvenementC();
            $categorieC = new CategorieC();
            
            // Récupérer tous les événements passés
            $evenements_passes = $evenementC->getPastEvenements();
            
            // Récupérer les catégories pour le filtre
            $categories = $categorieC->getAllCategories();
            
            // Format de date en français
            setlocale(LC_TIME, 'fr_FR.UTF-8', 'fr_FR', 'french');
            $mois = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
            
            // Formater les dates
            foreach ($evenements_passes as &$evt) {
                $date_obj = new DateTime($evt['date_evenement']);
                $jour = $date_obj->format('d');
                $mois_nom = $mois[(int)$date_obj->format('m') - 1];
                $annee = $date_obj->format('Y');
                $evt['date_formatee'] = $jour . ' ' . $mois_nom . ' ' . $annee;
            }
            
            $data = [
                'evenements_passes' => $evenements_passes,
                'categories' => $categories
            ];
            
            $this->render('front/historique', $data);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }
}
?>
