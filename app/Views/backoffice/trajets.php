<?php
$title = 'Suivi des trajets';
$extraCss = ['css/backoffice/livraisons.css'];
require __DIR__ . '/_partials/header.php';
?>

<div class="admin-wrapper">
    <h1>Trajets temps réel</h1>
    <p style="color: var(--text-medium); margin-bottom: 24px;">Historique des positions récupérées via l’API.</p>

    <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo htmlspecialchars($messageType ?? 'info'); ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <section class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Trajet</th>
                    <th>Commande / Jeu</th>
                    <th>Client</th>
                    <th>Statut API</th>
                    <th>Coordonnées</th>
                    <th>Dernière MAJ</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($trajets)): ?>
                    <tr>
                        <td colspan="7" style="text-align:center;color:var(--text-medium);padding:40px;">
                            Aucun trajet enregistré.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($trajets as $trajet): ?>
                        <tr>
                            <td>#<?php echo (int)$trajet['id_trajet']; ?><br><small><?php echo htmlspecialchars($trajet['identifiant_suivi']); ?></small></td>
                            <td>#<?php echo htmlspecialchars($trajet['numero_commande']); ?><br><?php echo htmlspecialchars($trajet['nom_jeu'] ?? 'Jeu'); ?></td>
                            <td><?php echo htmlspecialchars($trajet['prenom_utilisateur'] . ' ' . $trajet['nom_utilisateur']); ?></td>
                            <td><?php echo htmlspecialchars($trajet['statut_realtime']); ?></td>
                            <td><?php echo $trajet['position_lat'] && $trajet['position_lng']
                                ? htmlspecialchars($trajet['position_lat'] . ', ' . $trajet['position_lng'])
                                : '<span style="color:var(--text-light);">N/A</span>'; ?></td>
                            <td><?php echo htmlspecialchars($trajet['derniere_mise_a_jour']); ?></td>
                            <td>
                                <div class="actions">
                                    <form method="post">
                                        <input type="hidden" name="action" value="sync_trajet">
                                        <input type="hidden" name="id_trajet" value="<?php echo (int)$trajet['id_trajet']; ?>">
                                        <button class="btn ghost" type="submit">Synchroniser</button>
                                    </form>
                                    <form method="post" onsubmit="return confirm('Supprimer ce trajet ?');">
                                        <input type="hidden" name="action" value="delete_trajet">
                                        <input type="hidden" name="id_trajet" value="<?php echo (int)$trajet['id_trajet']; ?>">
                                        <button class="btn danger" type="submit">Supprimer</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </section>
</div>

</main>
</body>
</html>

