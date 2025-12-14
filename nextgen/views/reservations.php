<?php include __DIR__ . '/header.php'; ?>

<div class="admin-container">
    <div class="admin-page-header">
        <h1>Gestion des Réservations</h1>

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
                    <th>Nom complet</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Événement</th>
                    <th>Places</th>
                    <th>Points</th>
                    <th>Date réservation</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reservations)): ?>
                    <tr><td colspan="8">Aucune réservation</td></tr>
                <?php else: ?>
                    <?php foreach ($reservations as $res): ?>
                        <tr>
                            <td><?php echo $res['id_reservation']; ?></td>
                            <td><?php echo htmlspecialchars($res['nom_complet']); ?></td>
                            <td><?php echo htmlspecialchars($res['email']); ?></td>
                            <td><?php echo htmlspecialchars($res['telephone']); ?></td>
                            <td><?php echo htmlspecialchars($res['evenement_titre'] ?? 'N/A'); ?></td>
                            <td><?php echo $res['nombre_places']; ?></td>
                            <td><?php echo intval($res['points_generes'] ?? 0); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($res['date_reservation'])); ?></td>
                            <td>
                                          <a href="<?php echo WEB_ROOT; ?>/index.php?c=reservation&amp;a=delete&amp;id=<?php echo $res['id_reservation']; ?>" 
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

