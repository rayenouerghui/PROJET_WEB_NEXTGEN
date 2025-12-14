<?php include __DIR__ . '/header.php'; ?>

<div class="leaderboard-page">
    <h1>🏆 Meilleurs participants</h1>
    <p>Voici le classement des participants ayant généré le plus de points convertis en dons :</p>
    <table class="leaderboard-table">
        <thead>
            <tr>
                <th>Rang</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Points convertis en dons</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $db = Database::getInstance();
        $rang = 1;
        $pointsField = null;
        try {
            // Try to rank by points_generes (new column)
            $result = $db->query("SELECT nom_complet, email, SUM(COALESCE(points_generes,0)) AS total_points FROM reservation GROUP BY email, nom_complet ORDER BY total_points DESC LIMIT 10");
            $pointsField = 'total_points';
        } catch (Exception $e) {
            // Column doesn't exist or other SQL error: fallback to ranking by number of reservations
            $result = $db->query("SELECT nom_complet, email, COUNT(*) AS total_reservations FROM reservation GROUP BY email, nom_complet ORDER BY total_reservations DESC LIMIT 10");
            $pointsField = 'total_reservations';
        }

        while ($row = $result->fetch()) : ?>
            <tr>
                <td><?php echo $rang++; ?></td>
                <td><?php echo htmlspecialchars($row['nom_complet']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><strong><?php echo intval($row[$pointsField]); ?></strong></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<style>
/* Ensure readable text on the light leaderboard card even when site uses a dark theme */
.leaderboard-page { max-width: 880px; margin: 40px auto; border-radius: 12px; padding: 32px; color: #0b1220; /* keep text color dark as requested */
    /* pink-violet glass surface to match the site background */
    background: linear-gradient(135deg, rgba(184,90,209,0.95) 0%, rgba(214,51,132,0.95) 50%, rgba(124,58,237,0.95) 100%);
    box-shadow: 0 8px 30px rgba(0,0,0,0.35);
}
.leaderboard-page h1 { text-align: center; color: #0b1220; text-shadow: none; }
.leaderboard-table { width: 100%; border-collapse: collapse; margin-top: 24px; background: rgba(255,255,255,0.06); border-radius:8px; }
.leaderboard-table th, .leaderboard-table td { padding: 12px 8px; border-bottom: 1px solid rgba(255,255,255,0.08); text-align: center; color: #0b1220; }
.leaderboard-table th { background: rgba(255,255,255,0.12); color: #111; }
.leaderboard-table td a { color: inherit; }

/* Ensure participant names remain the same (do not color them pink) */
.leaderboard-table tbody tr td:nth-child(2) { color: #0b1220 !important; font-weight:600; }
</style>

</body>
</html>
