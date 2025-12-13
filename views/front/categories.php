<?php include __DIR__ . '/header.php'; ?>

    <div class="events-page">
        <div class="events-hero">
            <h1>Choisissez une catégorie</h1>
            <p>Parcourez les catégories et cliquez sur l'une d'elles pour voir les événements correspondants.</p>
        </div>

        <!-- container where events will be injected after a category is chosen -->
        <div id="eventsPreview" class="events-grid" style="margin-bottom:18px;display:none;"></div>

        <div id="categoriesContainer" class="categories-grid">
            <?php if (!empty($categories_js)): ?>
                <?php foreach ($categories_js as $cat): ?>
                    <a class="event-card category-card" href="/projet/index.php?c=front&amp;a=events&amp;cat=<?php echo htmlspecialchars($cat['id']); ?>" data-cat="<?php echo htmlspecialchars($cat['id']); ?>">
                        <div class="category-name"><?php echo htmlspecialchars($cat['name']); ?></div>
                        <?php if (!empty($cat['description'])): ?>
                            <p class="category-desc"><?php echo htmlspecialchars($cat['description']); ?></p>
                        <?php endif; ?>
                        <div class="event-places">Événements : <?php echo intval($cat['count'] ?? 0); ?></div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">Aucune catégorie disponible.</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Categories page — links open the events page filtered by category -->

<script>
// Intercept category clicks: load events via fetch and update hero/title
(function(){
    var container = document.getElementById('categoriesContainer');
    var preview = document.getElementById('eventsPreview');
    var heroTitle = document.querySelector('.events-hero h1');
    if (!container) return;

    container.addEventListener('click', function(e){
        var a = e.target.closest && e.target.closest('a.event-card.category-card');
        if (!a) return;
        // allow ctrl/cmd click or middle-click to open in new tab
        if (e.metaKey || e.ctrlKey || e.button === 1) return;
        e.preventDefault();
        // immediately update hero title so user sees the change right away
        if (heroTitle) heroTitle.textContent = 'Découvrez nos événements';
        var href = a.href;
        // fetch the events page and extract #eventsContainer
        fetch(href, { credentials: 'same-origin' }).then(function(res){
            if (!res.ok) throw new Error('Network response not ok');
            return res.text();
        }).then(function(text){
            var parser = new DOMParser();
            var doc = parser.parseFromString(text, 'text/html');
            var events = doc.getElementById('eventsContainer');
            if (events) {
                // inject a header with a close button and then the events HTML
                // Remove any previously injected wrapper to avoid duplicates
                var prevWrapper = preview.querySelector('.injected-events');
                if (prevWrapper) prevWrapper.remove();

                // Create a wrapper so we can remove injected content cleanly later
                var wrapper = document.createElement('div');
                wrapper.className = 'injected-events';
                wrapper.innerHTML = '<div class="preview-header" style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-bottom:1px solid rgba(0,0,0,0.04);background:#fff;margin-bottom:12px;"><strong style="font-size:1rem">Événements</strong><button id="closePreview" style="background:#f3f4f6;border:1px solid #e5e7eb;padding:6px 10px;border-radius:8px;cursor:pointer">Fermer</button></div>' + events.innerHTML;
                preview.appendChild(wrapper);
                preview.style.display = '';
                // change hero title to 'Découvrez nos événements'
                if (heroTitle) heroTitle.textContent = 'Découvrez nos événements';
                // If the server-rendered events include a back button, override it when injected inside the preview
                try {
                    // only look inside the wrapper we just created
                    var injectedBackButtons = wrapper.querySelectorAll('.back-to-categories');
                    injectedBackButtons.forEach(function(btn){
                        // remove any inline onclick behavior for safety
                        btn.removeAttribute('onclick');
                        btn.addEventListener('click', function(e){
                            e.preventDefault();
                            // remove wrapper and hide preview
                            wrapper.remove();
                            preview.style.display = 'none';
                            if (heroTitle) heroTitle.textContent = 'Choisissez une catégorie';
                        });
                    });
                } catch (err) {
                    // ignore if query fails
                }
                // attach full preview listeners (reservation behavior) after injecting content
                if (typeof attachFormListeners === 'function') attachFormListeners(preview);
                // wire the close button to hide preview and restore title (DO NOT change URL)
                var closeBtn = document.getElementById('closePreview');
                if (closeBtn) {
                    closeBtn.addEventListener('click', function(){
                        // remove wrapper and hide preview
                        var wrapper = preview.querySelector('.injected-events');
                        if (wrapper) wrapper.remove();
                        preview.style.display = 'none';
                        if (heroTitle) heroTitle.textContent = 'Choisissez une catégorie';
                    });
                }
                // scroll to top of preview
                preview.scrollIntoView({ behavior: 'smooth', block: 'start' });
            } else {
                // fallback: navigate directly
                window.location.href = href;
            }
        }).catch(function(){
            // on error fall back to full navigation
            window.location.href = href;
        });
    }, false);
})();
</script>

<script>
// reservation behaviors for preview are provided by `/projet/public/js/front-events.js`
// which exposes `attachFormListeners(root)` used above after injection.
</script>

</body>
</html>
