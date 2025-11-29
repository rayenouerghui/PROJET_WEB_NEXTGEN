<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes livraisons - NextGen</title>
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="/PROJET_WEB_NEXTGEN-main/public/manifest.json">
    <meta name="theme-color" content="#667eea">
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Orbitron:wght@700;900&family=Rajdhani:wght@600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Styles -->
    <link rel="stylesheet" href="/PROJET_WEB_NEXTGEN-main/public/css/friend_styles.css">
    <link rel="stylesheet" href="/PROJET_WEB_NEXTGEN-main/public/vendor/leaflet/leaflet.css">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body { 
            background: url('/PROJET_WEB_NEXTGEN-main/public/images/bg-jeux.gif') no-repeat center center fixed !important; 
            background-size: cover !important; 
            margin: 0; 
            color: white; 
            min-height: 100vh; 
            font-family: 'Inter', sans-serif;
        }
        body::before { 
            content: ''; 
            position: fixed; 
            inset: 0; 
            background: rgba(0,0,0,0.7); 
            backdrop-filter: blur(5px); 
            z-index: -1; 
        }
        
        main { 
            position: relative; 
            z-index: 1; 
            max-width: 1200px; 
            margin: 2rem auto; 
            padding: 0 1.5rem; 
        }

        h1, h2, h3 { 
            font-family: 'Rajdhani', sans-serif; 
            font-weight: 800; 
            text-shadow: 0 0 20px rgba(255,255,255,0.3); 
        }

        .card { 
            background: linear-gradient(135deg, rgba(56,28,135,0.95), rgba(59,7,100,0.9)); 
            border-radius: 1.5rem; 
            padding: 2rem; 
            margin-bottom: 2rem; 
            box-shadow: 0 15px 40px rgba(0,0,0,0.6); 
            border: 1px solid rgba(139,92,246,0.6); 
            color: white;
        }

        .btn { 
            padding: 0.8rem 2rem; 
            border-radius: 50px; 
            font-weight: 700; 
            text-transform: uppercase; 
            border: none; 
            cursor: pointer; 
            transition: all 0.3s; 
            font-family: 'Rajdhani', sans-serif;
            letter-spacing: 1px;
        }
        
        .btn.primary { 
            background: linear-gradient(45deg, #8b5cf6, #ec4899); 
            box-shadow: 0 0 20px rgba(139,92,246,0.5); 
            color: white;
        }
        
        .btn.primary:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 0 40px rgba(236,72,153,0.8); 
        }

        .btn.secondary {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            color: white;
        }
        .btn.secondary:hover {
            background: rgba(255,255,255,0.2);
        }

        .btn.danger {
            background: linear-gradient(45deg, #ef4444, #b91c1c);
            color: white;
        }

        /* Form Elements */
        input, select, textarea {
            background: rgba(0,0,0,0.3) !important;
            border: 1px solid rgba(139,92,246,0.4) !important;
            color: white !important;
            border-radius: 12px !important;
            padding: 12px !important;
            width: 100%;
            box-sizing: border-box;
            margin-bottom: 1rem;
        }
        
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #ec4899 !important;
            box-shadow: 0 0 15px rgba(236,72,153,0.3);
        }

        /* Grid Layouts */
        .commandes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .commande-card {
            background: rgba(0,0,0,0.3);
            border-radius: 1rem;
            padding: 1.5rem;
            border: 1px solid rgba(255,255,255,0.1);
            transition: transform 0.3s;
        }
        .commande-card:hover {
            transform: translateY(-5px);
            border-color: #8b5cf6;
        }

        .livraison-card {
            background: rgba(0,0,0,0.3);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid #8b5cf6;
        }

        .badge {
            padding: 0.4rem 1rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-preparée { background: #f59e0b; color: black; }
        .badge-en_route { background: #10b981; color: white; box-shadow: 0 0 10px #10b981; }
        .badge-livrée { background: #ec4899; color: white; }
        .badge-annulée { background: #ef4444; color: white; }

        /* Map */
        .trajet-map {
            height: 300px;
            border-radius: 1rem;
            margin-top: 1rem;
            border: 2px solid rgba(139,92,246,0.3);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #1a1a2e; }
        ::-webkit-scrollbar-thumb { background: #8b5cf6; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #ec4899; }
    </style>
</head>
<body>
    <main>
        <header style="text-align: center; margin-bottom: 3rem; background: transparent !important; box-shadow: none !important; border: none !important;">
            <p style="color: #ec4899; font-weight: bold; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 0.5rem;">NextGen • Livraison</p>
            <h1 style="font-size: 3rem; margin: 0.5rem 0; color: white; text-shadow: 0 0 20px rgba(139,92,246,0.5);">ESPACE LIVRAISONS</h1>
            <p style="font-size: 1.2rem; color: #a5b4fc;">
                <?php if ($profil): ?>
                    Bonjour <span style="color: #00ffc3;"><?php echo htmlspecialchars($profil['prenom']); ?></span>, gère tes commandes et suis tes livraisons en temps réel.
                <?php else: ?>
                    Gère tes commandes et suis tes livraisons en temps réel.
                <?php endif; ?>
            </p>
        </header>

        <?php if (!empty($message)): ?>
            <script>
                Swal.fire({
                    icon: '<?php echo $messageType === 'success' ? 'success' : 'info'; ?>',
                    title: '<?php echo $messageType === 'success' ? 'Succès' : 'Information'; ?>',
                    text: '<?php echo addslashes($message); ?>',
                    background: '#1a1a2e',
                    color: '#fff',
                    confirmButtonColor: '#8b5cf6'
                });
            </script>
        <?php endif; ?>

        <!-- SECTION 1: COMMANDES A LIVRER -->
        <section class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <div>
                    <h2 style="margin: 0; color: #00ffc3;">1. Mes Commandes</h2>
                    <p style="margin: 0.5rem 0 0; color: #ccc;">Choisis une commande à faire livrer</p>
                </div>
                <!-- Simuler achat btn -->
                <form method="post" style="margin:0;">
                    <input type="hidden" name="action" value="creer_commande">
                    <input type="hidden" name="id_jeu" value="<?php echo $jeux[0]['id_jeu'] ?? 1; ?>">
                    <button class="btn secondary" type="submit" style="font-size: 0.8rem;">
                        <i class="bi bi-plus-circle"></i> Simuler un achat
                    </button>
                </form>
            </div>

            <?php if (empty($commandes)): ?>
                <div style="text-align: center; padding: 2rem; border: 2px dashed rgba(255,255,255,0.1); border-radius: 1rem;">
                    <i class="bi bi-cart-x" style="font-size: 3rem; color: #666;"></i>
                    <p>Aucune commande en attente de livraison.</p>
                </div>
            <?php else: ?>
                <div class="commandes-grid">
                    <?php foreach ($commandes as $commande): ?>
                        <article class="commande-card">
                            <div style="display: flex; justify-content: space-between; align-items: start;">
                                <div>
                                    <span style="color: #ec4899; font-size: 0.8rem; font-weight: bold;">#<?php echo htmlspecialchars($commande['numero_commande']); ?></span>
                                    <h3 style="margin: 0.5rem 0;"><?php echo htmlspecialchars($commande['nom_jeu'] ?? 'Jeu mystère'); ?></h3>
                                    <p style="font-size: 0.9rem; color: #aaa;">
                                        <i class="bi bi-calendar"></i> <?php echo htmlspecialchars(date('d/m/Y', strtotime($commande['date_commande']))); ?>
                                    </p>
                                </div>
                                <div style="text-align: right;">
                                    <p style="font-size: 1.2rem; font-weight: bold; color: #00ffc3; margin: 0;">
                                        <?php echo number_format((float)$commande['total'], 2, ',', ' '); ?> €
                                    </p>
                                </div>
                            </div>
                            
                            <?php if (in_array($commande['id_commande'], $idsCommandesLivrees ?? [])): ?>
                                <button 
                                    class="btn secondary"
                                    type="button"
                                    disabled
                                    style="width: 100%; margin-top: 1rem; opacity: 0.5; cursor: not-allowed;"
                                >
                                    <i class="bi bi-check-circle"></i> Livraison déjà planifiée
                                </button>
                            <?php else: ?>
                                <button 
                                    class="btn primary planifier-btn"
                                    type="button"
                                    style="width: 100%; margin-top: 1rem;"
                                    data-commande="<?php echo (int)$commande['id_commande']; ?>"
                                    data-commande-label="#<?php echo htmlspecialchars($commande['numero_commande']); ?> - <?php echo htmlspecialchars($commande['nom_jeu'] ?? 'Jeu'); ?>"
                                >
                                    <i class="bi bi-truck"></i> Planifier la livraison
                                </button>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <!-- SECTION 2: PLANIFICATION -->
        <section class="card" id="planifier-section" style="border-color: #ec4899;">
            <form method="post" id="livraisonForm">
                <input type="hidden" name="action" value="creer_livraison">
                <input type="hidden" name="id_commande" id="selectedCommande">
                <input type="hidden" name="position_lat" id="position_lat">
                <input type="hidden" name="position_lng" id="position_lng">

                <div style="text-align: center; margin-bottom: 2rem;">
                    <h2 style="color: #ec4899;">2. Planifier la livraison</h2>
                    <p id="commandeSummary" style="font-size: 1.2rem; font-weight: bold;">Sélectionne une commande ci-dessus pour commencer</p>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; align-items: start;">
                    <!-- Left: Form -->
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem;">Mode de livraison</label>
                        <div style="display: grid; gap: 1rem; margin-bottom: 1.5rem;">
                            <?php foreach ($deliveryModes as $key => $mode): ?>
                                <label style="display: flex; align-items: center; background: rgba(0,0,0,0.2); padding: 1rem; border-radius: 12px; cursor: pointer; border: 1px solid rgba(255,255,255,0.1);">
                                    <input type="radio" name="mode_livraison" value="<?php echo $key; ?>" <?php echo $key === 'standard' ? 'checked' : ''; ?> style="width: auto; margin: 0 1rem 0 0;">
                                    <div>
                                        <strong style="display: block; color: #fff;"><?php echo htmlspecialchars($mode['label']); ?></strong>
                                        <small style="color: #00ffc3;"><?php echo $mode['price'] > 0 ? '+' . number_format($mode['price'], 2, ',', ' ') . ' €' : 'Gratuit'; ?></small>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>

                        <label style="display: block; margin-bottom: 0.5rem;">Notes pour le livreur <span style="color: #ef4444;">*</span></label>
                        <textarea name="notes_client" id="notes_client" rows="3" placeholder="Ex: Badge 1234, 2ème étage..."></textarea>
                        
                        <input type="hidden" name="adresse_complete" id="adresse_complete">
                        <input type="hidden" name="ville" id="ville">
                        <input type="hidden" name="code_postal" id="code_postal">
                    </div>

                    <!-- Right: Map Picker -->
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem;">Destination</label>
                        <div id="pickerMap" style="height: 300px; border-radius: 12px; border: 1px solid rgba(139,92,246,0.5);"></div>
                        
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.5rem;">
                            <p id="selectedAddrDisplay" style="margin: 0; color: #00ffc3; font-size: 0.9rem;">Clique sur la carte pour choisir l'adresse</p>
                            <button type="button" id="btn-geo" class="btn secondary" style="font-size: 0.8rem; padding: 0.4rem 0.8rem;">
                                <i class="bi bi-geo-alt-fill"></i> Me localiser
                            </button>
                        </div>
                    </div>
                </div>

                <div style="text-align: center; margin-top: 2rem;">
                    <button type="submit" class="btn primary" style="font-size: 1.2rem; padding: 1rem 3rem;">
                        Valider la livraison
                    </button>
                </div>
            </form>
        </section>

        <!-- SECTION 3: SUIVI -->
        <section class="card">
            <h2 style="color: #00ffc3; margin-bottom: 2rem;">3. Suivi en temps réel</h2>
            
            <?php if (empty($livraisons)): ?>
                <p style="text-align: center; color: #aaa;">Aucune livraison programmée.</p>
            <?php else: ?>
                <div style="display: grid; gap: 2rem;">
                    <?php foreach ($livraisons as $livraison): ?>
                        <article class="livraison-card">
                            <div style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                                <div>
                                    <h3 style="margin: 0;"><?php echo htmlspecialchars($livraison['nom_jeu'] ?? 'Jeu'); ?></h3>
                                    <p style="margin: 0.5rem 0; color: #aaa;">
                                        Commande #<?php echo htmlspecialchars($livraison['numero_commande']); ?>
                                    </p>
                                </div>
                                <div>
                                    <span class="badge badge-<?php echo htmlspecialchars($livraison['statut']); ?>">
                                        <?php echo ucfirst($livraison['statut']); ?>
                                    </span>
                                </div>
                            </div>

                            <div style="margin: 1.5rem 0; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; font-size: 0.9rem;">
                                <div>
                                    <strong style="color: #8b5cf6;">Adresse:</strong><br>
                                    <?php echo htmlspecialchars($livraison['adresse_complete']); ?>
                                </div>
                                <div>
                                    <strong style="color: #8b5cf6;">Date prévue:</strong><br>
                                    <?php echo htmlspecialchars(date('d/m/Y', strtotime($livraison['date_livraison']))); ?>
                                </div>
                                <div>
                                    <strong style="color: #8b5cf6;">Prix:</strong><br>
                                    <?php echo number_format((float)$livraison['prix_livraison'], 2, ',', ' '); ?> €
                                </div>
                            </div>

                            <?php if (in_array($livraison['statut'], ['en_route', 'livrée'], true) && $livraison['position_lat']): ?>
                                <div class="trajet-map" id="map-<?php echo (int)$livraison['id_livraison']; ?>"
                                     data-lat="<?php echo $livraison['position_lat']; ?>"
                                     data-lng="<?php echo $livraison['position_lng']; ?>"
                                     data-current-lat="<?php echo $livraison['trajet']['position_lat'] ?? $livraison['position_lat']; ?>"
                                     data-current-lng="<?php echo $livraison['trajet']['position_lng'] ?? $livraison['position_lng']; ?>">
                                </div>
                                <div style="text-align: center; margin-top: 1rem;">
                                    <a href="/PROJET_WEB_NEXTGEN-main/public/tracking-moving.php?id_livraison=<?php echo (int)$livraison['id_livraison']; ?>" 
                                       target="_blank" class="btn primary" style="font-size: 0.9rem;">
                                        <i class="bi bi-arrows-fullscreen"></i> Plein écran
                                    </a>
                                </div>
                            <?php endif; ?>

                            <div style="margin-top: 1.5rem; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1rem; text-align: right;">
                                <form method="post" onsubmit="return confirm('Annuler cette livraison ?');" style="display: inline;">
                                    <input type="hidden" name="action" value="supprimer_livraison">
                                    <input type="hidden" name="id_livraison" value="<?php echo (int)$livraison['id_livraison']; ?>">
                                    <button class="btn danger" style="font-size: 0.8rem; padding: 0.5rem 1rem;">Annuler</button>
                                </form>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <script src="/PROJET_WEB_NEXTGEN-main/public/vendor/leaflet/leaflet.js"></script>
    <script>
        // --- Custom Icon Definition ---
        const locationIcon = L.divIcon({
            html: '<i class="bi bi-geo-alt-fill" style="font-size: 48px; color: #ec4899; filter: drop-shadow(0 0 15px rgba(236,72,153,0.9));"></i>',
            className: '', // Remove default leaflet-div-icon styles
            iconSize: [48, 48],
            iconAnchor: [24, 48], // Bottom center
            popupAnchor: [0, -48]
        });

        // --- Map Picker Logic ---
        let pickerMap, pickerMarker;
        
        function initPickerMap() {
            pickerMap = L.map('pickerMap').setView([36.8065, 10.1815], 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap'
            }).addTo(pickerMap);

            pickerMap.on('click', async function(e) {
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;

                if (pickerMarker) pickerMarker.remove();
                pickerMarker = L.marker([lat, lng], {icon: locationIcon}).addTo(pickerMap);

                document.getElementById('position_lat').value = lat;
                document.getElementById('position_lng').value = lng;
                document.getElementById('selectedAddrDisplay').textContent = `Position: ${lat.toFixed(5)}, ${lng.toFixed(5)} (Recherche adresse...)`;

                // Reverse Geocode
                try {
                    const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
                    const data = await res.json();
                    const addr = data.display_name || 'Adresse inconnue';
                    
                    document.getElementById('adresse_complete').value = addr;
                    document.getElementById('ville').value = data.address.city || data.address.town || '';
                    document.getElementById('code_postal').value = data.address.postcode || '';
                    document.getElementById('selectedAddrDisplay').textContent = addr;
                } catch (err) {
                    document.getElementById('selectedAddrDisplay').textContent = `Position: ${lat.toFixed(5)}, ${lng.toFixed(5)}`;
                    document.getElementById('adresse_complete').value = `Lat: ${lat}, Lng: ${lng}`;
                }
            });
        }

        // --- Init Maps ---
        document.addEventListener('DOMContentLoaded', () => {
            initPickerMap();

            // Init Tracking Maps
            document.querySelectorAll('.trajet-map').forEach(el => {
                const destLat = parseFloat(el.dataset.lat);
                const destLng = parseFloat(el.dataset.lng);
                const curLat = parseFloat(el.dataset.currentLat);
                const curLng = parseFloat(el.dataset.currentLng);

                const map = L.map(el.id).setView([curLat, curLng], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

                const carIcon = L.divIcon({
                    html: '<i class="bi bi-truck" style="font-size:24px; color:#ec4899;"></i>',
                    className: '',
                    iconSize: [24, 24],
                    iconAnchor: [12, 12]
                });

                L.marker([destLat, destLng]).addTo(map).bindPopup('Destination');
                L.marker([curLat, curLng], {icon: carIcon}).addTo(map).bindPopup('Livreur');
                
                const group = new L.featureGroup([
                    L.marker([destLat, destLng]),
                    L.marker([curLat, curLng])
                ]);
                map.fitBounds(group.getBounds().pad(0.2));
            });

            // --- Form Selection Logic ---
            const btns = document.querySelectorAll('.planifier-btn');
            btns.forEach(btn => {
                btn.addEventListener('click', () => {
                    document.getElementById('selectedCommande').value = btn.dataset.commande;
                    document.getElementById('commandeSummary').textContent = `Commande sélectionnée : ${btn.dataset.commandeLabel}`;
                    document.getElementById('commandeSummary').style.color = '#00ffc3';
                    document.getElementById('planifier-section').scrollIntoView({behavior: 'smooth'});
                });
            });

            // --- Geolocation Logic ---
            document.getElementById('btn-geo').addEventListener('click', async () => {
                Swal.fire({
                    title: 'Localisation...',
                    text: 'Recherche de votre position (GPS/IP)',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading(),
                    background: '#1a1a2e',
                    color: '#fff'
                });

                const updateMap = async (lat, lng, source) => {
                    if (pickerMarker) pickerMarker.remove();
                    pickerMarker = L.marker([lat, lng], {icon: locationIcon}).addTo(pickerMap);
                    pickerMap.setView([lat, lng], 16);

                    document.getElementById('position_lat').value = lat;
                    document.getElementById('position_lng').value = lng;
                    
                    try {
                        const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
                        const data = await res.json();
                        const addr = data.display_name || 'Adresse inconnue';
                        
                        document.getElementById('adresse_complete').value = addr;
                        document.getElementById('ville').value = data.address.city || data.address.town || '';
                        document.getElementById('code_postal').value = data.address.postcode || '';
                        document.getElementById('selectedAddrDisplay').textContent = addr;
                        
                        Swal.fire({
                            icon: 'success', 
                            title: 'Trouvé !', 
                            text: `Position détectée via ${source}`,
                            timer: 2000,
                            background: '#1a1a2e',
                            color: '#fff'
                        });
                    } catch (err) {
                        document.getElementById('selectedAddrDisplay').textContent = `Lat: ${lat}, Lng: ${lng}`;
                        Swal.close();
                    }
                };

                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (pos) => updateMap(pos.coords.latitude, pos.coords.longitude, 'GPS'),
                        async () => {
                            // Fallback IP
                            try {
                                const res = await fetch('https://ipapi.co/json/');
                                const data = await res.json();
                                if (data.latitude && data.longitude) {
                                    updateMap(data.latitude, data.longitude, 'IP (' + data.city + ')');
                                } else {
                                    throw new Error('IP fail');
                                }
                            } catch (e) {
                                Swal.fire({icon: 'error', title: 'Erreur', text: 'Impossible de vous localiser.', background: '#1a1a2e', color: '#fff'});
                            }
                        },
                        { enableHighAccuracy: true, timeout: 5000 }
                    );
                } else {
                    // Fallback IP directly
                    try {
                        const res = await fetch('https://ipapi.co/json/');
                        const data = await res.json();
                        updateMap(data.latitude, data.longitude, 'IP (' + data.city + ')');
                    } catch (e) {
                        Swal.fire({icon: 'error', title: 'Erreur', text: 'Géolocalisation non supportée.', background: '#1a1a2e', color: '#fff'});
                    }
                }
            });

            // --- Validation ---
            document.getElementById('livraisonForm').addEventListener('submit', function(e) {
                const cmd = document.getElementById('selectedCommande').value;
                const lat = document.getElementById('position_lat').value;
                const notes = document.getElementById('notes_client').value;

                if (!cmd) {
                    e.preventDefault();
                    Swal.fire({icon: 'warning', title: 'Attention', text: 'Sélectionne une commande d\'abord !', background: '#1a1a2e', color: '#fff'});
                    return;
                }
                if (!lat) {
                    e.preventDefault();
                    Swal.fire({icon: 'warning', title: 'Attention', text: 'Clique sur la carte pour définir la destination !', background: '#1a1a2e', color: '#fff'});
                    return;
                }
                if (!notes) {
                    e.preventDefault();
                    Swal.fire({icon: 'warning', title: 'Attention', text: 'Ajoute une note pour le livreur (étage, code, etc.)', background: '#1a1a2e', color: '#fff'});
                    return;
                }
            });
        });
    </script>
</body>
</html>
