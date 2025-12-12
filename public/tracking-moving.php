<?php
$id_livraison = isset($_GET['id_livraison']) ? (int)$_GET['id_livraison'] : 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Suivi en temps réel - NextGen</title>
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="/PROJET_WEB_NEXTGEN-main/public/manifest.json">
    <meta name="theme-color" content="#667eea">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="/PROJET_WEB_NEXTGEN-main/public/images/icon-192.png">
    
    <!-- MapLibre GL JS -->
    <link rel="stylesheet" href="https://unpkg.com/maplibre-gl@3.6.2/dist/maplibre-gl.css">
    <script src="https://unpkg.com/maplibre-gl@3.6.2/dist/maplibre-gl.js"></script>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="/PROJET_WEB_NEXTGEN-main/public/css/tracking_custom.css">
    
    <style>
        /* Voice Control Styles */
        .voice-button {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            color: white;
            font-size: 2rem;
            cursor: pointer;
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.4);
            transition: all 0.3s ease;
            z-index: 1000;
        }
        
        .voice-button:hover {
            transform: scale(1.1);
            box-shadow: 0 12px 32px rgba(102, 126, 234, 0.6);
        }
        
        .voice-button.listening {
            animation: pulse 1.5s infinite;
            background: linear-gradient(135deg, #10b981, #059669);
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.15); }
        }
        
        #voice-indicator {
            position: fixed;
            bottom: 7rem;
            right: 2rem;
            background: rgba(0, 0, 0, 0.85);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            max-width: 300px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            z-index: 999;
            display: none;
        }
    </style>
