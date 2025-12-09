<?php
// view/backoffice/admin_reclamations.php

session_start();
require_once '../../controller/ReclamationController.php';
require_once '../../controller/TraitementController.php';

$reclamationController = new ReclamationController();
$traitementController = new TraitementController();

// Gestion de la suppression
if (isset($_POST['delete']) && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $result = $reclamationController->delete($id);
    if ($result['success']) {
        header('Location: admin_reclamations.php?success=delete');
    } else {
        header('Location: admin_reclamations.php?error=delete&msg=' . urlencode($result['message']));
    }
    exit;
}

// Gestion de la mise à jour du statut
if (isset($_POST['update_statut'])) {
    $id = (int)$_POST['id'];
    $statut = $_POST['statut'];
    $result = $reclamationController->updateStatut($id, $statut);
    if ($result['success']) {
        header('Location: admin_reclamations.php?success=statut');
    } else {
        header('Location: admin_reclamations.php?error=statut');
    }
    exit;
}

// Gestion de l'ajout de traitement
if (isset($_POST['add_traitement'])) {
    require_once '../../models/Traitement.php';
    $traitement = new Traitement();
    $traitement->setIdReclamation((int)$_POST['id_reclamation'])
               ->setIdUser($_SESSION['user']['id'] ?? null)
               ->setContenu(trim($_POST['contenu'] ?? ''))
               ->setDateReclamation(date('Y-m-d H:i:s'));
    
    // Validation côté serveur
    if (empty(trim($_POST['contenu'] ?? ''))) {
        header('Location: admin_reclamations.php?modal=' . (int)$_POST['id_reclamation'] . '&error=contenu_vide');
        exit;
    }
    
    $result = $traitementController->create($traitement);
    if ($result['success']) {
        // Mettre à jour le statut de la réclamation en "En traitement" si c'est le premier traitement
        $reclamation = $reclamationController->readById((int)$_POST['id_reclamation']);
        if ($reclamation && !isset($reclamation['error']) && $reclamation['statut'] == 'En attente') {
            $reclamationController->updateStatut((int)$_POST['id_reclamation'], 'En traitement');
        }
        header('Location: admin_reclamations.php?modal=' . (int)$_POST['id_reclamation'] . '&success=traitement');
    } else {
        // Propager le message d'erreur détaillé si disponible
        $msg = isset($result['message']) ? '&msg=' . urlencode($result['message']) : '';
        header('Location: admin_reclamations.php?modal=' . (int)$_POST['id_reclamation'] . '&error=traitement' . $msg);
    }
    exit;
}

// Gestion de la modification de traitement
if (isset($_POST['update_traitement'])) {
    require_once '../../models/Traitement.php';
    $traitement = new Traitement();
    $traitement->setIdTraitement((int)$_POST['id_traitement'])
               ->setIdReclamation((int)$_POST['id_reclamation'])
               ->setIdUser($_SESSION['user']['id'] ?? null)
               ->setContenu(trim($_POST['contenu'] ?? ''))
               ->setDateReclamation($_POST['date_originale'] ?? date('Y-m-d H:i:s'));
    
    if (empty(trim($_POST['contenu'] ?? ''))) {
        header('Location: admin_reclamations.php?modal=' . (int)$_POST['id_reclamation'] . '&edit_traitement=' . (int)$_POST['id_traitement'] . '&error=contenu_vide');
        exit;
    }
    
    $result = $traitementController->update($traitement);
    if ($result['success']) {
        header('Location: admin_reclamations.php?modal=' . (int)$_POST['id_reclamation'] . '&success=traitement_modifie');
    } else {
        header('Location: admin_reclamations.php?modal=' . (int)$_POST['id_reclamation'] . '&error=traitement_modifie');
    }
    exit;
}

// Gestion de la suppression de traitement
if (isset($_GET['delete_traitement'])) {
    $idTraitement = (int)$_GET['delete_traitement'];
    $idReclamation = isset($_GET['modal']) ? (int)$_GET['modal'] : null;
    
    $result = $traitementController->delete($idTraitement);
    if ($result['success']) {
        if ($idReclamation) {
            header('Location: admin_reclamations.php?modal=' . $idReclamation . '&success=traitement_supprime');
        } else {
            header('Location: admin_reclamations.php?success=traitement_supprime');
        }
    } else {
        if ($idReclamation) {
            header('Location: admin_reclamations.php?modal=' . $idReclamation . '&error=traitement_supprime');
        } else {
            header('Location: admin_reclamations.php?error=traitement_supprime');
        }
    }
    exit;
}

