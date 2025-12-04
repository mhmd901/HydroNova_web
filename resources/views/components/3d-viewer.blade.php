<div class="stl-viewer-overlay" data-viewer-overlay hidden>
    <div class="stl-viewer-header">
        <div>
            <h4 class="mb-0 text-white" data-viewer-title>3D Viewer</h4>
            <small class="text-muted">Drag to orbit, scroll to zoom, right-click to pan.</small>
        </div>
        <button type="button" class="btn btn-outline-light btn-sm" data-viewer-close>
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <div class="stl-viewer-canvas">
        <canvas id="fullscreenStlCanvas"></canvas>
        <div class="stl-viewer-status" data-viewer-status>Loading…</div>
    </div>
</div>

<style>
.mini-3d-preview {
    position: relative;
    width: 100%;
    height: 150px;
    border-radius: 12px;
    background: #f4f7fb;
    overflow: hidden;
}
.mini-3d-preview--public {
    height: 180px;
}
.mini-3d-preview canvas {
    width: 100%;
    height: 100%;
    display: block;
}
.mini-3d-placeholder {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    color: #6c757d;
    background: linear-gradient(135deg, rgba(255,255,255,0.9), rgba(244,247,251,0.9));
}
.stl-viewer-overlay {
    position: fixed;
    inset: 0;
    background: rgba(2, 10, 24, 0.92);
    backdrop-filter: blur(4px);
    z-index: 1090;
    display: flex;
    flex-direction: column;
    padding: 24px;
}
.stl-viewer-overlay[hidden] {
    display: none;
}
.stl-viewer-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}
.stl-viewer-canvas {
    position: relative;
    flex: 1;
    border-radius: 18px;
    overflow: hidden;
    background: radial-gradient(circle, rgba(9,20,40,0.85) 0%, rgba(2,8,20,1) 100%);
}
.stl-viewer-canvas canvas {
    width: 100%;
    height: 100%;
    display: block;
}
.stl-viewer-status {
    position: absolute;
    top: 16px;
    right: 20px;
    padding: 6px 14px;
    border-radius: 999px;
    background: rgba(0,0,0,0.45);
    color: #f8f9fa;
    font-size: 0.85rem;
}
</style>

<script type="module">
import * as THREE from 'https://unpkg.com/three@0.161.0/build/three.module.js';
import { OrbitControls } from 'https://unpkg.com/three@0.161.0/examples/jsm/controls/OrbitControls.js';
import { STLLoader } from 'https://unpkg.com/three@0.161.0/examples/jsm/loaders/STLLoader.js';

class BaseStlRenderer {
    constructor(canvas) {
        this.canvas = canvas;
        this.loader = new STLLoader();
    }

    prepareScene(options = {}) {
        if (this.renderer) {
            return;
        }
        const width = this.canvas.clientWidth || this.canvas.parentElement.clientWidth;
        const height = this.canvas.clientHeight || this.canvas.parentElement.clientHeight || 300;

        this.scene = new THREE.Scene();
        if (options.backgroundColor !== undefined) {
            this.scene.background = new THREE.Color(options.backgroundColor);
        }

        this.camera = new THREE.PerspectiveCamera(45, width / height, 0.1, 2000);
        this.camera.position.set(0, 0.8, 2.5);

        this.renderer = new THREE.WebGLRenderer({ canvas: this.canvas, antialias: true, alpha: options.alpha ?? false });
        this.renderer.setPixelRatio(window.devicePixelRatio);
        this.renderer.setSize(width, height);

        this.controls = new OrbitControls(this.camera, this.renderer.domElement);
        this.controls.enableDamping = true;
        this.controls.enablePan = options.enablePan ?? true;

        const ambient = new THREE.AmbientLight(0xffffff, 0.6);
        const key = new THREE.DirectionalLight(0xffffff, 0.8);
        key.position.set(2, 2, 2);
        const rim = new THREE.DirectionalLight(0xffffff, 0.4);
        rim.position.set(-2, -1, -2);
        this.scene.add(ambient, key, rim);
    }

