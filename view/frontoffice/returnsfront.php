<?php
session_start();

if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
    header('Location: connexion.php');
    exit;
}

require_once '../../controller/ReclamationController.php';
require_once '../../controller/TraitementController.php';

$reclamationController = new ReclamationController();
$traitementController = new TraitementController();

$userId = $_SESSION['user']['id'];
$reclamations = $reclamationController->readByUserId($userId);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Mes Réclamations – NextGen</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="styles.css">
  <style>
    .reclamations-container {
      max-width: 1000px;
      margin: 2rem auto;
      padding: 0 1rem;
    }
    .page-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 2rem;
    }
    .reclamation-card {
      background: white;
      border-radius: 1rem;
      padding: 1.5rem;
      margin-bottom: 1.5rem;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      transition: transform 0.3s, box-shadow 0.3s;
    }
    .reclamation-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .reclamation-header {
      display: flex;
      justify-content: space-between;
      align-items: start;
      margin-bottom: 1rem;
      padding-bottom: 1rem;
      border-bottom: 1px solid #e5e7eb;
    }
    .reclamation-title {
      font-size: 1.25rem;
      font-weight: 600;
      color: #111827;
      margin-bottom: 0.5rem;
    }
    .reclamation-meta {
      display: flex;
      gap: 1rem;
      flex-wrap: wrap;
      color: #6b7280;
      font-size: 0.875rem;
    }
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
    .reclamation-description {
      color: #374151;
      line-height: 1.6;
      margin-bottom: 1rem;
      white-space: pre-wrap;
    }
    .traitements-section {
      margin-top: 1rem;
      padding-top: 1rem;
      border-top: 1px solid #e5e7eb;
    }
    .traitement-item {
      background: #f9fafb;
      padding: 1rem;
      margin-bottom: 0.75rem;
      border-radius: 0.5rem;
      border-left: 3px solid #4f46e5;
    }
    .traitement-item .auteur {
      font-weight: 600;
      color: #4f46e5;
      margin-bottom: 0.25rem;
    }
    .traitement-item .date {
      font-size: 0.875rem;
      color: #6b7280;
      margin-bottom: 0.5rem;
    }
    .traitement-item .contenu {
      color: #111827;
      white-space: pre-wrap;
    }
    .btn-new {
      background: #4f46e5;
      color: white;
      padding: 0.75rem 1.5rem;
      border-radius: 0.5rem;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      font-weight: 600;
      transition: all 0.3s;
    }
    .btn-new:hover {
      background: #4338ca;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    }
    .empty-state {
      text-align: center;
      padding: 4rem 2rem;
      color: #6b7280;
    }
    .empty-state i {
      font-size: 4rem;
      margin-bottom: 1rem;
      color: #d1d5db;
    }
  </style>
</head>
  <link rel="stylesheet" href="../assets/green-theme.css">
<body>
  <!-- HEADER -->
  <header>
    <div class="container nav">
      <div class="left">
        <a href="index.php" class="logo">NextGen</a>
        <nav class="menu">
          <a href="index.php">Accueil</a>
          <a href="catalogue.php">Produits</a>
          <a href="apropos.html">À Propos</a>
        </nav>
      </div>

      <div style="display:flex; gap:1.5rem; align-items:center; margin-left: auto;">
        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
          <a href="../backoffice/admin_users.php" style="display: flex; align-items: center; gap: 0.5rem; color: #4f46e5; text-decoration: none; font-weight: 600; padding: 0.5rem 1rem; border-radius: 0.375rem; transition: all 0.3s; white-space: nowrap;" onmouseover="this.style.background='rgba(79, 70, 229, 0.1)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.background='transparent'; this.style.transform='translateY(0)';">
            <i class="bi bi-person-gear"></i> Administration
          </a>
        <?php endif; ?>

        <?php if (isset($_SESSION['user'])): 
          $photo = !empty($_SESSION['user']['photo_profil']) 
              ? '../../resources/' . $_SESSION['user']['photo_profil'] 
              : '../../resources/default.jpg';
        ?>
          <div class="user-dropdown">
            <button class="user-btn">
              <span class="user-name">Bienvenue <?= htmlspecialchars($_SESSION['user']['prenom']) ?></span>
              <img src="<?= $photo ?>" alt="Profil" class="user-avatar">
            </button>

            <div class="dropdown-menu">
              <a href="profil.php">
                <i class="bi bi-person-circle"></i> Gérer mon profil
              </a>
              <a href="tracking_last.php">
                <i class="bi bi-truck"></i> Mes livraisons
              </a>
              <a href="returnsfront.php">
                <i class="bi bi-exclamation-circle"></i> Mes réclamations
              </a>
              <hr>
              <a href="../backoffice/logout.php" class="logout-item">
                <i class="bi bi-box-arrow-right"></i> Déconnexion
              </a>
            </div>
          </div>
        <?php else: ?>
          <a href="connexion.php" style="color:#4f46e5; font-weight:600;">Connexion</a>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <main style="padding: 2rem 0; min-height: calc(100vh - 200px);">
    <div class="reclamations-container">
      <div class="page-header">
        <div>
          <h1 style="margin-bottom: 0.5rem; color: #111827;">📋 Mes Réclamations</h1>
          <p style="color: #6b7280;">Consultez l'état de vos réclamations et les réponses de notre équipe.</p>
        </div>
        <a href="reclamation.php" class="btn-new">
          <i class="bi bi-plus-circle"></i> Nouvelle réclamation
        </a>
      </div>

      <?php if (empty($reclamations) || (isset($reclamations['error']))): ?>
        <div class="empty-state">
          <i class="bi bi-inbox"></i>
          <h2>Aucune réclamation</h2>
          <p style="margin-bottom: 2rem;">Vous n'avez pas encore soumis de réclamation.</p>
          <a href="reclamation.php" class="btn-new">
            <i class="bi bi-plus-circle"></i> Créer une réclamation
          </a>
        </div>
      <?php else: ?>
        <?php foreach ($reclamations as $rec): ?>
          <div class="reclamation-card">
            <div class="reclamation-header">
              <div style="flex: 1;">
                <div class="reclamation-title">
                  <?= htmlspecialchars($rec['type'] ?? 'Réclamation') ?> 
                  #<?= $rec['idReclamation'] ?>
                </div>
                <div class="reclamation-meta">
                  <span><i class="bi bi-calendar"></i> <?= date('d/m/Y H:i', strtotime($rec['dateReclamation'])) ?></span>
                  <?php if ($rec['jeu_titre']): ?>
                    <span><i class="bi bi-box"></i> <?= htmlspecialchars($rec['jeu_titre']) ?></span>
                  <?php elseif ($rec['produitConcerne']): ?>
                    <span><i class="bi bi-box"></i> <?= htmlspecialchars($rec['produitConcerne']) ?></span>
                  <?php endif; ?>
                </div>
              </div>
              <div>
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
              </div>
            </div>

            <div class="reclamation-description">
              <?= nl2br(htmlspecialchars($rec['description'] ?? '')) ?>
            </div>

            <?php
              $traitements = $traitementController->readByReclamationId($rec['idReclamation']);
              if (!empty($traitements) && !isset($traitements['error'])):
            ?>
              <div class="traitements-section">
                <h3 style="font-size: 1rem; margin-bottom: 1rem; color: #374151;">
                  <i class="bi bi-chat-dots"></i> Réponses de l'équipe
                </h3>
                <?php foreach ($traitements as $traitement): ?>
                  <div class="traitement-item">
                    <div class="auteur">
                      <i class="bi bi-person-circle"></i> 
                      <?= htmlspecialchars($traitement['auteur_nom'] ?? 'Admin') . ' ' . htmlspecialchars($traitement['auteur_prenom'] ?? '') ?>
                    </div>
                    <div class="date">
                      <i class="bi bi-clock"></i> <?= date('d/m/Y à H:i', strtotime($traitement['dateReclamation'])) ?>
                    </div>
                    <div class="contenu">
                      <?= nl2br(htmlspecialchars($traitement['contenu'] ?? '')) ?>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php else: ?>
              <div class="traitements-section">
                <p style="color: #6b7280; font-style: italic;">
                  <i class="bi bi-hourglass-split"></i> En attente de réponse de l'équipe...
                </p>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </main>

  <!-- FOOTER -->
  <footer>
    <div class="container footer-grid">
      <div>
        <h3>NextGen</h3>
        <p>Plateforme de vente de jeux vidéo à vocation solidaire</p>
      </div>
      <div>
        <h3>Liens Utiles</h3>
        <ul>
          <li><a href="catalogue.php">Catalogue</a></li>
          <li><a href="apropos.html">À Propos</a></li>
        </ul>
      </div>
      <div>
        <h3>Support</h3>
        <ul>
          <li><a href="reclamation.php">Réclamation</a></li>
          <li><a href="returnsfront.php">Mes réclamations</a></li>
        </ul>
      </div>
    </div>
  </footer>
</body>
</html>

