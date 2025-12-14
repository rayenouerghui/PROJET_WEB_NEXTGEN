<?php include __DIR__ . '/header.php'; ?>

<div class="historique-page">
    <div class="page-header">
        <h1>Historique des événements</h1>
        <p>Retrouvez tous les événements passés organisés par NextGen Events</p>
    </div>

    <!-- Filtre par catégorie -->
    <?php if (!empty($categories)): ?>
    <div class="filter-section">
        <label for="categoryFilter">Filtrer par catégorie :</label>
        <select id="categoryFilter" class="filter-select">
            <option value="all">Toutes les catégories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat->getIdCategoriev(); ?>"><?php echo htmlspecialchars($cat->getNomCategoriev()); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php endif; ?>

    <!-- Liste des événements passés -->
    <div class="historique-container">
        <?php if (empty($evenements_passes)): ?>
            <div class="empty-state">
                <div class="empty-icon">📭</div>
                <h2>Aucun événement passé</h2>
                <p>Il n'y a pas encore d'événements dans l'historique.</p>
                <?php if (!defined('WEB_ROOT')) require_once __DIR__ . '/../../config/paths.php'; ?>
                <a href="<?php echo WEB_ROOT; ?>/index.php?c=front&amp;a=events" class="btn btn-primary">Voir les événements à venir</a>
            </div>
        <?php else: ?>
            <div class="historique-timeline">
                <?php 
                $current_year = null;
                $current_month = null;
                foreach ($evenements_passes as $evt): 
                    $date_obj = new DateTime($evt['date_evenement']);
                    $year = $date_obj->format('Y');
                    $month = $date_obj->format('m');
                    
                    // Afficher l'année si elle change
                    if ($current_year !== $year):
                        if ($current_year !== null):
                            echo '</div>'; // Fermer le groupe de mois précédent
                        endif;
                        echo '<div class="timeline-year">';
                        echo '<h2 class="year-title">' . $year . '</h2>';
                        $current_year = $year;
                        $current_month = null;
                    endif;
                    
                    // Afficher le mois si il change
                    $mois_noms = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
                    if ($current_month !== $month):
                        if ($current_month !== null):
                            echo '</div>'; // Fermer le groupe de mois précédent
                        endif;
                        echo '<div class="timeline-month">';
                        echo '<h3 class="month-title">' . $mois_noms[(int)$month - 1] . '</h3>';
                        $current_month = $month;
                    endif;
                ?>
                    <div class="historique-event-card" data-category="<?php echo $evt['id_categorie']; ?>">
                        <div class="event-date-badge">
                            <span class="event-day"><?php echo $date_obj->format('d'); ?></span>
                            <span class="event-month-short"><?php echo substr($mois_noms[(int)$month - 1], 0, 3); ?></span>
                        </div>
                        <div class="event-content">
                            <div class="event-header">
                                <span class="event-category"><?php echo htmlspecialchars($evt['nom_categorie'] ?? 'Général'); ?></span>
                                <span class="event-status past">Terminé</span>
                            </div>
                            <h3 class="event-title"><?php echo htmlspecialchars($evt['titre']); ?></h3>
                            <p class="event-description"><?php echo htmlspecialchars($evt['description']); ?></p>
                            <div class="event-details">
                                <div class="event-detail-item">
                                    <span class="detail-icon">📍</span>
                                    <span><?php echo htmlspecialchars($evt['lieu']); ?></span>
                                </div>
                                <div class="event-detail-item">
                                    <span class="detail-icon">📅</span>
                                    <span><?php echo $evt['date_formatee']; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php 
                endforeach; 
                // Fermer les derniers groupes
                if ($current_month !== null) echo '</div>';
                if ($current_year !== null) echo '</div>';
                ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script type="text/javascript">
    // Filtrage par catégorie
    (function() {
        var filter = document.getElementById('categoryFilter');
        if (!filter) return;
        
        filter.addEventListener('change', function() {
            var selectedCategory = this.value;
            var eventCards = document.querySelectorAll('.historique-event-card');
            
            eventCards.forEach(function(card) {
                if (selectedCategory === 'all' || card.getAttribute('data-category') === selectedCategory) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    })();
</script>

</body>
</html>

