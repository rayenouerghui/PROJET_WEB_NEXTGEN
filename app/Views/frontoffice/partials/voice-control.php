<!-- Voice Control for Livraisons Page -->
<button class="voice-btn-float" id="voice-btn" title="Commande vocale">
    <span class="voice-icon">🎤</span>
    <span class="voice-pulse"></span>
</button>
<div id="voice-indicator-float"></div>

<style>
.voice-btn-float {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    cursor: pointer;
    box-shadow: 0 8px 24px rgba(102, 126, 234, 0.4);
    transition: all 0.3s ease;
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
}

.voice-icon {
    font-size: 2rem;
    position: relative;
    z-index: 2;
}

.voice-pulse {
    position: absolute;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: rgba(102, 126, 234, 0.4);
    opacity: 0;
    pointer-events: none;
}

.voice-btn-float:hover {
    transform: scale(1.1);
    box-shadow: 0 12px 32px rgba(102, 126, 234, 0.6);
}

.voice-btn-float.listening {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    animation: buttonPulse 1.5s ease-in-out infinite;
}

.voice-btn-float.listening .voice-pulse {
    animation: ringPulse 1.5s ease-out infinite;
}

@keyframes buttonPulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

@keyframes ringPulse {
    0% {
        transform: scale(0.8);
        opacity: 0.8;
    }
    100% {
        transform: scale(2);
        opacity: 0;
    }
}

#voice-indicator-float {
    position: fixed;
    bottom: 8rem;
    right: 2rem;
    background: rgba(0, 0, 0, 0.95);
    color: white;
    padding: 1.2rem 1.5rem;
    border-radius: 16px;
    font-family: 'Inter', sans-serif;
    font-size: 0.95rem;
    max-width: 320px;
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.4);
    backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    z-index: 999;
    display: none;
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

#voice-indicator-float.speaking {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.9), rgba(5, 150, 105, 0.9));
}
</style>

<script>
class VoiceCtrl {
    constructor() {
        this.recognition = null;
        this.synthesis = window.speechSynthesis;
        this.isListening = false;
        this.deliveries = <?php echo json_encode($livraisons ?? []); ?>;
        
        this.initRecognition();
        this.initButton();
        
        console.log('✅ Voice Control Ready!', this.deliveries.length, 'deliveries');
    }
    
    initRecognition() {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!SpeechRecognition) {
            console.warn('⚠️ Speech recognition not supported');
            return;
        }
        
        this.recognition = new SpeechRecognition();
        this.recognition.lang = 'fr-FR';
        this.recognition.continuous = true;
        this.recognition.interimResults = true;
        
        this.recognition.onstart = () => {
            console.log('🎤 Listening started');
            this.isListening = true;
            document.getElementById('voice-btn').classList.add('listening');
            this.showMsg('🎤 Je vous écoute...');
        };
        
        this.recognition.onresult = (event) => {
            const last = event.results.length - 1;
            const command = event.results[last][0].transcript.toLowerCase();
            const isFinal = event.results[last].isFinal;
            
            console.log('🎤', isFinal ? 'Final:' : 'Interim:', command);
            
            if (!isFinal) {
                this.showMsg('🎤 "' + command + '"');
            } else {
                this.processCommand(command);
            }
        };
        
        this.recognition.onerror = (event) => {
            console.error('❌ Speech error:', event.error);
            if (event.error === 'no-speech') {
                this.showMsg('❌ Aucune parole détectée');
            } else if (event.error === 'not-allowed') {
                this.showMsg('❌ Microphone bloqué');
                alert('Veuillez autoriser l\'accès au microphone');
            } else {
                this.showMsg('❌ Erreur: ' + event.error);
            }
        };
        
