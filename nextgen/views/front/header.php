<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>NextGen - Gestion d'Événements</title>
    <?php 
    if (!defined('WEB_ROOT')) {
        require_once __DIR__ . '/../../config/paths.php';
    }
    ?>
    <link rel="stylesheet" href="<?php echo WEB_ROOT; ?>/public/css/style.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo WEB_ROOT; ?>/public/css/front.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo WEB_ROOT; ?>/public/css/frontoffice.css" type="text/css" />
    <script src="<?php echo WEB_ROOT; ?>/public/js/disable-html5.js"></script>
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
    <?php if (session_status() === PHP_SESSION_NONE) { session_start(); } ?>
    <?php include __DIR__ . '/../../view/frontoffice/includes/navigation.php'; ?>

    <?php
      $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
      $currentFile = basename($currentPath ?: '');
      $isEventsSection = (isset($_GET['c']) && $_GET['c'] === 'front' && in_array($_GET['a'] ?? '', ['events','historique','leaderboard','points'], true))
        || (isset($_GET['c']) && $_GET['c'] === 'front' && ($_GET['a'] ?? '') === 'categories')
        || ($currentFile === 'points.php');
    ?>
    <?php if ($isEventsSection): ?>
      <div class="site-header events-nav-bar" style="position:sticky;top:64px;z-index:900;background:#ffffff;box-shadow:0 2px 8px rgba(0,0,0,0.1);border-bottom:1px solid #e5e7eb;">
        <div class="header-inner" style="padding:12px 18px;max-width:1200px;margin:0 auto;display:flex;align-items:center;justify-content:center;">
          <nav class="main-nav" style="flex:1;display:flex;justify-content:center;">
            <ul style="display:flex;list-style:none;gap:0.75rem;margin:0;padding:0;align-items:center;flex-wrap:wrap;">
              <li><a href="<?php echo WEB_ROOT; ?>/index.php?c=front&amp;a=events" class="nav-link" style="text-decoration:none;color:#0b1220;padding:10px 20px;border-radius:999px;transition:all 0.22s cubic-bezier(.2,.9,.3,1);display:inline-block;font-size:0.95rem;font-weight:600;background:transparent;">Événements</a></li>
              <li><a href="<?php echo WEB_ROOT; ?>/index.php?c=front&amp;a=historique" class="nav-link" style="text-decoration:none;color:#0b1220;padding:10px 20px;border-radius:999px;transition:all 0.22s cubic-bezier(.2,.9,.3,1);display:inline-block;font-size:0.95rem;font-weight:600;background:transparent;">Historique</a></li>
              <li><a href="<?php echo WEB_ROOT; ?>/views/front/points.php" class="nav-link" style="text-decoration:none;color:#0b1220;padding:10px 20px;border-radius:999px;transition:all 0.22s cubic-bezier(.2,.9,.3,1);display:inline-block;font-size:0.95rem;font-weight:600;background:transparent;">Points &amp; Dons</a></li>
              <li><a href="<?php echo WEB_ROOT; ?>/index.php?c=front&amp;a=leaderboard" class="nav-link" style="text-decoration:none;color:#0b1220;padding:10px 20px;border-radius:999px;transition:all 0.22s cubic-bezier(.2,.9,.3,1);display:inline-block;font-size:0.95rem;font-weight:600;background:transparent;">Classement</a></li>
              <li><a href="#" id="contact-toggle" class="nav-link" style="text-decoration:none;color:#0b1220;padding:10px 20px;border-radius:999px;transition:all 0.22s cubic-bezier(.2,.9,.3,1);display:inline-block;font-size:0.95rem;font-weight:600;background:transparent;">Contact</a></li>
            </ul>
          </nav>
        </div>
      </div>
      <style>
        .events-nav-bar .nav-link:hover {
          transform: translateY(-4px);
          background: linear-gradient(180deg, rgba(79,70,229,0.08), rgba(79,70,229,0.04)) !important;
          box-shadow: 0 8px 24px rgba(79,70,229,0.15), 0 4px 12px rgba(15,23,42,0.08) !important;
          color: #4f46e5 !important;
        }
        .events-nav-bar .nav-link.active,
        .events-nav-bar .nav-link[aria-current='page'] {
          background: #4f46e5 !important;
          color: #fff !important;
          box-shadow: 0 10px 30px rgba(79,70,229,0.25), 0 4px 12px rgba(79,70,229,0.15) !important;
        }
        .events-nav-bar {
          backdrop-filter: blur(10px);
          -webkit-backdrop-filter: blur(10px);
        }
      </style>
    <?php endif; ?>

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
            <a href="<?php echo WEB_ROOT; ?>/views/front/points.php">Découvrir le programme</a>
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
                                                href = '<?php echo WEB_ROOT; ?>/index.php?c=front&a=events&cat=' + encodeURIComponent(cat);
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

            