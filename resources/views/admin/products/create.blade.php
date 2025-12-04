@extends('layouts.admin')

@section('title', 'Create Product')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Create Product</h3>
            <p class="text-muted mb-0">Upload details, images, and instantly preview the STL model before saving.</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Products
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="card shadow-sm border-0">
        @csrf
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Product Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Price ($)</label>
                    <input type="number" name="price" step="0.01" class="form-control" value="{{ old('price') }}" min="0" required>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea name="description" rows="4" class="form-control">{{ old('description') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Product Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <small class="text-muted">JPEG, PNG, JPG, WEBP up to 4 MB.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">3D Model (.stl)</label>
                    <label id="modelDropZone" for="model3dInput" class="model-drop-zone mb-0">
                        <input type="file" id="model3dInput" name="model_3d" accept=".stl" class="d-none">
                        <i class="bi bi-cloud-arrow-up fs-1 text-primary mb-2"></i>
                        <p class="mb-1">Drag & drop STL file here</p>
                        <p class="text-muted small mb-2">or click to browse from your device</p>
                        <span id="modelFileName" class="badge bg-light text-dark">No file selected</span>
                    </label>
                    <small class="text-muted d-block mt-2">Only binary/ASCII STL files are supported.</small>
                    @error('model_3d')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h5 class="mb-0">Live 3D Preview</h5>
                                    <small class="text-muted">Models are auto-centered, scaled, and lit for clarity.</small>
                                </div>
                                <div class="text-muted small" id="previewStatus">Awaiting STL upload</div>
                            </div>
                            <div class="model-preview-area">
                                <canvas id="modelPreviewCanvas"></canvas>
                                <p class="text-muted small mb-0" id="modelPreviewPlaceholder">
                                    Upload an STL file to see it rendered in real-time.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white text-end">
            <button type="submit" class="btn btn-success px-4">
                <i class="bi bi-check2-circle"></i> Save Product
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script type="module">
import * as THREE from 'https://unpkg.com/three@0.161.0/build/three.module.js';
import { OrbitControls } from 'https://unpkg.com/three@0.161.0/examples/jsm/controls/OrbitControls.js';
import { STLLoader } from 'https://unpkg.com/three@0.161.0/examples/jsm/loaders/STLLoader.js';

class InlineStlPreviewer {
    constructor(canvas, placeholder, statusEl) {
        this.canvas = canvas;
        this.placeholder = placeholder;
        this.statusEl = statusEl;
        this.loader = new STLLoader();
    }

    init() {
        if (this.renderer) {
            return;
        }
        const width = this.canvas.clientWidth || this.canvas.parentElement.clientWidth;
        const height = this.canvas.clientHeight || 360;

        this.scene = new THREE.Scene();
        this.scene.background = new THREE.Color(0xf2f5f9);

        this.camera = new THREE.PerspectiveCamera(45, width / height, 0.1, 1000);
        this.camera.position.set(0, 0.6, 2.2);

        this.renderer = new THREE.WebGLRenderer({ canvas: this.canvas, antialias: true });
        this.renderer.setPixelRatio(window.devicePixelRatio);
        this.renderer.setSize(width, height);

        this.controls = new OrbitControls(this.camera, this.renderer.domElement);
        this.controls.enableDamping = true;

        const ambient = new THREE.AmbientLight(0xffffff, 0.6);
        const keyLight = new THREE.DirectionalLight(0xffffff, 0.85);
        keyLight.position.set(2, 2, 2);
        const fillLight = new THREE.DirectionalLight(0xffffff, 0.45);
        fillLight.position.set(-2, -1, -2);
        this.scene.add(ambient, keyLight, fillLight);

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
        const height = this.canvas.clientHeight || 360;
        this.camera.aspect = width / height;
        this.camera.updateProjectionMatrix();
        this.renderer.setSize(width, height);
    }

    loadArrayBuffer(buffer, label = 'Preview ready') {
        this.init();
        const geometry = this.loader.parse(buffer);
        this.displayGeometry(geometry, label);
    }

    displayGeometry(geometry, label) {
        geometry.computeVertexNormals();
        geometry.center();
        geometry.computeBoundingBox();
        geometry.computeBoundingSphere();

        if (this.mesh) {
            this.scene.remove(this.mesh);
        }

        const size = new THREE.Vector3();
        geometry.boundingBox.getSize(size);
        const maxDim = Math.max(size.x, size.y, size.z) || 1;
        const scale = 1.5 / maxDim;

        const material = new THREE.MeshStandardMaterial({
            color: 0x3081f0,
            metalness: 0.15,
            roughness: 0.35,
            flatShading: false,
        });

        this.mesh = new THREE.Mesh(geometry, material);
        this.mesh.scale.setScalar(scale);
        this.scene.add(this.mesh);

        if (this.placeholder) {
            this.placeholder.classList.add('d-none');
        }
        if (this.statusEl) {
            this.statusEl.textContent = label;
        }
    }
}

function initStlUploader() {
    const input = document.querySelector('#model3dInput');
    const dropZone = document.querySelector('#modelDropZone');
    const fileName = document.querySelector('#modelFileName');
    const canvas = document.querySelector('#modelPreviewCanvas');
    const placeholder = document.querySelector('#modelPreviewPlaceholder');
    const statusEl = document.querySelector('#previewStatus');

    if (!input || !dropZone || !canvas) {
        return;
    }

    const viewer = new InlineStlPreviewer(canvas, placeholder, statusEl);

    const handleFile = (file) => {
        if (!file) {
            return;
        }
        if (!file.name.toLowerCase().endsWith('.stl')) {
            alert('Please upload a valid .stl file.');
            return;
        }
        fileName.textContent = file.name;
        statusEl.textContent = 'Parsing STL...';
        const reader = new FileReader();
        reader.onload = (event) => viewer.loadArrayBuffer(event.target.result, 'Preview ready');
        reader.readAsArrayBuffer(file);
    };

    dropZone.addEventListener('click', () => input.click());
    dropZone.addEventListener('dragover', (event) => {
        event.preventDefault();
        dropZone.classList.add('drag-over');
    });
    dropZone.addEventListener('dragleave', () => dropZone.classList.remove('drag-over'));
    dropZone.addEventListener('drop', (event) => {
        event.preventDefault();
        dropZone.classList.remove('drag-over');
        const file = event.dataTransfer.files?.[0];
        if (file) {
            const transfer = new DataTransfer();
            transfer.items.add(file);
            input.files = transfer.files;
            handleFile(file);
        }
    });

    input.addEventListener('change', (event) => {
        const file = event.target.files?.[0];
        handleFile(file);
    });
}

document.addEventListener('DOMContentLoaded', initStlUploader);
</script>
@endsection
