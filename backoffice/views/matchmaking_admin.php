<style>
.error-message {
    color: #ef4444;
    font-size: 14px;
    margin-top: 5px;
    display: none;
}
.matchmaking-dashboard {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}
.dashboard-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border-left: 4px solid #2563eb;
}
.dashboard-card h3 {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 10px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.dashboard-card .value {
    font-size: 32px;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 5px;
}
.dashboard-card .label {
    font-size: 12px;
    color: #9ca3af;
}
.dashboard-card.waiting {
    border-left-color: #f59e0b;
}
.dashboard-card.active {
    border-left-color: #10b981;
}
.dashboard-card.total {
    border-left-color: #2563eb;
}
</style>

<div class="attentes-container">
    <div class="page-header">
        <h1 class="page-title">Gestion du Matchmaking</h1>
        <p class="page-subtitle">Gérez les files d'attente et les sessions de match</p>
    </div>

    <?php if (isset($message) && $message): ?>
        <div class="alert alert-<?php echo isset($messageType) ? $messageType : 'info'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="matchmaking-dashboard">
        <div class="dashboard-card waiting">
            <h3>En Attente</h3>
            <div class="value"><?php echo isset($attentes) ? count($attentes) : 0; ?></div>
            <div class="label">Utilisateurs en file d'attente</div>
        </div>
        <div class="dashboard-card active">
            <h3>Sessions Actives</h3>
            <div class="value"><?php echo isset($sessions) ? count(array_filter($sessions, function($s) { return isset($s['statut']) && $s['statut'] === 'active'; })) : 0; ?></div>
            <div class="label">Sessions en cours</div>
        </div>
        <div class="dashboard-card total">
            <h3>Total Sessions</h3>
            <div class="value"><?php echo isset($sessions) ? count($sessions) : 0; ?></div>
            <div class="label">Toutes les sessions</div>
        </div>
        <div class="dashboard-card">
            <h3>Jeux en Attente</h3>
            <div class="value"><?php echo isset($attentesParJeu) ? count($attentesParJeu) : 0; ?></div>
            <div class="label">Jeux avec des attentes</div>
        </div>
    </div>

    <div class="section">
        <h2 class="section-title">📋 Files d'Attente</h2>
        
        <?php if (!isset($attentesParJeu) || empty($attentesParJeu)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">📭</div>
                <h3 class="empty-state-title">Aucune attente active</h3>
                <p class="empty-state-text">Aucun utilisateur n'est actuellement en attente de match.</p>
            </div>
        <?php else: ?>
            <?php foreach ($attentesParJeu as $groupe): ?>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><?php echo htmlspecialchars($groupe['nom_jeu']); ?> (<?php echo count($groupe['attentes']); ?> en attente)</h3>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Utilisateur</th>
                                    <th>Email</th>
                                    <th>Date d'ajout</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($groupe['attentes'] as $attente): ?>
                                    <tr>
                                        <td><?php echo $attente['id_attente']; ?></td>
                                        <td><?php echo htmlspecialchars($attente['prenom'] . ' ' . $attente['nom']); ?></td>
                                        <td><?php echo htmlspecialchars($attente['email']); ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($attente['date_ajout'])); ?></td>
                                        <td>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="supprimer_attente">
                                                <input type="hidden" name="id_attente" value="<?php echo $attente['id_attente']; ?>">
                                                <button type="submit" class="btn-icon btn-danger" onclick="return confirm('Supprimer cette attente?')">Supprimer</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <form method="POST" style="margin-top: 16px;">
                            <input type="hidden" name="action" value="verifier_matchs">
                            <input type="hidden" name="id_jeu" value="<?php echo $groupe['id_jeu']; ?>">
                            <button type="submit" class="btn btn-success">🔍 Vérifier les matchs pour ce jeu</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="section">
        <h2 class="section-title">🎮 Sessions Actives</h2>
        
        <?php if (!isset($sessions) || empty($sessions)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">🎯</div>
                <h3 class="empty-state-title">Aucune session active</h3>
                <p class="empty-state-text">Aucune session de match n'est actuellement active.</p>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Jeu</th>
                            <th>Participants</th>
                            <th>Lien</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>Modifier</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sessions as $session): ?>
                            <tr>
                                <td><?php echo $session['id_session']; ?></td>
                                <td><?php echo htmlspecialchars($session['nom_jeu']); ?></td>
                                <td><?php echo count($session['participants']); ?> joueur(s)</td>
                                <td><a href="<?php echo htmlspecialchars($session['lien_session']); ?>" target="_blank">Lien</a></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($session['date_creation'])); ?></td>
                                <td>
                                    <form method="POST" style="display: inline-block;">
                                        <input type="hidden" name="action" value="modifier_session">
                                        <input type="hidden" name="id_session" value="<?php echo $session['id_session']; ?>">
                                        <select name="statut" onchange="this.form.submit()" style="padding: 5px; border-radius: 5px; margin-right: 5px;">
                                            <option value="active" <?php echo $session['statut'] === 'active' ? 'selected' : ''; ?>>Active</option>
                                            <option value="terminee" <?php echo $session['statut'] === 'terminee' ? 'selected' : ''; ?>>Terminée</option>
                                            <option value="annulee" <?php echo $session['statut'] === 'annulee' ? 'selected' : ''; ?>>Annulée</option>
                                        </select>
                                    </form>
                                    <span class="badge badge-<?php echo $session['statut'] === 'active' ? 'success' : 'warning'; ?>"><?php echo htmlspecialchars($session['statut']); ?></span>
                                </td>
                                <td>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="supprimer_session">
                                        <input type="hidden" name="id_session" value="<?php echo $session['id_session']; ?>">
                                        <button type="submit" class="btn-icon btn-danger" onclick="return confirm('Supprimer cette session?')">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <div class="section">
        <h2 class="section-title">🧹 Maintenance</h2>
        <form method="POST" data-validate>
            <input type="hidden" name="action" value="nettoyer_attentes">
            <div class="form-group">
                <label>Supprimer les anciennes attentes de plus de (jours):</label>
                <input type="text" name="jours" value="7" data-validate="required number" data-min="1" data-max="365">
            </div>
            <button type="submit" class="btn btn-danger">Nettoyer les anciennes attentes</button>
        </form>
    </div>
</div>

