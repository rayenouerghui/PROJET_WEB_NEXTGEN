/**
 * Voice Controller for Tracking Page
 * Simplified version with French voice commands
 */

class VoiceController {
    constructor() {
        this.recognition = null;
        this.synthesis = window.speechSynthesis;
        this.isListening = false;

        this.init();
    }

    init() {
        // Check browser support
        if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
            console.warn('Speech recognition not supported');
            return;
        }

        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        this.recognition = new SpeechRecognition();

        this.recognition.lang = 'fr-FR';
        this.recognition.continuous = false;
        this.recognition.interimResults = false;

        this.recognition.onresult = (event) => {
            const command = event.results[0][0].transcript.toLowerCase();
            console.log('🎤 Voice command:', command);
            this.processCommand(command);
        };

        this.recognition.onerror = (event) => {
            console.error('Speech recognition error:', event.error);
            this.updateIndicator('❌ Erreur');
        };

        this.recognition.onend = () => {
            this.isListening = false;
            this.updateIndicator('🎤 Cliquez pour parler');
        };
    }

    start() {
        if (!this.recognition) {
            alert('Commande vocale non supportée sur ce navigateur');
            return;
        }

        if (this.isListening) {
            this.stop();
            return;
        }

        this.isListening = true;
        this.updateIndicator('🎤 Écoute...');

        try {
            this.recognition.start();
        } catch (e) {
            console.error('Failed to start recognition:', e);
            this.isListening = false;
            this.updateIndicator('❌ Erreur');
        }
    }

    stop() {
        if (this.recognition && this.isListening) {
            this.recognition.stop();
            this.isListening = false;
        }
    }

    processCommand(command) {
        console.log('Processing:', command);

        // Check for tracking/location commands
        if (command.includes('où') || command.includes('position') || command.includes('localisation')) {
            this.handleLocationCommand();
        }
        // Check for ETA commands
        else if (command.includes('quand') || command.includes('arrive') || command.includes('livraison')) {
            this.handleETACommand();
        }
        // Check for status commands
        else if (command.includes('statut') || command.includes('état')) {
            this.handleStatusCommand();
        }
        // Unknown command
        else {
            this.speak('Commande non reconnue. Dites "où est ma commande" ou "quand arrive mon colis"');
        }
    }

    handleLocationCommand() {
        // Get current delivery data from global scope
        if (typeof currentDeliveryData !== 'undefined' && currentDeliveryData) {
            const city = currentDeliveryData.ville_destination || 'inconnue';
            const address = currentDeliveryData.adresse_destination || '';

            let message = `Votre colis est en route vers ${city}`;
            if (address) {
                message += `, ${address}`;
            }

            this.speak(message);
        } else {
            this.speak('Impossible de localiser le colis pour le moment');
        }
    }

    handleETACommand() {
        if (typeof currentDeliveryData !== 'undefined' && currentDeliveryData) {
            const deliveryDate = new Date(currentDeliveryData.date_estimee);
            const today = new Date();

            today.setHours(0, 0, 0, 0);
            deliveryDate.setHours(0, 0, 0, 0);

            const diffTime = deliveryDate - today;
            const daysRemaining = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            let message = '';

            if (daysRemaining < 0) {
                message = 'Votre colis est en retard. Contactez le support.';
            } else if (daysRemaining === 0) {
                message = 'Votre colis devrait arriver aujourd\'hui!';
            } else if (daysRemaining === 1) {
                message = 'Votre colis devrait arriver demain.';
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
            this.speak('Date de livraison non disponible');
        }
    }

    handleStatusCommand() {
        if (typeof currentDeliveryData !== 'undefined' && currentDeliveryData) {
            const status = currentDeliveryData.statut || 'en cours';
            const statusText = status === 'en_cours' ? 'en cours de livraison' : status;

            this.speak(`Le statut de votre colis est: ${statusText}`);
        } else {
            this.speak('Statut non disponible');
        }
    }

    speak(text) {
        console.log('🔊 Speaking:', text);

        // Cancel any ongoing speech
        this.synthesis.cancel();

        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'fr-FR';
        utterance.rate = 0.9;
        utterance.pitch = 1;

        this.synthesis.speak(utterance);

        // Update visual indicator
        this.updateIndicator('🔊 ' + text);
    }

    updateIndicator(text) {
        const indicator = document.getElementById('voice-indicator');
        if (indicator) {
            indicator.textContent = text;
            indicator.style.display = 'block';

            // Auto-hide after 3 seconds
            setTimeout(() => {
                if (indicator.textContent === text) {
                    indicator.style.display = 'none';
                }
            }, 3000);
        }
    }
}

// Global voice controller instance
let voiceController;

// Initialize when page loads
document.addEventListener('DOMContentLoaded', () => {
    voiceController = new VoiceController();
});
