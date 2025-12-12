<?php
/**
 * PWA API - User Orders Endpoint
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../../../config/db.php';

// Get user ID from query parameter
$userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

if ($userId <= 0) {
    echo json_encode([
       'success' => false,
        'error' => 'Invalid user ID',
        'deliveries' => []
    ]);
    exit;
}

try {
   $db = Database::getInstance()->getConnection();
    
    // Fetch user deliveries with related information
    $sql = "
        SELECT 
            l.id_livraison as id,
            l.id_commande,
            l.adresse_complete as address,
            l.ville as city,
            l.code_postal as postal_code,
            l.date_livraison as delivery_date,
            l.mode_livraison as delivery_mode,
            l.statut as status,
            l.position_lat as lat,
            l.position_lng as lng,
            c.numero_commande as order_number,
            j.titre as game_name,
            u.nom as user_last_name,
            u.prenom as user_first_name
        FROM livraison l
        LEFT JOIN jeu_achete c ON l.id_commande = c.id_achat
        LEFT JOIN utilisateur u ON c.user_id = u.id_user
        LEFT JOIN jeu_jeu j ON c.jeu_id = j.id_jeu
        WHERE c.user_id = :user_id
        AND l.statut != 'annulée'
        ORDER BY l.created_at DESC
    ";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([':user_id' => $userId]);
    $deliveries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'deliveries' => $deliveries,
        'count' => count($deliveries)
    ]);
    
} catch (Exception $e) {
    error_log('PWA Orders API Error: ' . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'error' => 'Database error',
        'deliveries' => []
    ]);
}
