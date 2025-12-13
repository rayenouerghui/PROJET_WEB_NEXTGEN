<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>NextGen - Gestion d'Événements</title>
    <link rel="stylesheet" href="/projet/public/css/style.css" type="text/css" />
    <link rel="stylesheet" href="/projet/public/css/front.css" type="text/css" />
    <script src="/projet/public/js/disable-html5.js"></script>
    <style>
        /* Contact modal: ensure labels and links are readable (black) on light modal */
        .contact-modal-content { color: #0b1220; }
        .contact-modal-details strong { color: #000000; }
        .contact-modal-details a { color: #0b1220; text-decoration: none; }
        .contact-modal-details a:hover { text-decoration: underline; }
        /* (No special header Contact button styling) */
    </style>
</head>
<body>
    <div class="site-header">
        <div class="header-inner">
            <a class="brand" href="/projet/index.php">
                <div class="brand-logo">
                    <img src="/projet/public/images/logo.png" alt="Logo NextGen" />
                </div>
                <div class="brand-text">
                    <span class="brand-title">NextGen Events</span>
                </div>
            </a>


            <div class="main-nav">
                <ul>
                        <li><a href="/projet/index.php?c=front&amp;a=index">Accueil</a></li>
                        <li><a href="/projet/index.php?c=front&amp;a=categories">Événements</a></li>
                        <li><a href="/projet/index.php?c=front&amp;a=historique">Historique des événements</a></li>
                        <li><a href="/projet/views/front/points.php">Points transformés en dons</a></li>
                        <li><a href="/projet/index.php?c=front&amp;a=leaderboard">Meilleurs participants</a></li>
                    <li><a href="#" id="contact-toggle">Contact</a></li>
                </ul>
            </div>
        </div>

<!-- small script: toggle .scrolled on header when page is scrolled -->
<script>
    (function(){
        var header = document.querySelector('.site-header') || document.querySelector('header');
        if(!header) return;
        var onScroll = function(){
            if(window.scrollY > 40) header.classList.add('scrolled'); else header.classList.remove('scrolled');
        };
        window.addEventListener('scroll', onScroll, {passive:true});
        document.addEventListener('DOMContentLoaded', onScroll);
    })();
</script>

    </div>

    <!-- Modal Contact (affiché quand on clique sur "Contact") -->
    <div class="contact-modal" id="contact-modal">
        <div class="contact-modal-backdrop"></div>
        <div class="contact-modal-content" role="dialog" aria-modal="true" aria-labelledby="contact-modal-title">
            <button class="contact-modal-close" id="contact-close" aria-label="Fermer la fenêtre de contact">×</button>
            <h2 id="contact-modal-title">Contact</h2>
            <p>Pour toute question concernant les événements ou les réservations, vous pouvez nous joindre :</p>
            <div class="contact-modal-details">
                <div>
                    <strong>Téléphone :</strong>
                    <a href="tel:+21612345678">+216 12 345 678</a>
                </div>
                <div>
                    <strong>Email :</strong>
                    <a href="mailto:contact@nextgen-events.com">contact@nextgen-events.com</a>
                </div>
            </div>
        </div>
    </div>

    <div class="points-banner">
        <span>
            🎁 Chaque réservation crédite des points solidaires convertis en dons.
            <a href="/projet/views/front/points.php">Découvrir le programme</a>
        </span>
    </div>

    <script type="text/javascript">
        (function () {
            var toggle = document.getElementById('contact-toggle');
            var modal = document.getElementById('contact-modal');
            var closeBtn = document.getElementById('contact-close');

            if (!toggle || !modal || !closeBtn) return;

            var openModal = function (event) {
                event.preventDefault();
                modal.classList.add('open');
                modal.setAttribute('aria-hidden', 'false');
            };

            var closeModal = function () {
                modal.classList.remove('open');
                modal.setAttribute('aria-hidden', 'true');
            };

            toggle.addEventListener('click', openModal);
            closeBtn.addEventListener('click', closeModal);
            modal.addEventListener('click', function (e) {
                if (e.target.classList.contains('contact-modal-backdrop')) {
                    closeModal();
                }
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeModal();
                }
            });
        })();
    </script>

    <!-- Auto-fit header script: reduces nav spacing/font until header fits on one line -->
    <script type="text/javascript">
        (function(){
            var headerInner = document.querySelector('.site-header .header-inner');
            var navList = document.querySelector('.main-nav ul');
            if (!headerInner || !navList) return;

            // Read CSS variable helpers
            var docEl = document.documentElement;
            var getNum = function(v, fallback){
                var val = getComputedStyle(docEl).getPropertyValue(v);
                if (!val) return fallback;
                return parseFloat(val.trim());
            };

            var fontSize = getNum('--nav-font-size', 1);
            var padVert = getNum('--nav-pad-vert', 0.35);
            var padHorz = getNum('--nav-pad-horz', 0.7);

            var minFont = 0.72; // don't go below this
            var minPadH = 0.28;

            function fits(){
                // header should be approximately one row (height close to initial header height)
                return headerInner.scrollWidth <= headerInner.clientWidth + 6 && headerInner.offsetHeight <= 72;
            }

            function shrinkStep(){
                var changed = false;
                if (!fits()){
                    if (fontSize > minFont){ fontSize = Math.max(minFont, fontSize - 0.06); changed = true; }
                    if (padHorz > minPadH){ padHorz = Math.max(minPadH, padHorz - 0.06); changed = true; }
                }
                if (changed){
                    docEl.style.setProperty('--nav-font-size', fontSize + 'rem');
                    docEl.style.setProperty('--nav-pad-horz', padHorz + 'rem');
                    // small visual nudge to reflow
                    navList.style.webkitOverflowScrolling = 'touch';
                }
                return changed;
            }

            function ensureOneLine(){
                var loops = 0;
                // try shrinking a few times
                while (!fits() && loops < 8){
                    if (!shrinkStep()) break;
                    loops++;
                }

                // Final fallback: enable horizontal scroll. Keep brand visible.
                if (!fits()){
                    navList.style.overflowX = 'auto';
                    // Do NOT hide the brand title — keep logo and name visible.
                    // If necessary we could instead hide low-priority nav items here.
                }
            }

            // Run on load and on resize (debounced)
            window.addEventListener('load', ensureOneLine);
            var to;
            window.addEventListener('resize', function(){ clearTimeout(to); to = setTimeout(ensureOneLine, 120); });
        })();
    </script>

            <!-- Global category-card click handler: ensures category cards navigate to events page -->
            <script type="text/javascript">
                (function(){
                    document.addEventListener('DOMContentLoaded', function(){
                        var cards = document.querySelectorAll('.category-card');
                        Array.prototype.forEach.call(cards, function(card){
                            card.style.cursor = 'pointer';
                            card.addEventListener('click', function(e){
                                    try {
                                        // If user opened link with Ctrl/Cmd/Shift or middle-click, allow default behavior
                                        if (e.ctrlKey || e.metaKey || e.shiftKey || e.button === 1) {
                                            return;
                                        }
                                        var href = card.getAttribute('href');
                                        var target = card.getAttribute('target');
                                        var cat = card.getAttribute('data-cat');

                                        // If the anchor is meant to open in a new tab, do not intercept
                                        if (target && target.toLowerCase() === '_blank') {
                                            return;
                                        }

                                        if (!href || href.indexOf('javascript:') === 0) {
                                            if (cat) {
                                                href = '/projet/index.php?c=front&a=events&cat=' + encodeURIComponent(cat);
                                            }
                                        }

                                        if (href) {
                                            // intercept only when we need to force navigation in the same tab
                                            e.preventDefault();
                                            window.location.href = href;
                                        }
                                    } catch (err) {
                                        console.error('Navigation error', err);
                                    }
                                });
                        });
                    });
                })();
            </script>

            