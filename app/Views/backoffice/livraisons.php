<?php
// No header require, we build the full page structure to match friend's design
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Admin – Livraisons</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Rajdhani:wght@600;800&display=swap" rel="stylesheet">
  
  <!-- Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body { 
      background: url('/PROJET_WEB_NEXTGEN-main/public/images/bg-jeux.gif') no-repeat center center fixed !important; 
      background-size: cover !important; 
      margin: 0; 
      color: white; 
      min-height: 100vh; 
      font-family: 'Inter', sans-serif;
      overflow-x: hidden;
    }
    
    body::before { 
      content: ''; 
      position: fixed; 
      inset: 0; 
      background: rgba(0,0,0,0.8); 
      backdrop-filter: blur(8px); 
      z-index: -1; 
    }

    .admin-container {
      max-width: 1400px;
      margin: 0 auto;
      padding: 2rem;
    }

    .admin-header {
      text-align: center;
      margin-bottom: 3rem;
      padding: 2rem;
      background: linear-gradient(135deg, rgba(56,28,135,0.95), rgba(59,7,100,0.9));
      border-radius: 20px;
      border: 1px solid rgba(139,92,246,0.6);
      box-shadow: 0 20px 60px rgba(0,0,0,0.5);
    }

    h1 {
      font-family: 'Rajdhani', sans-serif;
      font-size: 3rem;
      font-weight: 800;
      background: linear-gradient(135deg, #a855f7, #ec4899, #06b6d4);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      margin-bottom: 0.5rem;
      text-shadow: 0 0 30px rgba(139,92,246,0.5);
    }

    .subtitle {
      color: #a5b4fc;
      font-size: 1.1rem;
    }

    .stats-row {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2rem;
    }

    .stat-card {
      background: linear-gradient(135deg, rgba(56,28,135,0.9), rgba(59,7,100,0.85));
      padding: 1.5rem;
      border-radius: 16px;
      border: 1px solid rgba(139,92,246,0.4);
      text-align: center;
      transition: transform 0.3s, box-shadow 0.3s;
    }

    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 40px rgba(139,92,246,0.4);
    }

    .stat-label {
      font-size: 0.8rem;
      text-transform: uppercase;
      letter-spacing: 2px;
      color: #a855f7;
      margin-bottom: 0.5rem;
      font-weight: 800;
    }

    .stat-value {
      font-size: 2rem;
      font-weight: 900;
      color: #00ffc3;
    }

    .action-bar {
      background: rgba(0,0,0,0.4);
      padding: 1.5rem;
      border-radius: 16px;
      margin-bottom: 2rem;
      border: 1px solid rgba(139,92,246,0.3);
    }

    .action-bar summary {
      cursor: pointer;
      font-weight: 700;
      color: #ec4899;
      font-size: 1.1rem;
      padding: 0.5rem;
      user-select: none;
    }

    .action-bar summary:hover {
      color: #00ffc3;
    }

    .action-bar form {
      margin-top: 1.5rem;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
    }

    input, select {
      background: rgba(0,0,0,0.5) !important;
      border: 1px solid rgba(139,92,246,0.5) !important;
      color: white !important;
      padding: 0.8rem !important;
      border-radius: 10px !important;
      font-size: 0.9rem;
    }

    input:focus, select:focus {
      outline: none;
      border-color: #ec4899 !important;
      box-shadow: 0 0 15px rgba(236,72,153,0.4);
    }

    .btn {
      padding: 0.8rem 1.5rem;
      border-radius: 10px;
      border: none;
      cursor: pointer;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
      transition: all 0.3s;
      font-family: 'Rajdhani', sans-serif;
      font-size: 0.9rem;
    }

    .btn-primary {
      background: linear-gradient(45deg, #8b5cf6, #ec4899);
      color: white;
      box-shadow: 0 0 20px rgba(139,92,246,0.5);
    }

    .btn-primary:hover {
      transform: translateY(-3px);
      box-shadow: 0 0 30px rgba(236,72,153,0.8);
    }

    .btn-success {
      background: linear-gradient(45deg, #10b981, #34d399);
      color: white;
    }

    .btn-danger {
      background: linear-gradient(45deg, #ef4444, #b91c1c);
      color: white;
    }

    .table-container {
      background: linear-gradient(135deg, rgba(56,28,135,0.95), rgba(59,7,100,0.9));
      border-radius: 20px;
      padding: 2rem;
      border: 1px solid rgba(139,92,246,0.6);
      box-shadow: 0 20px 60px rgba(0,0,0,0.5);
      overflow-x: auto;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th {
      background: rgba(0,0,0,0.4);
      padding: 1.2rem;
      text-align: left;
      font-weight: 800;
      color: #a855f7;
      text-transform: uppercase;
      letter-spacing: 1px;
      font-size: 0.85rem;
      border-bottom: 2px solid rgba(139,92,246,0.5);
    }

    td {
      padding: 1.2rem;
      border-bottom: 1px solid rgba(255,255,255,0.1);
      color: #e5e7eb;
    }

    tr:hover {
      background: rgba(139,92,246,0.1);
    }

    .badge {
      padding: 0.5rem 1rem;
      border-radius: 50px;
      font-size: 0.8rem;
      font-weight: bold;
      text-transform: uppercase;
      display: inline-block;
    }

    .badge-preparée { background: #f59e0b; color: black; }
    .badge-commandee { background: #f59e0b; color: black; }
    .badge-emballee { background: #8b5cf6; color: white; }
    .badge-en_route { background: #10b981; color: white; box-shadow: 0 0 15px #10b981; }
    .badge-livrée { background: #ec4899; color: white; }
    .badge-annulée { background: #ef4444; color: white; }

    .actions {
      display: flex;
      gap: 0.5rem;
      flex-wrap: wrap;
    }

    .actions form {
      display: inline;
    }

    .actions select {
      padding: 0.5rem !important;
      font-size: 0.8rem;
    }

    .actions .btn {
      padding: 0.5rem 1rem;
      font-size: 0.8rem;
    }

    @media (max-width: 768px) {
      h1 {
        font-size: 2rem;
      }
      .admin-container {
        padding: 1rem;
      }
      table {
        font-size: 0.85rem;
      }
    }
  </style>
</head>
<body>

  <?php if (!empty($message)): ?>
    <script>
        Swal.fire({
            icon: '<?php echo $messageType === 'success' ? 'success' : 'error'; ?>',
            title: '<?php echo $messageType === 'success' ? 'Succès' : 'Erreur'; ?>',
            text: '<?php echo addslashes($message); ?>',
            timer: 3000,
            background: '#1a1a2e',
            color: '#fff',
            confirmButtonColor: '#8b5cf6'
        });
    </script>
  <?php endif; ?>

  <div class="admin-container">
    <div class="admin-header">
      <p style="color: #ec4899; font-weight: bold; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 0.5rem;">
        <i class="bi bi-shield-lock-fill"></i> NextGen • Admin Panel
      </p>
      <h1>GESTION DES LIVRAISONS</h1>
      <p class="subtitle">Interface d'administration • Suivi en temps réel</p>
    </div>

    <!-- Stats Row -->
    <div class="stats-row">
      <?php foreach ($stats as $statut => $count): ?>
        <div class="stat-card">
          <div class="stat-label"><?php echo ucfirst($statut); ?></div>
          <div class="stat-value"><?php echo $count; ?></div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Create Form -->
    <details class="action-bar">
      <summary><i class="bi bi-plus-circle"></i> Créer une nouvelle livraison manuelle</summary>
      <form method="post">
        <input type="hidden" name="action" value="create_livraison">
        
        <select name="id_commande" required>
          <option value="">-- Sélectionner une commande --</option>
          <?php foreach ($commandes as $cmd): ?>
            <option value="<?php echo $cmd['id_commande']; ?>">
              #<?php echo $cmd['numero_commande']; ?> - <?php echo htmlspecialchars($cmd['nom_utilisateur']); ?>
            </option>
          <?php endforeach; ?>
        </select>
        
        <input type="text" name="adresse_complete" placeholder="Adresse complète" required>
        <input type="text" name="ville" placeholder="Ville" required>
        <input type="text" name="code_postal" placeholder="Code Postal" required>
        <input type="date" name="date_livraison" required>
        <input type="text" name="notes_client" placeholder="Notes">
        
        <button type="submit" class="btn btn-success">
          <i class="bi bi-check-lg"></i> Créer
        </button>
      </form>
    </details>

    <!-- Table -->
    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Client</th>
            <th>Jeu</th>
            <th>Adresse</th>
            <th>Statut</th>
            <th>Date Prévue</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($livraisons)): ?>
            <tr>
              <td colspan="7" style="text-align:center; padding:3rem; color:#a5b4fc;">
                <i class="bi bi-inbox" style="font-size: 3rem; display: block; margin-bottom: 1rem;"></i>
                Aucune livraison en cours
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($livraisons as $l): ?>
              <tr>
                <td><strong style="color: #ec4899;">#<?php echo $l['id_livraison']; ?></strong></td>
                <td>
                  <?php echo htmlspecialchars($l['prenom_utilisateur'] . ' ' . $l['nom_utilisateur']); ?><br>
                  <small style="color: #a5b4fc;">Cmd #<?php echo $l['numero_commande']; ?></small>
                </td>
                <td><?php echo htmlspecialchars($l['nom_jeu'] ?? 'N/A'); ?></td>
                <td style="max-width:250px;">
                  <?php echo htmlspecialchars($l['adresse_complete']); ?><br>
                  <small style="color: #a5b4fc;"><?php echo htmlspecialchars($l['ville']); ?></small>
                </td>
                <td>
                  <span class="badge badge-<?php echo $l['statut']; ?>">
                    <?php echo ucfirst($l['statut']); ?>
                  </span>
                </td>
                <td><?php echo date('d/m/Y', strtotime($l['date_livraison'])); ?></td>
                <td>
                  <div class="actions">
                    <!-- Update Status -->
                    <form method="post">
                      <input type="hidden" name="action" value="update_statut">
                      <input type="hidden" name="id_livraison" value="<?php echo $l['id_livraison']; ?>">
                      <select name="statut" onchange="this.form.submit()">
                        <?php foreach ($statuts as $s): ?>
                          <option value="<?php echo $s; ?>" <?php echo $l['statut'] === $s ? 'selected' : ''; ?>>
                            <?php echo ucfirst($s); ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </form>

                    <?php if ($l['statut'] === 'preparée'): ?>
                      <form method="post">
                        <input type="hidden" name="action" value="confirm_livraison">
                        <input type="hidden" name="id_livraison" value="<?php echo $l['id_livraison']; ?>">
                        <button type="submit" class="btn btn-success" title="Confirmer et lancer">
                          <i class="bi bi-check-lg"></i>
                        </button>
                      </form>
                    <?php endif; ?>

                    <?php if (in_array($l['statut'], ['en_route', 'livrée'])): ?>
                      <a href="/PROJET_WEB_NEXTGEN-main/public/tracking-moving.php?id_livraison=<?php echo $l['id_livraison']; ?>" 
                         target="_blank" class="btn btn-primary" title="Voir le suivi">
                        <i class="bi bi-eye"></i>
                      </a>
                    <?php endif; ?>

                    <form method="post" onsubmit="return confirm('Supprimer cette livraison ?');">
                      <input type="hidden" name="action" value="delete_livraison">
                      <input type="hidden" name="id_livraison" value="<?php echo $l['id_livraison']; ?>">
                      <button type="submit" class="btn btn-danger" title="Supprimer">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</body>
</html>
