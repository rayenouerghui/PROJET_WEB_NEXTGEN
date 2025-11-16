<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Événements - <?= htmlspecialchars($category['nom_categorie']) ?> | NEXTGEN</title>
    <link rel="stylesheet" href="/projet/public/css/style.css">
    <style>
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            padding: 0;
            border-radius: 20px;
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
            animation: modalSlideIn 0.4s ease-out;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 25px;
            border-radius: 20px 20px 0 0;
            position: relative;
        }

        .close-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            font-size: 1.5rem;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .close-btn:hover {
            background: rgba(255,255,255,0.3);
            transform: rotate(90deg);
        }

        .modal-body {
            padding: 30px;
        }

        .reservation-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
        }

        .reservation-steps::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 10%;
            right: 10%;
            height: 3px;
            background: var(--gray-light);
            z-index: 1;
        }

        .step {
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .step-number {
            width: 40px;
            height: 40px;
            background: var(--gray-light);
            color: var(--gray);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .step.active .step-number {
            background: var(--primary);
            color: white;
            transform: scale(1.1);
        }

        .step-label {
            font-size: 0.8rem;
            color: var(--gray);
        }

        .step.active .step-label {
            color: var(--primary);
            font-weight: 600;
        }

        .form-section {
            display: none;
        }

        .form-section.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateX(20px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .form-navigation {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            gap: 15px;
        }

        .btn-full {
            flex: 1;
        }

        .event-preview {
            background: var(--light);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 25px;
            text-align: center;
        }

        .event-preview h4 {
            margin-bottom: 10px;
            color: var(--dark);
        }

        .confirmation-message {
            text-align: center;
            padding: 40px 20px;
        }

        .confirmation-icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="?page=front&action=index" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; margin-bottom: 20px; text-decoration: none;">
            ← Retour aux catégories
        </a>
        
        <div class="header">
            <h1><?= htmlspecialchars($category['nom_categorie']) ?></h1>
            <p><?= htmlspecialchars($category['description_categorie']) ?></p>
        </div>

        <div class="events-grid">
            <?php if (empty($events)): ?>
                <div style="text-align: center; grid-column: 1 / -1;">
                    <p>Aucun événement dans cette catégorie pour le moment.</p>
                </div>
            <?php else: ?>
                <?php foreach ($events as $event): 
                    $date = new DateTime($event['date_evenement']);
                    $formattedDate = $date->format('d/m/Y à H:i');
                ?>
                    <div class="event-card">
                        <div class="event-category"><?= htmlspecialchars($event['nom_categorie']) ?></div>
                        <h2 class="event-title"><?= htmlspecialchars($event['titre']) ?></h2>
                        
                        <div class="event-date">📅 <?= $formattedDate ?></div>
                        <div class="event-lieu">📍 <?= htmlspecialchars($event['lieu']) ?></div>
                        
                        <p style="color: #555; line-height: 1.6;"><?= nl2br(htmlspecialchars($event['description'])) ?></p>
                        
                        <button class="reserve-btn" onclick="openReservationModal(<?= $event['id_evenement'] ?>, '<?= htmlspecialchars($event['titre']) ?>', '<?= $formattedDate ?>', '<?= htmlspecialchars($event['lieu']) ?>')">
                            🎟️ Réserver maintenant
                        </button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- MODAL DE RÉSERVATION -->
    <div id="reservationModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 style="margin: 0; color: white;">Réserver votre place</h2>
                <button class="close-btn" onclick="closeReservationModal()">×</button>
            </div>
            
            <div class="modal-body">
                <!-- ÉTAPES DE RÉSERVATION -->
                <div class="reservation-steps">
                    <div class="step active" id="step1">
                        <div class="step-number">1</div>
                        <div class="step-label">Événement</div>
                    </div>
                    <div class="step" id="step2">
                        <div class="step-number">2</div>
                        <div class="step-label">Informations</div>
                    </div>
                    <div class="step" id="step3">
                        <div class="step-number">3</div>
                        <div class="step-label">Confirmation</div>
                    </div>
                </div>

                <!-- ÉTAPE 1: PRÉVISUALISATION ÉVÉNEMENT -->
                <div class="form-section active" id="section1">
                    <div class="event-preview">
                        <h4 id="modalEventTitle"></h4>
                        <p><strong>📅 Date:</strong> <span id="modalEventDate"></span></p>
                        <p><strong>📍 Lieu:</strong> <span id="modalEventLieu"></span></p>
                    </div>
                    <p style="text-align: center; margin-bottom: 25px; color: var(--gray);">
                        Vérifiez les détails de l'événement avant de continuer.
                    </p>
                    <div class="form-navigation">
                        <button type="button" class="btn btn-secondary btn-full" onclick="closeReservationModal()">Annuler</button>
                        <button type="button" class="btn btn-primary btn-full" onclick="showSection(2)">Continuer</button>
                    </div>
                </div>

                <!-- ÉTAPE 2: FORMULAIRE -->
                <div class="form-section" id="section2">
                    <form id="reservationForm" method="post" action="?page=front&action=reservation">
                        <input type="hidden" name="id_evenement" id="formEventId">
                        
                        <div class="form-group">
                            <label for="nom_complet">Nom complet *</label>
                            <input type="text" id="nom_complet" name="nom_complet" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="telephone">Téléphone</label>
                            <input type="text" id="telephone" name="telephone">
                        </div>
                        
                        <div class="form-group">
                            <label for="nombre_places">Nombre de places *</label>
                            <select id="nombre_places" name="nombre_places" required>
                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                    <option value="<?= $i ?>"><?= $i ?> place<?= $i > 1 ? 's' : '' ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Message (optionnel)</label>
                            <textarea id="message" name="message" rows="3" placeholder="Un message pour les organisateurs..."></textarea>
                        </div>

                        <div class="form-navigation">
                            <button type="button" class="btn btn-secondary btn-full" onclick="showSection(1)">Retour</button>
                            <button type="submit" class="btn btn-primary btn-full">Confirmer la réservation</button>
                        </div>
                    </form>
                </div>

                <!-- ÉTAPE 3: CONFIRMATION -->
                <div class="form-section" id="section3">
                    <div class="confirmation-message">
                        <div class="confirmation-icon">✅</div>
                        <h3>Réservation envoyée !</h3>
                        <p>Merci pour votre réservation. Vous recevrez un email de confirmation.</p>
                        <button type="button" class="btn btn-primary btn-full" onclick="closeReservationModal()">Fermer</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentEventId = null;

        function openReservationModal(eventId, title, date, lieu) {
            currentEventId = eventId;
            
            // Mettre à jour les informations de l'événement
            document.getElementById('modalEventTitle').textContent = title;
            document.getElementById('modalEventDate').textContent = date;
            document.getElementById('modalEventLieu').textContent = lieu;
            document.getElementById('formEventId').value = eventId;
            
            // Mettre à jour l'action du formulaire avec l'event_id
            document.getElementById('reservationForm').action = '?page=front&action=reservation&event_id=' + eventId;
            
            // Réinitialiser le formulaire
            document.getElementById('reservationForm').reset();
            
            // Réinitialiser les étapes
            showSection(1);
            
            // Afficher le modal
            document.getElementById('reservationModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeReservationModal() {
            document.getElementById('reservationModal').style.display = 'none';
            document.body.style.overflow = 'auto';
            showSection(1); // Retour à l'étape 1
        }

        function showSection(sectionNumber) {
            // Cacher toutes les sections
            document.querySelectorAll('.form-section').forEach(section => {
                section.classList.remove('active');
            });
            
            // Désactiver toutes les étapes
            document.querySelectorAll('.step').forEach(step => {
                step.classList.remove('active');
            });
            
            // Afficher la section demandée
            document.getElementById('section' + sectionNumber).classList.add('active');
            
            // Activer les étapes jusqu'à celle demandée
            for (let i = 1; i <= sectionNumber; i++) {
                document.getElementById('step' + i).classList.add('active');
            }
        }

        // Fermer le modal en cliquant à l'extérieur
        document.getElementById('reservationModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeReservationModal();
            }
        });

        // Fermer avec la touche Echap
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeReservationModal();
            }
        });

        // SUPPRIMER le blocage de soumission du formulaire
        // NE PAS AJOUTER de addEventListener pour 'submit' qui fait e.preventDefault()
        
        // Validation simple avant soumission (optionnel)
        document.getElementById('reservationForm').addEventListener('submit', function(e) {
            // Validation simple - laisse le formulaire s'envoyer normalement
            const nom = document.getElementById('nom_complet').value;
            const email = document.getElementById('email').value;
            
            if (nom.length < 2) {
                e.preventDefault();
                alert('Le nom doit contenir au moins 2 caractères');
                return;
            }
            
            if (!email.match(/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/)) {
                e.preventDefault();
                alert('Email invalide');
                return;
            }
            
            // Si validation OK, le formulaire s'envoie normalement
        });
    </script>
</body>
</html>