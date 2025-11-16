<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalogue - NextGen</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . "/_partials/header.php"; ?>

    <!-- Catalog Section -->
    <section class="catalog-section">
        <div class="container">
            <h1 class="page-title">Catalogue de Jeux</h1>
            
            <!-- Search and Sort -->
            <div class="catalog-filters">
                <div class="filter-group" style="flex: 2;">
                    <input type="text" id="searchInput" placeholder="Rechercher un jeu...">
                </div>
                <div class="filter-group">
                    <label>Trier par:</label>
                    <select id="sortSelect">
                        <option value="name">Nom (A-Z)</option>
                        <option value="price-asc">Prix (Croissant)</option>
                        <option value="price-desc">Prix (Décroissant)</option>
                        <option value="category">Catégorie</option>
                    </select>
                </div>
            </div>

            <!-- Games Grid -->
            <div class="games-grid" id="gamesGrid">
                <!-- Games will be loaded dynamically -->
            </div>

            <!-- Pagination -->
            <div class="pagination" id="pagination">
                <!-- Pagination will be generated dynamically -->
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>NextGen</h3>
                    <p>Plateforme de vente de jeux vidéo à vocation solidaire</p>
                </div>
                <div class="footer-section">
                    <h4>Liens Utiles</h4>
                    <ul>
                        <li><a href="catalog.php">Catalogue</a></li>
                        <li><a href="about.php">À Propos</a></li>
                        <li><a href="donations.php">Nos Dons</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Support</h4>
                    <ul>
                        <li><a href="contact.php">Contact</a></li>
                        <li><a href="returns.php">Retours</a></li>
                        <li><a href="faq.php">FAQ</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 NextGen. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script>
        let allGames = [];
        let currentUserId = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>;
        
        // Load games
        function loadGames() {
            fetch('../../api/games.php?action=getAll')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        allGames = data.games;
                        displayGames(allGames);
                    }
                })
                .catch(error => {
                    console.error('Error loading games:', error);
                    document.getElementById('gamesGrid').innerHTML = '<p>Aucun jeu disponible pour le moment.</p>';
                });
        }
        
        function displayGames(games) {
            const grid = document.getElementById('gamesGrid');
            if (games.length === 0) {
                grid.innerHTML = '<p style="text-align: center; padding: 40px; color: var(--text-light);">Aucun jeu disponible pour le moment.</p>';
                return;
            }
            
            grid.innerHTML = games.map(game => {
                const price = game.est_gratuit ? 'Gratuit' : game.prix + ' TND';
                // If src_img exists, it's a filename in public/images, otherwise use default
                const imageSrc = game.src_img ? `../../public/images/${game.src_img}` : '../../public/images/default-game.jpg';
                const isPurchased = game.isPurchased || false;
                
                return `
                    <div class="game-card" data-game-id="${game.id_jeu}">
                        <img src="${imageSrc}" alt="${game.titre}" onerror="this.src='../../public/images/default-game.jpg'">
                        <div class="game-card-content">
                            <h3>${game.titre}</h3>
                            <p class="game-category">${game.nom_categorie || 'Non catégorisé'}</p>
                            <p class="game-price">${price}</p>
                            ${isPurchased ? 
                                '<a href="game-details.php?id=' + game.id_jeu + '" class="btn btn-primary">Voir les détails</a>' :
                                '<button class="btn btn-primary purchase-btn" data-game-id="' + game.id_jeu + '">Acheter</button>'
                            }
                        </div>
                    </div>
                `;
            }).join('');
            
            // Add purchase event listeners
            document.querySelectorAll('.purchase-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const gameId = this.getAttribute('data-game-id');
                    purchaseGame(gameId);
                });
            });
        }
        
        function purchaseGame(gameId) {
            if (!currentUserId) {
                window.location.href = 'login.php';
                return;
            }
            
            const formData = new FormData();
            formData.append('gameId', gameId);
            
            fetch('../../api/games.php?action=purchase', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove purchase button
                    const gameCard = document.querySelector(`[data-game-id="${gameId}"]`);
                    const btn = gameCard.querySelector('.purchase-btn');
                    if (btn) {
                        btn.remove();
                        const detailsLink = document.createElement('a');
                        detailsLink.href = `game-details.php?id=${gameId}`;
                        detailsLink.className = 'btn btn-primary';
                        detailsLink.textContent = 'Voir les détails';
                        gameCard.querySelector('.game-card-content').appendChild(detailsLink);
                    }
                    alert('Achat réussi! Votre crédit restant: ' + data.new_credit + ' TND');
                } else {
                    alert('Erreur: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Une erreur est survenue lors de l\'achat.');
            });
        }
        
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const filtered = allGames.filter(game => 
                game.titre.toLowerCase().includes(searchTerm) ||
                (game.nom_categorie && game.nom_categorie.toLowerCase().includes(searchTerm))
            );
            displayGames(filtered);
        });
        
        // Sort functionality
        document.getElementById('sortSelect').addEventListener('change', function() {
            const sortBy = this.value;
            let sorted = [...allGames];
            
            switch(sortBy) {
                case 'name':
                    sorted.sort((a, b) => a.titre.localeCompare(b.titre));
                    break;
                case 'price-asc':
                    sorted.sort((a, b) => (a.prix || 0) - (b.prix || 0));
                    break;
                case 'price-desc':
                    sorted.sort((a, b) => (b.prix || 0) - (a.prix || 0));
                    break;
                case 'category':
                    sorted.sort((a, b) => (a.nom_categorie || '').localeCompare(b.nom_categorie || ''));
                    break;
            }
            
            displayGames(sorted);
        });
        
        // Load games on page load
        loadGames();
    </script>
</body>
</html>

