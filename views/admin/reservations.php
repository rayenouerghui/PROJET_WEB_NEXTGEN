<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Réservations</title>
    <link rel="stylesheet" href="/projet/public/css/style.css">
    <style>
        .admin-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            padding: 30px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 20px;
            color: white;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }

        .admin-header h1 {
            margin: 0;
            font-size: 2.2rem;
            font-weight: 700;
        }

        .admin-nav {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .admin-nav a {
            padding: 15px 25px;
            background: var(--dark);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .admin-nav a:hover {
            background: #374151;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        .admin-nav a.active {
            background: var(--primary);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .table-card {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .table-header h2 {
            color: var(--dark);
            margin: 0;
            font-size: 1.4rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .reservations-count {
            background: var(--primary);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        .admin-table th {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 18px;
            text-align: left;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .admin-table td {
            padding: 16px;
            border-bottom: 1px solid #f1f5f9;
        }

        .admin-table tr:last-child td {
            border-bottom: none;
        }

        .admin-table tr:hover td {
            background: #f8fafc;
        }

        .reservation-event {
            font-weight: 600;
            color: var(--dark);
        }

        .reservation-name {
            font-weight: 500;
            color: var(--dark);
        }

        .reservation-email {
            color: var(--primary);
            font-size: 0.9rem;
        }

        .reservation-phone {
            color: var(--gray);
            font-size: 0.9rem;
        }

        .places-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            background: var(--success);
            color: white;
            border-radius: 50%;
            font-weight: 600;
            font-size: 0.8rem;
        }

        .reservation-date {
            color: var(--gray);
            font-size: 0.9rem;
        }

        .reservation-message {
            max-width: 200px;
            color: var(--gray);
            font-size: 0.9rem;
        }

        .btn-admin {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }

        .btn-secondary {
            background: var(--gray);
            color: white;
        }

        .btn-secondary:hover {
            background: #57534e;
            transform: translateY(-2px);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--gray);
        }

        .empty-state .icon {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        @media (max-width: 768px) {
            .admin-header {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }
            
            .admin-nav {
                justify-content: center;
            }
            
            .table-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .admin-table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>📊 Administration - Réservations</h1>
            <a href="?page=front&action=index" class="btn-admin btn-secondary">👀 Voir le site</a>
        </div>

        <div class="admin-nav">
            <a href="?page=admin&action=events" class="btn-secondary">📅 Événements</a>
            <a href="?page=admin&action=reservations" class="active">📊 Réservations</a>
        </div>

        <div class="table-card">
            <div class="table-header">
                <h2>📋 Liste des réservations</h2>
                <div class="reservations-count">
                    📊 <?= isset($reservations) ? count($reservations) : 0 ?> réservation(s)
                </div>
            </div>
            
            <?php if (!isset($reservations) || empty($reservations)): ?>
                <div class="empty-state">
                    <div class="icon">📭</div>
                    <h3>Aucune réservation</h3>
                    <p>Les réservations apparaîtront ici lorsqu'elles seront faites par les utilisateurs.</p>
                </div>
            <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Événement</th>
                            <th>Participant</th>
                            <th>Contact</th>
                            <th>Places</th>
                            <th>Date</th>
                            <th>Message</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservations as $reservation): 
                            $date = new DateTime($reservation['date_reservation']);
                            $formattedDate = $date->format('d/m/Y H:i');
                        ?>
                            <tr>
                                <td><strong>#<?= $reservation['id_reservation'] ?></strong></td>
                                <td class="reservation-event"><?= htmlspecialchars($reservation['evenement']) ?></td>
                                <td>
                                    <div class="reservation-name"><?= htmlspecialchars($reservation['nom_complet']) ?></div>
                                </td>
                                <td>
                                    <div class="reservation-email"><?= htmlspecialchars($reservation['email']) ?></div>
                                    <div class="reservation-phone"><?= htmlspecialchars($reservation['telephone'] ?? 'Non renseigné') ?></div>
                                </td>
                                <td>
                                    <div class="places-badge"><?= $reservation['nombre_places'] ?></div>
                                </td>
                                <td class="reservation-date"><?= $formattedDate ?></td>
                                <td class="reservation-message">
                                    <?= htmlspecialchars($reservation['message'] ?? 'Aucun message') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>