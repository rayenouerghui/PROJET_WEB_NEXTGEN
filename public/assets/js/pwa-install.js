/**
 * PWA Installation Handler
 * Manages service worker registration and app installation prompt
 */

(function () {
    'use strict';

    let deferredPrompt = null;

    /**
     * Register Service Worker
     */
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/PROJET_WEB_NEXTGEN-main/public/sw.js')
                .then(registration => {
                    console.log('✅ PWA: Service Worker registered successfully');
                    console.log('📦 PWA: Scope:', registration.scope);

                    // Check for updates periodically
                    setInterval(() => {
                        registration.update();
                    }, 60000); // Check every minute
                })
                .catch(error => {
                    console.log('❌ PWA: Service Worker registration failed:', error);
                });
        });
    }

    /**
     * Handle Install Prompt
     */
    window.addEventListener('beforeinstallprompt', (e) => {
        // Prevent the mini-infobar from appearing
        e.preventDefault();

        // Store the event for later use
        deferredPrompt = e;

        // Show install button if it exists
        const installBtn = document.getElementById('pwa-install-btn');
        if (installBtn) {
            installBtn.style.display = 'block';
        }

        console.log('📱 PWA: Install prompt available');
    });

    /**
     * Install PWA function (can be called from UI)
     */
    window.installPWA = function () {
        if (!deferredPrompt) {
            console.log('⚠️ PWA: Install prompt not available');
            showInstallInstructions();
            return;
        }

        // Show the install prompt
        deferredPrompt.prompt();

        // Wait for user choice
        deferredPrompt.userChoice.then((choiceResult) => {
            if (choiceResult.outcome === 'accepted') {
                console.log('✅ PWA: User accepted installation');

                // Show success message
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Installation en cours!',
                        text: 'L\'application sera disponible sur votre écran d\'accueil',
                        timer: 2000,
                        confirmButtonColor: '#8b5cf6'
                    });
                }
            } else {
                console.log('❌ PWA: User dismissed installation');
            }

            // Clear the prompt
            deferredPrompt = null;
        });
    };

    /**
     * Show install instructions for iOS/other browsers
     */
    function showInstallInstructions() {
        const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
        const isAndroid = /Android/.test(navigator.userAgent);

        let instructions = '';

        if (isIOS) {
            instructions = `
                <div style="text-align: left;">
                    <p>Pour installer sur iOS:</p>
                    <ol>
                        <li>Appuyez sur le bouton <strong>Partager</strong> (⬆️)</li>
                        <li>Sélectionnez <strong>"Sur l'écran d'accueil"</strong></li>
                        <li>Appuyez sur <strong>Ajouter</strong></li>
                    </ol>
                </div>
            `;
        } else if (isAndroid) {
            instructions = `
                <div style="text-align: left;">
                    <p>Pour installer sur Android:</p>
                    <ol>
                        <li>Appuyez sur le menu Chrome (⋮)</li>
                        <li>Sélectionnez <strong>"Ajouter à l'écran d'accueil"</strong></li>
                        <li>Confirmez l'installation</li>
                    </ol>
                </div>
            `;
        } else {
            instructions = '<p>Cette application peut être installée comme une application native!</p>';
        }

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '📱 Installer l\'application',
                html: instructions,
                icon: 'info',
                confirmButtonColor: '#8b5cf6',
                confirmButtonText: 'Compris!'
            });
        }
    }

    /**
     * Check if already installed
     */
    window.addEventListener('appinstalled', () => {
        console.log('✅ PWA: App installed successfully');
        deferredPrompt = null;

        // Hide install button
        const installBtn = document.getElementById('pwa-install-btn');
        if (installBtn) {
            installBtn.style.display = 'none';
        }

        // Show success message
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Installé!',
                text: 'L\'application est maintenant disponible sur votre écran d\'accueil',
                timer: 2500,
                confirmButtonColor: '#8b5cf6'
            });
        }
    });

    /**
     * Handle service worker updates
     */
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.addEventListener('controllerchange', () => {
            console.log('🔄 PWA: New version available, reloading...');
            window.location.reload();
        });
    }

    console.log('🎯 PWA: Installation handler loaded');
})();