    fitGeometry(geometry, color = 0x2f7df6) {
        geometry.computeVertexNormals();
        geometry.center();
        geometry.computeBoundingBox();

        if (this.mesh) {
            this.scene.remove(this.mesh);
        }

        const size = new THREE.Vector3();
        geometry.boundingBox.getSize(size);
        const maxDim = Math.max(size.x, size.y, size.z) || 1;
        const scale = 1.8 / maxDim;

        this.mesh = new THREE.Mesh(
            geometry,
            new THREE.MeshStandardMaterial({
                color,
                metalness: 0.2,
                roughness: 0.35,
            })
        );
        this.mesh.scale.setScalar(scale);
        this.scene.add(this.mesh);
    }
}

class MiniStlPreview extends BaseStlRenderer {
    constructor(container) {
        super(container.querySelector('canvas') ?? container);
        this.container = container;
        this.placeholder = container.querySelector('.mini-3d-placeholder');
        this.rotationSpeed = 0.01;
        this.prepareScene({ backgroundColor: 0xf4f7fb, alpha: false, enablePan: false });
        this.animate();
    }

    load(url) {
        if (!url) {
            return;
        }
        this.loader.load(
            url,
            (geometry) => {
                this.fitGeometry(geometry, 0x1f7aed);
                if (this.placeholder) {
                    this.placeholder.style.display = 'none';
                }
            },
            undefined,
            () => {
                if (this.placeholder) {
                    this.placeholder.textContent = 'Unable to load STL';
                }
            }
        );
    }

    animate() {
        requestAnimationFrame(() => this.animate());
        if (this.mesh) {
            this.mesh.rotation.y += this.rotationSpeed;
        }
        if (this.controls) {
            this.controls.update();
        }
        if (this.renderer && this.scene && this.camera) {
            this.renderer.render(this.scene, this.camera);
        }
    }
}

class FullscreenStlViewer extends BaseStlRenderer {
    constructor(canvas, overlay, titleEl, statusEl) {
        super(canvas);
        this.overlay = overlay;
        this.titleEl = titleEl;
        this.statusEl = statusEl;
        this.prepareScene({ backgroundColor: 0x050c1c, alpha: false });
        window.addEventListener('resize', () => this.handleResize());
        this.animate();
    }

    animate() {
        requestAnimationFrame(() => this.animate());
        if (this.controls) {
            this.controls.update();
        }
        if (this.renderer && this.scene && this.camera) {
            this.renderer.render(this.scene, this.camera);
        }
    }

    handleResize() {
        if (!this.renderer || !this.camera) {
            return;
        }
        const width = this.canvas.clientWidth || this.canvas.parentElement.clientWidth;
        const height = this.canvas.clientHeight || this.canvas.parentElement.clientHeight;
        this.camera.aspect = width / height;
        this.camera.updateProjectionMatrix();
        this.renderer.setSize(width, height);
    }

    open(url, title = '3D Viewer') {
        if (!url) {
            return;
        }
        this.overlay.hidden = false;
        this.overlay.setAttribute('aria-hidden', 'false');
        this.titleEl.textContent = title;
        this.statusEl.textContent = 'Loading STL...';

        this.loader.load(
            url,
            (geometry) => {
                this.fitGeometry(geometry, 0x4fd8ff);
                this.statusEl.textContent = 'Loaded';
            },
            undefined,
            () => {
                this.statusEl.textContent = 'Failed to load STL';
            }
        );
    }

    close() {
        this.overlay.hidden = true;
        this.overlay.setAttribute('aria-hidden', 'true');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const overlay = document.querySelector('[data-viewer-overlay]');
    if (!overlay) {
        return;
    }

    const canvas = overlay.querySelector('#fullscreenStlCanvas');
    const titleEl = overlay.querySelector('[data-viewer-title]');
    const statusEl = overlay.querySelector('[data-viewer-status]');
    const closeBtn = overlay.querySelector('[data-viewer-close]');

    const fullscreenViewer = new FullscreenStlViewer(canvas, overlay, titleEl, statusEl);

    closeBtn?.addEventListener('click', () => fullscreenViewer.close());
    overlay?.addEventListener('click', (event) => {
        if (event.target === overlay) {
            fullscreenViewer.close();
        }
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && overlay && !overlay.hidden) {
            fullscreenViewer.close();
        }
    });

    document.querySelectorAll('[data-open-viewer]').forEach((button) => {
        button.addEventListener('click', () => {
            fullscreenViewer.open(button.dataset.modelUrl, button.dataset.modelName);
        });
    });

    document.querySelectorAll('[data-mini-viewer]').forEach((container) => {
        const preview = new MiniStlPreview(container);
        preview.load(container.dataset.modelUrl);
    });
});
</script>
