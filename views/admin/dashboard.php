<?php include __DIR__ . '/header.php'; ?>

<div class="admin-container">
    <div class="dashboard-header">
        <h1>Dashboard Administrateur</h1>
        <p class="dashboard-subtitle">Vue d'ensemble de votre plateforme</p>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['info'])): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($_SESSION['info']); unset($_SESSION['info']); ?></div>
    <?php endif; ?>

    <!-- Statistiques principales -->
    <div class="stats-grid">
        <div class="stat-card stat-card-primary">
            <div class="stat-icon">📁</div>
            <div class="stat-content">
                <h3>Catégories</h3>
                <p class="stat-number"><?php echo $stats['categories']; ?></p>
                <span class="stat-label">Total</span>
            </div>
        </div>
        <div class="stat-card stat-card-success">
            <div class="stat-icon">🎉</div>
            <div class="stat-content">
                <h3>Événements</h3>
                <p class="stat-number"><?php echo $stats['evenements']; ?></p>
                <span class="stat-label">Total</span>
            </div>
        </div>
        <div class="stat-card stat-card-info">
            <div class="stat-icon">📋</div>
            <div class="stat-content">
                <h3>Réservations</h3>
                <p class="stat-number"><?php echo $stats['reservations']; ?></p>
                <span class="stat-label">Total</span>
            </div>
        </div>
        <div class="stat-card stat-card-warning">
            <div class="stat-icon">📅</div>
            <div class="stat-content">
                <h3>Réservations aujourd'hui</h3>
                <p class="stat-number"><?php echo $stats['reservations_today']; ?></p>
                <span class="stat-label">Aujourd'hui</span>
            </div>
        </div>
    </div>

    <!-- Graphique des réservations (7 derniers jours) -->
    <div class="dashboard-card chart-card">
        <h2>Réservations des 7 derniers jours</h2>
        <div class="chart-container">
            <!-- Remplacé <canvas> (HTML5) par un conteneur DIV compatible XHTML/HTML4 -->
            <div id="reservationsChart" class="bar-chart" style="height:150px; width:100%; display:block;"></div>
        </div>
    </div>

    <div class="dashboard-grid">
        <!-- Dernières réservations -->
        <div class="dashboard-card">
            <div class="card-header">
                <h2>Dernières réservations</h2>
                <a href="/projet/index.php?c=reservation&amp;a=index" class="btn-link">Voir tout</a>
            </div>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Événement</th>
                            <th>Places</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($dernieres_reservations)): ?>
                            <tr><td colspan="4" class="text-center">Aucune réservation</td></tr>
                        <?php else: ?>
                            <?php foreach ($dernieres_reservations as $res): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($res['nom_complet']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($res['evenement_titre'] ?? 'N/A'); ?></td>
                                    <td><span class="badge"><?php echo $res['nombre_places']; ?></span></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($res['date_reservation'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Événements à venir -->
        <div class="dashboard-card">
            <div class="card-header">
                <h2>Événements à venir</h2>
                <a href="/projet/index.php?c=evenement&amp;a=index" class="btn-link">Voir tout</a>
            </div>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Catégorie</th>
                            <th>Date</th>
                            <th>Lieu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($evenements_prochains)): ?>
                            <tr><td colspan="4" class="text-center">Aucun événement à venir</td></tr>
                        <?php else: ?>
                            <?php foreach ($evenements_prochains as $evt): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($evt['titre']); ?></strong></td>
                                    <td><span class="badge badge-category"><?php echo htmlspecialchars($evt['nom_categorie'] ?? 'N/A'); ?></span></td>
                                    <td><?php echo date('d/m/Y', strtotime($evt['date_evenement'])); ?></td>
                                    <td><?php echo htmlspecialchars($evt['lieu']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Statistiques par catégorie -->
    <div class="dashboard-card">
        <h2>Événements par catégorie</h2>
        <div class="categories-stats">
            <?php foreach ($stats_categories as $stat): ?>
                <div class="category-stat-item">
                    <div class="category-stat-label"><?php echo htmlspecialchars($stat['nom']); ?></div>
                    <div class="category-stat-bar">
                        <div class="category-stat-fill" style="width: <?php echo $stats['evenements'] > 0 ? ($stat['total'] / $stats['evenements'] * 100) : 0; ?>%"></div>
                    </div>
                    <div class="category-stat-value"><?php echo $stat['total']; ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

    </div> <!-- .admin-main -->
    </div> <!-- .admin-layout -->

<script type="text/javascript">
    // Graphique des réservations (implémentation sans <canvas> pour compatibilité XHTML/HTML4)
    (function() {
        var container = document.getElementById('reservationsChart');
        if (!container) return;

        var data = <?php echo json_encode($reservations_7jours); ?>;
        var values = data.map(function(d) { return d.total; });
        var maxValue = Math.max.apply(Math, values.length ? values : [1]);
        if (maxValue === 0) maxValue = 1;

        var chartHeight = 150; // hauteur en px (doit correspondre au style du conteneur)
        container.style.position = 'relative';
        container.style.fontFamily = 'Arial, sans-serif';

        // Clear
        while (container.firstChild) container.removeChild(container.firstChild);

        // Créer des barres en DIV
        for (var i = 0; i < data.length; i++) {
            var item = data[i];

            var wrap = document.createElement('div');
            wrap.style.display = 'inline-block';
            wrap.style.verticalAlign = 'bottom';
            wrap.style.width = Math.floor(100 / data.length) + '%';
            wrap.style.textAlign = 'center';
            wrap.style.boxSizing = 'border-box';
            wrap.style.padding = '0 4px';

            var valueDiv = document.createElement('div');
            valueDiv.style.fontSize = '12px';
            valueDiv.style.color = '#333';
            valueDiv.style.marginBottom = '6px';
            valueDiv.appendChild(document.createTextNode(item.total));

            var bar = document.createElement('div');
            var barHeight = Math.round((item.total / maxValue) * (chartHeight - 20));
            bar.style.height = barHeight + 'px';
            bar.style.width = '60%';
            bar.style.margin = '0 auto';
            bar.style.backgroundColor = '#4CAF50';
            bar.style.borderRadius = '2px';

            var label = document.createElement('div');
            label.style.fontSize = '10px';
            label.style.color = '#666';
            label.style.marginTop = '6px';
            label.appendChild(document.createTextNode(item.date));

            wrap.appendChild(valueDiv);
            wrap.appendChild(bar);
            wrap.appendChild(label);
            container.appendChild(wrap);
        }
    })();
</script>

</body>
</html>
