@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Edit Product</h3>
            <p class="text-muted mb-0">Update product details and refresh the STL model preview instantly.</p>
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

    <form action="{{ route('admin.products.update', $id) }}" method="POST" enctype="multipart/form-data" class="card shadow-sm border-0">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Product Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $product['name'] ?? '') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Price ($)</label>
                    <input type="number" name="price" step="0.01" min="0" class="form-control" value="{{ old('price', $product['price'] ?? 0) }}" required>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea name="description" rows="4" class="form-control">{{ old('description', $product['description'] ?? '') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Product Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <small class="text-muted">JPEG, PNG, JPG, WEBP up to 4 MB.</small>
                    @if (!empty($product['image_path']))
                        <div class="mt-3 d-flex align-items-center gap-3">
                            <img src="{{ asset('storage/' . $product['image_path']) }}" alt="Current image" class="rounded border" style="width: 90px; height: 90px; object-fit: cover;">
                            <div class="text-muted small">Current image</div>
                        </div>
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">3D Model (.stl)</label>
                    <label id="modelDropZone" for="model3dInput" class="model-drop-zone mb-0">
                        <input type="file" id="model3dInput" name="model_3d" accept=".stl" class="d-none">
                        <i class="bi bi-cloud-arrow-up fs-1 text-primary mb-2"></i>
                        <p class="mb-1">Drag & drop STL file here</p>
                        <p class="text-muted small mb-2">or click to browse from your device</p>
                        <span id="modelFileName" class="badge bg-light text-dark">{{ !empty($product['model_3d']) ? basename($product['model_3d']) : 'No file selected' }}</span>
                    </label>
                    <small class="text-muted d-block mt-2">
                        @if (!empty($product['model_3d']))
                            Current model stored as {{ basename($product['model_3d']) }}.
                        @else
                            Upload an STL file to enable 3D previewing.
                        @endif
                    </small>
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
                                    <small class="text-muted">The viewer automatically re-centers and scales your STL.</small>
                                </div>
                                <div class="text-muted small" id="previewStatus">
                                    {{ !empty($product['model_3d']) ? 'Loaded existing model' : 'Awaiting STL upload' }}
                                </div>
                            </div>
                            <div class="model-preview-area">
                                <canvas id="modelPreviewCanvas" data-initial-model="{{ !empty($product['model_3d']) ? asset('storage/' . $product['model_3d']) : '' }}"></canvas>
                                <p class="text-muted small mb-0 {{ !empty($product['model_3d']) ? 'd-none' : '' }}" id="modelPreviewPlaceholder">
                                    {{ !empty($product['model_3d']) ? '' : 'Upload an STL file to see it rendered in real-time.' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white text-end">
            <button type="submit" class="btn btn-success px-4">
                <i class="bi bi-save"></i> Update Product
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
        this.camera.position.set(0, 0.6, 2.4);

        this.renderer = new THREE.WebGLRenderer({ canvas: this.canvas, antialias: true });
        this.renderer.setPixelRatio(window.devicePixelRatio);
        this.renderer.setSize(width, height);

        this.controls = new OrbitControls(this.camera, this.renderer.domElement);
        this.controls.enableDamping = true;

        const ambient = new THREE.AmbientLight(0xffffff, 0.55);
        const top = new THREE.DirectionalLight(0xffffff, 0.9);
        top.position.set(1.5, 2, 1.5);
        const rim = new THREE.DirectionalLight(0xffffff, 0.4);
        rim.position.set(-1.5, -0.5, -1.5);
        this.scene.add(ambient, top, rim);

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

    loadFromUrl(url) {
        if (!url) {
            return;
        }
        this.init();
        this.statusEl.textContent = 'Loading existing STL...';
        this.loader.load(url, (geometry) => this.displayGeometry(geometry, 'Existing model loaded'));
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
            color: 0x2f7df6,
            metalness: 0.2,
            roughness: 0.35,
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
    const initialUrl = canvas?.dataset?.initialModel ?? '';

    if (!input || !dropZone || !canvas) {
        return;
    }

    const viewer = new InlineStlPreviewer(canvas, placeholder, statusEl);
    if (initialUrl) {
        viewer.loadFromUrl(initialUrl);
    }

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

    input.addEventListener('change', (event) => handleFile(event.target.files?.[0]));
}

document.addEventListener('DOMContentLoaded', initStlUploader);
</script>
@endsection
