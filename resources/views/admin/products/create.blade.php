@extends('layouts.admin')

@section('title', 'Add New Product')

@section('content')
<div class="container py-4">
  <h3 class="fw-bold mb-4">Add New Product</h3>

  @if ($errors->any())
      <div class="alert alert-danger">
          <ul class="mb-0">
              @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
          </ul>
      </div>
  @endif

  <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
      @csrf
      <div class="row g-3">
          <div class="col-md-6">
              <label class="form-label">Product Name</label>
              <input type="text" name="name" class="form-control" required>
          </div>

          <div class="col-md-6">
              <label class="form-label">Price ($)</label>
              <input type="number" name="price" step="0.01" class="form-control" required>
          </div>

          <div class="col-md-12">
              <label class="form-label">Description</label>
              <textarea name="description" class="form-control" rows="3"></textarea>
          </div>

          <div class="col-md-6">
              <label class="form-label">Product Image</label>
              <input type="file" name="image" class="form-control" accept="image/*">
          </div>

          {{-- ✅ 3D Model Upload with Preview --}}
          <div class="col-md-6">
              <label class="form-label">3D Model (.stl)</label>
              <div id="dropZone" class="border border-2 p-3 text-center rounded bg-light"
                   style="cursor:pointer; border-style: dashed; transition: background-color .2s;">
                  <p class="mb-0 text-muted">Drag & drop your .stl file here or click to upload</p>
                  <input type="file" id="model3D" name="model_3d" accept=".stl" class="d-none">
              </div>
              <small class="text-muted">Supported format: .stl</small>
          </div>

          {{-- ✅ Live 3D Preview --}}
          <div class="col-12 mt-4">
              <div class="bg-dark rounded" style="height:400px; position:relative;">
                  <canvas id="previewCanvas" style="width:100%; height:100%; display:block;"></canvas>
              </div>
          </div>

          <div class="col-12 text-end mt-4">
              <button type="submit" class="btn btn-success px-4 shadow-sm">Save Product</button>
          </div>
      </div>
  </form>
</div>

@endsection

@section('scripts')
<script src="https://unpkg.com/three@0.160.0/build/three.min.js"></script>
<script src="https://unpkg.com/three@0.160.0/examples/js/loaders/STLLoader.js"></script>
<script src="https://unpkg.com/three@0.160.0/examples/js/controls/OrbitControls.js"></script>

<script>
let scene, camera, renderer, controls, mesh;

// 🟢 Initialize Three.js Preview
function initPreview() {
    const canvas = document.getElementById('previewCanvas');
    scene = new THREE.Scene();
    scene.background = new THREE.Color(0x1a1a1a);

    camera = new THREE.PerspectiveCamera(60, canvas.clientWidth / canvas.clientHeight, 0.1, 1000);
    camera.position.set(1, 1, 2);

    renderer = new THREE.WebGLRenderer({ canvas, antialias: true });
    renderer.setSize(canvas.clientWidth, canvas.clientHeight);

    controls = new THREE.OrbitControls(camera, renderer.domElement);
    controls.enableDamping = true;
    controls.dampingFactor = 0.05;

    const light1 = new THREE.DirectionalLight(0xffffff, 1);
    light1.position.set(1, 1, 1);
    scene.add(light1);
    scene.add(new THREE.AmbientLight(0xffffff, 0.4));

    animate();
}

function animate() {
    requestAnimationFrame(animate);
    controls.update();
    renderer.render(scene, camera);
}

function loadSTL(file) {
    const loader = new THREE.STLLoader();
    const reader = new FileReader();
    reader.onload = (e) => {
        const arrayBuffer = e.target.result;
        const geometry = loader.parse(arrayBuffer);
        if (mesh) {
            scene.remove(mesh);
        }
        const material = new THREE.MeshStandardMaterial({ color: 0x2DAA9E });
        mesh = new THREE.Mesh(geometry, material);

        geometry.computeBoundingBox();
        const center = new THREE.Vector3();
        geometry.boundingBox.getCenter(center);
        mesh.position.sub(center);

        scene.add(mesh);
    };
    reader.readAsArrayBuffer(file);
}

// 🟢 Drag & Drop
const dropZone = document.getElementById('dropZone');
const fileInput = document.getElementById('model3D');

dropZone.addEventListener('click', () => fileInput.click());
dropZone.addEventListener('dragover', e => {
    e.preventDefault();
    dropZone.classList.add('bg-secondary', 'text-white');
});
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('bg-secondary', 'text-white'));
dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.classList.remove('bg-secondary', 'text-white');
    const file = e.dataTransfer.files[0];
    if (file && file.name.endsWith('.stl')) {
        fileInput.files = e.dataTransfer.files;
        loadSTL(file);
    } else {
        alert('Please upload a valid .stl file.');
    }
});
fileInput.addEventListener('change', e => {
    const file = e.target.files[0];
    if (file && file.name.endsWith('.stl')) {
        loadSTL(file);
    } else {
        alert('Please select a valid .stl file.');
    }
});

window.addEventListener('load', initPreview);
</script>
@endsection
