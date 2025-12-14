<?php include __DIR__ . '/header.php'; ?>


<div class="homepage">
    <!-- Hero Section (uses front.css .hero) -->
    <section class="hero">
        <div class="container">
            <div class="hero-card">
                <h1><span class="animated-gradient">Jouer. Participer. Changer le monde.</span></h1>
                <p class="hero-sub">Participez à des événements gaming solidaires et transformez vos points en dons réels pour soutenir des associations humaines.</p>
                <div class="hero-buttons">
                    <?php if (!defined('WEB_ROOT')) require_once __DIR__ . '/../../config/paths.php'; ?>
                    <a href="<?php echo WEB_ROOT; ?>/index.php?c=front&amp;a=events" class="btn btn-primary">Découvrir les événements</a>
                    <a href="<?php echo WEB_ROOT; ?>/index.php?c=front&amp;a=categories" class="btn btn-secondary">Voir les catégories</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Navigation handler moved to views/front/header.php to centralize front scripts -->

    <!-- Impact / Stats (front.css .impact + .stats) -->
    <section class="impact">
        <div class="container">
            <h2>Notre Impact</h2>
            <?php
                // Use stats prepared by FrontC::index
                $events_count = isset($stats['total_evenements']) ? $stats['total_evenements'] : 0;
                $categories_count = isset($stats['total_categories']) ? $stats['total_categories'] : 0;
                $donations = isset($stats['total_donations']) ? $stats['total_donations'] : 0; // may be 0 if not provided
            ?>

            <div class="stats">
                    <div class="stat">
                        <div class="num"><?php echo intval($events_count); ?></div>
                        <div class="label">Événements disponibles</div>
                    </div>
                    <div class="stat">
                        <div class="num"><?php echo intval($categories_count); ?></div>
                        <div class="label">Catégories</div>
                    </div>
                    <div class="stat">
                        <div class="num"><?php echo intval($donations); ?> TND</div>
                        <div class="label">Dons collectés</div>
                    </div>
            </div>
        </div>
    </section>

        <!-- Événements à venir: section removed as requested -->

    <!-- Categories removed from homepage as requested -->

    <!-- Section 2 : Comment ça marche ? -->
    <section class="features-section">
        <div class="container">
            <h2 class="section-title">3 étapes simples pour faire la différence</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">🔸</div>
                    <h3>1. Choisissez un événement</h3>
                    <p>Des tournois, défis et animations solidaires.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🔸</div>
                    <h3>2. Jouez et gagnez des points</h3>
                    <p>Vos performances vous permettent de collecter plus de points.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🔸</div>
                    <h3>3. Transformez vos points en dons</h3>
                    <p>Chaque point compte et soutient une association partenaire.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 3 : Nos causes soutenues -->
    <section class="features-section">
        <div class="container">
            <h2 class="section-title">Des événements au service de l’humanité</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">👶</div>
                    <h3>Protection des enfants</h3>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">❤️</div>
                    <h3>Aide médicale</h3>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🌍</div>
                    <h3>Environnement</h3>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🛡️</div>
                    <h3>Soutien aux personnes vulnérables</h3>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 4 : Prochaines dates importantes -->
    <!-- Section 'Événements à venir' removed to keep categories page clean. -->

    <!-- Section 5 : Rejoignez le mouvement -->
    <section class="features-section">
        <div class="container">
            <h2 class="section-title">Devenez acteur du changement</h2>
            <p>Chaque participation compte.<br>En rejoignant nos événements, vous aidez des associations à réaliser leurs projets.</p>
            <div class="hero-buttons">
                <a href="<?php echo WEB_ROOT; ?>/index.php?c=front&amp;a=events" class="btn btn-primary btn-large">🟩 Je participe maintenant</a>
            </div>
        </div>
    </section>
</div>

</body>
</html>