$reclamations = $reclamationController->readAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Admin Réclamations – NextGen</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <link rel="stylesheet" href="styles.css">
  <script src="../frontoffice/script.js"></script>
  <style>
    .statut-badge {
      display: inline-block;
      padding: 0.25rem 0.75rem;
      border-radius: 0.375rem;
      font-size: 0.875rem;
      font-weight: 500;
    }
    .statut-en-attente { background: #fef3c7; color: #92400e; }
    .statut-en-traitement { background: #dbeafe; color: #1e40af; }
    .statut-resolue { background: #d1fae5; color: #065f46; }
    .statut-fermee { background: #e5e7eb; color: #374151; }
    
    .modal {
      display: none;
      position: fixed;
      z-index: 9999;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.6);
      backdrop-filter: blur(2px);
    }
    .modal-content {
      background-color: #fff;
      margin: 2% auto;
      padding: 3rem 2rem 2rem 2rem;
      border-radius: 0.75rem;
      width: calc(100% - 300px);
      max-width: 1000px;
      max-height: 90vh;
      overflow-y: auto;
      box-shadow: 0 20px 60px rgba(0,0,0,0.3);
      position: relative;
      margin-left: 280px;
      margin-right: 20px;
    }
    @media (max-width: 1024px) {
      .modal-content {
        width: calc(100% - 120px);
        margin-left: 120px;
        margin-right: 20px;
        padding: 1.5rem;
      }
    }
    @media (max-width: 768px) {
      .modal-content {
        width: 95%;
        margin: 2% auto;
        margin-left: auto;
        margin-right: auto;
        padding: 1rem;
      }
    }
    .close {
      color: #6b7280;
      float: right;
      font-size: 32px;
      font-weight: bold;
      cursor: pointer;
      line-height: 1;
      position: absolute;
      top: 1rem;
      right: 1rem;
      background: white;
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      transition: all 0.3s;
      z-index: 10;
    }
    .close:hover { 
      color: #dc2626; 
      background: #fee2e2;
      transform: scale(1.1) rotate(90deg);
    }
    
    .traitements-list {
      margin-top: 2rem;
      padding-top: 1.5rem;
      border-top: 2px solid #e5e7eb;
    }
    .traitement-item {
      background: #f9fafb;
      padding: 1.25rem;
      margin-bottom: 1rem;
      border-radius: 0.5rem;
      border-left: 4px solid #4f46e5;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
      transition: all 0.3s;
    }
    .traitement-item:hover {
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      transform: translateY(-2px);
    }
    .traitement-item .auteur {
      font-weight: 600;
      color: #4f46e5;
      margin-bottom: 0.5rem;
      font-size: 0.95rem;
    }
    .traitement-item .date {
      font-size: 0.875rem;
      color: #6b7280;
      margin-bottom: 0.75rem;
    }
    .traitement-item .contenu {
      margin-top: 0.75rem;
      color: #111827;
      line-height: 1.6;
      padding: 0.75rem;
      background: white;
      border-radius: 0.375rem;
    }
    #modalContent h2 {
      margin-top: 0;
      margin-bottom: 1.5rem;
      color: #111827;
      font-size: 1.5rem;
      border-bottom: 2px solid #e5e7eb;
      padding-bottom: 0.75rem;
    }
    #modalContent h3 {
      color: #111827;
      font-size: 1.25rem;
      margin-top: 1.5rem;
      margin-bottom: 1rem;
    }
    #modalContent form {
      background: #f9fafb;
      padding: 1.5rem;
      border-radius: 0.5rem;
      border: 1px solid #e5e7eb;
    }
    #modalContent textarea {
      width: 100% !important;
      min-height: 120px;
      padding: 0.75rem;
      border: 2px solid #d1d5db;
      border-radius: 0.5rem;
      font-family: inherit;
      font-size: 0.95rem;
      transition: all 0.3s;
    }
    #modalContent textarea:focus {
      outline: none;
      border-color: #4f46e5;
      box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }
    #modalContent .btn-submit {
      background: #4f46e5;
      color: white;
      padding: 0.75rem 1.5rem;
      border: none;
      border-radius: 0.5rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
      box-shadow: 0 2px 4px rgba(79, 70, 229, 0.2);
    }
    #modalContent .btn-submit:hover {
      background: #4338ca;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    }
  </style>
  <link rel="stylesheet" href="../assets/green-theme.css">
