<?php
require_once __DIR__ . "/../Controllers/frontoffice/AuthController.php";
$authController = new AuthController();
$authController->requireAdmin();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - NextGen</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-container">
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <h2>NextGen Admin</h2>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="dashboard.php" class="active">📊 Tableau de Bord</a></li>
                    <li><a href="games.php">🎮 Gestion des Jeux</a></li>
                    <li><a href="users.php">👥 Gestion des Utilisateurs</a></li>
                    <li><a href="orders.php">🛒 Gestion des Commandes</a></li>
                    <li><a href="settings-admin.php">⚙️ Paramètres</a></li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <a href="index.php" class="view-site">Voir le Site</a>
                <a href="#" class="logout" id="logoutBtn">Déconnexion</a>
            </div>
        </aside>

        <main class="admin-main">
            <div class="admin-header">
                <h1>Gestion des Jeux</h1>
                <button class="btn btn-primary" id="addGameBtn">+ Ajouter un Jeu</button>
            </div>

            <div class="admin-content">
                <div class="games-table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Titre</th>
                                <th>Prix</th>
                                <th>Catégorie</th>
                                <th>Gratuit</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="gamesTableBody">
                            <!-- Games will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Add/Edit Game Modal -->
    <div id="gameModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close" id="closeModal">&times;</span>
            <h2 id="modalTitle">Ajouter un Jeu</h2>
            <form id="gameForm">
                <input type="hidden" id="gameId" name="gameId">
                <div class="form-group">
                    <label>Titre *</label>
                    <input type="text" id="titre" name="titre" required>
                </div>
                <div class="form-group">
                    <label>Prix (TND)</label>
                    <input type="number" id="prix" name="prix" step="0.01" min="0" value="0">
                </div>
                <div class="form-group">
                    <label>Nom de l'image</label>
                    <input type="text" id="src_img" name="src_img" placeholder="game1.jpg (dans public/images)">
                    <small style="color: var(--text-light); font-size: 12px;">Entrez uniquement le nom du fichier (ex: game1.jpg). Le fichier doit être dans public/images/</small>
                </div>
                <div class="form-group">
                    <label>Lien Externe</label>
                    <input type="text" id="lien_externe" name="lien_externe" placeholder="https://...">
                </div>
                <div class="form-group">
                    <label>Catégorie</label>
                    <select id="id_categorie" name="id_categorie">
                        <option value="">Aucune</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" id="est_gratuit" name="est_gratuit">
                        Jeu gratuit
                    </label>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" id="cancelBtn">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let categories = [];
        let currentEditId = null;

        // Load categories
        function loadCategories() {
            fetch('../../api/admin/games.php?action=getCategories')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        categories = data.categories;
                        const select = document.getElementById('id_categorie');
                        select.innerHTML = '<option value="">Aucune</option>';
                        categories.forEach(cat => {
                            const option = document.createElement('option');
                            option.value = cat.id_categorie;
                            option.textContent = cat.nom_categorie;
                            select.appendChild(option);
                        });
                    }
                });
        }

        // Load games
        function loadGames() {
            fetch('../../api/admin/games.php?action=getAll')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayGames(data.games);
                    }
                });
        }

        function displayGames(games) {
            const tbody = document.getElementById('gamesTableBody');
            if (games.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align: center;">Aucun jeu disponible</td></tr>';
                return;
            }

            tbody.innerHTML = games.map(game => `
                <tr>
                    <td>${game.id_jeu}</td>
                    <td>${game.titre}</td>
                    <td>${game.est_gratuit ? 'Gratuit' : game.prix + ' TND'}</td>
                    <td>${game.nom_categorie || '-'}</td>
                    <td>${game.est_gratuit ? 'Oui' : 'Non'}</td>
                    <td>
                        <button class="btn-edit" onclick="editGame(${game.id_jeu})">Modifier</button>
                        <button class="btn-delete" onclick="deleteGame(${game.id_jeu})">Supprimer</button>
                    </td>
                </tr>
            `).join('');
        }

        function openModal(isEdit = false, gameId = null) {
            document.getElementById('gameModal').style.display = 'block';
            document.getElementById('modalTitle').textContent = isEdit ? 'Modifier un Jeu' : 'Ajouter un Jeu';
            document.getElementById('gameForm').reset();
            currentEditId = gameId;
        }

        function closeModal() {
            document.getElementById('gameModal').style.display = 'none';
            currentEditId = null;
        }

        function editGame(id) {
                fetch(`../../api/admin/games.php?action=getById&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const game = data.game;
                        document.getElementById('gameId').value = game.id_jeu;
                        document.getElementById('titre').value = game.titre;
                        document.getElementById('prix').value = game.prix || 0;
                        document.getElementById('src_img').value = game.src_img || '';
                        document.getElementById('lien_externe').value = game.lien_externe || '';
                        document.getElementById('id_categorie').value = game.id_categorie || '';
                        document.getElementById('est_gratuit').checked = game.est_gratuit == 1;
                        openModal(true, id);
                    }
                });
        }

        function deleteGame(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce jeu ?')) {
                fetch(`../../api/admin/games.php?action=delete&id=${id}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            loadGames();
                        } else {
                            alert('Erreur: ' + data.message);
                        }
                    });
            }
        }

        // Event listeners
        document.getElementById('addGameBtn').addEventListener('click', () => openModal(false));
        document.getElementById('closeModal').addEventListener('click', closeModal);
        document.getElementById('cancelBtn').addEventListener('click', closeModal);

        document.getElementById('gameForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const action = currentEditId ? 'update' : 'create';
            const url = currentEditId ? 
                `../../api/admin/games.php?action=update&id=${currentEditId}` :
                '../../api/admin/games.php?action=create';
            
            fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeModal();
                    loadGames();
                } else {
                    alert('Erreur: ' + data.message);
                }
            });
        });

        document.getElementById('logoutBtn').addEventListener('click', function(e) {
            e.preventDefault();
            fetch('../../api/auth.php?action=logout')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = 'index.php';
                    }
                });
        });

        // Load on page load
        loadCategories();
        loadGames();
    </script>
</body>
</html>

