<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Gestion Événements</title>
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

        .form-card {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.08);
            margin-bottom: 30px;
            border: 1px solid rgba(255,255,255,0.2);
        }

        .form-card h2 {
            color: var(--dark);
            margin-bottom: 30px;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: var(--dark);
        }

        .form-control {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8fafc;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 120px;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            grid-column: 1 / -1;
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

        .events-count {
            background: var(--primary);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
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

        .event-title {
            font-weight: 600;
            color: var(--dark);
        }

        .event-category {
            display: inline-block;
            background: var(--light);
            color: var(--gray);
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .admin-actions {
            display: flex;
            gap: 8px;
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
            border: none;
            cursor: pointer;
        }

        .btn-edit {
            background: var(--warning);
            color: white;
        }

        .btn-edit:hover {
            background: #e58a08;
            transform: translateY(-2px);
        }

        .btn-delete {
            background: var(--danger);
            color: white;
        }

        .btn-delete:hover {
            background: #dc2626;
            transform: translateY(-2px);
        }

        .btn-add {
            background: var(--success);
            color: white;
            padding: 12px 25px;
        }

        .btn-add:hover {
            background: #0da271;
            transform: translateY(-2px);
        }

        .btn-cancel {
            background: var(--gray);
            color: white;
        }

        .btn-cancel:hover {
            background: #57534e;
            transform: translateY(-2px);
        }

        .alert {
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            border-left: 5px solid;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border-left-color: var(--success);
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border-left-color: var(--danger);
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

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .category-item {
            background: white;
            padding: 20px;
            border-radius: 12px;
            border-left: 4px solid var(--primary);
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .category-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 10px;
        }

        .category-name {
            font-weight: 600;
            color: var(--dark);
            font-size: 1.1rem;
            margin: 0;
        }

        .category-id {
            background: var(--primary);
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .category-desc {
            color: var(--gray);
            margin: 0;
            font-size: 0.9rem;
            line-height: 1.4;
        }

        @media (max-width: 1024px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
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
            
            .admin-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>🎯 Administration - Événements</h1>
            <a href="?page=front&action=index" class="btn-admin btn-cancel">👀 Voir le site</a>
        </div>

        <div class="admin-nav">
            <a href="?page=admin&action=events" class="active">📅 Événements</a>
            <a href="?page=admin&action=reservations">📊 Réservations</a>
        </div>

        <?php if (isset($success) && $success): ?>
            <div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if (isset($errors) && !empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $error): ?>
                    <div>❌ <?= htmlspecialchars($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- FORMULAIRE AJOUT/MODIFICATION ÉVÉNEMENT -->
        <div class="form-card">
            <h2><?= isset($eventToEdit) && $eventToEdit ? '✏️ Modifier un événement' : '➕ Ajouter un nouvel événement' ?></h2>
            <form method="post">
                <?php if (isset($eventToEdit) && $eventToEdit): ?>
                    <input type="hidden" name="id_evenement" value="<?= $eventToEdit['id_evenement'] ?>">
                <?php endif; ?>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>Titre de l'événement</label>
                        <input type="text" name="titre" class="form-control" 
                               value="<?= isset($eventToEdit) && $eventToEdit ? htmlspecialchars($eventToEdit['titre']) : '' ?>" 
                               placeholder="Entrez le titre de l'événement" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Catégorie</label>
                        <select name="id_categorie" class="form-control" required>
                            <option value="">-- Sélectionnez une catégorie --</option>
                            <?php if (isset($categories)): ?>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id_categorie'] ?>" 
                                        <?= (isset($eventToEdit) && $eventToEdit && $eventToEdit['id_categorie'] == $cat['id_categorie']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['nom_categorie']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Date et heure</label>
                        <input type="datetime-local" name="date_evenement" class="form-control"
                               value="<?= isset($eventToEdit) && $eventToEdit ? substr($eventToEdit['date_evenement'], 0, 16) : '' ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Lieu</label>
                        <input type="text" name="lieu" class="form-control" 
                               value="<?= isset($eventToEdit) && $eventToEdit ? htmlspecialchars($eventToEdit['lieu']) : '' ?>" 
                               placeholder="Lieu de l'événement" required>
                    </div>
                    
                    <div class="form-group full-width">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="4"
                                  placeholder="Description détaillée de l'événement"><?= isset($eventToEdit) && $eventToEdit ? htmlspecialchars($eventToEdit['description']) : '' ?></textarea>
                    </div>
                </div>
                
                <div class="form-actions">
                    <?php if (isset($eventToEdit) && $eventToEdit): ?>
                        <button type="submit" name="update_event" class="btn-admin btn-edit">💾 Enregistrer les modifications</button>
                        <a href="?page=admin&action=events" class="btn-admin btn-cancel">❌ Annuler</a>
                    <?php else: ?>
                        <button type="submit" name="add_event" class="btn-admin btn-add">✅ Créer l'événement</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- FORMULAIRE AJOUT CATÉGORIE -->
        <div class="form-card">
            <h2>🏷️ Gestion des Catégories</h2>
            
            <div style="background: #f8fafc; padding: 25px; border-radius: 15px; margin-bottom: 25px;">
                <h3 style="margin-top: 0; color: var(--dark);">
                    ➕ Ajouter une nouvelle catégorie
                </h3>
                <form method="post">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 600;">Nom de la catégorie *</label>
                            <input type="text" name="nom_categorie" 
                                   style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px;"
                                   placeholder="Ex: Concerts" required>
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 600;">Description</label>
                            <input type="text" name="description_categorie" 
                                   style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px;"
                                   placeholder="Description...">
                        </div>
                    </div>
                    <button type="submit" name="add_categorie" 
                            style="background: #27ae60; color: white; padding: 12px 24px; border: none; border-radius: 8px; cursor: pointer;">
                        ✅ Créer la catégorie
                    </button>
                </form>
            </div>

            <!-- Liste des catégories -->
            <div>
                <h3 style="color: var(--dark); margin-bottom: 20px;">
                    📋 Catégories existantes 
                    <?php if (isset($categories)): ?>
                        <span style="background: var(--primary); color: white; padding: 4px 12px; border-radius: 15px; font-size: 0.8rem;">
                            <?= count($categories) ?>
                        </span>
                    <?php endif; ?>
                </h3>
                
                <?php if (!isset($categories) || empty($categories)): ?>
                    <p style="text-align: center; color: #666; padding: 20px;">
                        Aucune catégorie créée.
                    </p>
                <?php else: ?>
                    <div class="categories-grid">
                        <?php foreach ($categories as $cat): ?>
                            <div class="category-item">
                                <div class="category-header">
                                    <h4 class="category-name"><?= htmlspecialchars($cat['nom_categorie']) ?></h4>
                                    <span class="category-id">ID: <?= $cat['id_categorie'] ?></span>
                                </div>
                                <p class="category-desc">
                                    <?= htmlspecialchars($cat['description_categorie'] ?: 'Aucune description') ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- LISTE DES ÉVÉNEMENTS -->
        <div class="table-card">
            <div class="table-header">
                <h2>📋 Événements créés 
                    <?php if (isset($events)): ?>
                        <span class="events-count"><?= count($events) ?></span>
                    <?php endif; ?>
                </h2>
            </div>
            
            <?php if (!isset($events) || empty($events)): ?>
                <div class="empty-state">
                    <div class="icon">📅</div>
                    <h3>Aucun événement créé</h3>
                    <p>Commencez par ajouter votre premier événement.</p>
                </div>
            <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Titre</th>
                            <th>Date</th>
                            <th>Lieu</th>
                            <th>Catégorie</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $event): 
                            $date = new DateTime($event['date_evenement']);
                            $formattedDate = $date->format('d/m/Y H:i');
                        ?>
                            <tr>
                                <td><strong>#<?= $event['id_evenement'] ?></strong></td>
                                <td class="event-title"><?= htmlspecialchars($event['titre']) ?></td>
                                <td><?= $formattedDate ?></td>
                                <td><?= htmlspecialchars($event['lieu']) ?></td>
                                <td><span class="event-category"><?= htmlspecialchars($event['nom_categorie'] ?? 'Non catégorisé') ?></span></td>
                                <td>
                                    <div class="admin-actions">
                                        <a href="?page=admin&action=events&edit=<?= $event['id_evenement'] ?>" class="btn-admin btn-edit">✏️ Modifier</a>
                                        <a href="?page=admin&action=events&delete=<?= $event['id_evenement'] ?>" class="btn-admin btn-delete" 
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet événement ?')">🗑️ Supprimer</a>
                                    </div>
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