</head>
<body class="admin-layout">

  <aside class="admin-sidebar">
    <h2>NextGen Admin</h2>
    <nav class="sidebar-menu">
      <a href="admin_jeux.php" class="item">Gestion des Jeux</a>
      <a href="admin_users.php" class="item">Gestion des Utilisateurs</a>
      <a href="admin_categories.php" class="item">Gestion des Catégories</a>
      <a href="admin_livraisons.php" class="item">Gestion des Livraisons</a>
      <a href="admin_reclamations.php" class="item active">Gestion des Réclamations</a>
    </nav>
    <div class="sidebar-actions">
      <a href="../frontoffice/catalogue.php" class="site" style="margin-bottom: 0.75rem;">
        Voir le Catalogue
      </a>
      <a href="../frontoffice/index.php" class="site">
        Voir le Site
      </a>
      <button class="logout" onclick="location.href='logout.php'">Déconnexion</button>
    </div>
  </aside>

  <main class="admin-main">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
      <h1 class="page-title">Gestion des Réclamations</h1>
    </div>

    <!-- Messages de succès / erreur -->
    <?php if (isset($_GET['success'])): ?>
      <div style="background:#d4edda; color:#155724; padding:1rem; border-radius:0.375rem; margin-bottom:1.5rem;">
        <?php
          $successMessages = [
              'statut'            => 'Statut mis à jour avec succès !',
              'delete'            => 'Réclamation supprimée avec succès !',
              'traitement'        => 'Traitement ajouté avec succès !',
              'traitement_modifie' => 'Traitement modifié avec succès !',
              'traitement_supprime' => 'Traitement supprimé avec succès !'
          ];
          echo isset($successMessages[$_GET['success']]) ? $successMessages[$_GET['success']] : 'Opération réussie !';
        ?>
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
      <div style="background:#f8d7da; color:#721c24; padding:1rem; border-radius:0.375rem; margin-bottom:1.5rem;">
        <?php
          $errorMessages = [
              'contenu_vide'        => 'Le contenu du traitement ne peut pas être vide.',
              'traitement'          => 'Erreur lors de l\'ajout du traitement.',
              'traitement_modifie'  => 'Erreur lors de la modification du traitement.',
              'traitement_supprime'  => 'Erreur lors de la suppression du traitement.'
          ];
          if (isset($_GET['msg'])) {
            echo htmlspecialchars($_GET['msg']);
          } elseif (isset($errorMessages[$_GET['error']])) {
            echo $errorMessages[$_GET['error']];
          } else {
            echo 'Une erreur est survenue.';
          }
        ?>
      </div>
    <?php endif; ?>

    <div class="table-container">
      <table class="admin-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Utilisateur</th>
            <th>Type</th>
            <th>Produit</th>
            <th>Description</th>
            <th>Date</th>
            <th>Traitements</th>
            <th>Statut</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($reclamations) || (isset($reclamations['error']))): ?>
            <tr>
              <td colspan="9" style="text-align:center; padding:2rem; color:#666;">
                <?= isset($reclamations['error']) ? htmlspecialchars($reclamations['error']) : 'Aucune réclamation trouvée.' ?>
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($reclamations as $rec): ?>
              <tr>
                <td><?= $rec['idReclamation'] ?></td>
                <td>
                  <?= htmlspecialchars($rec['user_nom'] ?? '') . ' ' . htmlspecialchars($rec['user_prenom'] ?? '') ?>
                  <br><small style="color:#6b7280;"><?= htmlspecialchars($rec['user_email'] ?? '') ?></small>
                </td>
                <td><?= htmlspecialchars($rec['type'] ?? '—') ?></td>
                <td>
                  <?php if ($rec['jeu_titre']): ?>
                    <?= htmlspecialchars($rec['jeu_titre']) ?>
                  <?php elseif ($rec['produitConcerne']): ?>
                    <?= htmlspecialchars($rec['produitConcerne']) ?>
                  <?php else: ?>
                    —
                  <?php endif; ?>
                </td>
                <td style="max-width: 300px;">
                  <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                    <?= htmlspecialchars($rec['description'] ?? '') ?>
                  </div>
                </td>
                <td><?= date('d/m/Y H:i', strtotime($rec['dateReclamation'])) ?></td>
                <td>
                  <?php
                    // Compter les traitements pour cette réclamation
                    $traitementsCount = $traitementController->readByReclamationId($rec['idReclamation']);
                    $count = (is_array($traitementsCount) && !isset($traitementsCount['error'])) ? count($traitementsCount) : 0;
                    if ($count > 0) {
                      echo '<span style="background: #dbeafe; color: #1e40af; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.875rem;">';
                      echo '<i class="bi bi-chat-dots"></i> ' . $count . ' traitement' . ($count > 1 ? 's' : '');
                      echo '</span>';
                    } else {
                      echo '<span style="color: #6b7280; font-size: 0.875rem;">Aucun traitement</span>';
                    }
                  ?>
                </td>
                <td>
                  <?php
                    $statut = $rec['statut'] ?? 'En attente';
                    switch($statut) {
                        case 'En attente':
                            $statutClass = 'statut-en-attente';
                            break;
                        case 'En traitement':
                            $statutClass = 'statut-en-traitement';
                            break;
                        case 'Résolue':
                            $statutClass = 'statut-resolue';
                            break;
                        case 'Fermée':
                            $statutClass = 'statut-fermee';
                            break;
                        default:
                            $statutClass = 'statut-en-attente';
                            break;
                    }
                  ?>
                  <span class="statut-badge <?= $statutClass ?>"><?= htmlspecialchars($statut) ?></span>
                </td>
                <td class="actions">
                  <a href="#" class="btn-edit" onclick="openModal(<?= $rec['idReclamation'] ?>); return false;">Voir/Traiter</a>
                  <form method="POST" style="display:inline;" onsubmit="return confirm('Supprimer cette réclamation ?')">
                    <input type="hidden" name="id" value="<?= $rec['idReclamation'] ?>">
                    <button type="submit" name="delete" class="btn-delete" style="border:none; background:none; cursor:pointer; color:#dc2626;">
                      Supprimer
                    </button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>

  <!-- Modal pour voir et traiter une réclamation -->
  <div id="reclamationModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal()">&times;</span>
      <div id="modalContent"></div>
    </div>
  </div>

  <script>
    function openModal(id) {
      fetch('admin_reclamations.php?modal=' + id)
        .then(response => response.text())
        .then(html => {
          document.getElementById('modalContent').innerHTML = html;
          document.getElementById('reclamationModal').style.display = 'block';
        })
        .catch(error => {
          console.error('Erreur:', error);
          alert('Erreur lors du chargement des détails');
        });
    }

    function closeModal() {
      document.getElementById('reclamationModal').style.display = 'none';
    }

    window.onclick = function(event) {
      const modal = document.getElementById('reclamationModal');
      if (event.target == modal) {
        closeModal();
      }
    }
  </script>

  <?php
  // Gestion de l'affichage modal (pour fetch)
  if (isset($_GET['modal'])) {
    $id = (int)$_GET['modal'];
    $reclamation = $reclamationController->readById($id);
    $traitements = $traitementController->readByReclamationId($id);
    
    // Gestion de l'édition d'un traitement
    $traitementEdit = null;
    if (isset($_GET['edit_traitement'])) {
      $idTraitement = (int)$_GET['edit_traitement'];
      $traitementData = $traitementController->readById($idTraitement);
      if (!isset($traitementData['error'])) {
        $traitementEdit = $traitementData;
      }
    }
    
    if (!isset($reclamation['error'])) {
      ?>
      <div>
        <h2>Réclamation #<?= $reclamation['idReclamation'] ?></h2>
        <div style="margin-top: 1rem; background: #f9fafb; padding: 1rem; border-radius: 0.5rem;">
          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div>
              <strong style="color: #6b7280; font-size: 0.875rem;">Utilisateur:</strong>
              <p style="margin: 0.25rem 0 0 0;">
                <?= htmlspecialchars($reclamation['user_nom'] ?? '') . ' ' . htmlspecialchars($reclamation['user_prenom'] ?? '') ?>
                <br><small style="color: #6b7280;"><?= htmlspecialchars($reclamation['user_email'] ?? '') ?></small>
              </p>
            </div>
            <div>
              <strong style="color: #6b7280; font-size: 0.875rem;">Type:</strong>
              <p style="margin: 0.25rem 0 0 0;"><?= htmlspecialchars($reclamation['type'] ?? '—') ?></p>
            </div>
            <div>
              <strong style="color: #6b7280; font-size: 0.875rem;">Produit:</strong>
              <p style="margin: 0.25rem 0 0 0;">
                <?php if ($reclamation['jeu_titre']): ?>
                  <i class="bi bi-box"></i> <?= htmlspecialchars($reclamation['jeu_titre']) ?>
                  <?php if ($reclamation['jeu_prix']): ?>
                    <br><small style="color: #6b7280;"><?= number_format($reclamation['jeu_prix'], 2) ?> TND</small>
                  <?php endif; ?>
                <?php elseif ($reclamation['produitConcerne']): ?>
                  <i class="bi bi-box"></i> <?= htmlspecialchars($reclamation['produitConcerne']) ?>
                <?php else: ?>
                  —
                <?php endif; ?>
              </p>
            </div>
            <div>
              <strong style="color: #6b7280; font-size: 0.875rem;">Date:</strong>
              <p style="margin: 0.25rem 0 0 0;">
                <i class="bi bi-calendar"></i> <?= date('d/m/Y H:i', strtotime($reclamation['dateReclamation'])) ?>
              </p>
            </div>
          </div>
        </div>
        
        <div style="margin-top: 1rem; padding: 1rem; background: #fef3c7; border-radius: 0.5rem; border-left: 4px solid #f59e0b;">
          <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
            <strong style="color: #92400e;">Statut actuel:</strong>
            <?php
              $statut = $reclamation['statut'] ?? 'En attente';
              switch($statut) {
                  case 'En attente':
                      $statutClass = 'statut-en-attente';
                      break;
                  case 'En traitement':
                      $statutClass = 'statut-en-traitement';
                      break;
                  case 'Résolue':
                      $statutClass = 'statut-resolue';
                      break;
                  case 'Fermée':
                      $statutClass = 'statut-fermee';
                      break;
                  default:
                      $statutClass = 'statut-en-attente';
                      break;
              }
            ?>
            <span class="statut-badge <?= $statutClass ?>" style="padding: 0.5rem 1rem; border-radius: 0.375rem; font-weight: 600;">
              <?= htmlspecialchars($statut) ?>
            </span>
            <div style="margin-left: auto;">
              <label style="font-weight: 600; color: #92400e; margin-right: 0.5rem;">Changer le statut:</label>
              <select name="statut" id="statutSelect" style="padding: 0.5rem; border: 2px solid #d1d5db; border-radius: 0.375rem; font-weight: 600;">
                <option value="En attente" <?= ($reclamation['statut'] == 'En attente') ? 'selected' : '' ?>>En attente</option>
                <option value="En traitement" <?= ($reclamation['statut'] == 'En traitement') ? 'selected' : '' ?>>En traitement</option>
                <option value="Résolue" <?= ($reclamation['statut'] == 'Résolue') ? 'selected' : '' ?>>Résolue</option>
                <option value="Fermée" <?= ($reclamation['statut'] == 'Fermée') ? 'selected' : '' ?>>Fermée</option>
              </select>
            </div>
          </div>
        </div>
        </div>
        
        <div style="margin-top: 1rem;">
          <p><strong>Description:</strong></p>
          <div style="background: #f9fafb; padding: 1rem; border-radius: 0.375rem; margin-top: 0.5rem;">
            <?= nl2br(htmlspecialchars($reclamation['description'] ?? '')) ?>
          </div>
        </div>

        <div class="traitements-list">
          <h3>
            <i class="bi bi-chat-dots"></i> Traitements 
            <span style="font-size: 0.875rem; font-weight: normal; color: #6b7280;">
              (<?= is_array($traitements) && !isset($traitements['error']) ? count($traitements) : 0 ?>)
            </span>
          </h3>
          <?php if (empty($traitements) || (isset($traitements['error']))): ?>
            <p style="color: #6b7280;">
              <i class="bi bi-info-circle"></i> Aucun traitement pour le moment.
            </p>
          <?php else: ?>
            <?php foreach ($traitements as $traitement): ?>
              <div class="traitement-item" style="position: relative;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                  <div>
                    <div class="auteur">
                      <i class="bi bi-person-circle"></i> 
                      <?= htmlspecialchars($traitement['auteur_nom'] ?? 'Admin') . ' ' . htmlspecialchars($traitement['auteur_prenom'] ?? '') ?>
                      <?php if ($traitement['auteur_email']): ?>
                        <small style="color: #6b7280; margin-left: 0.5rem;">(<?= htmlspecialchars($traitement['auteur_email']) ?>)</small>
                      <?php endif; ?>
                    </div>
                    <div class="date">
                      <i class="bi bi-clock"></i> <?= date('d/m/Y à H:i', strtotime($traitement['dateReclamation'])) ?>
                    </div>
                  </div>
                  <div style="display: flex; gap: 0.5rem;">
                    <a href="?modal=<?= $reclamation['idReclamation'] ?>&edit_traitement=<?= $traitement['idTraitement'] ?>" 
                       style="color: #4f46e5; text-decoration: none; font-size: 0.875rem;" 
                       title="Modifier">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <a href="?modal=<?= $reclamation['idReclamation'] ?>&delete_traitement=<?= $traitement['idTraitement'] ?>" 
                       onclick="return confirm('Supprimer ce traitement ?')"
                       style="color: #dc2626; text-decoration: none; font-size: 0.875rem;" 
                       title="Supprimer">
                      <i class="bi bi-trash"></i>
                    </a>
                  </div>
                </div>
                <div class="contenu"><?= nl2br(htmlspecialchars($traitement['contenu'] ?? '')) ?></div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <?php if ($traitementEdit): ?>
          <!-- Formulaire de modification de traitement -->
          <form method="POST" id="traitementForm" style="margin-top: 2rem; padding-top: 1rem; border-top: 2px solid #e5e7eb; background: #fef3c7; padding: 1rem; border-radius: 0.5rem;">
            <h3 style="color: #92400e; margin-bottom: 1rem;">
              <i class="bi bi-pencil"></i> Modifier le traitement #<?= $traitementEdit['idTraitement'] ?>
            </h3>
            <input type="hidden" name="id_traitement" value="<?= $traitementEdit['idTraitement'] ?>">
            <input type="hidden" name="id_reclamation" value="<?= $reclamation['idReclamation'] ?>">
            <input type="hidden" name="date_originale" value="<?= $traitementEdit['dateReclamation'] ?>">
            <div style="margin-bottom: 1rem;">
              <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Contenu du traitement <span style="color: #dc2626;">*</span></label>
              <textarea name="contenu" id="contenuTraitement" style="width: 100%; min-height: 100px; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-family: inherit;"><?= htmlspecialchars($traitementEdit['contenu'] ?? '') ?></textarea>
              <small style="color: #6b7280; display: block; margin-top: 0.25rem;">
                Minimum 10 caractères, maximum 5000 caractères
              </small>
            </div>
            <div style="margin-top: 1rem; display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
              <button type="submit" name="update_traitement" class="btn-submit">Modifier le traitement</button>
              <a href="?modal=<?= $reclamation['idReclamation'] ?>" style="padding: 0.5rem 1rem; background: #6b7280; color: white; border-radius: 0.375rem; text-decoration: none;">Annuler</a>
            </div>
          </form>
        <?php else: ?>
          <!-- Formulaire d'ajout de traitement -->
          <form method="POST" id="traitementForm" style="margin-top: 2rem; padding-top: 1rem; border-top: 2px solid #e5e7eb;">
            <h3>Ajouter un traitement</h3>
            <input type="hidden" name="id_reclamation" value="<?= $reclamation['idReclamation'] ?>">
            <div style="margin-bottom: 1rem;">
              <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Contenu du traitement <span style="color: #dc2626;">*</span></label>
              <textarea name="contenu" id="contenuTraitement" style="width: 100%; min-height: 100px; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-family: inherit;" placeholder="Votre réponse..."></textarea>
              <small style="color: #6b7280; display: block; margin-top: 0.25rem;">
                Minimum 10 caractères, maximum 5000 caractères
              </small>
            </div>
            <div style="margin-top: 1rem; display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
              <button type="submit" name="add_traitement" class="btn-submit">Ajouter le traitement</button>
            </div>
          </form>
        <?php endif; ?>
        <form method="POST" id="updateStatutForm" style="display: none;">
          <input type="hidden" name="id" value="<?= $reclamation['idReclamation'] ?>">
          <input type="hidden" name="statut" id="statutValue">
          <input type="hidden" name="update_statut" value="1">
        </form>
        <script>
          // Charger le script de validation
          (function() {
            const script = document.createElement('script');
            script.src = 'traitement_validation.js';
            document.head.appendChild(script);
          })();
          
          // Gestion du changement de statut (après chargement du DOM)
          setTimeout(function() {
            const statutSelect = document.getElementById('statutSelect');
            if (statutSelect) {
              statutSelect.addEventListener('change', function() {
                if (confirm('Voulez-vous vraiment changer le statut de la réclamation ?')) {
                  document.getElementById('statutValue').value = this.value;
                  document.getElementById('updateStatutForm').submit();
                } else {
                  // Restaurer la valeur précédente
                  this.value = '<?= $reclamation['statut'] ?? 'En attente' ?>';
                }
              });
            }
          }, 100);
        </script>
      </div>
      <?php
      exit;
    }
  }
  ?>
</body>
</html>

