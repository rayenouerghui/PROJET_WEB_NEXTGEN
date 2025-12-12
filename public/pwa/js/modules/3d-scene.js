/**
 * 3D Scene - Simple Canvas-based Route Visualization
 * Fallback implementation without Three.js dependency
 */

class Scene3D {
    constructor() {
        this.canvas = null;
        this.ctx = null;
        this.initialized = false;
        this.animationId = null;
        this.truckPosition = 0;
        this.rotation = 0;
    }

    init(delivery) {
        console.log('🎬 3D Scene initializing (Canvas fallback)...', delivery);

        const container = document.getElementById('3d-container');
        container.innerHTML = '';

        // Create canvas
        this.canvas = document.createElement('canvas');
        this.canvas.width = container.clientWidth;
        this.canvas.height = container.clientHeight;
        this.canvas.style.display = 'block';
        this.canvas.style.width = '100%';
        this.canvas.style.height = '100%';
        this.canvas.style.background = '#0a0a1a';

        container.appendChild(this.canvas);
        this.ctx = this.canvas.getContext('2d');

        // Add instructions
        this.addInstructions(container);

        // Start animation
        this.initialized = true;
        this.animate();

        console.log('✅ 3D Scene ready (Canvas mode)!');
    }

    addInstructions(container) {
        const instructions = document.createElement('div');
        instructions.style.cssText = `
            position: absolute;
            top: 20px;
            left: 20px;
            color: white;
            font-family: Inter, sans-serif;
            font-size: 14px;
            background: rgba(0, 0, 0, 0.7);
            padding: 15px;
            border-radius: 10px;
            backdrop-filter: blur(10px);
        `;
        instructions.innerHTML = `
            <div style="margin-bottom: 8px;"><strong>🎮 Vue 3D Route:</strong></div>
            <div>🚚 Camion en livraison</div>
            <div>📍 Trajet dynamique</div>
            <div>🎨 Animation en temps réel</div>
        `;
        container.appendChild(instructions);
    }

    animate() {
        if (!this.initialized || !this.ctx) return;

        this.animationId = requestAnimationFrame(() => this.animate());

        const w = this.canvas.width;
        const h = this.canvas.height;
        const ctx = this.ctx;

        // Clear canvas
        ctx.fillStyle = '#0a0a1a';
        ctx.fillRect(0, 0, w, h);

        // Draw grid
        this.drawGrid(ctx, w, h);

        // Draw route
        this.drawRoute(ctx, w, h);

        // Draw truck
        this.drawTruck(ctx, w, h);

        // Draw markers
        this.drawMarkers(ctx, w, h);

        // Update animation
        this.truckPosition += 0.003;
        if (this.truckPosition > 1) this.truckPosition = 0;
        this.rotation += 0.01;
    }

    drawGrid(ctx, w, h) {
        ctx.strokeStyle = 'rgba(102, 126, 234, 0.15)';
        ctx.lineWidth = 1;

        const gridSize = 50;

        for (let x = 0; x < w; x += gridSize) {
            ctx.beginPath();
            ctx.moveTo(x, 0);
            ctx.lineTo(x, h);
            ctx.stroke();
        }

        for (let y = 0; y < h; y += gridSize) {
            ctx.beginPath();
            ctx.moveTo(0, y);
            ctx.lineTo(w, y);
            ctx.stroke();
        }
    }

    drawRoute(ctx, w, h) {
        const cx = w / 2;
        const cy = h / 2;

        ctx.strokeStyle = '#667eea';
        ctx.lineWidth = 8;
        ctx.shadowColor = '#667eea';
        ctx.shadowBlur = 20;

        ctx.beginPath();

        for (let i = 0; i <= 100; i++) {
            const t = i / 100;
            const x = cx - 300 + t * 600;
            const y = cy + Math.sin(t * Math.PI * 4) * 100;

            if (i === 0) {
                ctx.moveTo(x, y);
            } else {
                ctx.lineTo(x, y);
            }
        }

        ctx.stroke();
        ctx.shadowBlur = 0;
    }

    drawTruck(ctx, w, h) {
        const cx = w / 2;
        const cy = h / 2;

        // Calculate truck position along route
        const t = this.truckPosition;
        const x = cx - 300 + t * 600;
        const y = cy + Math.sin(t * Math.PI * 4) * 100;

        // Save context
        ctx.save();
        ctx.translate(x, y);

        // Calculate rotation based on route direction
        const nextT = t + 0.01;
        const nextX = cx - 300 + nextT * 600;
        const nextY = cy + Math.sin(nextT * Math.PI * 4) * 100;
        const angle = Math.atan2(nextY - y, nextX - x);
        ctx.rotate(angle);

        // Draw truck body
        ctx.fillStyle = '#667eea';
        ctx.shadowColor = '#667eea';
        ctx.shadowBlur = 15;
        ctx.fillRect(-30, -15, 60, 30);

        // Draw truck cabin
        ctx.fillStyle = '#4f46e5';
        ctx.fillRect(15, -12, 20, 24);

        // Draw wheels
        ctx.fillStyle = '#222';
        ctx.shadowBlur = 5;
        ctx.beginPath();
        ctx.arc(-15, 15, 6, 0, Math.PI * 2);
        ctx.fill();
        ctx.beginPath();
        ctx.arc(10, 15, 6, 0, Math.PI * 2);
        ctx.fill();

        ctx.restore();
        ctx.shadowBlur = 0;
    }

    drawMarkers(ctx, w, h) {
        const cx = w / 2;
        const cy = h / 2;

        // Start marker (green)
        const startX = cx - 300;
        const startY = cy;

        ctx.fillStyle = '#10b981';
        ctx.shadowColor = '#10b981';
        ctx.shadowBlur = 20;
        ctx.beginPath();
        ctx.arc(startX, startY, 15, 0, Math.PI * 2);
        ctx.fill();

        ctx.fillStyle = 'white';
        ctx.font = 'bold 20px Inter';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText('🏁', startX, startY);

        // End marker (red)
        const endX = cx + 300;
        const endY = cy;

        ctx.fillStyle = '#ef4444';
        ctx.shadowColor = '#ef4444';
        ctx.shadowBlur = 20;
        ctx.beginPath();
        ctx.arc(endX, endY, 15, 0, Math.PI * 2);
        ctx.fill();

        ctx.fillStyle = 'white';
        ctx.fillText('🎯', endX, endY);

        ctx.shadowBlur = 0;
    }

    destroy() {
        console.log('🧹 Destroying 3D scene');
        this.initialized = false;

        if (this.animationId) {
            cancelAnimationFrame(this.animationId);
        }

        const container = document.getElementById('3d-container');
        if (container) {
            container.innerHTML = '';
        }
    }
}
