/**
 * Camera Viewer - Canvas-based Package Visualization
 * Works on both PC webcam and mobile cameras
 */

class CameraViewer {
    constructor() {
        this.stream = null;
        this.isActive = false;
        this.video = null;
        this.canvas = null;
        this.ctx = null;
        this.packageSize = 'medium';
        this.currentFacingMode = 'user'; // Start with front/PC webcam
        this.availableCameras = [];
    }

    async activate() {
        console.log('📸 Camera viewer activating...');

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            alert('Votre navigateur ne supporte pas l\'accès à la caméra');
            return;
        }

        await this.detectCameras();
        await this.startCamera();
    }

    async detectCameras() {
        try {
            const devices = await navigator.mediaDevices.enumerateDevices();
            this.availableCameras = devices.filter(device => device.kind === 'videoinput');
            console.log('📹 Available cameras:', this.availableCameras.length);
        } catch (err) {
            console.warn('Could not enumerate devices:', err);
        }
    }

    async startCamera() {
        try {
            const constraints = {
                video: {
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                }
            };

            // Add facingMode only if multiple cameras (mobile)
            if (this.availableCameras.length > 1) {
                constraints.video.facingMode = this.currentFacingMode;
            }

            this.stream = await navigator.mediaDevices.getUserMedia(constraints);
            console.log('✅ Camera access granted');
        } catch (err) {
            console.error('Camera error:', err);
            alert('Permission caméra refusée');
            return;
        }

        this.isActive = true;
        this.initCameraView();
    }

    initCameraView() {
        const container = document.getElementById('camera-container');
        container.style.position = 'relative';
        container.style.width = '100%';
        container.style.height = '100%';
        container.style.overflow = 'hidden';
        container.style.background = '#000';

        // Create video
        this.video = document.createElement('video');
        this.video.setAttribute('playsinline', '');
        this.video.autoplay = true;
        this.video.muted = true;
        this.video.style.width = '100%';
        this.video.style.height = '100%';
        this.video.style.objectFit = 'cover';

        // Create canvas
        this.canvas = document.createElement('canvas');
        this.canvas.style.position = 'absolute';
        this.canvas.style.top = '0';
        this.canvas.style.left = '0';
        this.canvas.style.width = '100%';
        this.canvas.style.height = '100%';
        this.canvas.style.pointerEvents = 'none';
        this.ctx = this.canvas.getContext('2d');

        container.innerHTML = '';
        container.appendChild(this.video);
        container.appendChild(this.canvas);

        this.video.srcObject = this.stream;

        this.video.onloadedmetadata = () => {
            this.canvas.width = this.video.videoWidth;
            this.canvas.height = this.video.videoHeight;
            console.log('📹 Video:', this.video.videoWidth, 'x', this.video.videoHeight);
            this.drawPackageOverlay();
        };

        this.addCameraControls();
    }

    drawPackageOverlay() {
        if (!this.isActive) return;

        const animate = () => {
            if (!this.isActive) return;

            this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);

            const sizes = {
                small: { width: 150, height: 180, label: '20cm × 25cm × 15cm' },
                medium: { width: 200, height: 250, label: '30cm × 40cm × 20cm' },
                large: { width: 280, height: 350, label: '45cm × 60cm × 30cm' }
            };

            const size = sizes[this.packageSize];
            const centerX = this.canvas.width / 2;
            const centerY = this.canvas.height / 2;

            // Semi-transparent fill
            this.ctx.fillStyle = 'rgba(102, 126, 234, 0.1)';
            this.ctx.fillRect(centerX - size.width / 2, centerY - size.height / 2, size.width, size.height);

            // Box outline
            this.ctx.strokeStyle = '#667eea';
            this.ctx.lineWidth = 3;
            this.ctx.shadowColor = '#667eea';
            this.ctx.shadowBlur = 15;
            this.ctx.strokeRect(centerX - size.width / 2, centerY - size.height / 2, size.width, size.height);

            // Corner markers
            this.drawCornerMarkers(centerX, centerY, size.width, size.height);

            // Labels
            this.ctx.font = 'bold 20px Inter';
            this.ctx.fillStyle = '#ffffff';
            this.ctx.shadowColor = '#000000';
            this.ctx.shadowBlur = 10;
            this.ctx.textAlign = 'center';
            this.ctx.fillText('📦 Votre Colis', centerX, centerY - size.height / 2 - 40);

            this.ctx.font = 'bold 16px Inter';
            this.ctx.fillText(size.label, centerX, centerY - size.height / 2 - 20);

            this.ctx.font = '14px Inter';
            this.ctx.fillStyle = '#a0aec0';
            this.ctx.fillText('Approximation de la taille réelle', centerX, centerY + size.height / 2 + 25);

            this.ctx.shadowBlur = 0;

            requestAnimationFrame(animate);
        };

        animate();
    }

    drawCornerMarkers(cx, cy, width, height) {
        const markerSize = 25;
        this.ctx.strokeStyle = '#10b981';
        this.ctx.lineWidth = 4;
        this.ctx.shadowColor = '#10b981';
        this.ctx.shadowBlur = 10;

        // Four corners
        this.ctx.beginPath();
        this.ctx.moveTo(cx - width / 2, cy - height / 2 + markerSize);
        this.ctx.lineTo(cx - width / 2, cy - height / 2);
        this.ctx.lineTo(cx - width / 2 + markerSize, cy - height / 2);
        this.ctx.stroke();

        this.ctx.beginPath();
        this.ctx.moveTo(cx + width / 2 - markerSize, cy - height / 2);
        this.ctx.lineTo(cx + width / 2, cy - height / 2);
        this.ctx.lineTo(cx + width / 2, cy - height / 2 + markerSize);
        this.ctx.stroke();

        this.ctx.beginPath();
        this.ctx.moveTo(cx - width / 2, cy + height / 2 - markerSize);
        this.ctx.lineTo(cx - width / 2, cy + height / 2);
        this.ctx.lineTo(cx - width / 2 + markerSize, cy + height / 2);
        this.ctx.stroke();

        this.ctx.beginPath();
        this.ctx.moveTo(cx + width / 2 - markerSize, cy + height / 2);
        this.ctx.lineTo(cx + width / 2, cy + height / 2);
        this.ctx.lineTo(cx + width / 2, cy + height / 2 - markerSize);
        this.ctx.stroke();
    }

    addCameraControls() {
        const controls = document.getElementById('camera-controls');
        let html = '<div class="camera-toolbar">';

        // Size buttons
        html += `
            <button onclick="cameraViewer.changeSize('small')">
                <i class="ri-box-3-line"></i> Petit
            </button>
            <button onclick="cameraViewer.changeSize('medium')" style="background: #667eea;">
                <i class="ri-box-2-line"></i> Moyen
            </button>
            <button onclick="cameraViewer.changeSize('large')">
                <i class="ri-box-1-line"></i> Grand
            </button>
        `;

        // Switch camera (only if multiple)
        if (this.availableCameras.length > 1) {
            html += `
                <button onclick="cameraViewer.switchCamera()" style="background: #f59e0b;" title="Changer de caméra">
                    <i class="ri-camera-switch-line"></i>
                </button>
            `;
        }

        // Snapshot & close
        html += `
            <button onclick="cameraViewer.takeSnapshot()" style="background: #10b981;">
                <i class="ri-camera-line"></i> Photo
            </button>
            <button onclick="app.closeCameraView()" style="background: #ef4444;">
                <i class="ri-close-line"></i> Fermer
            </button>
        </div>`;

        controls.innerHTML = html;
    }

    async switchCamera() {
        console.log('🔄 Switching camera...');

        this.currentFacingMode = this.currentFacingMode === 'user' ? 'environment' : 'user';

        if (this.stream) {
            this.stream.getTracks().forEach(track => track.stop());
        }

        await this.startCamera();
        console.log('✅ Switched to:', this.currentFacingMode);
    }

    changeSize(size) {
        if (['small', 'medium', 'large'].includes(size)) {
            this.packageSize = size;
            console.log('📦 Size:', size);

            const buttons = document.querySelectorAll('.camera-toolbar button');
            buttons.forEach((btn, idx) => {
                if (idx < 3) btn.style.background = 'var(--primary)';
            });

            const sizeIndex = { small: 0, medium: 1, large: 2 }[size];
            buttons[sizeIndex].style.background = '#667eea';
        }
    }

    takeSnapshot() {
        if (!this.video || !this.canvas) return;

        console.log('📸 Snapshot...');

        const snap = document.createElement('canvas');
        snap.width = this.video.videoWidth;
        snap.height = this.video.videoHeight;
        const snapCtx = snap.getContext('2d');

        snapCtx.drawImage(this.video, 0, 0);
        snapCtx.drawImage(this.canvas, 0, 0);

        snap.toBlob((blob) => {
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.download = `nextgen-package-${Date.now()}.png`;
            link.href = url;
            link.click();
            setTimeout(() => URL.revokeObjectURL(url), 100);
            console.log('✅ Saved!');
        }, 'image/png');
    }

    deactivate() {
        console.log('📸 Deactivating...');
        this.isActive = false;

        if (this.stream) {
            this.stream.getTracks().forEach(track => track.stop());
            this.stream = null;
        }

        const container = document.getElementById('camera-container');
        if (container) container.innerHTML = '';

        const controls = document.getElementById('camera-controls');
        if (controls) controls.innerHTML = '';

        this.video = null;
        this.canvas = null;
        this.ctx = null;
    }
}

let cameraViewer;
