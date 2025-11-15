<div class="modal fade" id="modelViewerModal" tabindex="-1" aria-labelledby="modelViewerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content bg-dark text-white overflow-hidden">
      <div class="modal-header border-0">
        <h5 class="modal-title" id="modelViewerModalLabel">3D Model Viewer</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0 position-relative">
        <div id="stlLoader" class="position-absolute top-50 start-50 translate-middle text-center" style="z-index:10;">
          <div class="spinner-border text-light" role="status"></div>
          <div class="small mt-2 text-muted">Loading 3D model…</div>
        </div>
        <canvas id="stlCanvas" style="width:100%; height:70vh; display:block; background:#1a1a1a;"></canvas>
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
  <div class="visually-hidden" id="stlNoUrl">No 3D model URL found for this product.</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/three@0.160.0/build/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.160.0/examples/js/controls/OrbitControls.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.160.0/examples/js/loaders/STLLoader.js"></script>

<script>
// Global viewer state (scoped to this partial include)
let scene, camera, renderer, controls, mesh, rafId;

function disposeViewer() {
  try {
    if (rafId) cancelAnimationFrame(rafId);
    rafId = null;
    if (controls) { controls.dispose(); controls = null; }
    if (mesh) {
      if (mesh.geometry) mesh.geometry.dispose();
      if (mesh.material) mesh.material.dispose();
      if (scene) scene.remove(mesh);
      mesh = null;
    }
    if (renderer) {
      renderer.dispose();
      renderer.forceContextLoss?.();
      renderer.domElement = null;
      renderer = null;
    }
    scene = null; camera = null;
  } catch (e) {
    console.error('[3D] dispose error:', e);
  }
}

function frameObject(geometry) {
  geometry.computeBoundingSphere();
  const bs = geometry.boundingSphere;
  const radius = Math.max(bs?.radius || 1, 0.001);
  const fov = (camera.fov * Math.PI) / 180;
  const dist = radius / Math.tan(fov / 2);
  camera.position.set(0, 0, dist * 1.6);
  camera.near = Math.max(dist - radius * 4, 0.001);
  camera.far = dist + radius * 10;
  camera.updateProjectionMatrix();
  controls.target.set(0, 0, 0);
  controls.update();
}

function init3DViewer(modelUrl) {
  // Defer actual loading to when modal is shown to ensure proper sizes
  const modalEl = document.getElementById('modelViewerModal');
  modalEl.dataset.modelUrl = modelUrl || '';
}

function startViewerWithUrl(modelUrl) {
  const canvas = document.getElementById('stlCanvas');
  const loaderEl = document.getElementById('stlLoader');
  const noUrlEl  = document.getElementById('stlNoUrl');

  disposeViewer();

  // Init Three.js
  scene = new THREE.Scene();
  scene.background = new THREE.Color(0x1a1a1a);

  const rect = canvas.getBoundingClientRect();
  const aspect = Math.max(rect.width, 1) / Math.max(rect.height, 1);
  camera = new THREE.PerspectiveCamera(60, aspect, 0.1, 2000);

  renderer = new THREE.WebGLRenderer({ canvas, antialias: true });
  renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
  renderer.setSize(rect.width || 1, rect.height || 1, false);

  // Lighting
  const ambientLight = new THREE.AmbientLight(0xffffff, 0.8);
  const directionalLight = new THREE.DirectionalLight(0xffffff, 1);
  directionalLight.position.set(1, 1, 1).normalize();
  scene.add(ambientLight, directionalLight);

  // Controls
  controls = new THREE.OrbitControls(camera, renderer.domElement);
  controls.enableDamping = true;
  controls.dampingFactor = 0.08;
  controls.autoRotate = true;
  controls.autoRotateSpeed = 0.75;

  // Load STL
  const safeUrl = (() => { try { return encodeURI(modelUrl || ''); } catch(e) { return modelUrl || ''; } })();
  if (!safeUrl) {
    console.error('[3D] No model URL provided');
    if (loaderEl) loaderEl.style.display = 'none';
    if (noUrlEl) noUrlEl.classList.remove('visually-hidden');
    return;
  }

  if (loaderEl) loaderEl.style.display = 'block';
  if (noUrlEl)  noUrlEl.classList.add('visually-hidden');

  // Fetch as ArrayBuffer then parse → reliable on same-origin
  fetch(safeUrl, { cache: 'no-store' })
    .then(res => { if (!res.ok) throw new Error('HTTP ' + res.status); return res.arrayBuffer(); })
    .then(buffer => {
      const loader = new THREE.STLLoader();
      const geometry = loader.parse(buffer);

      // Validate
      const pos = geometry.attributes && geometry.attributes.position;
      if (!pos || pos.count === 0) throw new Error('Empty STL geometry');

      // Center geometry and add mesh
      geometry.computeBoundingBox();
      const center = geometry.boundingBox.getCenter(new THREE.Vector3());
      geometry.translate(-center.x, -center.y, -center.z);
      geometry.computeVertexNormals();

      const material = new THREE.MeshStandardMaterial({ color: 0x2DAA9E, metalness: 0.3, roughness: 0.4, side: THREE.DoubleSide });
      mesh = new THREE.Mesh(geometry, material);
      scene.add(mesh);

      // Frame
      frameObject(geometry);

      if (loaderEl) loaderEl.style.display = 'none';
    })
    .catch(err => {
      console.error('[3D] Failed to load STL:', err);
      if (loaderEl) loaderEl.style.display = 'none';
      // Show a simple placeholder cube to prove viewer works
      const g = new THREE.BoxGeometry(1,1,1);
      const m = new THREE.MeshStandardMaterial({ color: 0xff5555 });
      mesh = new THREE.Mesh(g, m);
      scene.add(mesh);
      frameObject(g);
    });

  // Resize handling
  const onResize = () => {
    if (!renderer || !camera) return;
    const { clientWidth: w, clientHeight: h } = canvas;
    camera.aspect = Math.max(w, 1) / Math.max(h, 1);
    camera.updateProjectionMatrix();
    renderer.setSize(Math.max(w, 1), Math.max(h, 1), false);
  };
  window.addEventListener('resize', onResize);

  // Animate
  const animate = () => {
    rafId = requestAnimationFrame(animate);
    controls.update();
    renderer.render(scene, camera);
  };
  animate();
}

// Bootstrap modal integration
(function setupModalHooks(){
  const modalEl = document.getElementById('modelViewerModal');
  if (!modalEl) return;

  // jQuery (if present) – load on shown
  if (window.$ && typeof window.$.fn?.modal === 'function') {
    $('#modelViewerModal').on('shown.bs.modal', function (event) {
      const trigger = event.relatedTarget;
      const urlFromTrigger = trigger ? trigger.getAttribute('data-model-url') : null;
      const url = urlFromTrigger || modalEl.dataset.modelUrl || '';
      startViewerWithUrl(url);
    });
    $('#modelViewerModal').on('hidden.bs.modal', function () {
      disposeViewer();
    });
  }

  // Vanilla Bootstrap events
  modalEl.addEventListener('shown.bs.modal', (event) => {
    const trigger = event.relatedTarget;
    const urlFromTrigger = trigger ? trigger.getAttribute('data-model-url') : null;
    const url = urlFromTrigger || modalEl.dataset.modelUrl || '';
    startViewerWithUrl(url);
  });
  modalEl.addEventListener('hidden.bs.modal', () => {
    disposeViewer();
  });
})();
</script>
