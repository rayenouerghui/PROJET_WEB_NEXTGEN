<?php
$title = "Gestion Livraisons";
$subtitle = "Admin Panel";
$totalLivraisons = count($livraisons);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php include('partials/head-css.php'); ?>
    
    <!-- PWA Configuration -->
    <link rel="manifest" href="/PROJET_WEB_NEXTGEN-main/public/manifest.json">
    <meta name="theme-color" content="#8b5cf6">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="NextGen Admin">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<?php if (!empty($message)): ?>
    <script>
        Sw

al.fire({
            icon: '<?php echo $messageType === 'success' ? 'success' : ($messageType === 'warning' ? 'warning' : 'error'); ?>',
            title: '<?php echo $messageType === 'success' ? 'Succès' : ($messageType === 'warning' ? 'Attention' : 'Erreur'); ?>',
            text: '<?php echo addslashes($message); ?>',
            timer: 3000,
            confirmButtonColor: '#02c0ce'
        });
    </script>
<?php endif; ?>

<!-- Wrapper -->
<div class="wrapper">
    
    <!-- Topbar -->
    <?php include('partials/topbar.php'); ?>
    
    <!-- Sidebar -->
    <?php include('partials/sidenav.php'); ?>
    
    <!-- Page Content -->
    <div class="page-content">
        <div class="page-container">
            
            <!-- Stats Cards Row -->
            <div class="row row-cols-xxl-6 row-cols-md-3 row-cols-1 g-3 mb-4">
                <?php 
                $iconMap = [
                    'commandee' => 'ri-file-list-line',
                    'preparée' => 'ri-box-3-line',
                    'emballee' => 'ri-archive-line',
                    'en_route' => 'ri-truck-line',
                    'livrée' => 'ri-checkbox-circle-line',
                    'annulée' => 'ri-close-circle-line'
                ];
                $colorMap = [
                    'commandee' => 'secondary',
                    'preparée' => 'info',
                    'emballee' => 'warning',
                    'en_route' => 'primary',
                    'livrée' => 'success',
                    'annulée' => 'danger'
                ];
                foreach ($stats as $statut => $count): 
                    $icon = $iconMap[$statut] ?? 'ri-file-line';
                    $color = $colorMap[$statut] ?? 'secondary';
                ?>
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-2 justify-content-between">
                                <div>
                                    <h5 class="text-muted fs-13 fw-bold text-uppercase"><?= ucfirst($statut) ?></h5>
                                    <h3 class="my-2 py-1 fw-bold"><?= $count ?></h3>
                                </div>
                                <div class="avatar-lg flex-shrink-0">
                                    <span class="avatar-title bg-<?= $color ?>-subtle text-<?= $color ?> rounded-circle fs-32">
                                        <i class="<?= $icon ?>"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Create Form Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="header-title mb-0 d-inline-block">
                        <i class="ri-add-circle-line me-1"></i> Créer une nouvelle livraison manuelle
                    </h4>
                </div>
                <div class="card-body">
                    <form method="post" id="createForm">
                        <input type="hidden" name="action" value="create_livraison">
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Commande <span class="text-danger">*</span></label>
                                <select name="id_commande" id="create_commande" class="form-select">
                                    <option value="">-- Sélectionner une commande --</option>
                                    <?php foreach ($commandes as $cmd): ?>
                                        <option value="<?php echo $cmd['id_commande']; ?>">
                                            #<?php echo $cmd['numero_commande']; ?> - <?php echo htmlspecialchars($cmd['nom_utilisateur']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Adresse complète <span class="text-danger">*</span></label>
                                <input type="text" name="adresse_complete" id="create_adresse" class="form-control" placeholder="Ex: 123 Avenue Bourguiba">
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Ville <span class="text-danger">*</span></label>
                                <input type="text" name="ville" id="create_ville" class="form-control" placeholder="Ex: Tunis">
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Code Postal <span class="text-danger">*</span></label>
                                <input type="text" name="code_postal" id="create_cp" class="form-control" placeholder="Ex: 1000">
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Date de livraison <span class="text-danger">*</span></label>
                                <input type="text" name="date_livraison" id="create_date" class="form-control" placeholder="YYYY-MM-DD">
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label">Notes client (optionnel)</label>
                                <input type="text" name="notes_client" class="form-control" placeholder="Notes ou instructions spéciales">
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary" id="create_submit">
                                <i class="ri-check-line me-1"></i> Créer la livraison
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <script>
            document.getElementById('createForm').addEventListener('submit', function(e) {
                const commande = document.getElementById('create_commande').value;
                const adresse = document.getElementById('create_adresse').value.trim();
                const ville = document.getElementById('create_ville').value.trim();
                const cp = document.getElementById('create_cp').value.trim();
                const date = document.getElementById('create_date').value.trim();
                
                if (!commande) {
                    Swal.fire({ icon: 'error', title: 'Erreur', text: 'Veuillez sélectionner une commande' });
                    e.preventDefault();
                    return false;
                }
                if (!adresse || adresse.length < 5) {
                    Swal.fire({ icon: 'error', title: 'Erreur', text: 'Veuillez saisir une adresse complète (min 5 caractères)' });
                    e.preventDefault();
                    return false;
                }
                if (!ville || ville.length < 2) {
                    Swal.fire({ icon: 'error', title: 'Erreur', text: 'Veuillez saisir une ville' });
                    e.preventDefault();
                    return false;
                }
                if (!cp || cp.length < 4) {
                    Swal.fire({ icon: 'error', title: 'Erreur', text: 'Veuillez saisir un code postal valide' });
                    e.preventDefault();
                    return false;
                }
                if (!date || !/^\d{4}-\d{2}-\d{2}$/.test(date)) {
                    Swal.fire({ icon: 'error', title: 'Erreur', text: 'Veuillez saisir une date au format YYYY-MM-DD (ex: 2025-12-25)' });
                    e.preventDefault();
                    return false;
                }
            });
            </script>
            
            <!-- Table Card -->
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title mb-0">
                        <i class="ri-truck-line me-1"></i> Liste des livraisons
                    </h4>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap table-hover mb-0">
                            <thead class="table-light">
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
                                        <td colspan="7" class="text-center py-5">
                                            <i class="ri-inbox-line" style="font-size: 3rem; display: block; margin-bottom: 1rem; color: #a1a9b1;"></i>
                                            <span class="text-muted">Aucune livraison en cours</span>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($livraisons as $l): ?>
                                        <tr>
                                            <td><span class="fw-bold text-primary">#<?php echo $l['id_livraison']; ?></span></td>
                                            <td>
                                                <div class="fw-semibold"><?= htmlspecialchars($l['prenom_utilisateur'] . ' ' . $l['nom_utilisateur']) ?></div>
                                                <small class="text-muted">Cmd #<?= $l['numero_commande'] ?></small>
                                            </td>
                                            <td><?= htmlspecialchars($l['nom_jeu'] ?? 'N/A') ?></td>
                                            <td style="max-width: 250px;">
                                                <div><?= htmlspecialchars($l['adresse_complete']) ?></div>
                                                <small class="text-muted"><?= htmlspecialchars($l['ville']) ?></small>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= $colorMap[$l['statut']] ?? 'secondary' ?>">
                                                    <?= ucfirst($l['statut']) ?>
                                                </span>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($l['date_livraison'])) ?></td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <!-- Update Status -->
                                                    <form method="post" class="d-inline-block">
                                                        <input type="hidden" name="action" value="update_statut">
                                                        <input type="hidden" name="id_livraison" value="<?= $l['id_livraison'] ?>">
                                                        <select name="statut" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                                                            <?php foreach ($statuts as $s): ?>
                                                                <option value="<?= $s ?>" <?= $l['statut'] === $s ? 'selected' : '' ?>>
                                                                    <?= ucfirst($s) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </form>
                                                    
                                                    <!-- Confirm Button -->
                                                    <?php if ($l['statut'] === 'preparée'): ?>
                                                        <form method="post" class="d-inline-block">
                                                            <input type="hidden" name="action" value="confirm_livraison">
                                                            <input type="hidden" name="id_livraison" value="<?= $l['id_livraison'] ?>">
                                                            <button type="submit" class="btn btn-success btn-sm" title="Confirmer et lancer">
                                                                <i class="ri-check-line"></i>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                    
                                                    <!-- View Tracking -->
                                                    <?php if (in_array($l['statut'], ['en_route', 'livrée'])): ?>
                                                        <a href="/PROJET_WEB_NEXTGEN-main/public/tracking-moving.php?id_livraison=<?= $l['id_livraison'] ?>" 
                                                           target="_blank" class="btn btn-primary btn-sm" title="Voir le suivi">
                                                            <i class="ri-eye-line"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    
                                                    <!-- Delete Button -->
                                                    <form method="post" class="d-inline-block" onsubmit="return confirm('Supprimer cette livraison ?');">
                                                        <input type="hidden" name="action" value="delete_livraison">
                                                        <input type="hidden" name="id_livraison" value="<?= $l['id_livraison'] ?>">
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Supprimer">
                                                            <i class="ri-delete-bin-line"></i>
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
            </div>
            
        </div>
    </div>
    
</div>

<?php include('partials/footer-scripts.php'); ?>

</body>
</html>

