/**
 * Voice Controller - Web Speech API Integration
 */

class VoiceController {
    constructor(app) {
        this.app = app;
        this.recognition = null;
        this.synthesis = window.speechSynthesis;
        this.isListening = false;

        // Check browser support
        if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
            this.setupRecognition();
        } else {
            console.warn('⚠️ Speech Recognition not supported');
        }
    }

    setupRecognition() {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        this.recognition = new SpeechRecognition();

        this.recognition.continuous = true;
        this.recognition.interimResults = false;
        this.recognition.lang = 'fr-FR';
        this.recognition.maxAlternatives = 1;

        this.recognition.onresult = (event) => {
            const lastResult = event.results[event.results.length - 1];
            const command = lastResult[0].transcript.toLowerCase().trim();

            console.log('🎤 Voice command:', command);
            this.processCommand(command);
        };

        this.recognition.onerror = (event) => {
            console.error('Voice error:', event.error);

            if (event.error === 'no-speech') {
                this.showError('Aucun son détecté. Parlez plus fort.');
            } else if (event.error === 'not-allowed') {
                this.showError('Permission microphone refusée.');
                this.stop();
            }
        };

        this.recognition.onend = () => {
            if (this.isListening) {
                // Restart if still active
                this.recognition.start();
            }
        };
    }

    start() {
        if (!this.recognition) {
            alert('Commande vocale non supportée par votre navigateur');
            return;
        }

        this.isListening = true;

        try {
            this.recognition.start();
            this.showListeningIndicator();
            this.speak("Je vous écoute. Comment puis-je vous aider?");
        } catch (error) {
            console.error('Failed to start recognition:', error);
        }
    }

    stop() {
        this.isListening = false;

        if (this.recognition) {
            this.recognition.stop();
        }

        this.hideListeningIndicator();
        this.speak("Commande vocale désactivée.");
    }

    async processCommand(command) {
        console.log('Processing:', command);

        // Pattern matching for common commands
        if (this.matchesPattern(command, ['où', 'suivi', 'commande', 'colis'])) {
            await this.handleTrackingCommand(command);
        }
        else if (this.matchesPattern(command, ['carte', '3d', 'map', 'vue'])) {
            this.handleMapCommand();
        }
        else if (this.matchesPattern(command, ['caméra', 'camera', 'aperçu', 'taille'])) {
            this.handleCameraCommand();
        }
        else if (this.matchesPattern(command, ['quand', 'arrivée', 'eta', 'temps', 'arrive'])) {
            await this.handleETACommand();
        }
        else if (this.matchesPattern(command, ['actualiser', 'rafraîchir', 'refresh'])) {
            this.handleRefreshCommand();
        }
        else if (this.matchesPattern(command, ['stop', 'arrêt', 'fermer'])) {
            this.stop();
        }
        else if (this.matchesPattern(command, ['aide', 'help', 'commandes'])) {
            this.speakHelp();
        }
        else {
            this.speak("Désolé, je n'ai pas compris. Dites 'aide' pour voir les commandes disponibles.");
        }
    }

    matchesPattern(command, keywords) {
        return keywords.some(keyword => command.includes(keyword));
    }

    async handleTrackingCommand(command) {
        // Extract order number if present
        const numberMatch = command.match(/\d+/);
        const orderId = numberMatch ? numberMatch[0] : null;

        if (orderId) {
            this.speak(`Recherche de la commande numéro ${orderId}...`);
            // TODO: Show specific order
        } else if (this.app.currentDeliveries.length > 0) {
            const delivery = this.app.currentDeliveries[0];

            // Build detailed status message
            let message = `Votre commande est actuellement ${delivery.status}.`;

            // Add location if available
            if (delivery.city) {
                message += ` Destination: ${delivery.city}`;
                if (delivery.address) {
                    message += `, ${delivery.address}`;
                }
            }

            // Add game name if available
            if (delivery.game_name) {
                message += `. Article: ${delivery.game_name}`;
            }

            this.speak(message);
        } else {
            this.speak("Vous n'avez aucune livraison active pour le moment.");
        }
    }

    handleMapCommand() {
        this.speak("Activation de la carte 3D.");
        setTimeout(() => this.app.activate3DView(), 500);
    }

    handleCameraCommand() {
        this.speak("Activation de l'aperçu du colis. Pointez votre caméra vers votre porte.");
        setTimeout(() => this.app.activateCameraView(), 500);
    }

    async handleETACommand() {
        if (this.app.currentDeliveries.length > 0) {
            const delivery = this.app.currentDeliveries[0];

            // Parse delivery date
            const deliveryDate = new Date(delivery.delivery_date);
            const today = new Date();

            // Reset time to midnight for accurate day comparison
            today.setHours(0, 0, 0, 0);
            deliveryDate.setHours(0, 0, 0, 0);

            const diffTime = deliveryDate - today;
            const daysRemaining = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            let message = '';

            if (daysRemaining < 0) {
                message = "Votre colis est en retard. Veuillez consulter les détails ou contacter le support.";
            } else if (daysRemaining === 0) {
                message = "Votre colis devrait arriver aujourd'hui!";
                if (delivery.status === 'en_route') {
                    message += " Il est actuellement en route.";
                }
            } else if (daysRemaining === 1) {
                message = "Votre colis devrait arriver demain.";
            } else if (daysRemaining <= 7) {
                message = `Votre colis devrait arriver dans ${daysRemaining} jours.`;
            } else {
                const formattedDate = deliveryDate.toLocaleDateString('fr-FR', {
                    day: 'numeric',
                    month: 'long'
                });
                message = `Votre colis devrait arriver le ${formattedDate}.`;
            }

            this.speak(message);
        } else {
            this.speak("Aucune livraison en cours.");
        }
    }

    handleRefreshCommand() {
        this.speak("Actualisation des données...");
        this.app.loadOrders();
    }

    speakHelp() {
        this.speak("Voici les commandes disponibles: Où est ma commande, Carte 3D, Caméra, Quand arrive mon colis, Actualiser, ou Stop.");
    }

    speak(text) {
        if (!this.synthesis) return;

        // Cancel any ongoing speech
        this.synthesis.cancel();

        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'fr-FR';
        utterance.rate = 1.0;
        utterance.pitch = 1.0;
        utterance.volume = 1.0;

        this.synthesis.speak(utterance);
    }

    showListeningIndicator() {
        const indicator = document.getElementById('voice-indicator');
        indicator.style.display = 'flex';
        indicator.classList.add('active');
        indicator.innerHTML = `
            <div class="pulse" style="width: 10px; height: 10px; background: #10b981; border-radius: 50%; animation: pulse 1.5s ease-in-out infinite;"></div>
            <span>🎤 À l'écoute...</span>
        `;

        // Add pulse animation if not exists
        if (!document.getElementById('pulse-animation')) {
            const style = document.createElement('style');
            style.id = 'pulse-animation';
            style.innerHTML = `
                @keyframes pulse {
                    0%, 100% { transform: scale(1); opacity: 1; }
                    50% { transform: scale(1.5); opacity: 0.5; }
                }
            `;
            document.head.appendChild(style);
        }
    }

    hideListeningIndicator() {
        const indicator = document.getElementById('voice-indicator');
        indicator.style.display = 'none';
        indicator.classList.remove('active');
    }

    showError(message) {
        const indicator = document.getElementById('voice-indicator');
        indicator.style.display = 'flex';
        indicator.innerHTML = `<span style="color: #ef4444;">❌ ${message}</span>`;

        setTimeout(() => {
            this.hideListeningIndicator();
        }, 3000);
    }
}