        this.recognition.onend = () => {
            console.log('🎤 Listening stopped');
            this.isListening = false;
            document.getElementById('voice-btn').classList.remove('listening');
            setTimeout(() => this.hideMsg(), 2000);
        };
    }
    
    initButton() {
        const btn = document.getElementById('voice-btn');
        btn.addEventListener('click', () => this.toggleListening());
    }
    
    toggleListening() {
        if (!this.recognition) {
            this.speak('Commande vocale non supportée sur ce navigateur');
            return;
        }
        
        if (this.isListening) {
            this.recognition.stop();
        } else {
            try {
                this.recognition.start();
            } catch (e) {
                console.error('Failed to start:', e);
                this.showMsg('❌ Erreur de démarrage');
            }
        }
    }
    
    processCommand(command) {
        console.log('📝 Processing:', command);
        this.showMsg('💭 "' + command + '"');
        
        // Count deliveries
        if (command.includes('combien') || command.includes('nombre')) {
            const count = this.deliveries.length;
            this.speak(`Vous avez ${count} livraison${count > 1 ? 's' : ''} programmée${count > 1 ? 's' : ''}`);
        }
        // Location
        else if (command.includes('où') || command.includes('position') || command.includes('localisation')) {
            if (this.deliveries.length > 0) {
                const city = this.deliveries[0].ville || 'une ville inconnue';
                const address = this.deliveries[0].adresse_complete || '';
                this.speak(`Votre colis est en route vers ${city}. ${address ? 'Adresse: ' + address : ''}`);
            } else {
                this.speak('Aucune livraison en cours pour le moment');
            }
        }
        // ETA
        else if (command.includes('quand') || command.includes('arrive') || command.includes('délai')) {
            if (this.deliveries.length > 0) {
                const date = new Date(this.deliveries[0].date_livraison);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                date.setHours(0, 0, 0, 0);
                const days = Math.ceil((date - today) / (86400000));
                
                if (days < 0) {
                    this.speak('Attention! Votre colis est en retard');
                } else if (days === 0) {
                    this.speak('Bonne nouvelle! Votre colis devrait arriver aujourd\'hui');
                } else if (days === 1) {
                    this.speak('Votre colis devrait arriver demain');
                } else if (days <= 7) {
                    this.speak(`Votre colis devrait arriver dans ${days} jours`);
                } else {
                    const dateStr = date.toLocaleDateString('fr-FR', {day: 'numeric', month: 'long'});
                    this.speak(`Votre colis devrait arriver le ${dateStr}`);
                }
            } else {
                this.speak('Aucune livraison programmée');
            }
        }
        // Status
        else if (command.includes('statut') || command.includes('état')) {
            if (this.deliveries.length > 0) {
                const status = this.deliveries[0].statut || 'inconnu';
                const statusMap = {
                    'preparée': 'en préparation',
                    'en_route': 'en route',
                    'livrée': 'livrée',
                    'annulée': 'annulée'
                };
                this.speak(`Le statut de votre livraison est: ${statusMap[status] || status}`);
            } else {
                this.speak('Aucune livraison à vérifier');
            }
        }
        // Help
        else if (command.includes('aide') || command.includes('commande')) {
            this.speak('Je peux vous dire combien de livraisons vous avez, où est votre colis, quand il arrive, et son statut');
        }
        // Unknown
        else {
            this.speak('Commande non reconnue. Dites: combien de livraisons, où est mon colis, quand arrive mon colis, ou quel est le statut');
        }
    }
    
    speak(text) {
        console.log('🔊 Speaking:', text);
        
        this.synthesis.cancel();
        
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'fr-FR';
        utterance.rate = 0.95;
        utterance.pitch = 1.0;
        utterance.volume = 1.0;
        
        utterance.onstart = () => {
            const indicator = document.getElementById('voice-indicator-float');
            indicator.classList.add('speaking');
        };
        
        utterance.onend = () => {
            const indicator = document.getElementById('voice-indicator-float');
            indicator.classList.remove('speaking');
            setTimeout(() => this.hideMsg(), 1500);
        };
        
        this.synthesis.speak(utterance);
        this.showMsg('🔊 ' + text);
    }
    
    showMsg(text) {
        const el = document.getElementById('voice-indicator-float');
        el.textContent = text;
        el.style.display = 'block';
    }
    
    hideMsg() {
        const el = document.getElementById('voice-indicator-float');
        if (!this.isListening) {
            el.style.display = 'none';
        }
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    window.voiceCtrl = new VoiceCtrl();
});
</script>
