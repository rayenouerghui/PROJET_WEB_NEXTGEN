<?php include __DIR__ . '/header.php'; ?>

<div class="admin-container">
    <div class="admin-page-header">
        <h1>Gestion des Événements</h1>
        <a href="<?php echo WEB_ROOT; ?>/index.php?c=evenement&amp;a=create" class="btn btn-add">+ Ajouter un événement</a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['info'])): ?>
        <div class="alert alert-info" style="background:#d1ecf1;border:1px solid #bee5eb;color:#0c5460;padding:12px;border-radius:5px;margin-bottom:20px;"><?php echo htmlspecialchars($_SESSION['info']); unset($_SESSION['info']); ?></div>
    <?php endif; ?>

    <div class="table-card">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Titre</th>
                    <th>Catégorie</th>
                    <th>Date</th>
                    <th>Lieu</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($evenements)): ?>
                    <tr><td colspan="6">Aucun événement</td></tr>
                <?php else: ?>
                    <?php foreach ($evenements as $evt): ?>
                        <tr>
                            <td><?php echo $evt['id_evenement']; ?></td>
                            <td><?php echo htmlspecialchars($evt['titre']); ?></td>
                            <td><?php echo htmlspecialchars($evt['nom_categorie'] ?? 'N/A'); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($evt['date_evenement'])); ?></td>
                            <td><?php echo htmlspecialchars($evt['lieu']); ?></td>
                            <td>
                                <a href="<?php echo WEB_ROOT; ?>/index.php?c=evenement&amp;a=edit&amp;id=<?php echo $evt['id_evenement']; ?>" class="btn-admin btn-edit">Modifier</a>
                                          <a href="<?php echo WEB_ROOT; ?>/index.php?c=evenement&amp;a=delete&amp;id=<?php echo $evt['id_evenement']; ?>" 
                                              class="btn-admin btn-delete">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

    </div> <!-- .admin-main -->
    </div> <!-- .admin-layout -->

</body>
</html>

