/**
 * NextGen PWA - Main Application Controller
 */

class App {
    constructor() {
        this.voiceController = null;
        this.scene3D = null;
        this.cameraViewer = null;
        this.apiClient = new APIClient();
        this.currentDeliveries = [];

        this.init();
    }

    async init() {
        console.log('🚀 NextGen PWA Initializing...');

        // Initialize modules
        this.voiceController = new VoiceController(this);
        this.scene3D = new Scene3D();
        this.cameraViewer = new CameraViewer();

        // Make camera viewer globally accessible for button controls
        window.cameraViewer = this.cameraViewer;

        // Load user orders
        await this.loadOrders();

        console.log('✅ App Ready!');
    }

    async loadOrders() {
        const ordersContainer = document.getElementById('orders-list');

        try {
            // Get user ID
            const userId = this.getCurrentUserId();
            console.log('🔍 Loading orders for user:', userId);

            if (!userId) {
                console.warn('⚠️ No user ID - showing test instructions');
                ordersContainer.innerHTML = `
                    <div class="empty-state" style="text-align: center; padding: 2rem;">
                        <i class="ri-inbox-line" style="font-size: 4rem; opacity: 0.3;"></i>
                        <p style="margin: 1rem 0;">Pour tester: ajoutez <code style="background: rgba(102, 126, 234, 0.2); padding: 0.25rem 0.5rem; border-radius: 4px;">?user_id=1</code> à l'URL</p>
                        <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 1rem;">
                            Exemple:<br>
                            <code style="background: rgba(102, 126, 234, 0.1); padding: 0.5rem; border-radius: 4px; display: inline-block; margin-top: 0.5rem;">
                                ${window.location.href.split('?')[0]}?user_id=1
                            </code>
                        </p>
                    </div>
                `;
                return;
            }

            console.log('📡 Fetching deliveries from API...');
            const deliveries = await this.apiClient.getUserDeliveries(userId);
            console.log('✅ Received deliveries:', deliveries);

            this.currentDeliveries = deliveries;

            if (!deliveries || deliveries.length === 0) {
                console.warn('📦 No deliveries found for user', userId);
                ordersContainer.innerHTML = `
                    <div class="empty-state" style="text-align: center; padding: 2rem;">
                        <i class="ri-package-line" style="font-size: 4rem; opacity: 0.3;"></i>
                        <p>Aucune livraison trouvée</p>
                        <p style="font-size: 0.85rem; color: var(--text-muted);">
                            User ID: ${userId}
                        </p>
                    </div>
                `;
            } else {
                console.log(`✅ Rendering ${deliveries.length} deliveries`);
                this.renderOrders(deliveries);
            }

        } catch (error) {
            console.error('❌ Error loading orders:', error);
            ordersContainer.innerHTML = `
                <div class="error-state" style="text-align: center; padding: 2rem;">
                    <i class="ri-error-warning-line" style="font-size: 4rem; color: #ef4444;"></i>
                    <p>Erreur de chargement</p>
                    <p style="font-size: 0.85rem; color: var(--text-muted);">${error.message}</p>
                    <button onclick="app.loadOrders()" class="action-card" style="margin-top: 1rem; cursor: pointer;">
                        <i class="ri-refresh-line"></i>
                        <span>Réessayer</span>
                    </button>
                </div>
            `;
        }
    }

    renderOrders(deliveries) {
        const container = document.getElementById('orders-list');

        const html = deliveries.map(delivery => `
            <div class="order-card" onclick="app.viewDeliveryDetails(${delivery.id})">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                    <div>
                        <h3 style="font-size: 1.2rem; margin-bottom: 0.25rem;">
                            Commande #${delivery.order_number}
                        </h3>
                        <p style="color: var(--text-muted); font-size: 0.9rem;">
                            ${delivery.game_name || 'Jeu vidéo'}
                        </p>
                    </div>
                    <span class="status-badge status-${delivery.status}">
                        ${this.getStatusEmoji(delivery.status)} ${delivery.status}
                    </span>
                </div>
                
                <div style="display: flex; gap: 1rem; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1rem;">
                    <span><i class="ri-map-pin-line"></i> ${delivery.city}</span>
                    <span><i class="ri-calendar-line"></i> ${this.formatDate(delivery.delivery_date)}</span>
                </div>
                
                ${delivery.status === 'en_route' ? `
                    <button onclick="event.stopPropagation(); app.trackLive(${delivery.id})" 
                            class="btn-track">
                        <i class="ri-map-line"></i> Suivre en temps réel
                    </button>
                ` : ''}
            </div>
        `).join('');

        container.innerHTML = html;
    }

    getStatusEmoji(status) {
        const emojis = {
            'preparée': '📦',
            'en_route': '🚚',
            'livrée': '✅',
            'annulée': '❌'
        };
        return emojis[status] || '📦';
    }

    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' });
    }

    getCurrentUserId() {
        // Check URL parameter first (for testing: ?user_id=1)
        const urlParams = new URLSearchParams(window.location.search);
        const urlUserId = urlParams.get('user_id');

        if (urlUserId) {
            // Save to localStorage for persistence
            localStorage.setItem('userId', urlUserId);
            console.log('📝 User ID from URL:', urlUserId);
            return urlUserId;
        }

        // Check localStorage
        const storedUserId = localStorage.getItem('userId');
        if (storedUserId) {
            console.log('📝 User ID from localStorage:', storedUserId);
            return storedUserId;
        }

        console.warn('⚠️ No user ID found. Add ?user_id=YOUR_ID to URL');
        return null;
    }

    viewDeliveryDetails(deliveryId) {
        console.log('Viewing delivery:', deliveryId);
        this.trackLive(deliveryId);
    }

    trackLive(deliveryId) {
        window.open(`/PROJET_WEB_NEXTGEN-main/public/tracking-moving.php?id_livraison=${deliveryId}`, '_blank');
    }

    // Voice Control
    activateVoiceControl() {
        if (this.voiceController) {
            this.voiceController.start();
            document.getElementById('voice-btn').style.display = 'flex';
        }
    }

    deactivateVoiceControl() {
        if (this.voiceController) {
            this.voiceController.stop();
        }
    }

    // 3D View
    activate3DView() {
        console.log('🎬 Activating 3D view...');

        if (this.currentDeliveries.length === 0) {
            alert('Aucune livraison disponible pour la vue 3D');
            return;
        }

        const delivery = this.currentDeliveries[0];

        document.getElementById('3d-view').style.display = 'block';
        document.getElementById('main-view').style.display = 'none';

        // Initialize 3D scene with delivery data
        this.scene3D.init(delivery);
    }

    close3DView() {
        document.getElementById('3d-view').style.display = 'none';
        document.getElementById('main-view').style.display = 'block';

        if (this.scene3D) {
            this.scene3D.destroy();
        }
    }

    // Camera View
    async activateCameraView() {
        console.log('📸 Activating camera view...');

        document.getElementById('camera-view').style.display = 'block';
        document.getElementById('main-view').style.display = 'none';

        await this.cameraViewer.activate();
    }

    closeCameraView() {
        console.log('📸 Closing camera view...');

        document.getElementById('camera-view').style.display = 'none';
        document.getElementById('main-view').style.display = 'block';

        if (this.cameraViewer) {
            this.cameraViewer.deactivate();
        }
    }
}

// Initialize app when DOM is ready
let app;
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        app = new App();
    });
} else {
    app = new App();
}