</head>
<body>

    <div class="particles" id="particles"></div>
    
    <div id="map"></div>
    
    <div class="header-overlay">
        <div class="header-content">
            <h1>🚚 Suivi de Livraison en Temps Réel</h1>
            <div class="subtitle">Livraison #<?php echo $id_livraison; ?> • Carte interactive MapLibre GL JS</div>
        </div>
    </div>

    <div class="stats-panel">
        <div class="stats-grid" id="stats">
            <div class="stat-card">
                <div class="stat-label">Progression</div>
                <div class="stat-value">0%</div>
                <div class="stat-subtext">Chargement...</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Distance</div>
                <div class="stat-value">0 km</div>
                <div class="stat-subtext">Calcul...</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Statut</div>
                <div class="stat-value" style="font-size: 1.4rem;">Chargement...</div>
            </div>
        </div>
    </div>

    <div class="loading" id="loading">
        <div class="loader"></div>
        <div class="loading-text">Chargement de la carte...</div>
    </div>

    <!-- Voice Control Button -->
    <button class="voice-button" id="voice-btn" onclick="voiceController.start()" title="Commande vocale">
        🎤
    </button>
    
    <!-- Voice Indicator -->
    <div id="voice-indicator"></div>
    
    <!-- Voice Controller Script -->
    <script src="/PROJET_WEB_NEXTGEN-main/public/js/voice-tracking.js"></script>

    <script>
        
        const particlesContainer = document.getElementById('particles');
        for (let i = 0; i < 30; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            particle.style.left = Math.random() * 100 + '%';
            particle.style.animationDelay = Math.random() * 20 + 's';
            particle.style.animationDuration = (15 + Math.random() * 10) + 's';
            particlesContainer.appendChild(particle);
        }

        const LIVRAISON_ID = <?php echo $id_livraison; ?>;
        let map, carMarker, routeLayerId;

        
        async function initMap() {
            
            const data = await fetchTrackingData();
            if (!data || data.error) {
                document.getElementById('loading').innerHTML = '<div class="loading-text" style="color: #ff6b6b;">❌ Erreur: ' + (data?.error || 'Données introuvables') + '</div>';
                return;
            }

            
            map = new maplibregl.Map({
                container: 'map',
                style: {
                    version: 8,
                    sources: {
                        'osm': {
                            type: 'raster',
                            tiles: ['https://a.tile.openstreetmap.org/{z}/{x}/{y}.png'],
                            tileSize: 256,
                            attribution: '© OpenStreetMap contributors'
                        }
                    },
                    layers: [{
                        id: 'osm',
                        type: 'raster',
                        source: 'osm',
                        minzoom: 0,
                        maxzoom: 19
                    }]
                },
                center: [data.trajet.position_lng, data.trajet.position_lat],
                zoom: 13,
                pitch: 0,
                bearing: 0
            });

            map.addControl(new maplibregl.NavigationControl({ visualizePitch: true }), 'top-right');
            map.addControl(new maplibregl.FullscreenControl(), 'top-right');

            map.on('load', () => {
                document.getElementById('loading').style.display = 'none';
                setupMap(data);
                startTracking();
            });
        }

        function setupMap(data) {
            
            if (data.route && data.route.length > 0) {
                const routeCoords = data.route.map(p => [p.lng, p.lat]);
                const currentIndex = data.trajet.current_index || 0;

                
                const completedRoute = routeCoords.slice(0, currentIndex + 1);
                const remainingRoute = routeCoords.slice(currentIndex);

                
                if (completedRoute.length > 1) {
                    map.addSource('route-completed', {
                        type: 'geojson',
                        data: {
                            type: 'Feature',
                            properties: {},
                            geometry: {
                                type: 'LineString',
                                coordinates: completedRoute
                            }
                        }
                    });

                    
                    map.addLayer({
                        id: 'route-completed-glow',
                        type: 'line',
                        source: 'route-completed',
                        layout: {
                            'line-join': 'round',
                            'line-cap': 'round'
                        },
                        paint: {
                            'line-color': '#667eea',
                            'line-width': 20,
                            'line-opacity': 0.3,
                            'line-blur': 6
                        }
                    });

                    map.addLayer({
                        id: 'route-completed-line',
                        type: 'line',
                        source: 'route-completed',
                        layout: {
                            'line-join': 'round',
                            'line-cap': 'round'
                        },
                        paint: {
                            'line-color': [
                                'interpolate',
                                ['linear'],
                                ['line-progress'],
                                0, '#667eea',
                                0.5, '#a855f7',
                                1, '#ec4899'
                            ],
                            'line-width': 10,
                            'line-opacity': 1,
                            'line-gradient': true
                        }
                    });

                    
                    map.addLayer({
                        id: 'route-completed-pulse',
                        type: 'line',
                        source: 'route-completed',
                        layout: {
                            'line-join': 'round',
                            'line-cap': 'round'
                        },
                        paint: {
                            'line-color': '#ffffff',
                            'line-width': 5,
                            'line-opacity': 0.7
                        }
                    });
                }

                
                if (remainingRoute.length > 1) {
                    map.addSource('route-remaining', {
                        type: 'geojson',
                        data: {
                            type: 'Feature',
                            properties: {},
                            geometry: {
                                type: 'LineString',
                                coordinates: remainingRoute
                            }
                        }
                    });

                  
                    map.addLayer({
                        id: 'route-remaining-line',
                        type: 'line',
                        source: 'route-remaining',
                        layout: {
                            'line-join': 'round',
                            'line-cap': 'round'
                        },
                        paint: {
                            'line-color': '#1a1a1a',
                            'line-width': 7,
                            'line-opacity': 0.6,
                            'line-dasharray': [3, 3]
                        }
                    });
                }

                
                const bounds = routeCoords.reduce((bounds, coord) => {
                    return bounds.extend(coord);
                }, new maplibregl.LngLatBounds(routeCoords[0], routeCoords[0]));

                map.fitBounds(bounds, { padding: 120, duration: 1500 });
            }

        
            const originEl = document.createElement('div');
            originEl.style.width = '48px';
            originEl.style.height = '64px';
            originEl.style.backgroundImage = 'url(/PROJET_WEB_NEXTGEN-main/public/images/origin-marker.svg)';
            originEl.style.backgroundSize = 'contain';
            originEl.style.backgroundRepeat = 'no-repeat';
            originEl.style.cursor = 'pointer';
            originEl.style.filter = 'drop-shadow(0 8px 16px rgba(16, 185, 129, 0.5))';

            new maplibregl.Marker({
                element: originEl,
                anchor: 'bottom'
            })
            .setLngLat([data.origin.lng, data.origin.lat])
            .setPopup(new maplibregl.Popup({ offset: 25 }).setHTML('<div style="font-weight:700;color:#10b981;">📍 Départ</div><div style="font-size:0.9rem;">Entrepôt</div>'))
            .addTo(map);

            // Add destination marker with custom SVG
            const destEl = document.createElement('div');
            destEl.style.width = '48px';
            destEl.style.height = '64px';
            destEl.style.backgroundImage = 'url(/PROJET_WEB_NEXTGEN-main/public/images/destination-marker.svg)';
            destEl.style.backgroundSize = 'contain';
            destEl.style.backgroundRepeat = 'no-repeat';
            destEl.style.cursor = 'pointer';
            destEl.style.filter = 'drop-shadow(0 8px 16px rgba(239, 68, 68, 0.5))';

            new maplibregl.Marker({
                element: destEl,
                anchor: 'bottom'
            })
            .setLngLat([data.destination.lng, data.destination.lat])
            .setPopup(new maplibregl.Popup({ offset: 25 }).setHTML('<div style="font-weight:700;color:#ef4444;">🎯 Destination</div><div style="font-size:0.9rem;">Adresse de livraison</div>'))
            .addTo(map);

            // Create premium 3D delivery truck
            const carEl = document.createElement('div');
            carEl.style.width = '64px';
            carEl.style.height = '64px';
            carEl.style.backgroundImage = 'url(/PROJET_WEB_NEXTGEN-main/public/images/delivery-truck.svg)';
            carEl.style.backgroundSize = 'contain';
            carEl.style.backgroundRepeat = 'no-repeat';
            carEl.style.cursor = 'pointer';
            carEl.style.filter = 'drop-shadow(0 10px 20px rgba(102, 126, 234, 0.6))';
            carEl.style.transition = 'filter 0.3s ease';

            carEl.onmouseenter = () => {
                carEl.style.filter = 'drop-shadow(0 15px 30px rgba(102, 126, 234, 0.8))';
            };
            carEl.onmouseleave = () => {
                carEl.style.filter = 'drop-shadow(0 10px 20px rgba(102, 126, 234, 0.6))';
            };

            carMarker = new maplibregl.Marker({
                element: carEl,
                anchor: 'center',
                pitchAlignment: 'viewport',
                rotationAlignment: 'viewport'
            })
            .setLngLat([data.trajet.position_lng, data.trajet.position_lat])
            .setPopup(new maplibregl.Popup({ offset: 30 }).setHTML('<div style="font-weight:700;color:#667eea;">🚚 En livraison</div><div style="font-size:0.9rem;">Suivez votre colis en temps réel</div>'))
            .addTo(map);

            updateStats(data);
        }

        async function fetchTrackingData() {
            try {
                const response = await fetch(`/PROJET_WEB_NEXTGEN-main/public/api/trajet.php?id_livraison=${LIVRAISON_ID}`);
                return await response.json();
            } catch (err) {
                console.error('Fetch error:', err);
                return null;
            }
        }

        function startTracking() {
            let isAnimating = false;
            let animationFrame = null;

            setInterval(async () => {
                if (isAnimating) return;

                const data = await fetchTrackingData();
                if (!data || data.error) return;

                if (data.livraison.statut === 'livrée' || data.livraison.statut === 'annulée') {
                    return;
                }

                // Update car position smoothly
                if (carMarker) {
                    const currentPos = carMarker.getLngLat();
                    const targetPos = { lng: data.trajet.position_lng, lat: data.trajet.position_lat };
                    
                    animateCarMovement(currentPos, targetPos, 2000);
                }

                // Update route highlighting based on current progress
                if (data.route && data.route.length > 0) {
                    const routeCoords = data.route.map(p => [p.lng, p.lat]);
                    const currentIndex = data.trajet.current_index || 0;

                    const completedRoute = routeCoords.slice(0, currentIndex + 1);
                    const remainingRoute = routeCoords.slice(currentIndex);

                    // Update completed route
                    if (map.getSource('route-completed') && completedRoute.length > 1) {
                        map.getSource('route-completed').setData({
                            type: 'Feature',
                            properties: {},
                            geometry: {
                                type: 'LineString',
                                coordinates: completedRoute
                            }
                        });
                    }

                    // Update remaining route
                    if (map.getSource('route-remaining') && remainingRoute.length > 1) {
                        map.getSource('route-remaining').setData({
                            type: 'Feature',
                            properties: {},
                            geometry: {
                                type: 'LineString',
                                coordinates: remainingRoute
                            }
                        });
                    }
                }

                updateStats(data);
            }, 2000);

            function animateCarMovement(start, end, duration) {
                isAnimating = true;
                const startTime = performance.now();
                const startLng = start.lng;
                const startLat = start.lat;
                const endLng = end.lng;
                const endLat = end.lat;

                function animate(currentTime) {
                    const elapsed = currentTime - startTime;
                    const progress = Math.min(elapsed / duration, 1);
                    
                    const eased = progress < 0.5
                        ? 4 * progress * progress * progress
                        : 1 - Math.pow(-2 * progress + 2, 3) / 2;
                    
                    const lng = startLng + (endLng - startLng) * eased;
                    const lat = startLat + (endLat - startLat) * eased;
                    
                    carMarker.setLngLat({ lng, lat });
                    
                    if (progress < 1) {
                        animationFrame = requestAnimationFrame(animate);
                    } else {
                        isAnimating = false;
                    }
                }
                
                if (animationFrame) {
                    cancelAnimationFrame(animationFrame);
                }
                animationFrame = requestAnimationFrame(animate);
            }
        }

        function updateStats(data) {
            const pct = Math.round(data.progress?.progress_pct || 0);
            const km = (data.progress?.covered_km || 0).toFixed(1);
            const total = (data.progress?.total_km || 0).toFixed(1);
            const status = data.trajet?.statut_realtime || 'En cours';
            const isDelivered = data.livraison?.statut === 'livrée';

            document.getElementById('stats').innerHTML = `
                <div class="stat-card">
                    <div class="stat-label">Progression</div>
                    <div class="stat-value">${pct}%</div>
                    <div class="stat-subtext">${isDelivered ? '✅ Terminé' : '🚀 En cours'}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Distance</div>
                    <div class="stat-value">${km} km</div>
                    <div class="stat-subtext">sur ${total} km total</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Statut</div>
                    <div class="stat-value" style="font-size: 1.4rem;">${isDelivered ? '✅ Livrée' : '🚚 ' + status}</div>
                    <div class="stat-subtext">${isDelivered ? 'Colis reçu' : 'En déplacement'}</div>
                </div>
            `;
        }

        // Start the app
        initMap();
    </script>
</body>
</html>