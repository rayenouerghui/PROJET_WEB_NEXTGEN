<?php include __DIR__ . '/header.php'; ?>

    <div class="events-page events-page--list">
        <div class="events-shell">
            <div class="events-hero">
                <h1>Découvrez nos événements</h1>
                <p>Réservez votre place et cumulez des points convertis en dons.</p>
            </div>

        <?php
        // Déterminer le nom de la catégorie active côté serveur pour affichage clair
        $activeCategoryName = 'Toutes les catégories';
        $initialFilterServer = $initial_filter ?? ($_GET['cat'] ?? 'all');
        if (!empty($categories_js) && $initialFilterServer !== 'all') {
            foreach ($categories_js as $c) {
                if (strval($c['id']) === strval($initialFilterServer)) {
                    $activeCategoryName = $c['name'];
                    break;
                }
            }
        }
        ?>

        <!-- Header/meta removed per user preference (no H2 or server filter info) -->

        <?php if (!empty($categories_js) && !defined('WEB_ROOT')) require_once __DIR__ . '/../../config/paths.php'; ?>
        <?php if ((($initialFilterServer ?? ($_GET['cat'] ?? 'all')) !== 'all')): ?>
        <div class="events-controls">
            <a class="btn back-to-categories" href="<?php echo WEB_ROOT; ?>/index.php?c=front&amp;a=categories">← Retour aux catégories</a>
        </div>
        <?php endif; ?>

        <!-- debugBanner removed to avoid showing transient debug messages to users -->

        <!-- Categories are rendered in views/front/categories.php; removed from events.php -->

        <div id="eventsContainer" class="events-grid">
            <?php
            $categoryNameById = [];
            if (!empty($categories_js)) {
                foreach ($categories_js as $__c) {
                    $categoryNameById[strval($__c['id'])] = $__c['name'];
                }
            }

            if (!empty($evenements_js)):
                foreach ($evenements_js as $evt):
                    $catName = $categoryNameById[strval($evt['category'] ?? '')] ?? ($evt['category'] ?? 'Non catégorisé');
                    ?>
                    <div class="event-card">
                        <div class="event-card-content">
                            <div class="event-category"><?php echo htmlspecialchars($catName); ?></div>
                            <div class="event-title"><?php echo htmlspecialchars($evt['title']); ?></div>
                            <div class="event-date"> <?php echo htmlspecialchars($evt['date']); ?></div>
                            <div class="event-lieu"> <?php echo htmlspecialchars($evt['lieu']); ?></div>
                            <p><?php echo nl2br(htmlspecialchars($evt['description'])); ?></p>
                            <div class="event-places">Places disponibles : <?php echo intval($evt['places']); ?></div>
                            <div class="event-points"> Cette réservation génère <?php echo intval($evt['points']); ?> points convertis en dons</div>
                        </div>
                        <div class="event-card-actions">
                            <button type="button" class="reserve-btn" data-event="<?php echo htmlspecialchars($evt['id']); ?>">Réserver</button>
                        </div>
                    </div>
                <?php endforeach;
            else: ?>
                <div class="empty-state">
                    <h2>Aucun événement disponible pour le moment</h2>
                    <p>La page fonctionne, mais votre base de données ne contient pas encore d'événements dans la table <code>evenement</code>.</p>
                    <?php if (!defined('WEB_ROOT')) require_once __DIR__ . '/../../config/paths.php'; ?>
                    <div class="empty-actions">
                        <a class="btn btn-primary" href="<?php echo WEB_ROOT; ?>/index.php?c=evenement&amp;a=index">Aller à l'administration des événements</a>
                        <a class="btn btn-secondary" href="<?php echo WEB_ROOT; ?>/index.php?c=front&amp;a=categories">Voir les catégories</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        </div>

        <div id="reservationToast" class="reservation-toast"></div>
    </div>

    <?php if (!defined('WEB_ROOT')) require_once __DIR__ . '/../../config/paths.php'; ?>
    <script type="text/javascript">window.NEXTGEN_WEB_ROOT = <?php echo json_encode(WEB_ROOT); ?>;</script>
    <script src="<?php echo WEB_ROOT; ?>/public/js/front-events.js"></script>

</body>
</html>
