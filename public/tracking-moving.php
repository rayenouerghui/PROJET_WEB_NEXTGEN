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
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            overflow: hidden;
            height: 100vh;
            position: relative;
        }

        /* Animated Background Particles */
        .particles {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
        }

        .particle {
            position: absolute;
            width: 3px;
            height: 3px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            animation: float 20s infinite ease-in-out;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) translateX(0); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateY(-100vh) translateX(50px); opacity: 0; }
        }

        #map {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 2;
        }

        /* Premium Header Overlay with Glassmorphism */
        .header-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            padding: 28px 40px;
            background: linear-gradient(180deg, rgba(102, 126, 234, 0.95) 0%, rgba(102, 126, 234, 0) 100%);
            backdrop-filter: blur(30px);
            z-index: 1000;
            pointer-events: none;
        }

        .header-content {
            max-width: 1400px;
            margin: 0 auto;
        }

        h1 {
            font-size: 2.25rem;
            font-weight: 900;
            letter-spacing: -1px;
            background: linear-gradient(135deg, #ffffff 0%, #e0e7ff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            animation: fadeInDown 0.6s ease-out;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .subtitle {
            margin-top: 6px;
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        /* Premium Stats Panel with Glassmorphism */
        .stats-panel {
            position: absolute;
            bottom: 32px;
            left: 32px;
            right: 32px;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(40px) saturate(180%);
            border-radius: 32px;
            padding: 32px 40px;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.3), 
                        0 0 0 1px rgba(255, 255, 255, 0.2),
                        inset 0 1px 0 rgba(255, 255, 255, 0.3);
            z-index: 900;
            max-width: 1400px;
            margin: 0 auto;
            animation: slideUp 0.8s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 28px;
        }

        .stat-card {
            text-align: center;
            padding: 28px 24px;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0.05) 100%);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.25);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.5), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .stat-card:hover {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0.1) 100%);
            border-color: rgba(255, 255, 255, 0.4);
            transform: translateY(-8px) scale(1.03);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        }

        .stat-card:hover::before {
            opacity: 1;
        }

        .stat-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 2.5px;
            color: #06b6d4;
            margin-bottom: 12px;
            font-weight: 800;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 900;
            background: linear-gradient(135deg, #a855f7 0%, #ec4899 50%, #06b6d4 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 8px;
        }

        .stat-subtext {
            margin-top: 8px;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.6);
            font-weight: 500;
        }

        /* Custom MapLibre Controls */
        .maplibregl-ctrl-group {
            background: rgba(255, 255, 255, 0.15) !important;
            backdrop-filter: blur(20px) !important;
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3) !important;
            border-radius: 12px !important;
        }

        .maplibregl-ctrl-group button {
            background: transparent !important;
            color: rgba(255, 255, 255, 0.9) !important;
            width: 36px !important;
            height: 36px !important;
        }

        .maplibregl-ctrl-group button:hover {
            background: rgba(255, 255, 255, 0.25) !important;
            color: white !important;
        }

        /* Premium Loading State */
        .loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            z-index: 10000;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(30px);
            padding: 48px 64px;
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .loader {
            width: 80px;
            height: 80px;
            border: 6px solid rgba(255, 255, 255, 0.2);
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: spin 1s cubic-bezier(0.68, -0.55, 0.265, 1.55) infinite;
            margin: 0 auto 24px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .loading-text {
            color: white;
            font-weight: 700;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .header-overlay {
                padding: 20px 24px;
            }
            h1 {
                font-size: 1.5rem;
            }
            .subtitle {
                font-size: 0.9rem;
            }
            .stats-panel {
                bottom: 20px;
                left: 20px;
                right: 20px;
                padding: 24px;
            }
            .stats-grid {
                grid-template-columns: 1fr 1fr;
                gap: 16px;
            }
            .stat-value {
                font-size: 1.75rem;
            }
            .stat-card {
                padding: 20px 16px;
            }
        }
    </style>
</head>
<body>
    <!-- Animated Background Particles -->
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

    <script>
        // Create background particles
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

        // Initialize map
        async function initMap() {
            // Fetch initial data
            const data = await fetchTrackingData();
            if (!data || data.error) {
                document.getElementById('loading').innerHTML = '<div class="loading-text" style="color: #ff6b6b;">❌ Erreur: ' + (data?.error || 'Données introuvables') + '</div>';
                return;
            }

            // Create map
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
            // Add route if available
            if (data.route && data.route.length > 0) {
                const routeCoords = data.route.map(p => [p.lng, p.lat]);
                const currentIndex = data.trajet.current_index || 0;

                // Split route into completed and remaining sections
                const completedRoute = routeCoords.slice(0, currentIndex + 1);
                const remainingRoute = routeCoords.slice(currentIndex);

                // Add COMPLETED route source (origin to car) - BRIGHT
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

                    // Completed route - vibrant gradient
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

                    // Pulsing effect on completed route
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

                // Add REMAINING route source (car to destination) - BLACK for visibility
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

                    // Remaining route - black dashed for visibility
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

                // Fit bounds to entire route
                const bounds = routeCoords.reduce((bounds, coord) => {
                    return bounds.extend(coord);
                }, new maplibregl.LngLatBounds(routeCoords[0], routeCoords[0]));

                map.fitBounds(bounds, { padding: 120, duration: 1500 });
            }

            // Add origin marker with custom SVG
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