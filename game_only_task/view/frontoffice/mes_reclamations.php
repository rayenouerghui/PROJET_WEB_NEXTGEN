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

$userId = (int)$_SESSION['user']['id'];
$reclamations = $reclamationController->readByUserId($userId);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mes réclamations</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    .list-card { background: #fff; padding: 1rem; border-radius: 0.5rem; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
    .statut-badge { display:inline-block; padding:0.25rem 0.6rem; border-radius:0.375rem; font-weight:600; font-size:0.85rem; }
    .statut-en-attente { background: #fef3c7; color: #92400e; }
    .statut-en-traitement { background: #dbeafe; color: #1e40af; }
    .statut-resolue { background: #d1fae5; color: #065f46; }
    .statut-fermee { background: #e5e7eb; color: #374151; }
    .modal { display:none; position:fixed; left:0; top:0; right:0; bottom:0; background:rgba(0,0,0,0.6); z-index:999; }
    .modal-inner { background:#fff; margin:4% auto; max-width:900px; padding:1.5rem; border-radius:0.6rem; max-height:85vh; overflow:auto; }
    .close { float:right; cursor:pointer; font-size:1.4rem; }
    .traitement-item{ background:#f8fafc; padding:1rem; border-radius:0.5rem; margin-bottom:0.8rem; }
  </style>
  <link rel="stylesheet" href="../assets/green-theme.css">
</head>
<body>
  <div style="max-width:1100px;margin:2rem auto;padding:1rem;">
    <h1>Mes réclamations</h1>
    <p>Sur cette page vous voyez l'historique de vos réclamations et les réponses apportées par l'équipe.</p>

    <div style="margin-top:1rem; display:grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap:1rem;">
      <?php if (empty($reclamations) || isset($reclamations['error'])): ?>
        <div class="list-card">Aucune réclamation trouvée.</div>
      <?php else: ?>
        <?php foreach ($reclamations as $rec): ?>
          <div class="list-card">
            <div style="display:flex; justify-content:space-between; align-items:start; gap:1rem;">
              <div>
                <div style="font-weight:700;">#<?= htmlspecialchars($rec['idReclamation']) ?> - <?= htmlspecialchars($rec['type'] ?? '—') ?></div>
                <div style="color:#6b7280; font-size:0.95rem; margin-top:0.25rem;"><?= htmlspecialchars($rec['produitConcerne'] ?? ($rec['jeu_titre'] ?? '—')) ?></div>
                <div style="margin-top:0.5rem; color:#111827;"><?= nl2br(htmlspecialchars(substr($rec['description'] ?? '', 0, 220))) ?><?php if (strlen($rec['description'] ?? '')>220) echo '...'; ?></div>
              </div>
              <div style="text-align:right; min-width:120px;">
                <div style="font-size:0.9rem; color:#6b7280;"><?= date('d/m/Y H:i', strtotime($rec['dateReclamation'])) ?></div>
                <?php $statut = $rec['statut'] ?? 'En attente';
                      switch($statut){ case 'En attente': $c='statut-en-attente'; break; case 'En traitement': $c='statut-en-traitement'; break; case 'Résolue': $c='statut-resolue'; break; case 'Fermée': $c='statut-fermee'; break; default: $c='statut-en-attente'; }
                ?>
                <div style="margin-top:0.6rem;"><span class="statut-badge <?= $c ?>"><?= htmlspecialchars($statut) ?></span></div>
                <div style="margin-top:0.8rem;"><button onclick="openModal(<?= (int)$rec['idReclamation'] ?>)" style="padding:0.5rem 0.8rem; border-radius:0.4rem; border:1px solid #d1d5db; background:#fff; cursor:pointer;">Voir les détails</button></div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

  </div>

  <div id="modal" class="modal">
    <div class="modal-inner">
      <span class="close" onclick="closeModal()">&times;</span>
      <div id="modalContent">Chargement...</div>
    </div>
  </div>

  <script>
    function openModal(id){
      fetch('mes_reclamations.php?modal=' + id)
        .then(r => r.text())
        .then(html => {
          document.getElementById('modalContent').innerHTML = html;
          document.getElementById('modal').style.display = 'block';
        })
        .catch(e => { console.error(e); alert('Erreur de chargement'); });
    }
    function closeModal(){ document.getElementById('modal').style.display = 'none'; }

    // Permet fermer modal en cliquant hors
    window.onclick = function(e){ if(e.target == document.getElementById('modal')) closeModal(); }
  </script>

<?php
// Si appel modal via GET, retourner le contenu HTML minimal pour injection
if (isset($_GET['modal'])) {
    $id = (int)$_GET['modal'];
    $reclamation = $reclamationController->readById($id);
    // Vérifier que la réclamation appartient bien à l'utilisateur
    if (isset($reclamation['error']) || ((int)$reclamation['id_user'] !== $userId)) {
        echo '<div><strong>Réclamation introuvable ou accès refusé.</strong></div>';
        exit;
    }

    $traitements = $traitementController->readByReclamationId($id);
    ?>
    <div>
      <h2>Réclamation #<?= $reclamation['idReclamation'] ?> — <?= htmlspecialchars($reclamation['type'] ?? '') ?></h2>
      <p style="color:#6b7280;">Envoyée le <?= date('d/m/Y H:i', strtotime($reclamation['dateReclamation'])) ?></p>
      <div style="margin-top:0.8rem; padding:1rem; background:#f9fafb; border-radius:0.4rem;">
        <strong>Description:</strong>
        <div style="margin-top:0.5rem;"><?= nl2br(htmlspecialchars($reclamation['description'] ?? '')) ?></div>
      </div>

      <div style="margin-top:1rem;">
        <strong>Statut actuel:</strong>
        <?php $statut = $reclamation['statut'] ?? 'En attente'; ?>
        <span class="statut-badge <?= ($statut=='En traitement'? 'statut-en-traitement' : ($statut=='Résolue'? 'statut-resolue' : ($statut=='Fermée'? 'statut-fermee' : 'statut-en-attente'))) ?>"><?= htmlspecialchars($statut) ?></span>
      </div>

      <div style="margin-top:1rem;">
        <h3>Réponses</h3>
        <?php if (empty($traitements) || isset($traitements['error'])): ?>
          <p style="color:#6b7280;">Aucune réponse pour le moment.</p>
        <?php else: ?>
          <?php foreach ($traitements as $t): ?>
            <div class="traitement-item">
              <div style="font-weight:700; color:#111827;"><?= htmlspecialchars($t['auteur_nom'] ?? 'Admin') ?> <small style="color:#6b7280; font-weight:600;"><?= ($t['auteur_email'] ? ' - ' . htmlspecialchars($t['auteur_email']) : '') ?></small></div>
              <div style="color:#6b7280; font-size:0.9rem; margin-bottom:0.6rem;">Le <?= date('d/m/Y H:i', strtotime($t['dateReclamation'])) ?></div>
              <div style="white-space:pre-wrap;"><?= nl2br(htmlspecialchars($t['contenu'] ?? '')) ?></div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

    </div>
    <?php
    exit;
}
?>

</body>
</html>