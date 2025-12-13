<?php include __DIR__ . '/header.php'; ?>

    <div class="events-page">
        <div class="events-hero">
            <h1>Découvrez nos événements</h1>
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

        <?php if ((($initialFilterServer ?? ($_GET['cat'] ?? 'all')) !== 'all')): ?>
        <div class="events-controls" style="text-align:center;margin-bottom:10px;">
            <a class="btn back-to-categories" href="/projet/index.php?c=front&amp;a=categories" style="padding:8px 12px;border-radius:6px;border:1px solid #ddd;background:#fff;cursor:pointer;display:inline-block;text-decoration:none;color:inherit;">← Retour aux catégories</a>
        </div>
        <?php endif; ?>

        <!-- debugBanner removed to avoid showing transient debug messages to users -->

        <!-- Categories are rendered in views/front/categories.php; removed from events.php -->

        <div id="eventsContainer" class="events-grid">
            <?php
            // Server-side fallback rendering: if a category filter is active and
            // PHP provided events (evenements_js), render them. Otherwise keep
            // the container empty (no message).
            if ((($initialFilterServer ?? 'all') !== 'all') && !empty($evenements_js)):
                foreach ($evenements_js as $evt): ?>
                    <div class="event-card">
                        <div class="event-card-content">
                            <div class="event-category"><?php echo htmlspecialchars($evt['category']); ?></div>
                            <div class="event-title"><?php echo htmlspecialchars($evt['title']); ?></div>
                            <div class="event-date">📅 <?php echo htmlspecialchars($evt['date']); ?></div>
                            <div class="event-lieu">📍 <?php echo htmlspecialchars($evt['lieu']); ?></div>
                            <p><?php echo nl2br(htmlspecialchars($evt['description'])); ?></p>
                            <div class="event-places">Places disponibles : <?php echo intval($evt['places']); ?></div>
                            <div class="event-points">🎁 Cette réservation génère <?php echo intval($evt['points']); ?> points convertis en dons</div>
                        </div>
                        <div class="event-card-actions">
                            <button type="button" class="reserve-btn" data-event="<?php echo htmlspecialchars($evt['id']); ?>">Réserver</button>
                        </div>
                    </div>
                <?php endforeach;
            endif; ?>
        </div>
    </div>

    <div id="reservationToast" class="reservation-toast"></div>

    <script src="/projet/public/js/front-events.js"></script>
    <script type="text/javascript">
        (function () {
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    initEvents();
                });
            } else {
                initEvents();
            }

            function initEvents() {
            // Données chargées depuis la base de données via PHP
            var categories = <?php echo json_encode($categories_js ?? []); ?>;
            // If the server did not receive a category filter, do not expose events to the client
            <?php $client_events = (($initialFilterServer ?? 'all') !== 'all') ? ($evenements_js ?? []) : []; ?>
            var events = <?php echo json_encode($client_events); ?>;
            var initialFilter = <?php echo json_encode($initial_filter ?? 'all'); ?>;

            var categoriesContainer = document.getElementById('categoriesContainer');
            var container = document.getElementById('eventsContainer');
            var currentFilter = 'all';
            var pointsKey = 'nextgenPoints';
            var pointsBalance = parseInt(window.localStorage.getItem(pointsKey) || '0', 10);
            var toast = document.getElementById('reservationToast');

            function renderCategoryCards() {
                if (!categoriesContainer) return;
                // If the server already rendered category cards and JS data is empty,
                // preserve the server markup to avoid removing visible cards.
                if (categories && categories.length) {
                    categoriesContainer.innerHTML = '';
                }

                for (var i = 0; i < categories.length; i++) {
                    var c = categories[i];
                    var card = document.createElement('a');
                    card.className = 'event-card category-card';
                    card.href = '/projet/index.php?c=front&a=events&cat=' + encodeURIComponent(c.id);
                    card.setAttribute('data-cat', c.id);
                    card.innerHTML = '<div class="category-name">' + escapeHtml(c.name) + '</div>' +
                                     '<p class="category-desc">' + escapeHtml(c.description || '') + '</p>' +
                                     '<div class="event-places">Événements : ' + (c.count || 0) + '</div>';
                    // make anchor point to events page (navigation will reload and server will filter)
                    categoriesContainer.appendChild(card);
                }
                // No delegated click handler: anchors navigate to the events page with ?cat=ID
            }

            // Simple HTML escaper used by dynamic rendering
            function escapeHtml(str){
                if (!str && str !== 0) return '';
                return String(str)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#39;');
            }

            function showBanner(msg) {
                // intentionally no-op in production: visual debug banners disabled
                return;
            }

            function setActiveCategory(catId) {
                var cards = categoriesContainer.querySelectorAll('.category-card');
                for (var i = 0; i < cards.length; i++) {
                    cards[i].classList.remove('active');
                    if (cards[i].getAttribute('data-cat') == catId) {
                        cards[i].classList.add('active');
                    }
                }
            }

            function getCategoryName(id) {
                id = String(id);
                for (var i = 0; i < categories.length; i++) {
                    if (String(categories[i].id) === id) {
                        return categories[i].name;
                    }
                }
                return 'Non catégorisé';
            }

            function renderEvents(filter) {
                currentFilter = (filter || 'all');
                container.innerHTML = '';
                var hasResult = false;
                for (var i = 0; i < events.length; i++) {
                    if (currentFilter !== 'all' && String(events[i].category) !== String(currentFilter)) {
                        continue;
                    }
                    hasResult = true;
                    var card = document.createElement('div');
                    card.className = 'event-card';
                    card.innerHTML =
                        '<div class="event-card-content">' +
                            '<div class="event-category">' + getCategoryName(events[i].category) + '</div>' +
                            '<div class="event-title">' + events[i].title + '</div>' +
                            '<div class="event-date">📅 ' + events[i].date + '</div>' +
                            '<div class="event-lieu">📍 ' + events[i].lieu + '</div>' +
                            '<p>' + events[i].description + '</p>' +
                            '<div class="event-places">Places disponibles : ' + events[i].places + '</div>' +
                            '<div class="event-points">🎁 Cette réservation génère ' + events[i].points + ' points convertis en dons</div>' +
                        '</div>' +
                        '<div class="event-card-actions">' +
                            '<button type="button" class="reserve-btn" data-event="' + events[i].id + '" data-points="' + events[i].points + '">Réserver</button>' +
                        '</div>';
                    container.appendChild(card);
                }
                attachFormListeners();
            }

            // Reservation-related helpers (form building, validation, listeners)
            // moved to `/projet/public/js/front-events.js` and exposed as global functions:
            // - buildForm(event)
            // - attachFormListeners(root)
            // - toggleForm(eventId, root)
            // - addPoints(amount)
            // - showToast(message)
            // - updateEventPlaces(eventId, reserved, placesRestantes)
            // - validateForm(form, feedback, isLive)
            // - attachLiveValidation(form, feedback)

            // Initial rendering of categories and events
            var initial = initialFilter || 'all';
            if (categoriesContainer) {
                renderCategoryCards();
                // Apply initial filter and set active card
                setActiveCategory(initial);
            }
            renderEvents(initial);
            }
        })();
    </script>

    <script>
        // Ensure card dimensions at runtime — applies to server-rendered and JS-rendered cards
        (function(){
            var container = document.getElementById('eventsContainer');
            if (!container) return;

            // apply fixed sizing to event cards only when there are 1 or 2 cards
            function applyFixedSizing() {
                const container = document.getElementById('eventsContainer');
                if (!container) return;
                const cards = container.querySelectorAll('.event-card');
                const count = cards.length;

                // sizing values (increased height to match multi-card frame)
                const widthDesktop = 520;
                const widthTablet = 420;
                const height = 440;

                if (count === 0) return;

                if (count <= 2) {
                    cards.forEach(card => {
                        // mark as fixed-frame so CSS pins actions inside the card
                        card.classList.add('fixed-frame');
                        if (card.tagName === 'A') card.style.display = 'block';

                        if (window.innerWidth <= 768) {
                            card.style.width = '100%';
                            card.style.maxWidth = '100%';
                            card.style.height = 'auto';
                        } else if (window.innerWidth <= 992) {
                            card.style.width = widthTablet + 'px';
                            card.style.maxWidth = widthTablet + 'px';
                            card.style.height = height + 'px';
                        } else {
                            card.style.width = widthDesktop + 'px';
                            card.style.maxWidth = widthDesktop + 'px';
                            card.style.height = height + 'px';
                        }
                    });
                } else {
                    // more than 2 cards: remove inline sizing and fixed-frame class so CSS grid handles layout
                    cards.forEach(card => {
                        card.classList.remove('fixed-frame');
                        card.style.width = '';
                        card.style.maxWidth = '';
                        card.style.height = '';
                        if (card.tagName === 'A') card.style.display = '';
                    });
                }
            }

            // Initial pass
            applyFixedSizing();

            // Re-apply on resize
            window.addEventListener('resize', function(){ applyFixedSizing(); });

            // Observe DOM changes (cards added/removed)
            var mo = new MutationObserver(function(){ applyFixedSizing(); });
            mo.observe(container, { childList: true, subtree: true });
        })();
    </script>

</body>
</html>

